<?php

namespace App\Filament\Widgets;

use App\Models\AccountTransaction;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\Widget;

class ColorfulStatsOverviewWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.smart-dashboard-overview';

    protected function getViewData(): array
    {
        // Sales & Order Metrics
        $deliveredSales = (float) Order::where('status', 'delivered')->sum('total');
        $todaySales = (float) Order::where('status', '!=', 'cancelled')->whereDate('created_at', today())->sum('total');
        $activeOrderValue = (float) Order::whereIn('status', ['pending', 'processing', 'shipped'])->sum('total');
        $totalOrderValue = $deliveredSales + $activeOrderValue;

        // Order Counts by Status
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Accounts & Profit
        $totalExpenses = (float) AccountTransaction::where('type', 'expense')->sum('amount');
        $totalPurchases = (float) AccountTransaction::where('type', 'purchase')->sum('amount');
        $totalOtherIncome = (float) AccountTransaction::where('type', 'income')->sum('amount');
        $estimatedProfit = ($deliveredSales + $totalOtherIncome) - ($totalPurchases + $totalExpenses);

        // Dues
        $customerDue = (float) AccountTransaction::where('party_type', 'customer')->sum('due_amount');
        $supplierDue = (float) AccountTransaction::where('party_type', 'supplier')->sum('due_amount');

        // Inventory & Catalog
        $totalProducts = Product::count();
        $inStockProducts = Product::where('stock', '>', 0)->count();
        $lowStockCount = Product::whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $onSaleCount = Product::whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count();
        $totalCategories = Category::count();
        $stockValue = (float) Product::selectRaw(' COALESCE(SUM(price * stock), 0) as val ')->value('val');

        return [
            'deliveredSales' => $deliveredSales,
            'todaySales' => $todaySales,
            'activeOrderValue' => $activeOrderValue,
            'totalOrderValue' => $totalOrderValue,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'shippedOrders' => $shippedOrders,
            'deliveredOrders' => $deliveredOrders,
            'cancelledOrders' => $cancelledOrders,
            'totalExpenses' => $totalExpenses,
            'totalPurchases' => $totalPurchases,
            'estimatedProfit' => $estimatedProfit,
            'customerDue' => $customerDue,
            'supplierDue' => $supplierDue,
            'totalProducts' => $totalProducts,
            'inStockProducts' => $inStockProducts,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'onSaleCount' => $onSaleCount,
            'totalCategories' => $totalCategories,
            'stockValue' => $stockValue,
        ];
    }
}
