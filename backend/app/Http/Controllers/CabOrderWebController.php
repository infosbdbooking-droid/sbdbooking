<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CabOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class CabOrderWebController extends Controller
{
    /**
     * Display a listing of cab orders.
     */
    public function index()
    {
        $orders = CabOrder::with(['customer', 'car'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('cabOrders.index', compact('orders'));
    }

    /**
     * Display the specified cab order details.
     */
    public function show($id)
    {
        $order = CabOrder::with(['customer', 'car'])->findOrFail($id);
        
        return view('cabOrders.show', compact('order'));
    }

    /**
     * Generate and download the invoice PDF.
     */
    public function downloadInvoice($id)
    {
        $order = CabOrder::with(['customer', 'car'])->findOrFail($id);
        
        // Using fully qualified class name to ensure it resolves
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.cab-booking', compact('order'));
        
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
}
