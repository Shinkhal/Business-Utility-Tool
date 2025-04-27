<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'invoice_date', 'due_date', 'subtotal', 
        'tax_percent', 'tax_amount', 'discount_percent', 'discount_amount', 'total', 
        'notes', 'status', 'user_id'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = self::where('invoice_number', 'like', "INV-{$year}{$month}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if (!$lastInvoice) {
            return "INV-{$year}{$month}0001";
        }

        $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
        return "INV-{$year}{$month}" . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}