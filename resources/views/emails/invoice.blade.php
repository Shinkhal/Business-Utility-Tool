<!-- invoices/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .invoice-header {
            margin-bottom: 30px;
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
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .invoice-info {
            float: right;
            width: 40%;
            text-align: right;
        }
        .invoice-id {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
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
        .customer-details, .invoice-details {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 40%;
            float: right;
            margin-top: 20px;
        }
        .totals-table td {
            border: none;
            padding: 5px 10px;
        }
        .totals-table .total-row td {
            border-top: 2px solid #ddd;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .terms {
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-name">{{ $setting->company_name }}</div>
                <div>{{ $setting->address }}</div>
                <div>{{ $setting->city }}, {{ $setting->state }} {{ $setting->zip }}</div>
                <div>{{ $setting->country }}</div>
                <div>{{ $setting->phone }}</div>
                <div>{{ $setting->email }}</div>
                @if($setting->vat_number)
                    <div>VAT: {{ $setting->vat_number }}</div>
                @endif
            </div>
            <div class="invoice-info">
                <div class="invoice-id">INVOICE #{{ $invoice->invoice_number }}</div>
                <div class="
                    status-badge 
                    status-{{ $invoice->status }}"
                >
                    {{ ucfirst($invoice->status) }}
                </div>
                <div><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</div>
                <div><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="customer-details">
            <div class="section-title">Bill To</div>
            <div><strong>{{ $invoice->customer->name }}</strong></div>
            <div>{{ $invoice->customer->address }}</div>
            <div>{{ $invoice->customer->city }}, {{ $invoice->customer->state }} {{ $invoice->customer->zip }}</div>
            <div>{{ $invoice->customer->country }}</div>
            <div>{{ $invoice->customer->email }}</div>
            <div>{{ $invoice->customer->phone }}</div>
            @if($invoice->customer->vat_number)
                <div>VAT: {{ $invoice->customer->vat_number }}</div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Description</th>
                    <th style="width: 20%">Quantity</th>
                    <th style="width: 20%">Unit Price</th>
                    <th style="width: 20%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name ?? '' }}</strong>
                            <div>{{ $item->description }}</div>
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $setting->currency_symbol }} {{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                <td>Total:</td>
                <td class="text-right">{{ $setting->currency_symbol }} {{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
        
        <div style="clear: both;"></div>
        
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">Notes</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif
        
        <div class="terms">
            <div class="section-title">Payment Terms</div>
            <div>{{ $setting->payment_terms }}</div>
            @if($setting->payment_details)
                <div class="payment-details">
                    <div class="section-title">Payment Details</div>
                    <div>{{ $setting->payment_details }}</div>
                </div>
            @endif
        </div>
        
        <div class="footer">
            <p>{{ $setting->invoice_footer }}</p>
        </div>
    </div>
</body>
</html>