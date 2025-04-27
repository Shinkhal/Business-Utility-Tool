@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customers</h5>
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Customer
            </a>
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
            
            @if($customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->id }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $customer->city ?? '-' }}</td>
                                    <td>{{ $customer->country ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                onclick="if(confirm('Are you sure you want to delete this customer?')) { 
                                                    document.getElementById('delete-customer-{{ $customer->id }}').submit(); 
                                                }">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-customer-{{ $customer->id }}" 
                                                  action="{{ route('customers.destroy', $customer) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            @else
                <div class="text-center p-4">
                    <p class="mb-3">No customers found.</p>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add your first customer
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection