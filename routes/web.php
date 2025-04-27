<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::resource('products', ProductController::class);
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

// Customers
Route::resource('customers', CustomerController::class);
Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

// Invoices
Route::resource('invoices', InvoiceController::class);
Route::get('/invoices/{invoice}/send', [InvoiceController::class, 'sendEmail'])->name('invoices.send');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
Route::get('/invoices/{invoice}/paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.paid');
Route::get('/invoices/{invoice}/cancel', [InvoiceController::class, 'markAsCancelled'])->name('invoices.cancel');
Route::get('/invoices/{invoice}/preview-pdf', [PDFController::class, 'generateInvoicePDF'])->name('invoices.preview-pdf');

// Settings
Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

// Redirect home to dashboard
Route::get('/home', function() {
    return redirect()->route('dashboard');
});