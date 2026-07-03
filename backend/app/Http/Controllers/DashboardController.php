<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CabOrder;
use App\Models\Car;
use App\Models\User;
use App\Models\Roles;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isVendor = $user && $user->isVendor();

        $stats = [
            'total_bookings' => $isVendor ? CabOrder::where('vendor_id', $user->id)->count() : CabOrder::count(),
            'active_trips' => $isVendor 
                ? CabOrder::where('vendor_id', $user->id)->whereIn('booking_status', ['driver_assigned', 'on_the_way', 'started'])->count() 
                : CabOrder::whereIn('booking_status', ['driver_assigned', 'on_the_way', 'started'])->count(),
            'completed_trips' => $isVendor 
                ? CabOrder::where('vendor_id', $user->id)->where('booking_status', 'completed')->count() 
                : CabOrder::where('booking_status', 'completed')->count(),
            'pending_orders' => $isVendor 
                ? CabOrder::where('vendor_id', $user->id)->where('booking_status', 'pending')->count() 
                : CabOrder::where('booking_status', 'pending')->count(),
            'total_revenue' => $isVendor 
                ? CabOrder::where('vendor_id', $user->id)->where('booking_status', 'completed')->sum('vendor_earnings') 
                : CabOrder::where('booking_status', 'completed')->sum('total_amount'),
            'available_cars' => $isVendor 
                ? Car::where('vendor_id', $user->id)->where('status', 1)->count() 
                : Car::where('status', 1)->count(),
            'total_users' => $isVendor ? 0 : User::count(),
            'active_drivers' => $isVendor ? 0 : User::whereHas('roles', function($q){ 
                $q->where('title', 'LIKE', '%Driver%'); 
            })->count(),
        ];

        $recent_bookings = $isVendor 
            ? CabOrder::where('vendor_id', $user->id)->latest()->take(10)->get() 
            : CabOrder::latest()->take(10)->get();
        
        $today_trips = $isVendor 
            ? CabOrder::where('vendor_id', $user->id)->whereDate('pickup_date', Carbon::today())
                ->whereIn('booking_status', ['confirmed', 'driver_assigned', 'on_the_way', 'started'])
                ->get()
            : CabOrder::whereDate('pickup_date', Carbon::today())
                ->whereIn('booking_status', ['confirmed', 'driver_assigned', 'on_the_way', 'started'])
                ->get();

        // Revenue Analytics (Last 12 months)
        $revenue_query = CabOrder::where('booking_status', 'completed')
            ->where('created_at', '>=', Carbon::now()->startOfYear());
        
        if ($isVendor) {
            $revenue_query->where('vendor_id', $user->id);
            $revenue_raw = $revenue_query->selectRaw('SUM(vendor_earnings) as total, MONTH(created_at) as month')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        } else {
            $revenue_raw = $revenue_query->selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');
        }

        $chart_labels = [];
        $chart_values = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $chart_labels[] = $monthName;
            $chart_values[] = $revenue_raw->has($i) ? (float)$revenue_raw[$i]->total : 0;
        }

        // System Alerts
        $alerts_query = CabOrder::latest()->take(5);
        if ($isVendor) {
            $alerts_query->where('vendor_id', $user->id);
        }
        
        $alerts = $alerts_query->get()->map(function($order) use ($isVendor) {
            $type = 'info';
            $icon = 'fa-info-circle';
            $message = "Order #{$order->order_number} status updated to {$order->booking_status}";
            
            if ($order->booking_status == 'pending') {
                $type = 'blue';
                $icon = 'fa-plus';
                $message = "New booking received: #{$order->order_number}";
            } elseif ($order->booking_status == 'completed') {
                $type = 'green';
                $icon = 'fa-check';
                $message = "Payment completed for #{$order->order_number}";
            } elseif ($order->booking_status == 'cancelled') {
                $type = 'red';
                $icon = 'fa-times';
                $message = "Order #{$order->order_number} was cancelled";
            }

            $amount = $isVendor ? $order->vendor_earnings : $order->total_amount;

            return [
                'type' => $type,
                'icon' => $icon,
                'message' => $message,
                'subtext' => $order->customer_name . " - ₹" . number_format($amount, 2),
                'time' => $order->created_at->diffForHumans()
            ];
        });

        return view('dashboard', compact('stats', 'recent_bookings', 'today_trips', 'chart_labels', 'chart_values', 'alerts'));
    }
}
