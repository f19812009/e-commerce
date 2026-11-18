<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesTrendChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = '📈 বিগত ৭ দিনের বিক্রয় ও অর্ডার ট্রেন্ড (Sales & Order Trend)';
    protected static ?string $description = 'ডেলিভারিকৃত বিক্রয় বনাম মোট অর্ডার ভ্যালু (বাতিল ব্যতীত)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '285px';

    public static function canView(): bool
    {
        return (bool) (\App\Models\DashboardSetting::current()['show_sales_chart'] ?? true);
    }

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($d) => Carbon::today()->subDays($d));

        $labels = [];
        $deliveredData = [];
        $orderValueData = [];

        foreach ($days as $day) {
            $labels[] = $day->format('d M');

            $deliveredData[] = (float) Order::where('status', 'delivered')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total');

            $orderValueData[] = (float) Order::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'ডেলিভারিকৃত বিক্রয় (৳)',
                    'data' => $deliveredData,
                    'fill' => 'start',
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.16)',
                    'pointBackgroundColor' => '#059669',
                    'borderWidth' => 2.5,
                    'tension' => 0.35,
                ],
                [
                    'label' => 'মোট অর্ডার মূল্য (৳)',
                    'data' => $orderValueData,
                    'fill' => false,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'pointBackgroundColor' => '#3b82f6',
                    'borderDash' => [5, 5],
                    'borderWidth' => 2,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'min' => 0,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
