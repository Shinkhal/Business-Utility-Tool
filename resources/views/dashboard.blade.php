@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-primary mb-0">Dashboard</h1>
        <div>
            <button class="btn btn-outline-primary" id="refreshDashboard">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 p-3">
                        <i class="fas fa-chart-line text-success opacity-25 fa-2x"></i>
                    </div>
                    <h5 class="card-title text-muted">This Month's Revenue</h5>
                    <h2 class="text-success fw-bold mb-0">₹{{ number_format($revenueThisMonth, 2) }}</h2>
                    
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 p-3">
                        <i class="fas fa-clock text-warning opacity-25 fa-2x"></i>
                    </div>
                    <h5 class="card-title text-muted">Outstanding Invoices</h5>
                    <h2 class="text-warning fw-bold mb-0">₹{{ number_format($outstandingAmount, 2) }}</h2>
                    
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 p-3">
                        <i class="fas fa-exclamation-triangle text-danger opacity-25 fa-2x"></i>
                    </div>
                    <h5 class="card-title text-muted">Overdue Invoices</h5>
                    <h2 class="text-danger fw-bold mb-0">₹{{ number_format($overdueAmount, 2) }}</h2>
                   
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="d-inline-block p-3 bg-primary bg-opacity-10 rounded-circle mb-3">
                        <i class="fas fa-users text-primary fa-2x"></i>
                    </div>
                    <h5 class="card-title">Customers</h5>
                    <h2 class="fw-bold">{{ $customerCount }}</h2>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary mt-3">
                        View All
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="d-inline-block p-3 bg-info bg-opacity-10 rounded-circle mb-3">
                        <i class="fas fa-box text-info fa-2x"></i>
                    </div>
                    <h5 class="card-title">Products</h5>
                    <h2 class="fw-bold">{{ $productCount }}</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-info mt-3">
                        View All
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <div class="d-inline-block p-3 bg-secondary bg-opacity-10 rounded-circle mb-3">
                        <i class="fas fa-file-invoice text-secondary fa-2x"></i>
                    </div>
                    <h5 class="card-title">Invoices</h5>
                    <h2 class="fw-bold">{{ $invoiceCount }}</h2>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary mt-3">
                        View All
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Revenue Flow</h5>
                </div>
                <div class="card-body">
                    <div class="mermaid" id="revenueFlowchart">
                        flowchart LR
                            Start(["Business Operations"]) --> Products["Products<br>{{ number_format($productCount) }}"]
                            Start --> Customers["Customers<br>{{ number_format($customerCount) }}"]
                            Customers --> Invoices["Total Invoices<br>{{ number_format($invoiceCount) }}"]
                            Products --> Invoices
                            Invoices --> Paid["Paid<br>₹{{ number_format($revenueThisMonth, 2) }}"]
                            Invoices --> Outstanding["Outstanding<br>₹{{ number_format($outstandingAmount, 2) }}"]
                            Invoices --> Overdue["Overdue<br>₹{{ number_format($overdueAmount, 2) }}"]
                            Paid --> Revenue["Total Revenue<br>₹{{ number_format($revenueThisMonth + $outstandingAmount + $overdueAmount, 2) }}"]
                            Outstanding -.-> Revenue
                            Overdue -.-> Revenue
                            
                            classDef green fill:#d4edda,stroke:#28a745,color:#155724,rx:8,ry:8
                            classDef yellow fill:#fff3cd,stroke:#ffc107,color:#856404,rx:8,ry:8
                            classDef red fill:#f8d7da,stroke:#dc3545,color:#721c24,rx:8,ry:8
                            classDef blue fill:#cce5ff,stroke:#0d6efd,color:#004085,rx:8,ry:8
                            classDef purple fill:#e2d9f3,stroke:#6f42c1,color:#44056e,rx:8,ry:8
                            classDef default rx:8,ry:8
                            
                            class Paid green
                            class Outstanding yellow
                            class Overdue red
                            class Customers blue
                            class Products purple
                            class Revenue blue
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Monthly Revenue Trend</h5>
                </div>
                <div class="card-body">
                    <div class="mermaid" id="revenueChart">
                        flowchart LR
                            @foreach($monthlyRevenue as $index => $item)
                                @if($index == 0)
                                    {{ str_replace(' ', '', $item['month']) }}["{{ $item['month'] }}<br>₹{{ number_format($item['revenue'], 2) }}"]
                                @else
                                    {{ str_replace(' ', '', $monthlyRevenue[$index-1]['month']) }} --> {{ str_replace(' ', '', $item['month']) }}["{{ $item['month'] }}<br>₹{{ number_format($item['revenue'], 2) }}"]
                                @endif
                            @endforeach
                            
                            classDef default fill:#f5f5f5,stroke:#333,stroke-width:1px,rx:8,ry:8
                            @foreach($monthlyRevenue as $index => $item)
                                @php
                                    $colors = ['blue', 'green', 'purple', 'orange', 'cyan', 'pink'];
                                    $color = $colors[$index % count($colors)];
                                @endphp
                                classDef {{ $color }} fill:{{ getColorCode($color) }},stroke:{{ getDarkerColorCode($color) }},color:{{ getTextColorCode($color) }},rx:8,ry:8
                                class {{ str_replace(' ', '', $item['month']) }} {{ $color }}
                            @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Recent Invoices</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($recentInvoices as $invoice)
                            <li class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-decoration-none text-dark">
                                            #{{ $invoice->invoice_number }}
                                        </a>
                                        <div class="small text-muted">{{ $invoice->customer->name }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill px-2
                                            {{ $invoice->status === 'paid' ? 'bg-success bg-opacity-25 text-success' : '' }}
                                            {{ $invoice->status === 'sent' ? 'bg-warning bg-opacity-25 text-warning' : '' }}
                                            {{ $invoice->status === 'draft' ? 'bg-secondary bg-opacity-25 text-secondary' : '' }}
                                            {{ $invoice->status === 'cancelled' ? 'bg-danger bg-opacity-25 text-danger' : '' }}
                                        ">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <div class="small fw-bold text-secondary mt-1">₹{{ number_format($invoice->total, 2) }}</div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item border-0 py-4 text-center">
                                <div class="text-muted">
                                    <i class="fas fa-file-invoice opacity-25 mb-2"></i>
                                    <p class="mb-0">No recent invoices</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer bg-white text-center border-0">
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">
                        View All Invoices
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Mermaid
        mermaid.initialize({
            startOnLoad: true,
            theme: 'default',
            flowchart: {
                useMaxWidth: true,
                htmlLabels: true,
                curve: 'basis'
            }
        });
        
       
        
        // Refresh dashboard button
        const refreshButton = document.getElementById('refreshDashboard');
        if (refreshButton) {
            refreshButton.addEventListener('click', function() {
                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Refreshing...';
                
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
        }
    });
</script>
@endsection

@php
function getColorCode($color) {
    $colorMap = [
        'blue' => '#cce5ff',
        'green' => '#d4edda',
        'purple' => '#e2d9f3',
        'orange' => '#fff3cd',
        'cyan' => '#d1ecf1',
        'pink' => '#f8d7da'
    ];
    return $colorMap[$color] ?? '#f5f5f5';
}

function getDarkerColorCode($color) {
    $colorMap = [
        'blue' => '#0d6efd',
        'green' => '#28a745',
        'purple' => '#6f42c1',
        'orange' => '#fd7e14',
        'cyan' => '#17a2b8',
        'pink' => '#e83e8c'
    ];
    return $colorMap[$color] ?? '#333333';
}

function getTextColorCode($color) {
    $colorMap = [
        'blue' => '#004085',
        'green' => '#155724',
        'purple' => '#44056e',
        'orange' => '#856404',
        'cyan' => '#0c5460',
        'pink' => '#721c24'
    ];
    return $colorMap[$color] ?? '#212529';
}
@endphp