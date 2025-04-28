<!-- invoices/email-template.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .company-logo {
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 5px;
        }
        
        .invoice-details {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #1a5276;
            border-radius: 4px;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1a5276;
        }
        
        .message {
            margin-bottom: 30px;
        }
        
        .invoice-summary {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        
        .summary-label {
            font-weight: bold;
            color: #666666;
        }
        
        .total-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e0e0e0;
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }
        
        .cta-button {
            display: block;
            width: 100%;
            max-width: 250px;
            margin: 0 auto 30px;
            padding: 12px 20px;
            background-color: #1a5276;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .cta-button:hover {
            background-color: #154360;
        }
        
        .payment-methods {
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #666666;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 13px;
            color: #888888;
        }
        
        .social-links {
            margin: 15px 0;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            color: #1a5276;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            @if($setting->company_logo)
                <div class="company-logo">
                    <img src="{{ $setting->company_logo }}" alt="{{ $setting->company_name }}" width="180">
                </div>
            @else
                <div class="company-name">{{ $setting->company_name }}</div>
            @endif
        </div>
        
        <div class="greeting">Dear {{ $invoice->customer->name }},</div>
        
        <div class="message">
            <p>Thank you for choosing {{ $setting->company_name }}. We appreciate your business and are pleased to provide you with the invoice for our recent services.</p>
            
            <p>Your invoice is now ready and has been attached to this email. Please find the details below:</p>
        </div>
        
        <div class="invoice-details">
            <div class="summary-row">
                <span class="summary-label">Invoice Number:</span>
                <span>{{ $invoice->invoice_number }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Issue Date:</span>
                <span>{{ $invoice->invoice_date->format('M d, Y') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Due Date:</span>
                <span>{{ $invoice->due_date->format('M d, Y') }}</span>
            </div>
            <div class="summary-row total-row">
                <span class="summary-label">Total Amount:</span>
                <span>{{ $setting->currency_symbol }} {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
        
        <p>For a detailed breakdown of services and charges, please refer to the attached invoice.</p>
        
        @if($invoice->status !== 'paid')
            <p>We kindly request your prompt attention to this invoice. Payment is due by {{ $invoice->due_date->format('M d, Y') }}.</p>
            
            <a href="{{ $paymentUrl ?? '#' }}" class="cta-button">View & Pay Invoice</a>
            
            <div class="payment-methods">
                <p><strong>Payment Methods:</strong></p>
                <p>{{ $setting->payment_details ?? 'Please refer to the invoice for payment details.' }}</p>
            </div>
        @else
            <p>This invoice has been paid in full. Thank you for your prompt payment.</p>
        @endif
        
        <p>If you have any questions or concerns regarding this invoice, please don't hesitate to contact our billing department:</p>
        
        <div class="contact-info">
            <p><strong>{{ $setting->company_name }} Billing Department</strong></p>
            <p>Email: {{ $setting->billing_email ?? $setting->company_email }}</p>
            <p>Phone: {{ $setting->billing_phone ?? $setting->company_phone }}</p>
        </div>
        
        <p>We value your continued business and look forward to serving you again.</p>
        
        <p>Warm regards,</p>
        <p><strong>{{ $setting->owner_name ?? $setting->company_name }} Team</strong></p>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ $setting->company_name }}. All rights reserved.</p>
            
            @if(isset($setting->company_website) || isset($setting->social_facebook) || isset($setting->social_twitter) || isset($setting->social_linkedin))
                <div class="social-links">
                    @if(isset($setting->company_website))
                        <a href="{{ $setting->company_website }}" class="social-link">Website</a>
                    @endif
                    @if(isset($setting->social_facebook))
                        <a href="{{ $setting->social_facebook }}" class="social-link">Facebook</a>
                    @endif
                    @if(isset($setting->social_twitter))
                        <a href="{{ $setting->social_twitter }}" class="social-link">Twitter</a>
                    @endif
                    @if(isset($setting->social_linkedin))
                        <a href="{{ $setting->social_linkedin }}" class="social-link">LinkedIn</a>
                    @endif
                </div>
            @endif
            
            <p>This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
        </div>
    </div>
</body>
</html>