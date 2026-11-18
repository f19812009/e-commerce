<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $cart = app(\App\Services\CartService::class);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'email' => $request->user()->email,
                    'role'  => $request->user()->role,
                ] : null,
            ],
            'cart' => [
                'count'    => $cart->count(),
                'subtotal' => $cart->subtotal(),
            ],
            'app' => [
                'name'    => fn () => \App\Models\DashboardSetting::current()['brand_name'] ?? config('app.name'),
                'logo'    => fn () => \App\Models\DashboardSetting::logoUrl(),
                'favicon' => fn () => \App\Models\DashboardSetting::faviconUrl(),
                'site'    => fn () => \App\Models\DashboardSetting::current(),
                'categories' => fn () => Category::query()
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->with([
                        'children' => fn ($q) => $q->where('is_active', true)
                            ->withCount(['products' => fn ($pq) => $pq->active()->inStock()])
                            ->orderBy('sort_order')
                            ->orderBy('name'),
                    ])
                    ->withCount(['products' => fn ($pq) => $pq->active()->inStock()])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get()
                    ->map(function (Category $cat) {
                        // Also extract distinct brands or top 4 preview products for Mega Flyout Submenu
                        $subIds = $cat->children->pluck('id')->push($cat->id)->all();
                        $brands = Product::query()
                            ->active()
                            ->whereIn('category_id', $subIds)
                            ->whereNotNull('brand')
                            ->where('brand', '!=', '')
                            ->distinct()
                            ->limit(6)
                            ->pluck('brand')
                            ->values()
                            ->all();

                        $previewProducts = Product::query()
                            ->active()
                            ->inStock()
                            ->whereIn('category_id', $subIds)
                            ->latest()
                            ->take(3)
                            ->get(['id', 'name', 'slug', 'price', 'sale_price', 'image']);

                        return [
                            'id'               => $cat->id,
                            'name'             => $cat->name,
                            'slug'             => $cat->slug,
                            'image'            => $cat->image,
                            'image_url'        => $cat->image_url,
                            'icon'             => $cat->icon,
                            'variety_type'     => $cat->variety_type,
                            'products_count'   => $cat->products_count,
                            'children'         => $cat->children,
                            'brands'           => $brands,
                            'preview_products' => $previewProducts,
                        ];
                    }),
                'offers_count' => fn () => Product::active()
                    ->inStock()
                    ->whereNotNull('sale_price')
                    ->whereColumn('sale_price', '<', 'price')
                    ->count(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
