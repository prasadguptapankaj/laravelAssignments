<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $previousMonth = Carbon::now()->subMonth()->month;
        $currentYear = Carbon::now()->year;
        $previousYear = Carbon::now()->subYear()->year;

        $currentMonthSale = Order::whereMonth('created_at', $currentMonth)->sum('total_amount');
        $previousMonthSale = Order::whereMonth('created_at', $previousMonth)->sum('total_amount');
        $previousYearsCurrentMonthSale = Order::whereYear('created_at', $previousYear)->whereMonth('created_at', $currentMonth)->sum('total_amount');
        $currentYearSale = Order::whereYear('created_at', $currentYear)->sum('total_amount');
        $previousYearSale = Order::whereYear('created_at', $previousYear)->sum('total_amount');

        $orders = $this->unProcessedOrder();

        $topSellingProducts = $this->topSellingProducts();

        $topCustomers = $this->topCustomers();

        $userStats = $this->userRegistrationStats();

        $customerStats = $this->customerRegistrationStatsByYear();

        $salesData = $this->salesComparison();

        // dd($salesData);

        return view('dashboard.index', compact('salesData','customerStats', 'userStats','topCustomers','topSellingProducts','orders','currentMonthSale', 'previousMonthSale', 'previousYearsCurrentMonthSale', 'currentYearSale', 'previousYearSale'));
        
    }

    public function salesComparison()
    {
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        $currentYearSales = $this->getMonthlySales($currentYear);
        $previousYearSales = $this->getMonthlySales($previousYear);

        return (compact('currentYearSales', 'previousYearSales'));
    }

    private function getMonthlySales($year)
    {
        return Order::query()
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }


    public function newOrders(Request $request)
    {
        $newOrders = Order::with(['customer', 'billingAddress', 'shippingAddress'])
            ->where('status', 'new') // Adjust the status condition based on your setup
            ->orderBy('created_at', 'desc')
            ->paginate(10); // You can adjust the pagination based on your preference

        return view('dashboard.index', compact('newOrders'));
    }

    public function unProcessedOrder(){
        return Order::where('status', 'new')
                       ->orWhere('status', 'unprocessed')
                       ->with('customer') // Load customer details
                       ->paginate(10);
    }

    public function topSellingProducts()
    {
        return Product::select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_quantity_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subYears(2)) // Orders from the last 2 years
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(10)
            ->paginate(5);
    }

    public function topCustomers()
    {
        return Customer::select('customers.id', 'customers.name', DB::raw('COUNT(orders.id) as num_orders'), DB::raw('SUM(orders.total_amount) as total_order_amount'))
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('num_orders')
            ->limit(10)
            ->paginate(10);

    }

    public function userRegistrationStats()
    {
        return Customer::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(id) as count'))
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('count', 'month');

    }

    public function customerRegistrationStatsByYear()
    {
        return Customer::select(DB::raw("YEAR(created_at) as year"), DB::raw('COUNT(id) as count'))
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('count', 'year');

    }
}
