<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CabOrder;
use App\Models\Car;
use App\Models\User;
use App\Models\roles;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings' => CabOrder::count(),
            'active_trips' => CabOrder::whereIn('booking_status', ['driver_assigned', 'on_the_way', 'started'])->count(),
            'completed_trips' => CabOrder::where('booking_status', 'completed')->count(),
            'pending_orders' => CabOrder::where('booking_status', 'pending')->count(),
            'total_revenue' => CabOrder::where('booking_status', 'completed')->sum('total_amount'),
            'available_cars' => Car::where('status', 1)->count(),
            'total_users' => User::count(),
            'active_drivers' => User::whereHas('roles', function($q){ 
                $q->where('title', 'LIKE', '%Driver%'); 
            })->count(),
        ];

        $recent_bookings = CabOrder::latest()->take(10)->get();
        
        $today_trips = CabOrder::whereDate('pickup_date', Carbon::today())
            ->whereIn('booking_status', ['confirmed', 'driver_assigned', 'on_the_way', 'started'])
            ->get();

        // Revenue Analytics (Last 12 months)
        $revenue_raw = CabOrder::where('booking_status', 'completed')
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->selectRaw('SUM(total_amount) as total, MONTH(created_at) as month')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chart_labels = [];
        $chart_values = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $chart_labels[] = $monthName;
            $chart_values[] = $revenue_raw->has($i) ? (float)$revenue_raw[$i]->total : 0;
        }

        // System Alerts (Mocking some real logic based on recent events)
        $alerts = CabOrder::latest()->take(5)->get()->map(function($order) {
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

            return [
                'type' => $type,
                'icon' => $icon,
                'message' => $message,
                'subtext' => $order->customer_name . " - ₹" . number_format($order->total_amount, 2),
                'time' => $order->created_at->diffForHumans()
            ];
        });

        return view('dashboard', compact('stats', 'recent_bookings', 'today_trips', 'chart_labels', 'chart_values', 'alerts'));
    }
}
