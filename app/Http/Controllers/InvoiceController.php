<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use PDF;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $invoices = auth()->user()->invoices()->with('customer')->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = auth()->user()->customers()->get();
        $products = auth()->user()->products()->where('active', true)->get();
        $invoiceNumber = Invoice::generateInvoiceNumber();
        $setting = auth()->user()->setting;
        
        return view('invoices.create', compact('customers', 'products', 'invoiceNumber', 'setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        // Verify that the customer belongs to the current user
        $customer = Customer::findOrFail($validated['customer_id']);
        if ($customer->user_id !== auth()->id()) {
            return back()->withErrors(['customer_id' => 'Invalid customer selected.']);
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create invoice
            $invoice = new Invoice();
            $invoice->invoice_number = $validated['invoice_number'];
            $invoice->customer_id = $validated['customer_id'];
            $invoice->invoice_date = $validated['invoice_date'];
            $invoice->due_date = $validated['due_date'];
            $invoice->notes = $validated['notes'];
            $invoice->subtotal = $validated['subtotal'];
            $invoice->tax_percent = $validated['tax_percent'];
            $invoice->tax_amount = $validated['tax_amount'];
            $invoice->discount_percent = $validated['discount_percent'];
            $invoice->discount_amount = $validated['discount_amount'];
            $invoice->total = $validated['total'];
            $invoice->status = $validated['status'];
            $invoice->user_id = auth()->id();
            $invoice->save();

            // Create invoice items
            foreach ($validated['items'] as $item) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->product_id = $item['product_id'] ?? null;
                $invoiceItem->description = $item['description'];
                $invoiceItem->quantity = $item['quantity'];
                $invoiceItem->price = $item['price'];
                $invoiceItem->subtotal = $item['subtotal'];
                $invoiceItem->save();

                // Update product stock if product_id is provided
                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->user_id === auth()->id()) {
                        $product->stock -= $item['quantity'];
                        $product->save();
                    }
                }
            }

            DB::commit();

            if ($validated['status'] === 'sent') {
                // Send invoice by email
                try {
                    $this->sendInvoiceByEmail($invoice);
                } catch (\Exception $e) {
                    return redirect()->route('invoices.show', $invoice)
                        ->with('error', 'Invoice created but email could not be sent: ' . $e->getMessage());
                }
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while creating the invoice: ' . $e->getMessage()]);
        }
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load('customer', 'items.product');
        $setting = auth()->user()->setting;
        
        return view('invoices.show', compact('invoice', 'setting'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        // Only draft invoices can be edited
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be edited.');
        }
        
        $customers = auth()->user()->customers()->get();
        $products = auth()->user()->products()->where('active', true)->get();
        $invoice->load('customer', 'items.product');
        $setting = auth()->user()->setting;
        
        return view('invoices.edit', compact('invoice', 'customers', 'products', 'setting'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        // Only draft invoices can be updated
        if ($invoice->status !== 'draft') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Only draft invoices can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax_percent' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'discount_amount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        // Verify that the customer belongs to the current user
        $customer = Customer::findOrFail($validated['customer_id']);
        if ($customer->user_id !== auth()->id()) {
            return back()->withErrors(['customer_id' => 'Invalid customer selected.']);
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // First, restore the stock for existing items
            foreach ($invoice->items as $oldItem) {
                if ($oldItem->product_id) {
                    $product = Product::find($oldItem->product_id);
                    if ($product && $product->user_id === auth()->id()) {
                        $product->stock += $oldItem->quantity;
                        $product->save();
                    }
                }
            }
            
            // Update invoice details
            $invoice->customer_id = $validated['customer_id'];
            $invoice->invoice_date = $validated['invoice_date'];
            $invoice->due_date = $validated['due_date'];
            $invoice->notes = $validated['notes'];
            $invoice->subtotal = $validated['subtotal'];
            $invoice->tax_percent = $validated['tax_percent'];
            $invoice->tax_amount = $validated['tax_amount'];
            $invoice->discount_percent = $validated['discount_percent'];
            $invoice->discount_amount = $validated['discount_amount'];
            $invoice->total = $validated['total'];
            $invoice->status = $validated['status'];
            $invoice->save();

            // Get existing items
            $existingItems = $invoice->items->keyBy('id');
            $updatedItemIds = [];

            // Update or create invoice items
            foreach ($validated['items'] as $itemData) {
                if (!empty($itemData['id']) && isset($existingItems[$itemData['id']])) {
                    // Update existing item
                    $item = $existingItems[$itemData['id']];
                    $item->product_id = $itemData['product_id'] ?? null;
                    $item->description = $itemData['description'];
                    $item->quantity = $itemData['quantity'];
                    $item->price = $itemData['price'];
                    $item->subtotal = $itemData['subtotal'];
                    $item->save();
                    
                    $updatedItemIds[] = $item->id;
                } else {
                    // Create new item
                    $item = new InvoiceItem();
                    $item->invoice_id = $invoice->id;
                    $item->product_id = $itemData['product_id'] ?? null;
                    $item->description = $itemData['description'];
                    $item->quantity = $itemData['quantity'];
                    $item->price = $itemData['price'];
                    $item->subtotal = $itemData['subtotal'];
                    $item->save();
                    
                    $updatedItemIds[] = $item->id;
                }

                // Update product stock if product_id is provided
                if (!empty($itemData['product_id'])) {
                    $product = Product::find($itemData['product_id']);
                    if ($product && $product->user_id === auth()->id()) {
                        $product->stock -= $itemData['quantity'];
                        $product->save();
                    }
                }
            }

            // Delete items that are not in the updated list
            foreach ($existingItems as $existingItem) {
                if (!in_array($existingItem->id, $updatedItemIds)) {
                    $existingItem->delete();
                }
            }

            DB::commit();

            if ($validated['status'] === 'sent') {
                // Send invoice by email
                try {
                    $this->sendInvoiceByEmail($invoice);
                } catch (\Exception $e) {
                    return redirect()->route('invoices.show', $invoice)
                        ->with('error', 'Invoice updated but email could not be sent: ' . $e->getMessage());
                }
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while updating the invoice: ' . $e->getMessage()]);
        }
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        
        // Only draft or cancelled invoices can be deleted
        if (!in_array($invoice->status, ['draft', 'cancelled'])) {
            return redirect()->route('invoices.index')
                ->with('error', 'Only draft or cancelled invoices can be deleted.');
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Restore product stock for all items
            foreach ($invoice->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->user_id === auth()->id()) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }
            
            // Delete invoice (will cascade delete items due to foreign key constraint)
            $invoice->delete();
            
            DB::commit();
            
            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('invoices.index')
                ->with('error', 'An error occurred while deleting the invoice: ' . $e->getMessage());
        }
    }
    
    public function sendEmail(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        try {
            $this->sendInvoiceByEmail($invoice);
            
            // Update status to sent if it was draft
            if ($invoice->status === 'draft') {
                $invoice->status = 'sent';
                $invoice->save();
            }
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice sent successfully.');
                
        } catch (\Exception $e) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }
    
    protected function sendInvoiceByEmail(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product', 'user.setting');
        $setting = $invoice->user->setting;
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'setting'));
        
        Mail::to($invoice->customer->email)
    ->send(new InvoiceMail($invoice, $pdf, $setting));  // Pass setting here
    }
    
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load('customer', 'items.product');
        $setting = auth()->user()->setting;
        
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'setting'));
        
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
    
    public function markAsPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        if ($invoice->status === 'cancelled') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot mark a cancelled invoice as paid.');
        }
        
        $invoice->status = 'paid';
        $invoice->save();
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }
    
    public function markAsCancelled(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot cancel a paid invoice.');
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Restore product stock
            foreach ($invoice->items as $item) {
                if ($item->product_id) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->user_id === auth()->id()) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }
            
            $invoice->status = 'cancelled';
            $invoice->save();
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Failed to cancel invoice: ' . $e->getMessage());
        }
    }
}