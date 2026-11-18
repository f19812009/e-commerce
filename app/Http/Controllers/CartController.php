<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(private CartService $cart) {}

    public function index()
    {
        return Inertia::render('Cart/Index', [
            'items'    => $this->cart->items()->values(),
            'subtotal' => $this->cart->subtotal(),
            'shipping' => $this->cart->shipping(),
            'total'    => $this->cart->total(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        if ($product->stock < 1) {
            return back()->with('error', 'দুঃখিত, পণ্যটি স্টকে নেই।');
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $variantLabel = $request->input('variant_label');
        $variantPrice = $request->filled('variant_price') ? (float) $request->input('variant_price') : null;

        $this->cart->add($product, $qty, $variantLabel, $variantPrice);

        return back()->with('success', "'{$product->name}' কার্টে যোগ হয়েছে!");
    }

    public function buyNow(Request $request, Product $product)
    {
        if ($product->stock < 1) {
            return back()->with('error', 'দুঃখিত, পণ্যটি স্টকে নেই।');
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $variantLabel = $request->input('variant_label');
        $variantPrice = $request->filled('variant_price') ? (float) $request->input('variant_price') : null;

        $this->cart->add($product, $qty, $variantLabel, $variantPrice);

        return redirect()->route('checkout.index');
    }

    public function update(Request $request, int $product)
    {
        $qty = (int) $request->input('quantity', 1);
        $this->cart->update($product, $qty);

        if (request()->expectsJson()) {
            return response()->json([
                'count'    => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
                'shipping' => $this->cart->shipping(),
                'total'    => $this->cart->total(),
            ]);
        }

        return back()->with('success', 'কার্ট আপডেট হয়েছে।');
    }

    public function remove(int $product)
    {
        $this->cart->remove($product);
        return back()->with('success', 'পণ্যটি কার্ট থেকে সরানো হয়েছে।');
    }

    public function clear()
    {
        $this->cart->clear();
        return back()->with('success', 'কার্ট খালি করা হয়েছে।');
    }
}
