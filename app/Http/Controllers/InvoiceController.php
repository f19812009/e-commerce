<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        // Security check: if customer, must own the order or have placed it in current session; if admin, allowed
        $isOwner = auth()->check() && (auth()->user()->is_admin || $order->user_id === auth()->id());
        $isSessionGuest = session('last_order_id') === $order->id;
        
        if (!$isOwner && !$isSessionGuest && !auth()->check()) {
            abort(403, 'অননুমোদিত অ্যাক্সেস');
        }

        $order->load(['items.product']);

        $pdf = Pdf::loadView('invoices.order-invoice', compact('order'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function stream(Order $order)
    {
        $isOwner = auth()->check() && (auth()->user()->is_admin || $order->user_id === auth()->id());
        $isSessionGuest = session('last_order_id') === $order->id;
        
        if (!$isOwner && !$isSessionGuest && !auth()->check()) {
            abort(403);
        }

        $order->load(['items.product']);

        $pdf = Pdf::loadView('invoices.order-invoice', compact('order'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }
}
