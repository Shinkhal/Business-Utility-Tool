@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-primary">Invoices</h5>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create Invoice
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->customer->name }}</td>
                                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                    <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                    <td class="text-end fw-bold">{{ number_format($invoice->total, 2) }}</td>
                                    <td>
                                        @if($invoice->status == 'paid')
                                            <span class="badge bg-success rounded-pill">Paid</span>
                                        @elseif($invoice->status == 'sent')
                                            <span class="badge bg-info rounded-pill">Sent</span>
                                        @elseif($invoice->status == 'draft')
                                            <span class="badge bg-secondary rounded-pill">Draft</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($invoice->status == 'draft')
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm expanded-dropdown">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('invoices.pdf', $invoice) }}">
                                                            <i class="fas fa-download me-2 text-primary"></i> Download PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('invoices.preview-pdf', $invoice) }}" target="_blank">
                                                            <i class="fas fa-file-pdf me-2 text-danger"></i> Preview PDF
                                                        </a>
                                                    </li>
                                                    
                                                    @if($invoice->status == 'draft' || $invoice->status == 'sent')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('invoices.send', $invoice) }}"
                                                               onclick="return confirm('Are you sure you want to send this invoice to {{ $invoice->customer->email }}?')">
                                                                <i class="fas fa-envelope me-2 text-info"></i> Send Email
                                                            </a>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($invoice->status == 'sent')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('invoices.paid', $invoice) }}"
                                                               onclick="return confirm('Mark this invoice as paid?')">
                                                                <i class="fas fa-check-circle me-2 text-success"></i> Mark as Paid
                                                            </a>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($invoice->status != 'paid' && $invoice->status != 'cancelled')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('invoices.cancel', $invoice) }}"
                                                               onclick="return confirm('Are you sure you want to cancel this invoice?')">
                                                                <i class="fas fa-ban me-2 text-warning"></i> Cancel Invoice
                                                            </a>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($invoice->status == 'draft' || $invoice->status == 'cancelled')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" 
                                                               onclick="if(confirm('Are you sure you want to delete this invoice?')) { 
                                                                   document.getElementById('delete-invoice-{{ $invoice->id }}').submit(); 
                                                               }">
                                                                <i class="fas fa-trash me-2"></i> Delete
                                                            </a>
                                                            <form id="delete-invoice-{{ $invoice->id }}" 
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
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-invoice fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted mb-3">No invoices found</h5>
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Create your first invoice
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .expanded-dropdown {
        max-height: 300px !important;
        overflow-y: auto !important;
        padding-top: 8px !important;
        padding-bottom: 8px !important;
    }
</style>

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush

@endsection