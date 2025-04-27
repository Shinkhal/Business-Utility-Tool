<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'company_email', 'company_phone', 
        'company_address', 'company_logo', 'tax_number', 'default_tax_rate'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}