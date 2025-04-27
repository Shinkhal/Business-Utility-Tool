@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Invoice #{{ $invoice->invoice_number }}</h5>
            <div>
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm me-1">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
                </a>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-cog"></i> Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('invoices.pdf', $invoice) }}">
                                <i class="fas fa-download me-2"></i> Download PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('invoices.preview-pdf', $invoice) }}" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> Preview PDF
                            </a>
                        </li>
                        
                        @if($invoice->status == 'draft')
                            <li>
                                <a class="dropdown-item" href="{{ route('invoices.edit', $invoice) }}">
                                    <i class="fas fa-edit me-2"></i> Edit Invoice
                                </a>
                            </li>
                        @endif
                        
                        @if($invoice->status == 'draft' || $invoice->status == 'sent')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('invoices.send', $invoice) }}"
                                   onclick="return confirm('Are you sure you want to send this invoice to {{ $invoice->customer->email }}?')">
                                    <i class="fas fa-envelope me-2"></i> Send Email
                                </a>
                            </li>
                        @endif
                        
                        @if($invoice->status == 'sent')
                            <li>
                                <a class="dropdown-item" href="{{ route('invoices.paid', $invoice) }}"
                                   onclick="return confirm('Mark this invoice as paid?')">
                                    <i class="fas fa-check-circle me-2"></i> Mark as Paid
                                </a>
                            </li>
                        @endif
                        
                        @if($invoice->status != 'paid' && $invoice->status != 'cancelled')
                            <li>
                                <a class="dropdown-item" href="{{ route('invoices.cancel', $invoice) }}"
                                   onclick="return confirm('Are you sure you want to cancel this invoice?')">
                                    <i class="fas fa-ban me-2"></i> Cancel Invoice
                                </a>
                            </li>
                        @endif
                        
                        @if($invoice->status == 'draft' || $invoice->status == 'cancelled')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" 
                                   onclick="if(confirm('Are you sure you want to delete this invoice?')) { 
                                       document.getElementById('delete-invoice-form').submit(); 
                                   }">
                                    <i class="fas fa-trash me-2"></i> Delete
                                </a>
                                <form id="delete-invoice-form" 
                                      action="{{ route('invoices.destroy', $invoice) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h4 class="mb-1">{{ $setting->company_name }}</h4>
                            <p class="mb-0">{{ $setting->company_address }}</p>
                            <p class="mb-0">{{ $setting->company_city }}, {{ $setting->company_state }} {{ $setting->company_postal_code }}</p>
                            <p class="mb-0">{{ $setting->company_country }}</p>
                            <p class="mb-0">{{ $setting->company_phone }}</p>
                            <p class="mb-0">{{ $setting->company_email }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="text-muted mb-2">Bill To:</h5>
                        <h5 class="mb-1">{{ $invoice->customer->name }}</h5>
                        <p class="mb-0">{{ $invoice->customer->address }}</p>
                        <p class="mb-0">{{ $invoice->customer->city }}, {{ $invoice->customer->state }} {{ $invoice->customer->postal_code }}</p>
                        <p class="mb-0">{{ $invoice->customer->country }}</p>
                        <p class="mb-0">{{ $invoice->customer->phone }}</p>
                        <p class="mb-0">{{ $invoice->customer->email }}</p>
                    </div>
                </div>
                
                <div class="col-md-5">
                    <div class="bg-light p-4 rounded">
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Invoice Number:</div>
                            <div class="col-6 text-end">{{ $invoice->invoice_number }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Invoice Date:</div>
                            <div class="col-6 text-end">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-muted">Due Date:</div>
                            <div class="col-6 text-end">{{ $invoice->due_date->format('M d, Y') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-muted">Status:</div>
                            <div class="col-6 text-end">
                                @if($invoice->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($invoice->status == 'sent')
                                    <span class="badge bg-info">Sent</span>
                                @elseif($invoice->status == 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive mb-4">
                <table class="table table-striped border">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%">Description</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>
                                    <div>{{ $item->description }}</div>
                                    @if($item->product)
                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Subtotal:</td>
                            <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end">Tax ({{ $invoice->tax_percent }}%):</td>
                                <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end">Discount ({{ $invoice->discount_percent }}%):</td>
                                <td class="text-end">-{{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold">{{ number_format($invoice->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            @if($invoice->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Notes</h6>
                    </div>
                    <div class="card-body">
                        {{ $invoice->notes }}
                    </div>
                </div>
            @endif
            
            <div class="text-center mt-4">
                <p class="mb-0">Thank you for your business!</p>
            </div>
        </div>
        
        @if($invoice->status != 'draft' && $invoice->status != 'cancelled')
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Payment Terms</small>
                        <p class="mb-0">{{ $setting->payment_terms ?? 'Payment due within 30 days.' }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        @if($setting->bank_details)
                            <small class="text-muted">Bank Information</small>
                            <p class="mb-0">{{ $setting->bank_details }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection