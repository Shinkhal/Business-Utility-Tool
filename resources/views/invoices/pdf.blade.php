<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px; /* Reduced font size */
            line-height: 1.4;
            color: #333;
            background-color: #fff;
            margin: auto;
        }
        
        .container {
            width: 100%;
            max-width: 100%;
            padding: 10px;
        }
        
        /* Header styling with reduced spacing */
        .invoice-header {
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .invoice-header:after {
            content: "";
            display: table;
            clear: both;
        }
        
        .company-info {
            float: left;
            width: 60%;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1a3057;
        }
        
        .invoice-info {
            float: right;
            width: 35%;
            text-align: right;
        }
        
        .invoice-id {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #1a3057;
        }
        
        /* Status badge styling */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .status-draft {
            background-color: #f5f5f5;
            color: #7f8c8d;
        }
        
        .status-sent {
            background-color: #3498db;
            color: #fff;
        }
        
        .status-paid {
            background-color: #2ecc71;
            color: #fff;
        }
        
        .status-cancelled {
            background-color: #e74c3c;
            color: #fff;
        }
        
        /* Customer and billing details */
        .details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .customer-details, .invoice-details {
            width: 48%;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
            color: #1a3057;
            text-transform: uppercase;
        }
        
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
        }
        
        .text-right {
            text-align: right;
        }
        
        /* Totals section */
        .totals-section {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        .totals-table {
            width: 35%;
            float: right;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .totals-table td {
            padding: 4px 8px;
            border-bottom: none;
        }
        
        .totals-table .total-row td {
            border-top: 1px solid #ddd;
            font-weight: 700;
        }
        
        /* Notes section */
        .notes {
            margin-top: 10px;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
            font-size: 10px;
        }
        
        /* Footer styling */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
        
        /* Utilities */
        .mb-2 {
            margin-bottom: 4px;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        /* Two-column layout for payment details and notes */
        .bottom-section {
            display: flex;
            justify-content: space-between;
        }
        
        .payment-terms {
            width: 48%;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 4px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="invoice-header">
    @if($setting->company_logo)
        <div class="company-logo">
            <img src="{{ public_path('storage/'.str_replace('public/', '', $setting->company_logo)) }}" alt="{{ $setting->company_name }} Logo" style="max-height: 60px; max-width: 200px;">
        </div>
    @endif

    <div class="company-info">
        <div class="company-name">{{ $setting->company_name }}</div>
        <div class="mb-2">{{ $setting->company_address }}</div>
        <div class="mb-2">{{ $setting->company_phone }}</div>
        <div class="mb-2">{{ $setting->company_email }}</div>
        @if($setting->tax_number)
            <div>GST/Tax ID: {{ $setting->tax_number }}</div>
        @endif
    </div>

    <div class="invoice-info">
        <div class="invoice-id">INVOICE #{{ $invoice->invoice_number }}</div>
        <div class="status-badge status-{{ $invoice->status }}">
            {{ ucfirst($invoice->status) }}
        </div>
        <div class="mb-2">Issue Date: {{ $invoice->invoice_date->format('M d, Y') }}</div>
        <div class="mb-2">Due Date: {{ $invoice->due_date->format('M d, Y') }}</div>
    </div>
</div>


        <div class="details-container">
            <div class="customer-details">
                <div class="section-title">Bill To</div>
                <div style="font-weight: 600;">{{ $invoice->customer->name }}</div>
                <div class="mb-2">{{ $invoice->customer->address }}</div>
                <div class="mb-2">{{ $invoice->customer->city }}, {{ $invoice->customer->state }} {{ $invoice->customer->zip }}</div>
                <div class="mb-2">{{ $invoice->customer->country }}</div>
                <div class="mb-2">{{ $invoice->customer->phone }}</div>
                <div class="mb-2">{{ $invoice->customer->email }}</div>
                @if($invoice->customer->vat_number)
                    <div>VAT ID: {{ $invoice->customer->vat_number }}</div>
                @endif
            </div>
        </div>

        <div class="section-title">Invoice Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Description</th>
                    <th style="width: 15%">Quantity</th>
                    <th style="width: 20%">Unit Price</th>
                    <th style="width: 25%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $item->product->name ?? '' }}</div>
                            <div style="font-size: 10px; color: #666;">{{ $item->description }}</div>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $setting->currency_symbol }} {{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_amount > 0)
                <tr>
                    <td>Tax ({{ $invoice->tax_percent }}%):</td>
                    <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->discount_amount > 0)
                <tr>
                    <td>Discount ({{ $invoice->discount_percent }}%):</td>
                    <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Due:</td>
                    <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($invoice->total, 2) }}</td>
                </tr>
            </table>
            <div style="clear: both;"></div>
        </div>
        
        <div class="bottom-section">
            @if($invoice->notes)
            <div class="notes">
                <div class="section-title">Notes</div>
                <div>{{ $invoice->notes }}</div>
            </div>
            @endif
            
        </div>
        
        <div class="footer">
            <p>{{ $setting->invoice_footer }}</p>
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>