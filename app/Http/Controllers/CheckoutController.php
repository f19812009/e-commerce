<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'কার্ট খালি আছে।');
        }

        return Inertia::render('Checkout/Index', [
            'items'    => $this->cart->items()->values(),
            'subtotal' => $this->cart->subtotal(),
            'shipping' => $this->cart->shipping(),
            'total'    => $this->cart->total(),
        ]);
    }

    public function placeOrder(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'কার্ট খালি আছে।');
        }

        $data = $request->validate([
            'customer_name'    => 'required|string|max:120',
            'customer_email'   => 'nullable|email|max:120',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'city'             => 'required|string|max:80',
            'postal_code'      => 'nullable|string|max:20',
            'payment_method'   => 'required|in:cod,bkash,nagad',
            'notes'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                ...$data,
                'user_id'  => auth()->id(),
                'subtotal' => $this->cart->subtotal(),
                'shipping' => $this->cart->shipping(),
                'total'    => $this->cart->total(),
            ]);

            foreach ($this->cart->items() as $item) {
                $order->items()->create([
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);

                // Decrease stock and log history
                $product = \App\Models\Product::find($item['id']);
                if ($product) {
                    $prevStock = $product->stock;
                    $newStock = max(0, $prevStock - $item['quantity']);
                    $product->update(['stock' => $newStock]);

                    \App\Models\StockHistory::create([
                        'product_id'     => $product->id,
                        'type'           => 'order_sale',
                        'quantity'       => -$item['quantity'],
                        'previous_stock' => $prevStock,
                        'current_stock'  => $newStock,
                        'reference'      => 'Order #' . $order->order_number,
                        'note'           => 'অর্ডারের মাধ্যমে স্বয়ংক্রিয় স্টক হ্রাস',
                        'user_id'        => auth()->id(),
                    ]);
                }
            }

            DB::commit();
            $this->cart->clear();
            session(['last_order_id' => $order->id]);

            return redirect()->route('checkout.success', $order)
                ->with('success', 'অর্ডার সফলভাবে দেওয়া হয়েছে!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'অর্ডার দেওয়া যায়নি। আবার চেষ্টা করুন।');
        }
    }

    public function success(Order $order)
    {
        // Prevent viewing someone else's order if logged in or order has user_id
        if ($order->user_id && (!auth()->check() || $order->user_id !== auth()->id())) {
            // If they just placed the order in this session, allow viewing
            if (session('last_order_id') !== $order->id) {
                abort(403);
            }
        }

        $order->load('items');

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function orderDetail(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        $order->load('items');
        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function trackOrder(Request $request)
    {
        $rawQuery = trim((string) $request->query('q', ''));
        $cleanQuery = ltrim($rawQuery, '#');
        $orders = [];

        if ($cleanQuery !== '') {
            $orders = Order::with('items')
                ->where('order_number', $cleanQuery)
                ->orWhere('customer_phone', $cleanQuery)
                ->latest()
                ->take(10)
                ->get();
        }

        return Inertia::render('Orders/Track', [
            'query'  => $rawQuery,
            'orders' => $orders,
        ]);
    }
}
