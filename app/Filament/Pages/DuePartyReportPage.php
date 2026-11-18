<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use Filament\Pages\Page;

class DuePartyReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Dashboard Report';

    protected static ?string $navigationLabel = 'Due & Party Ledger';

    protected static ?string $title = 'বকেয়া ও পার্টি লেজার রিপোর্ট (Customer & Supplier Due Report)';

    protected static ?string $slug = 'reports/due-party';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.due-party-report';

    public string $partyType = 'all';
    public string $search = '';
    public bool $onlyDue = false;

    protected function getViewData(): array
    {
        $service = app(ReportService::class);
        $report = $service->getDuePartyReport(
            $this->partyType,
            $this->search,
            $this->onlyDue
        );

        return [
            'report' => $report,
        ];
    }
}
