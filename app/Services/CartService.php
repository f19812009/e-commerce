<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-based shopping cart service.
 *
 * Cart structure in session:
 * [
 *   product_id => [
 *     'id'       => int,
 *     'name'     => string,
 *     'price'    => float,
 *     'image'    => string,
 *     'quantity' => int,
 *     'subtotal' => float,
 *   ],
 *   ...
 * ]
 */
class CartService
{
    private const SESSION_KEY = 'cart';

    // ---------- Read ----------

    public function all(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function items(): Collection
    {
        return collect($this->all());
    }

    public function count(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    public function subtotal(): float
    {
        return array_sum(array_column($this->all(), 'subtotal'));
    }

    public function shipping(): float
    {
        // Free shipping above ৳1000, otherwise ৳80
        return $this->subtotal() >= 1000 ? 0 : 80;
    }

    public function total(): float
    {
        return $this->subtotal() + $this->shipping();
    }

    public function isEmpty(): bool
    {
        return empty($this->all());
    }

    // ---------- Write ----------

    public function add(Product $product, int $qty = 1, ?string $variantLabel = null, ?float $variantPrice = null): void
    {
        $cart = $this->all();
        $id   = $product->id;
        $unitPrice = ($variantPrice !== null && $variantPrice > 0) ? (float) $variantPrice : (float) $product->effective_price;
        $displayName = $variantLabel ? "{$product->name} ({$variantLabel})" : $product->name;

        if (isset($cart[$id])) {
            $newQty = min($cart[$id]['quantity'] + $qty, $product->stock);
            $cart[$id]['name']     = $displayName;
            $cart[$id]['price']    = $unitPrice;
            $cart[$id]['quantity'] = $newQty;
            $cart[$id]['subtotal'] = round($newQty * $unitPrice, 2);
        } else {
            $cart[$id] = [
                'id'       => $id,
                'name'     => $displayName,
                'price'    => $unitPrice,
                'image'    => $product->image_url,
                'quantity' => min($qty, $product->stock),
                'subtotal' => round(min($qty, $product->stock) * $unitPrice, 2),
            ];
        }

        session([self::SESSION_KEY => $cart]);
    }

    public function update(int $productId, int $qty): void
    {
        $cart = $this->all();

        if (!isset($cart[$productId])) return;

        if ($qty <= 0) {
            $this->remove($productId);
            return;
        }

        $cart[$productId]['quantity'] = $qty;
        $cart[$productId]['subtotal'] = round($qty * $cart[$productId]['price'], 2);
        session([self::SESSION_KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $cart = $this->all();
        unset($cart[$productId]);
        session([self::SESSION_KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
