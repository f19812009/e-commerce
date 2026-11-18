<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockHistoryResource\Pages;
use App\Models\StockHistory;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockHistoryResource extends Resource
{
    protected static ?string $model = StockHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Inventory & Stock';

    protected static ?string $navigationLabel = 'Stock Log & Adjustment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stock Adjustment / Entry')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $p = Product::find($state);
                                    if ($p) {
                                        $set('previous_stock', $p->stock);
                                    }
                                }
                            }),
                        Forms\Components\Select::make('type')
                            ->label('Movement Type')
                            ->options([
                                'opening'     => 'Opening Stock',
                                'stock_in'    => 'Stock In (Purchase/Restock)',
                                'stock_out'   => 'Stock Out (Damaged/Lost)',
                                'adjustment'  => 'Stock Adjustment',
                            ])
                            ->required()
                            ->default('stock_in'),
                        Forms\Components\TextInput::make('previous_stock')
                            ->label('Current / Previous Stock')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity to Change (+ or -)')
                            ->helperText('যেমন: 10 যোগ করতে 10, অথবা 5 কমাতে -5 লিখুন')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference (Invoice / Challan No.)')
                            ->placeholder('e.g. PO-1025')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('note')
                            ->label('Note / Reason')
                            ->placeholder('স্টক পরিবর্তনের কারণ...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('d M, Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'order_sale' => 'danger',
                        'stock_in', 'opening', 'order_cancel_return' => 'success',
                        'stock_out' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Change Qty')
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : (string)$state)
                    ->color(fn ($state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('previous_stock')
                    ->label('Previous')
                    ->numeric(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Resulting Stock')
                    ->numeric()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Handled By')
                    ->placeholder('System')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'order_sale'          => 'Order Sale',
                        'stock_in'            => 'Stock In',
                        'stock_out'           => 'Stock Out',
                        'adjustment'          => 'Adjustment',
                        'opening'             => 'Opening',
                        'order_cancel_return' => 'Return / Cancel',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockHistories::route('/'),
            'create' => Pages\CreateStockHistory::route('/create'),
        ];
    }
}
