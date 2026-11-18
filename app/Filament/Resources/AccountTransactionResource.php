<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountTransactionResource\Pages;
use App\Models\AccountTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountTransactionResource extends Resource
{
    protected static ?string $model = AccountTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Accounts & Finance';

    protected static ?string $navigationLabel = 'Ledger & Transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Entry')
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('type')
                            ->label('Transaction Type')
                            ->options([
                                'income'           => 'Income (আয় / অন্যান্য আয়)',
                                'expense'          => 'Expense (খরচ - ভাড়া/বিদ্যুৎ/বেতন)',
                                'purchase'         => 'Purchase (পণ্য ক্রয়)',
                                'sales_collection' => 'Sales Collection (বিক্রয় জমা)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Description / Title')
                            ->placeholder('যেমন: অফিস ভাড়া, মধু ক্রয়, ডেলিভারি খরচ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->label('Category / Head')
                            ->placeholder('e.g. Rent, Salary, Utility, Marketing, Packaging')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('amount')
                            ->label('Paid / Received Amount (৳)')
                            ->prefix('৳')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('due_amount')
                            ->label('Remaining Due (বকেয়া ৳)')
                            ->prefix('৳')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\Select::make('party_type')
                            ->label('Party Type')
                            ->options([
                                'customer' => 'Customer',
                                'supplier' => 'Supplier / Vendor',
                                'other'    => 'Other / Internal',
                            ])
                            ->default('other'),
                        Forms\Components\TextInput::make('party_name')
                            ->label('Party Name')
                            ->placeholder('যেমন: করিম ট্রেডার্স / কাস্টমার নাম')
                            ->maxLength(255),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash'  => 'Cash (নগদ টাকা)',
                                'bank'  => 'Bank Transfer',
                                'bkash' => 'bKash',
                                'nagad' => 'Nagad',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\TextInput::make('reference_no')
                            ->label('Reference / Voucher No.')
                            ->placeholder('e.g. VCH-001 / PO-99')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('d M, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income', 'sales_collection' => 'success',
                        'expense' => 'danger',
                        'purchase' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (৳)')
                    ->money('BDT')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('due_amount')
                    ->label('Due (৳)')
                    ->money('BDT')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('party_name')
                    ->label('Party / Person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('reference_no')
                    ->label('Ref #')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'income'           => 'Income',
                        'expense'          => 'Expense',
                        'purchase'         => 'Purchase',
                        'sales_collection' => 'Sales Collection',
                    ]),
                Tables\Filters\SelectFilter::make('party_type')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        'other'    => 'Other',
                    ]),
                Tables\Filters\Filter::make('has_due')
                    ->label('Has Outstanding Due')
                    ->query(fn ($query) => $query->where('due_amount', '>', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountTransactions::route('/'),
            'create' => Pages\CreateAccountTransaction::route('/create'),
            'edit' => Pages\EditAccountTransaction::route('/{record}/edit'),
        ];
    }
}
