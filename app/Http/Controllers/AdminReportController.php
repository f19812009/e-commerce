<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    /**
     * Download or Stream PDF Report
     */
    public function pdf(Request $request, string $type)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        switch ($type) {
            case 'sales':
                $data = $this->reportService->getSalesReport(
                    $startDate,
                    $endDate,
                    $request->query('status', 'all'),
                    $request->query('payment_method', 'all')
                );
                $title = 'SALES & ORDER REPORT';
                break;

            case 'profit-loss':
                $data = $this->reportService->getProfitLossReport($startDate, $endDate);
                $title = 'PROFIT & LOSS FINANCIAL STATEMENT';
                break;

            case 'inventory':
                $data = $this->reportService->getInventoryReport(
                    $request->query('category_id', 'all'),
                    $request->query('stock_status', 'all'),
                    $startDate,
                    $endDate
                );
                $title = 'INVENTORY & STOCK VALUATION REPORT';
                break;

            case 'due-party':
                $data = $this->reportService->getDuePartyReport(
                    $request->query('party_type', 'all'),
                    $request->query('search', ''),
                    (bool) $request->query('only_due', false)
                );
                $title = 'PARTY LEDGER & OUTSTANDING DUE REPORT';
                break;

            default:
                abort(404);
        }

        $pdf = Pdf::loadView('reports.pdf-report', [
            'type'        => $type,
            'title'       => $title,
            'data'        => $data,
            'generatedAt' => now()->format('d M, Y h:i A'),
        ])->setPaper('a4', 'portrait');

        $filename = "al-aqsa-{$type}-report-" . now()->format('Y-m-d') . ".pdf";

        if ($request->boolean('stream')) {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
    }

    /**
     * Export Report as CSV (UTF-8 with BOM for Excel compatibility)
     */
    public function csv(Request $request, string $type): StreamedResponse
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $filename = "al-aqsa-{$type}-report-" . now()->format('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($request, $type, $startDate, $endDate) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            if ($type === 'sales') {
                $data = $this->reportService->getSalesReport(
                    $startDate,
                    $endDate,
                    $request->query('status', 'all'),
                    $request->query('payment_method', 'all')
                );

                fputcsv($out, ['AL-AQSA MART - SALES & ORDER REPORT']);
                fputcsv($out, ['Period', $data['period']['start'] . ' to ' . $data['period']['end']]);
                fputcsv($out, []);

                fputcsv($out, ['SUMMARY METRICS', 'VALUE']);
                fputcsv($out, ['Total Orders', $data['summary']['total_orders_count']]);
                fputcsv($out, ['Delivered Orders', $data['summary']['delivered_count']]);
                fputcsv($out, ['Delivered Sales Revenue (BDT)', number_format($data['summary']['delivered_revenue'], 2, '.', '')]);
                fputcsv($out, ['Pending/Running Orders Value (BDT)', number_format($data['summary']['pending_revenue'], 2, '.', '')]);
                fputcsv($out, ['Cancelled Orders Value (BDT)', number_format($data['summary']['cancelled_amount'], 2, '.', '')]);
                fputcsv($out, ['Shipping Collected (BDT)', number_format($data['summary']['total_shipping'], 2, '.', '')]);
                fputcsv($out, []);

                fputcsv($out, ['TOP SELLING PRODUCTS']);
                fputcsv($out, ['Product Name', 'SKU', 'Sold Qty', 'Revenue (BDT)', 'COGS (BDT)', 'Est. Profit (BDT)', 'Current Stock']);
                foreach ($data['top_products'] as $prod) {
                    fputcsv($out, [
                        $prod['product_name'],
                        $prod['sku'],
                        $prod['total_qty'],
                        number_format($prod['total_revenue'], 2, '.', ''),
                        number_format($prod['total_cogs'], 2, '.', ''),
                        number_format($prod['estimated_profit'], 2, '.', ''),
                        $prod['current_stock'],
                    ]);
                }
                fputcsv($out, []);

                fputcsv($out, ['ORDER DETAILS']);
                fputcsv($out, ['Order #', 'Date', 'Customer Name', 'Phone', 'City', 'Status', 'Payment Method', 'Payment Status', 'Total (BDT)']);
                foreach ($data['orders'] as $order) {
                    fputcsv($out, [
                        $order->order_number,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->customer_phone,
                        $order->city,
                        strtoupper($order->status),
                        strtoupper($order->payment_method),
                        strtoupper($order->payment_status),
                        number_format((float) $order->total, 2, '.', ''),
                    ]);
                }
            } elseif ($type === 'profit-loss') {
                $data = $this->reportService->getProfitLossReport($startDate, $endDate);
                $s = $data['summary'];

                fputcsv($out, ['AL-AQSA MART - PROFIT & LOSS REPORT']);
                fputcsv($out, ['Period', $data['period']['start'] . ' to ' . $data['period']['end']]);
                fputcsv($out, []);

                fputcsv($out, ['FINANCIAL HEAD', 'AMOUNT (BDT)']);
                fputcsv($out, ['Delivered Order Revenue', number_format($s['delivered_sales'], 2, '.', '')]);
                fputcsv($out, ['Cost of Goods Sold (COGS)', number_format($s['delivered_cogs'], 2, '.', '')]);
                fputcsv($out, ['Gross Product Profit', number_format($s['gross_product_profit'], 2, '.', '')]);
                fputcsv($out, ['Other Income (Ledger)', number_format($s['other_income'], 2, '.', '')]);
                fputcsv($out, ['Product Purchases (Ledger)', number_format($s['total_purchases'], 2, '.', '')]);
                fputcsv($out, ['Operating Expenses (Ledger)', number_format($s['total_expenses'], 2, '.', '')]);
                fputcsv($out, ['Net Operating Profit (Sales+Income - Purchase-Expense)', number_format($s['net_operating_profit'], 2, '.', '')]);
                fputcsv($out, ['Net Margin Profit (Gross Profit+Income - Expense)', number_format($s['net_margin_profit'], 2, '.', '')]);
                fputcsv($out, []);

                fputcsv($out, ['LEDGER TRANSACTIONS']);
                fputcsv($out, ['Date', 'Title', 'Type', 'Category', 'Party Name', 'Payment Method', 'Amount (BDT)', 'Due (BDT)', 'Reference']);
                foreach ($data['transactions'] as $tr) {
                    fputcsv($out, [
                        optional($tr->transaction_date)->format('Y-m-d'),
                        $tr->title,
                        strtoupper($tr->type),
                        $tr->category ?? '-',
                        $tr->party_name ?? '-',
                        strtoupper($tr->payment_method),
                        number_format((float) $tr->amount, 2, '.', ''),
                        number_format((float) $tr->due_amount, 2, '.', ''),
                        $tr->reference_no ?? '-',
                    ]);
                }
            } elseif ($type === 'inventory') {
                $data = $this->reportService->getInventoryReport(
                    $request->query('category_id', 'all'),
                    $request->query('stock_status', 'all'),
                    $startDate,
                    $endDate
                );

                fputcsv($out, ['AL-AQSA MART - INVENTORY & STOCK VALUATION REPORT']);
                fputcsv($out, []);
                fputcsv($out, ['Product Name', 'SKU', 'Category', 'Current Stock', 'Low Threshold', 'Cost Price (BDT)', 'Selling Price (BDT)', 'Total Cost Valuation (BDT)', 'Total Retail Valuation (BDT)', 'Total Sold Qty']);
                foreach ($data['products'] as $p) {
                    $cost = (float) ($p->cost_price ?? 0);
                    $sell = (float) $p->effective_price;
                    $stock = max(0, (int) $p->stock);
                    fputcsv($out, [
                        $p->name,
                        $p->sku ?? 'N/A',
                        $p->category?->name ?? 'Uncategorized',
                        $p->stock,
                        $p->low_stock_threshold,
                        number_format($cost, 2, '.', ''),
                        number_format($sell, 2, '.', ''),
                        number_format($stock * $cost, 2, '.', ''),
                        number_format($stock * $sell, 2, '.', ''),
                        (int) ($p->total_sold_qty ?? 0),
                    ]);
                }
            } elseif ($type === 'due-party') {
                $data = $this->reportService->getDuePartyReport(
                    $request->query('party_type', 'all'),
                    $request->query('search', ''),
                    (bool) $request->query('only_due', false)
                );

                fputcsv($out, ['AL-AQSA MART - PARTY LEDGER & OUTSTANDING DUE REPORT']);
                fputcsv($out, []);
                fputcsv($out, ['Party Name', 'Party Type', 'Transactions Count', 'Total Paid/Received (BDT)', 'Total Remaining Due (BDT)', 'Last Transaction Date']);
                foreach ($data['party_summary'] as $party) {
                    fputcsv($out, [
                        $party['party_name'],
                        strtoupper($party['party_type']),
                        $party['transactions_count'],
                        number_format($party['total_paid'], 2, '.', ''),
                        number_format($party['total_due'], 2, '.', ''),
                        $party['last_date'],
                    ]);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
