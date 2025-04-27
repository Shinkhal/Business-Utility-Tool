@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Product Details</span>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit</a>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        @if($product->image)
                        <div class="col-md-4 mb-4">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                        @else
                        <div class="col-md-12">
                        @endif
                            <h3>{{ $product->name }}</h3>
                            
                            <div class="mb-3">
                                <span class="badge {{ $product->active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Price:</strong> {{ number_format($product->price, 2) }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>Stock:</strong> {{ $product->stock }}
                            </div>
                            
                            @if($product->sku)
                            <div class="mb-3">
                                <strong>SKU:</strong> {{ $product->sku }}
                            </div>
                            @endif
                            
                            @if($product->description)
                            <div class="mb-4">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $product->description }}</p>
                            </div>
                            @endif
                            
                            <div class="mb-3">
                                <strong>Created:</strong> {{ $product->created_at->format('M d, Y') }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>Last Updated:</strong> {{ $product->updated_at->format('M d, Y') }}
                            </div>
                            
                            <div class="mt-4">
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection