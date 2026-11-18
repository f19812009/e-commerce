<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class InventoryReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Dashboard Report';

    protected static ?string $navigationLabel = 'Stock & Valuation Report';

    protected static ?string $title = 'স্টক ও ইনভেন্টরি ভ্যালুয়েশন রিপোর্ট (Inventory & Stock Report)';

    protected static ?string $slug = 'reports/inventory';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.inventory-report';

    public string $categoryId = 'all';
    public string $stockStatus = 'all';
    public string $startDate = '';
    public string $endDate = '';

    public function mount(): void
    {
        $this->startDate = Carbon::today()->startOfMonth()->toDateString();
        $this->endDate = Carbon::today()->toDateString();
    }

    protected function getViewData(): array
    {
        $service = app(ReportService::class);
        $report = $service->getInventoryReport(
            $this->categoryId,
            $this->stockStatus,
            $this->startDate,
            $this->endDate
        );

        return [
            'report' => $report,
        ];
    }
}
