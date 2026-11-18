<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SalesReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationGroup = 'Dashboard Report';

    protected static ?string $navigationLabel = 'Sales & Order Report';

    protected static ?string $title = 'বিক্রয় ও অর্ডার রিপোর্ট (Sales & Top Products Report)';

    protected static ?string $slug = 'reports/sales';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.sales-report';

    public string $startDate = '';
    public string $endDate = '';
    public string $status = 'all';
    public string $paymentMethod = 'all';
    public string $preset = 'this_month';

    public function mount(): void
    {
        $this->applyPreset('this_month');
    }

    public function applyPreset(string $preset): void
    {
        $this->preset = $preset;
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                $this->startDate = $today->toDateString();
                $this->endDate = $today->toDateString();
                break;
            case 'last_7_days':
                $this->startDate = $today->copy()->subDays(6)->toDateString();
                $this->endDate = $today->toDateString();
                break;
            case 'this_month':
                $this->startDate = $today->copy()->startOfMonth()->toDateString();
                $this->endDate = $today->toDateString();
                break;
            case 'last_month':
                $this->startDate = $today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
                $this->endDate = $today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();
                break;
            case 'this_year':
                $this->startDate = $today->copy()->startOfYear()->toDateString();
                $this->endDate = $today->toDateString();
                break;
            case 'all_time':
                $this->startDate = '2020-01-01';
                $this->endDate = $today->toDateString();
                break;
        }
    }

    protected function getViewData(): array
    {
        $service = app(ReportService::class);
        $report = $service->getSalesReport(
            $this->startDate,
            $this->endDate,
            $this->status,
            $this->paymentMethod
        );

        return [
            'report' => $report,
        ];
    }
}
