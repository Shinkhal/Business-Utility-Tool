<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\PDF;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    protected $pdf;

    /**
     * Create a new message instance.
     *
     * @param Invoice $invoice
     * @param PDF $pdf
     * @return void
     */
    public function __construct(Invoice $invoice, PDF $pdf)
    {
        $this->invoice = $invoice;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
{
    // Ensure the setting is loaded via the user relationship
    $setting = $this->invoice->user->setting;

    // Use a fallback if no setting is found
    $companyName = $setting ? $setting->company_name : config('app.name');
    
    // Build the email with the subject and the PDF attachment
    return $this->subject("Invoice #{$this->invoice->invoice_number} from {$companyName}")
                ->view('emails.invoice', ['setting' => $setting]) // Pass setting to the view
                ->attachData($this->pdf->output(), "invoice-{$this->invoice->invoice_number}.pdf", [
                    'mime' => 'application/pdf',
                ]);
}
}
