<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * 1. Sales & Order Report Data
     */
    public function getSalesReport(
        ?string $startDate = null,
        ?string $endDate = null,
        string $status = 'all',
        string $paymentMethod = 'all'
    ): array {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $baseQuery = Order::query()->whereBetween('created_at', [$start, $end]);

        if ($paymentMethod !== 'all') {
            $baseQuery->where('payment_method', $paymentMethod);
        }

        // Overall KPIs in period (before status filter for status cards, or with status filter)
        $allPeriodOrders = (clone $baseQuery)->get();

        $totalOrdersCount = $allPeriodOrders->count();
        $deliveredOrders = $allPeriodOrders->where('status', 'delivered');
        $pendingOrders = $allPeriodOrders->whereIn('status', ['pending', 'processing', 'shipped']);
        $cancelledOrders = $allPeriodOrders->where('status', 'cancelled');

        $deliveredRevenue = (float) $deliveredOrders->sum('total');
        $pendingRevenue = (float) $pendingOrders->sum('total');
        $cancelledAmount = (float) $cancelledOrders->sum('total');
        $grossOrderAmount = (float) $allPeriodOrders->where('status', '!=', 'cancelled')->sum('total');
        $totalShipping = (float) $allPeriodOrders->where('status', '!=', 'cancelled')->sum('shipping');
        $validOrdersCount = $allPeriodOrders->where('status', '!=', 'cancelled')->count();
        $averageOrderValue = $validOrdersCount > 0 ? ($grossOrderAmount / $validOrdersCount) : 0;

        // Status Breakdown
        $statusBreakdown = [];
        foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st) {
            $group = $allPeriodOrders->where('status', $st);
            $statusBreakdown[$st] = [
                'status' => ucfirst($st),
                'count'  => $group->count(),
                'total'  => (float) $group->sum('total'),
            ];
        }

        // Payment Method Breakdown (non-cancelled)
        $paymentBreakdown = $allPeriodOrders
            ->where('status', '!=', 'cancelled')
            ->groupBy('payment_method')
            ->map(fn ($rows, $method) => [
                'method' => strtoupper($method ?: 'cod'),
                'count'  => $rows->count(),
                'total'  => (float) $rows->sum('total'),
            ])
            ->values()
            ->all();

        // Daily Sales Breakdown
        $dailySales = $allPeriodOrders
            ->groupBy(fn ($order) => $order->created_at->format('Y-m-d'))
            ->map(function ($orders, $date) {
                $nonCancelled = $orders->where('status', '!=', 'cancelled');
                $delivered = $orders->where('status', 'delivered');
                return [
                    'date'             => Carbon::parse($date)->format('d M, Y'),
                    'raw_date'         => $date,
                    'orders_count'     => $orders->count(),
                    'delivered_count'  => $delivered->count(),
                    'delivered_amount' => (float) $delivered->sum('total'),
                    'total_amount'     => (float) $nonCancelled->sum('total'),
                ];
            })
            ->sortByDesc('raw_date')
            ->values()
            ->all();

        // Top Selling Products in Period
        $topProductsQuery = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', '!=', 'cancelled');

        if ($status !== 'all') {
            $topProductsQuery->where('orders.status', $status);
        }
        if ($paymentMethod !== 'all') {
            $topProductsQuery->where('orders.payment_method', $paymentMethod);
        }

        $topProducts = $topProductsQuery
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'products.sku',
                'products.cost_price',
                'products.stock as current_stock',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(order_items.quantity * COALESCE(products.cost_price, 0)) as total_cogs')
            )
            ->groupBy(
                'order_items.product_id',
                'order_items.product_name',
                'products.sku',
                'products.cost_price',
                'products.stock'
            )
            ->orderByDesc('total_qty')
            ->limit(25)
            ->get()
            ->map(function ($row) {
                $rev = (float) $row->total_revenue;
                $cogs = (float) $row->total_cogs;
                return [
                    'product_id'       => $row->product_id,
                    'product_name'     => $row->product_name,
                    'sku'              => $row->sku ?? 'N/A',
                    'total_qty'        => (int) $row->total_qty,
                    'total_revenue'    => $rev,
                    'total_cogs'       => $cogs,
                    'estimated_profit' => $rev - $cogs,
                    'current_stock'    => $row->current_stock ?? 0,
                ];
            })
            ->all();

        // Filtered Orders List
        $ordersQuery = clone $baseQuery;
        if ($status !== 'all') {
            $ordersQuery->where('status', $status);
        }
        $orders = $ordersQuery->withCount('items')->latest()->limit(200)->get();

        return [
            'period' => [
                'start' => $start->format('d M, Y'),
                'end'   => $end->format('d M, Y'),
            ],
            'summary' => [
                'total_orders_count' => $totalOrdersCount,
                'delivered_count'    => $deliveredOrders->count(),
                'pending_count'      => $pendingOrders->count(),
                'cancelled_count'    => $cancelledOrders->count(),
                'delivered_revenue'  => $deliveredRevenue,
                'pending_revenue'    => $pendingRevenue,
                'cancelled_amount'   => $cancelledAmount,
                'gross_order_amount' => $grossOrderAmount,
                'total_shipping'     => $totalShipping,
                'average_order_val'  => $averageOrderValue,
            ],
            'status_breakdown'  => $statusBreakdown,
            'payment_breakdown' => $paymentBreakdown,
            'daily_sales'       => $dailySales,
            'top_products'      => $topProducts,
            'orders'            => $orders,
        ];
    }

    /**
     * 2. Profit & Loss / Accounts Report Data
     */
    public function getProfitLossReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        // 1. Delivered Orders Revenue & COGS
        $deliveredOrders = Order::query()
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $deliveredSalesRevenue = (float) $deliveredOrders->sum('total');
        $deliveredSubtotal = (float) $deliveredOrders->sum('subtotal');
        $deliveredShipping = (float) $deliveredOrders->sum('shipping');

        $deliveredCogs = (float) OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [$start, $end])
            ->sum(DB::raw('order_items.quantity * COALESCE(products.cost_price, 0)'));

        $grossProductProfit = $deliveredSalesRevenue - $deliveredCogs;

        // 2. Account Transactions in period
        $transactions = AccountTransaction::query()
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $otherIncome = (float) $transactions->where('type', 'income')->sum('amount');
        $salesCollection = (float) $transactions->where('type', 'sales_collection')->sum('amount');
        $totalPurchases = (float) $transactions->where('type', 'purchase')->sum('amount');
        $totalExpenses = (float) $transactions->where('type', 'expense')->sum('amount');

        // Total Operating Cashflow Profit (matching Dashboard logic)
        $totalInflow = $deliveredSalesRevenue + $otherIncome;
        $totalOutflow = $totalPurchases + $totalExpenses;
        $netOperatingProfit = $totalInflow - $totalOutflow;

        // Product Margin Based Net Profit (Gross Profit + Other Income - Operating Expenses)
        $netMarginProfit = ($grossProductProfit + $otherIncome) - $totalExpenses;

        $profitMarginPercent = $totalInflow > 0 ? round(($netOperatingProfit / $totalInflow) * 100, 1) : 0;

        // 3. Expense & Purchase Breakdown by Category
        $expenseByCategory = $transactions
            ->whereIn('type', ['expense', 'purchase'])
            ->groupBy(fn ($t) => $t->category ?: 'Uncategorized (সাধারণ)')
            ->map(fn ($group, $cat) => [
                'category'   => $cat,
                'type'       => $group->first()->type,
                'count'      => $group->count(),
                'amount'     => (float) $group->sum('amount'),
                'due_amount' => (float) $group->sum('due_amount'),
            ])
            ->sortByDesc('amount')
            ->values()
            ->all();

        // 4. Payment Method Summary (Cash, Bank, bKash, Nagad)
        $methodSummary = [];
        foreach (['cash' => 'Cash (নগদ)', 'bank' => 'Bank Transfer', 'bkash' => 'bKash', 'nagad' => 'Nagad'] as $key => $label) {
            $mTrans = $transactions->where('payment_method', $key);
            $in = (float) $mTrans->whereIn('type', ['income', 'sales_collection'])->sum('amount');
            $out = (float) $mTrans->whereIn('type', ['expense', 'purchase'])->sum('amount');
            $orderSalesByMethod = (float) $deliveredOrders->where('payment_method', $key)->sum('total');

            $methodSummary[] = [
                'method'       => $label,
                'order_sales'  => $orderSalesByMethod,
                'ledger_in'    => $in,
                'ledger_out'   => $out,
                'net_balance'  => ($orderSalesByMethod + $in) - $out,
            ];
        }

        return [
            'period' => [
                'start' => $start->format('d M, Y'),
                'end'   => $end->format('d M, Y'),
            ],
            'summary' => [
                'delivered_sales'      => $deliveredSalesRevenue,
                'delivered_subtotal'   => $deliveredSubtotal,
                'delivered_shipping'   => $deliveredShipping,
                'delivered_cogs'       => $deliveredCogs,
                'gross_product_profit' => $grossProductProfit,
                'other_income'         => $otherIncome,
                'sales_collection'     => $salesCollection,
                'total_purchases'      => $totalPurchases,
                'total_expenses'       => $totalExpenses,
                'total_inflow'         => $totalInflow,
                'total_outflow'        => $totalOutflow,
                'net_operating_profit' => $netOperatingProfit,
                'net_margin_profit'    => $netMarginProfit,
                'profit_margin_pct'    => $profitMarginPercent,
            ],
            'expense_by_category' => $expenseByCategory,
            'method_summary'      => $methodSummary,
            'transactions'        => $transactions,
        ];
    }

    /**
     * 3. Inventory & Stock Valuation Report Data
     */
    public function getInventoryReport(
        string $categoryId = 'all',
        string $stockStatus = 'all',
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $allProducts = Product::with('category')
            ->withSum(['orderItems as total_sold_qty' => function ($q) {
                $q->whereHas('order', fn ($oq) => $oq->where('status', '!=', 'cancelled'));
            }], 'quantity')
            ->orderBy('stock', 'asc')
            ->get();

        // Overall KPIs across all products
        $totalProductsCount = $allProducts->count();
        $activeProductsCount = $allProducts->where('is_active', true)->count();
        $totalStockUnits = (int) $allProducts->sum('stock');
        $lowStockCount = $allProducts->filter(fn ($p) => $p->stock > 0 && $p->stock <= $p->low_stock_threshold)->count();
        $outOfStockCount = $allProducts->filter(fn ($p) => $p->stock <= 0)->count();

        $totalCostValuation = (float) $allProducts->sum(fn ($p) => max(0, $p->stock) * (float) ($p->cost_price ?? 0));
        $totalRetailValuation = (float) $allProducts->sum(fn ($p) => max(0, $p->stock) * (float) $p->effective_price);
        $potentialStockProfit = $totalRetailValuation - $totalCostValuation;

        // Category-wise Valuation Summary
        $categoryValuation = $allProducts
            ->groupBy(fn ($p) => $p->category?->name ?? 'Uncategorized')
            ->map(function ($items, $catName) {
                $costVal = (float) $items->sum(fn ($p) => max(0, $p->stock) * (float) ($p->cost_price ?? 0));
                $retailVal = (float) $items->sum(fn ($p) => max(0, $p->stock) * (float) $p->effective_price);
                return [
                    'category'         => $catName,
                    'products_count'   => $items->count(),
                    'total_units'      => (int) $items->sum('stock'),
                    'cost_valuation'   => $costVal,
                    'retail_valuation' => $retailVal,
                    'expected_profit'  => $retailVal - $costVal,
                ];
            })
            ->sortByDesc('retail_valuation')
            ->values()
            ->all();

        // Filtered Products List
        $filteredProducts = $allProducts->filter(function ($p) use ($categoryId, $stockStatus) {
            if ($categoryId !== 'all' && (string) $p->category_id !== (string) $categoryId) {
                return false;
            }
            if ($stockStatus === 'low_stock') {
                return $p->stock > 0 && $p->stock <= $p->low_stock_threshold;
            }
            if ($stockStatus === 'out_of_stock') {
                return $p->stock <= 0;
            }
            if ($stockStatus === 'in_stock') {
                return $p->stock > $p->low_stock_threshold;
            }
            return true;
        })->values();

        // Stock Movement Logs in period
        $stockMovements = StockHistory::with(['product', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->limit(150)
            ->get();

        $movementSummary = StockHistory::query()
            ->whereBetween('created_at', [$start, $end])
            ->select(
                'type',
                DB::raw('COUNT(*) as logs_count'),
                DB::raw('SUM(ABS(quantity)) as total_qty')
            )
            ->groupBy('type')
            ->get()
            ->keyBy('type')
            ->all();

        return [
            'period' => [
                'start' => $start->format('d M, Y'),
                'end'   => $end->format('d M, Y'),
            ],
            'summary' => [
                'total_products'        => $totalProductsCount,
                'active_products'       => $activeProductsCount,
                'total_stock_units'     => $totalStockUnits,
                'low_stock_count'       => $lowStockCount,
                'out_of_stock_count'    => $outOfStockCount,
                'total_cost_valuation'  => $totalCostValuation,
                'total_retail_valuation'=> $totalRetailValuation,
                'potential_profit'      => $potentialStockProfit,
            ],
            'category_valuation' => $categoryValuation,
            'products'           => $filteredProducts,
            'stock_movements'    => $stockMovements,
            'movement_summary'   => $movementSummary,
            'categories'         => Category::orderBy('name')->pluck('name', 'id')->all(),
        ];
    }

    /**
     * 4. Due & Party Ledger Report Data
     */
    public function getDuePartyReport(
        string $partyType = 'all',
        string $search = '',
        bool $onlyDue = false
    ): array {
        $allTransactions = AccountTransaction::query()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $customerDue = (float) $allTransactions->where('party_type', 'customer')->sum('due_amount');
        $supplierDue = (float) $allTransactions->where('party_type', 'supplier')->sum('due_amount');
        $otherDue = (float) $allTransactions->where('party_type', 'other')->sum('due_amount');

        // Unpaid Orders (COD / Unpaid non-cancelled orders)
        $unpaidOrders = Order::query()
            ->where('status', '!=', 'cancelled')
            ->whereIn('payment_status', ['unpaid', 'pending'])
            ->latest()
            ->get();

        $unpaidOrdersAmount = (float) $unpaidOrders->sum('total');
        $unpaidOrdersCount = $unpaidOrders->count();

        // Filtered Ledger Transactions
        $filteredTransactions = $allTransactions->filter(function ($t) use ($partyType, $search, $onlyDue) {
            if ($partyType !== 'all' && $t->party_type !== $partyType) {
                return false;
            }
            if ($onlyDue && (float) $t->due_amount <= 0) {
                return false;
            }
            if ($search !== '') {
                $needle = mb_strtolower(trim($search));
                $haystack = mb_strtolower(($t->party_name ?? '') . ' ' . ($t->title ?? '') . ' ' . ($t->reference_no ?? ''));
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }
            return true;
        })->values();

        // Party-wise Aggregated Summary
        $partySummary = $filteredTransactions
            ->groupBy(fn ($t) => ($t->party_type ?? 'other') . '::' . ($t->party_name ?: 'Unnamed Party'))
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'party_name'         => $first->party_name ?: 'Unnamed Party (সাধারণ)',
                    'party_type'         => $first->party_type ?? 'other',
                    'transactions_count' => $rows->count(),
                    'total_paid'         => (float) $rows->sum('amount'),
                    'total_due'          => (float) $rows->sum('due_amount'),
                    'last_date'          => optional($first->transaction_date)->format('d M, Y') ?? '-',
                ];
            })
            ->sortByDesc('total_due')
            ->values()
            ->all();

        return [
            'summary' => [
                'customer_due'         => $customerDue,
                'supplier_due'         => $supplierDue,
                'other_due'            => $otherDue,
                'total_ledger_due'     => $customerDue + $supplierDue + $otherDue,
                'unpaid_orders_amount' => $unpaidOrdersAmount,
                'unpaid_orders_count'  => $unpaidOrdersCount,
            ],
            'party_summary' => $partySummary,
            'transactions'  => $filteredTransactions,
            'unpaid_orders' => $unpaidOrders,
        ];
    }
}
