<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\StockHistory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('invoice')
                ->label('Download PDF Invoice')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (): string => route('orders.invoice.download', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $order = $this->record;

        // If order status was changed to cancelled, restore the stock
        if ($order->wasChanged('status') && $order->status === 'cancelled') {
            $order->load('items.product');

            foreach ($order->items as $item) {
                if ($item->product) {
                    $prevStock = $item->product->stock;
                    $newStock = $prevStock + $item->quantity;
                    $item->product->update(['stock' => $newStock]);

                    StockHistory::create([
                        'product_id'     => $item->product->id,
                        'type'           => 'order_cancel_return',
                        'quantity'       => $item->quantity,
                        'previous_stock' => $prevStock,
                        'current_stock'  => $newStock,
                        'reference'      => 'Order #' . $order->order_number . ' (Cancelled)',
                        'note'           => 'অর্ডার বাতিল হওয়ায় স্বয়ংক্রিয় স্টক ফেরত (Restock)',
                        'user_id'        => auth()->id(),
                    ]);
                }
            }
        }
    }
}
