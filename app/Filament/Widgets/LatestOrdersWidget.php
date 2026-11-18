<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = '🛒 সর্বশেষ অর্ডারসমূহ ও ট্র্যাকিং (Recent Orders)';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (bool) (\App\Models\DashboardSetting::current()['show_recent_orders'] ?? true);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('অর্ডার নং')
                    ->weight('bold')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('গ্রাহক')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('ফোন নম্বর'),

                Tables\Columns\TextColumn::make('city')
                    ->label('শহর')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('total')
                    ->label('মোট বিল')
                    ->money('BDT')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('ট্র্যাকিং স্ট্যাটাস')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '⏳ Pending',
                        'processing' => '⚙️ Processing',
                        'shipped' => '🚚 Shipped',
                        'delivered' => '✅ Delivered',
                        'cancelled' => '❌ Cancelled',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('courier_name')
                    ->label('কুরিয়ার / ট্র্যাকিং')
                    ->placeholder('নির্ধারিত হয়নি')
                    ->description(fn (Order $record): ?string => $record->tracking_code ? "ID: {$record->tracking_code}" : null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('তারিখ ও সময়')
                    ->dateTime('d M Y, h:i A'),
            ])
            ->actions([
                Tables\Actions\Action::make('manage')
                    ->label('ট্র্যাকিং / ম্যানেজ')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->url(fn (Order $record): string => url("/admin/orders/{$record->id}/edit")),
                Tables\Actions\Action::make('invoice')
                    ->label('ইনভয়েস')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Order $record): string => route('orders.invoice.download', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('এখনো কোনো অর্ডার আসেনি')
            ->emptyStateDescription('নতুন অর্ডার আসলে এখানে সাথে সাথে তালিকা ও ট্র্যাকিং বাটন দেখা যাবে।')
            ->emptyStateIcon('heroicon-o-shopping-bag')
            ->paginated(false);
    }
}
