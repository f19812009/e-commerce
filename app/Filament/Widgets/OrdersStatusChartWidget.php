<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersStatusChartWidget extends ChartWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = '📊 অর্ডার স্ট্যাটাস সারাংশ (Order Distribution)';
    protected static ?string $description = 'বর্তমান অর্ডারসমূহের ধাপ অনুযায়ী অনুপাত';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '285px';

    public static function canView(): bool
    {
        return (bool) (\App\Models\DashboardSetting::current()['show_order_chart'] ?? true);
    }

    protected function getData(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $delivered = Order::where('status', 'delivered')->count();
        $cancelled = Order::where('status', 'cancelled')->count();

        $total = $pending + $processing + $shipped + $delivered + $cancelled;

        if ($total === 0) {
            return [
                'datasets' => [
                    [
                        'label' => 'Orders',
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                        'borderWidth' => 0,
                    ],
                ],
                'labels' => ['কোনো অর্ডার নেই (0)'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [$pending, $processing, $shipped, $delivered, $cancelled],
                    'backgroundColor' => [
                        '#f59e0b', // Pending (amber)
                        '#3b82f6', // Processing (blue)
                        '#6366f1', // Shipped (indigo)
                        '#10b981', // Delivered (emerald)
                        '#ef4444', // Cancelled (red)
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '68%',
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
