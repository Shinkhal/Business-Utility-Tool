<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Calculate total revenue this month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $revenueThisMonth = $user->invoices()
            ->where('status', 'paid')
            ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
            ->sum('total');
            
        // Calculate outstanding invoices
        $outstandingAmount = $user->invoices()
            ->where('status', 'sent')
            ->where('due_date', '>=', now())
            ->sum('total');
            
        // Calculate overdue invoices
        $overdueAmount = $user->invoices()
            ->where('status', 'sent')
            ->where('due_date', '<', now())
            ->sum('total');
        
        // Get recent invoices
        $recentInvoices = $user->invoices()
            ->with('customer')
            ->latest()
            ->limit(5)
            ->get();
            
        // Count total customers, products, and invoices
        $customerCount = $user->customers()->count();
        $productCount = $user->products()->count();
        $invoiceCount = $user->invoices()->count();
        
        // Get monthly revenue for chart
        $monthlyRevenue = $this->getMonthlyRevenue();
        
        return view('dashboard', compact(
            'revenueThisMonth', 
            'outstandingAmount', 
            'overdueAmount', 
            'recentInvoices',
            'customerCount',
            'productCount',
            'invoiceCount',
            'monthlyRevenue'
        ));
    }
    
    private function getMonthlyRevenue()
    {
        $user = auth()->user();
        $data = [];
        
        // Get revenue for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $revenue = $user->invoices()
                ->where('status', 'paid')
                ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->sum('total');
                
            $data[] = [
                'month' => $date->format('M'),
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }
}