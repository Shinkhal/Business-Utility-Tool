@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Details</h5>
            <div>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Customers
                </a>
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
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 35%">Customer Name:</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $customer->phone ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="border-bottom pb-2 mb-3">Address Information</h6>
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 35%">Address:</th>
                            <td>{{ $customer->address ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>City:</th>
                            <td>{{ $customer->city ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>State/Province:</th>
                            <td>{{ $customer->state ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Postal Code:</th>
                            <td>{{ $customer->postal_code ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Country:</th>
                            <td>{{ $customer->country ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">Customer Invoices</h6>
                    
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                            <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                            <td>{{ number_format($invoice->total, 2) }}</td>
                                            <td>
                                                @if($invoice->status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($invoice->status == 'sent')
                                                    <span class="badge bg-info">Sent</span>
                                                @elseif($invoice->status == 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @else
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $invoices->links() }}
                        </div>
                    @else
                        <div class="text-center p-3">
                            <p>No invoices found for this customer.</p>
                            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection