<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function generateInvoicePDF(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        $invoice->load('customer', 'items.product');
        $setting = Auth::user()->setting;
        
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'setting'));
        
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}