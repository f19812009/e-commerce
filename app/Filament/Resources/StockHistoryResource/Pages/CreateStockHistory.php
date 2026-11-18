<?php

namespace App\Filament\Resources\StockHistoryResource\Pages;

use App\Filament\Resources\StockHistoryResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;

class CreateStockHistory extends CreateRecord
{
    protected static string $resource = StockHistoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::findOrFail($data['product_id']);
        $prevStock = $product->stock;
        $qtyChange = (int) $data['quantity'];
        $newStock = max(0, $prevStock + $qtyChange);

        // Update product stock immediately
        $product->update(['stock' => $newStock]);

        $data['previous_stock'] = $prevStock;
        $data['current_stock'] = $newStock;
        $data['user_id'] = auth()->id();

        return $data;
    }
}
