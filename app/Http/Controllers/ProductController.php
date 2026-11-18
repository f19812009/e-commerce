<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OfferBanner;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->get('category');
        $onSaleOnly = $request->boolean('on_sale');
        $brandFilter = $request->get('brand');
        $isSearchingOrFiltering = $request->filled('search')
            || $request->filled('min_price')
            || $request->filled('max_price')
            || $onSaleOnly
            || $request->filled('brand')
            || ($request->filled('sort') && $request->get('sort') !== 'latest');

        return Inertia::render('Products/Index', [
            'products' => function () use ($request, $categorySlug, $onSaleOnly, $brandFilter) {
                $query = Product::active()->inStock()->with('category');

                // Fixed Search (Wrapped in closure so active/inStock/category filters are respected)
                if ($search = trim((string) $request->get('search', ''))) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('brand', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%");
                    });
                }

                // Filter by category (includes subcategories if a parent category is selected)
                if ($categorySlug) {
                    $selectedCat = Category::where('slug', $categorySlug)->first();
                    if ($selectedCat) {
                        $catIds = Category::where('parent_id', $selectedCat->id)
                            ->pluck('id')
                            ->push($selectedCat->id)
                            ->all();
                        $query->whereIn('category_id', $catIds);
                    } else {
                        $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
                    }
                }

                // Filter by Special Offer / Discounted products
                if ($onSaleOnly) {
                    $query->whereNotNull('sale_price')
                          ->whereColumn('sale_price', '<', 'price');
                }

                // Filter by Brand
                if ($brandFilter) {
                    $query->where('brand', $brandFilter);
                }

                // Filter by price range
                if ($minPrice = $request->get('min_price')) {
                    $query->whereRaw('COALESCE(sale_price, price) >= ?', [$minPrice]);
                }
                if ($maxPrice = $request->get('max_price')) {
                    $query->whereRaw('COALESCE(sale_price, price) <= ?', [$maxPrice]);
                }

                // Sort
                match ($request->get('sort', 'latest')) {
                    'price_asc'  => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
                    'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
                    'discount'   => $query->orderByRaw('(price - COALESCE(sale_price, price)) DESC'),
                    'name'       => $query->orderBy('name'),
                    default      => $query->latest(),
                };

                return $query->paginate(12)->withQueryString();
            },
            'categories' => fn () => Category::query()
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')->orderBy('name')])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'allCategoriesFlat' => fn () => Category::where('is_active', true)->orderBy('name')->get(['id', 'parent_id', 'name', 'slug', 'icon', 'image']),
            'specialOffers' => function () use ($categorySlug, $isSearchingOrFiltering) {
                if (!$categorySlug && !$isSearchingOrFiltering) {
                    return Product::active()
                        ->inStock()
                        ->with('category')
                        ->whereNotNull('sale_price')
                        ->whereColumn('sale_price', '<', 'price')
                        ->latest()
                        ->take(10)
                        ->get();
                }
                return [];
            },
            'categoriesWithProducts' => function () use ($categorySlug, $isSearchingOrFiltering) {
                if (!$categorySlug && !$isSearchingOrFiltering) {
                    return Category::whereHas('products', fn ($q) => $q->active()->inStock())
                        ->with(['products' => fn ($q) => $q->active()->inStock()->latest()->take(6)])
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get();
                }
                return [];
            },
            'sliders'      => fn () => Slider::active()->get(),
            'offerBanners' => fn () => OfferBanner::active()->get(),
            'filters'      => $request->only(['search', 'category', 'sort', 'min_price', 'max_price', 'on_sale', 'brand']),
        ]);
    }

    public function show(string $productOrSlug)
    {
        $product = Product::where('slug', $productOrSlug)
            ->orWhere('id', $productOrSlug)
            ->firstOrFail();

        abort_if(!$product->is_active, 404);
        $product->load('category');

        return Inertia::render('Products/Show', [
            'product' => fn () => $product,
            'related' => fn () => Product::active()
                ->inStock()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(6)
                ->get(),
        ]);
    }
}
