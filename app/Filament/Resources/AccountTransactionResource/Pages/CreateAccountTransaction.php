<?php

namespace App\Filament\Resources\AccountTransactionResource\Pages;

use App\Filament\Resources\AccountTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAccountTransaction extends CreateRecord
{
    protected static string $resource = AccountTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        return $data;
    }
}
