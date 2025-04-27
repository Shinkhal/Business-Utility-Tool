<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = auth()->user()->products()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products',
            'image' => 'nullable|image|max:2048',
            'active' => 'sometimes|boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['user_id'] = auth()->id();
        $product = Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'image' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        // Check if the product is used in any invoice
        if ($product->invoiceItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot delete product as it is used in invoices.');
        }

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
    
    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = auth()->user()->products()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->limit(10)
            ->get();
            
        return response()->json($products);
    }
}