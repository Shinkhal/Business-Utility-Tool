@extends('layouts.app')

@section('styles')
<style>
    .delete-row {
        color: #dc3545;
        cursor: pointer;
    }
    .delete-row:hover {
        color: #bd2130;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Invoice #{{ $invoice->invoice_number }}</h5>
            <div>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Invoice
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Customer Information</h6>
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Invoice Details</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="invoice_number" class="form-label">Invoice Number</label>
                                        <input type="text" class="form-control" id="invoice_number" value="{{ $invoice->invoice_number }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="sent" {{ old('status', $invoice->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                                        @error('invoice_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Invoice Items</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 25%">Product</th>
                                        <th style="width: 30%">Description</th>
                                        <th style="width: 10%">Quantity</th>
                                        <th style="width: 15%">Price</th>
                                        <th style="width: 15%">Subtotal</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <select class="form-select product-select" name="items[{{ $index }}][product_id]">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}" {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }} ({{ $product->sku }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control item-description" name="items[{{ $index }}][description]" required value="{{ $item['description'] }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" min="1" required value="{{ $item['quantity'] }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-price" name="items[{{ $index }}][price]" step="0.01" min="0" required value="{{ $item['price'] }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-subtotal" name="items[{{ $index }}][subtotal]" step="0.01" min="0" required readonly value="{{ $item['subtotal'] }}">
                                                </td>
                                                <td class="text-center">
                                                    <i class="fas fa-trash delete-row"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($invoice->items as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <select class="form-select product-select" name="items[{{ $index }}][product_id]">
                                                        <option value="">Select Product</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }} ({{ $product->sku ?? 'N/A' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control item-description" name="items[{{ $index }}][description]" required value="{{ $item->description }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" min="1" required value="{{ $item->quantity }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-price" name="items[{{ $index }}][price]" step="0.01" min="0" required value="{{ $item->price }}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control item-subtotal" name="items[{{ $index }}][subtotal]" step="0.01" min="0" required readonly value="{{ $item->subtotal }}">
                                                </td>
                                                <td class="text-center">
                                                    <i class="fas fa-trash delete-row"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Notes</h6>
                                <div class="mb-3">
                                    <textarea class="form-control" id="notes" name="notes" rows="5">{{ old('notes', $invoice->notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span id="summary-subtotal">{{ number_format($invoice->subtotal, 2) }}</span>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-7">
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="tax_percent" name="tax_percent" step="0.01" min="0" max="100" value="{{ old('tax_percent', $invoice->tax_percent) }}">
                                            <span class="input-group-text">% Tax</span>
                                        </div>
                                    </div>
                                    <div class="col-5 text-end">
                                        <span id="summary-tax">{{ number_format($invoice->tax_amount, 2) }}</span>
                                        <input type="hidden" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $invoice->tax_amount) }}">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-7">
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="{{ old('discount_percent', $invoice->discount_percent) }}">
                                            <span class="input-group-text">% Discount</span>
                                        </div>
                                    </div>
                                    <div class="col-5 text-end">
                                        <span id="summary-discount">{{ number_format($invoice->discount_amount, 2) }}</span>
                                        <input type="hidden" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}">
                                    </div>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <h5>Total</h5>
                                    <h5 id="summary-total">{{ number_format($invoice->total, 2) }}</h5>
                                    <input type="hidden" id="subtotal" name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}">
                                    <input type="hidden" id="total" name="total" value="{{ old('total', $invoice->total) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Row Template (Hidden) -->
<table class="d-none">
    <tbody id="item-template">
        <tr class="item-row">
            <td>
                <select class="form-select product-select" name="items[__INDEX__][product_id]">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}">
                            {{ $product->name }} ({{ $product->sku ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="items[__INDEX__][id]" value="">
            </td>
            <td>
                <input type="text" class="form-control item-description" name="items[__INDEX__][description]" required>
            </td>
            <td>
                <input type="number" class="form-control item-quantity" name="items[__INDEX__][quantity]" min="1" required value="1">
            </td>
            <td>
                <input type="number" class="form-control item-price" name="items[__INDEX__][price]" step="0.01" min="0" required value="0.00">
            </td>
            <td>
                <input type="number" class="form-control item-subtotal" name="items[__INDEX__][subtotal]" step="0.01" min="0" required readonly value="0.00">
            </td>
            <td class="text-center">
                <i class="fas fa-trash delete-row"></i>
            </td>
        </tr>
    </tbody>
</table>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowIndex = document.querySelectorAll('.item-row').length;
        
        // Add new item row
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const template = document.getElementById('item-template').innerHTML;
            const newRow = template.replace(/__INDEX__/g, rowIndex);
            
            document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', newRow);
            bindRowEvents(document.querySelectorAll('.item-row')[rowIndex]);
            rowIndex++;
            
            // Update totals
            updateTotals();
        });
        
        // Bind events to existing rows
        document.querySelectorAll('.item-row').forEach(function(row) {
            bindRowEvents(row);
        });
        
        // Handle delete row event
        document.querySelector('#itemsTable tbody').addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-row')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    updateTotals();
                } else {
                    alert('Invoice must have at least one item.');
                }
            }
        });
        
        // Handle tax and discount changes
        document.getElementById('tax_percent').addEventListener('input', updateTotals);
        document.getElementById('discount_percent').addEventListener('input', updateTotals);
        
        // Initial calculation
        updateTotals();
        
        // Form validation
        document.getElementById('invoiceForm').addEventListener('submit', function(e) {
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the invoice.');
                return false;
            }
            
            // Update all calculations before submit
            updateTotals();
            return true;
        });
        
        // Function to bind events to row elements
        function bindRowEvents(row) {
            // Product selection
            const productSelect = row.querySelector('.product-select');
            productSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (option.value) {
                    row.querySelector('.item-price').value = option.dataset.price;
                    
                    // Only update description if it's empty or matches a previous product description
                    const descriptionField = row.querySelector('.item-description');
                    if (!descriptionField.value || descriptionField.dataset.autoFilled === 'true') {
                        descriptionField.value = option.dataset.description || '';
                        descriptionField.dataset.autoFilled = 'true';
                    }
                } else {
                    if (row.querySelector('.item-description').dataset.autoFilled === 'true') {
                        row.querySelector('.item-description').value = '';
                    }
                    row.querySelector('.item-price').value = '0.00';
                }
                updateRowTotal(row);
                updateTotals();
            });
            
            // Quantity and price changes
            row.querySelector('.item-quantity').addEventListener('input', function() {
                updateRowTotal(row);
                updateTotals();
            });
            
            row.querySelector('.item-price').addEventListener('input', function() {
                updateRowTotal(row);
                updateTotals();
            });
            
            // Manual description changes
            row.querySelector('.item-description').addEventListener('input', function(){
                this.dataset.autoFilled = 'false';
            });
            updateRowTotal(row);

        }

// Calculate row total
function updateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const subtotal = quantity * price;
    row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
}

// Update all totals
function updateTotals() {
    // Calculate subtotal
    let subtotal = 0;
    document.querySelectorAll('.item-subtotal').forEach(function(input) {
        subtotal += parseFloat(input.value) || 0;
    });
    
    // Tax calculation
    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    const taxAmount = subtotal * (taxPercent / 100);
    
    // Discount calculation
    const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
    const discountAmount = subtotal * (discountPercent / 100);
    
    // Total calculation
    const total = subtotal + taxAmount - discountAmount;
    
    // Update summary display
    document.getElementById('summary-subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('summary-tax').textContent = taxAmount.toFixed(2);
    document.getElementById('summary-discount').textContent = discountAmount.toFixed(2);
    document.getElementById('summary-total').textContent = total.toFixed(2);
    
    // Update hidden fields
    document.getElementById('subtotal').value = subtotal.toFixed(2);
    document.getElementById('tax_amount').value = taxAmount.toFixed(2);
    document.getElementById('discount_amount').value = discountAmount.toFixed(2);
    document.getElementById('total').value = total.toFixed(2);
}

    });
</script>
@endsection