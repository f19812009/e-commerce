<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }} - Al-Aqsa Mart</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.45;
            padding: 15px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 18px;
            border-bottom: 2px solid #0b2b22;
            padding-bottom: 12px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0b2b22;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #b58d3d;
            font-weight: bold;
            letter-spacing: 1.5px;
        }
        .report-title {
            font-size: 15px;
            font-weight: bold;
            color: #0b2b22;
            text-align: right;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .summary-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            background: #f8fafc;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #0b2b22;
            margin-top: 3px;
        }
        .section-heading {
            font-size: 12px;
            font-weight: bold;
            color: #0b2b22;
            text-transform: uppercase;
            margin: 14px 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #0b2b22;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .data-table th {
            background-color: #0b2b22;
            color: #ffffff;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-success {
            color: #059669;
        }
        .text-danger {
            color: #e11d48;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="brand-title">AL-AQSA MART</div>
                <div class="brand-subtitle">OFFICIAL BUSINESS & FINANCIAL REPORT</div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    Hotline: 16297 | support@alaqsamart.com
                </div>
            </td>
            <td style="vertical-align: middle; text-align: right;">
                <div class="report-title">{{ $title }}</div>
                <div style="color: #475569; font-size: 10px; margin-top: 4px;">
                    @if(isset($data['period']))
                        <strong>Period:</strong> {{ $data['period']['start'] }} — {{ $data['period']['end'] }}<br>
                    @endif
                    <strong>Generated:</strong> {{ $generatedAt }}
                </div>
            </td>
        </tr>
    </table>

    @if($type === 'sales')
        @php $s = $data['summary']; @endphp
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Total Orders</div>
                    <div class="summary-value">{{ $s['total_orders_count'] }} ({{ $s['delivered_count'] }} Delivered)</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Delivered Revenue</div>
                    <div class="summary-value text-success">Tk {{ number_format($s['delivered_revenue'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Pending / Active Value</div>
                    <div class="summary-value">Tk {{ number_format($s['pending_revenue'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Average Order Value</div>
                    <div class="summary-value">Tk {{ number_format($s['average_order_val'], 2) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-heading">Top Selling Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th class="text-center">Sold Qty</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">COGS</th>
                    <th class="text-right">Est. Profit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['top_products'] as $i => $prod)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="font-bold">{{ $prod['product_name'] }}</td>
                        <td>{{ $prod['sku'] }}</td>
                        <td class="text-center font-bold">{{ $prod['total_qty'] }}</td>
                        <td class="text-right">Tk {{ number_format($prod['total_revenue'], 2) }}</td>
                        <td class="text-right">Tk {{ number_format($prod['total_cogs'], 2) }}</td>
                        <td class="text-right font-bold">{{ $prod['estimated_profit'] >= 0 ? 'Tk ' : '-Tk ' }}{{ number_format(abs($prod['estimated_profit']), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No sales records found for this period.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-heading">Orders Breakdown (Max 200 Recent)</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['orders'] as $order)
                    <tr>
                        <td class="font-bold">{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->customer_phone }}</td>
                        <td>{{ strtoupper($order->status) }}</td>
                        <td>{{ strtoupper($order->payment_method) }}</td>
                        <td class="text-right font-bold">Tk {{ number_format($order->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($type === 'profit-loss')
        @php $s = $data['summary']; @endphp
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Delivered Sales + Income</div>
                    <div class="summary-value text-success">Tk {{ number_format($s['total_inflow'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Purchases + Expenses</div>
                    <div class="summary-value text-danger">Tk {{ number_format($s['total_outflow'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Net Operating Profit</div>
                    <div class="summary-value {{ $s['net_operating_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Tk {{ number_format($s['net_operating_profit'], 2) }}
                    </div>
                </td>
                <td width="25%">
                    <div class="summary-label">Gross Product Profit</div>
                    <div class="summary-value">Tk {{ number_format($s['gross_product_profit'], 2) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-heading">Profit & Loss Statement Summary</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Financial Account Head</th>
                    <th class="text-right">Amount (Tk)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1. Delivered Orders Revenue (Sales)</td>
                    <td class="text-right font-bold text-success">+ Tk {{ number_format($s['delivered_sales'], 2) }}</td>
                </tr>
                <tr>
                    <td>2. Other Income (Ledger Entries)</td>
                    <td class="text-right font-bold text-success">+ Tk {{ number_format($s['other_income'], 2) }}</td>
                </tr>
                <tr>
                    <td>3. Product Purchases (Inventory Restock / Ledger)</td>
                    <td class="text-right text-danger">- Tk {{ number_format($s['total_purchases'], 2) }}</td>
                </tr>
                <tr>
                    <td>4. Operating Expenses (Rent, Salary, Marketing, Utilities)</td>
                    <td class="text-right text-danger">- Tk {{ number_format($s['total_expenses'], 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Net Operating Cashflow Profit / (Loss)</td>
                    <td class="text-right font-bold">Tk {{ number_format($s['net_operating_profit'], 2) }}</td>
                </tr>
                <tr>
                    <td>Cost of Goods Sold (COGS on Delivered Products)</td>
                    <td class="text-right">Tk {{ number_format($s['delivered_cogs'], 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Product Margin Net Profit (Gross Profit + Income - Expenses)</td>
                    <td class="text-right font-bold">Tk {{ number_format($s['net_margin_profit'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-heading">Expense & Purchase Breakdown by Category</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Type</th>
                    <th class="text-center">Entries</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Remaining Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['expense_by_category'] as $cat)
                    <tr>
                        <td class="font-bold">{{ $cat['category'] }}</td>
                        <td>{{ strtoupper($cat['type']) }}</td>
                        <td class="text-center">{{ $cat['count'] }}</td>
                        <td class="text-right font-bold">Tk {{ number_format($cat['amount'], 2) }}</td>
                        <td class="text-right">Tk {{ number_format($cat['due_amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No expenses recorded in this period.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($type === 'inventory')
        @php $s = $data['summary']; @endphp
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Total Stock Units</div>
                    <div class="summary-value">{{ number_format($s['total_stock_units']) }} Units ({{ $s['total_products'] }} Items)</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Stock Cost Valuation</div>
                    <div class="summary-value">Tk {{ number_format($s['total_cost_valuation'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Stock Retail Valuation</div>
                    <div class="summary-value text-success">Tk {{ number_format($s['total_retail_valuation'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Low / Out of Stock</div>
                    <div class="summary-value text-danger">{{ $s['low_stock_count'] }} Low | {{ $s['out_of_stock_count'] }} Out</div>
                </td>
            </tr>
        </table>

        <div class="section-heading">Product Stock & Valuation Details</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th class="text-center">Stock</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Sell Price</th>
                    <th class="text-right">Cost Value</th>
                    <th class="text-right">Retail Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['products'] as $p)
                    @php
                        $cost = (float) ($p->cost_price ?? 0);
                        $sell = (float) $p->effective_price;
                        $qty = max(0, (int) $p->stock);
                    @endphp
                    <tr>
                        <td class="font-bold">{{ $p->name }}</td>
                        <td>{{ $p->sku ?? 'N/A' }}</td>
                        <td>{{ $p->category?->name ?? '-' }}</td>
                        <td class="text-center font-bold {{ $p->stock <= $p->low_stock_threshold ? 'text-danger' : '' }}">{{ $p->stock }}</td>
                        <td class="text-right">Tk {{ number_format($cost, 2) }}</td>
                        <td class="text-right">Tk {{ number_format($sell, 2) }}</td>
                        <td class="text-right">Tk {{ number_format($qty * $cost, 2) }}</td>
                        <td class="text-right font-bold">Tk {{ number_format($qty * $sell, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>

    @elseif($type === 'due-party')
        @php $s = $data['summary']; @endphp
        <table class="summary-table">
            <tr>
                <td width="25%">
                    <div class="summary-label">Customer Ledger Due</div>
                    <div class="summary-value text-danger">Tk {{ number_format($s['customer_due'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Supplier Payable Due</div>
                    <div class="summary-value text-danger">Tk {{ number_format($s['supplier_due'], 2) }}</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Unpaid Orders (COD)</div>
                    <div class="summary-value">Tk {{ number_format($s['unpaid_orders_amount'], 2) }} ({{ $s['unpaid_orders_count'] }})</div>
                </td>
                <td width="25%">
                    <div class="summary-label">Total Ledger Due</div>
                    <div class="summary-value">Tk {{ number_format($s['total_ledger_due'], 2) }}</div>
                </td>
            </tr>
        </table>

        <div class="section-heading">Party-wise Ledger Summary</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Party Name</th>
                    <th>Party Type</th>
                    <th class="text-center">Entries</th>
                    <th class="text-right">Total Paid / Received</th>
                    <th class="text-right">Outstanding Due</th>
                    <th>Last Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['party_summary'] as $party)
                    <tr>
                        <td class="font-bold">{{ $party['party_name'] }}</td>
                        <td>{{ strtoupper($party['party_type']) }}</td>
                        <td class="text-center">{{ $party['transactions_count'] }}</td>
                        <td class="text-right">Tk {{ number_format($party['total_paid'], 2) }}</td>
                        <td class="text-right font-bold {{ $party['total_due'] > 0 ? 'text-danger' : '' }}">Tk {{ number_format($party['total_due'], 2) }}</td>
                        <td>{{ $party['last_date'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No party records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Al-Aqsa Mart Admin Panel — Computer-generated Official Report.
    </div>

</body>
</html>
