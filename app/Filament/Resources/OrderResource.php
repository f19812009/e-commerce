<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Orders (অর্ডার ও ট্র্যাকিং)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('🚚 অর্ডার স্ট্যাটাস ও ট্র্যাকিং আপডেট (Order Tracking & Status)')
                    ->description('এখান থেকে অর্ডারের ধাপ (Pending → Processing → Shipped → Delivered) এবং কুরিয়ার ট্র্যাকিং তথ্য আপডেট করলে গ্রাহক সাথে সাথে `/track-order` পেজে দেখতে পাবেন।')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('অর্ডার স্ট্যাটাস (Order Status)')
                            ->options([
                                'pending' => '⏳ Pending (অর্ডার গৃহীত)',
                                'processing' => '⚙️ Processing (প্রস্তুত হচ্ছে)',
                                'shipped' => '🚚 Shipped (ডেলিভারির পথে)',
                                'delivered' => '✅ Delivered (ডেলিভারি সম্পন্ন)',
                                'cancelled' => '❌ Cancelled (বাতিল)',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('payment_status')
                            ->label('পেমেন্ট স্ট্যাটাস (Payment Status)')
                            ->options([
                                'unpaid' => 'Unpaid (বকেয়া)',
                                'paid' => 'Paid (পরিশোধিত)',
                                'refunded' => 'Refunded (ফেরত)',
                            ])
                            ->default('unpaid')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('courier_name')
                            ->label('কুরিয়ার সার্ভিস (Courier Name)')
                            ->options([
                                'Steadfast Courier' => 'Steadfast Courier',
                                'Pathao Courier' => 'Pathao Courier',
                                'RedX Delivery' => 'RedX Delivery',
                                'Sundarban Courier' => 'Sundarban Courier',
                                'SA Paribahan' => 'SA Paribahan',
                                'Paperfly' => 'Paperfly',
                                'Own Delivery (নিজস্ব ডেলিভারি)' => 'Own Delivery (নিজস্ব ডেলিভারি)',
                            ])
                            ->searchable()
                            ->placeholder('কুরিয়ার নির্বাচন করুন'),
                        Forms\Components\TextInput::make('tracking_code')
                            ->label('ট্র্যাকিং কোড / কনসাইনমেন্ট আইডি (Tracking Code)')
                            ->placeholder('যেমন: SF-8934201 বা CN-10293')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->required()
                            ->maxLength(255)
                            ->default('cod'),
                        Forms\Components\Textarea::make('tracking_note')
                            ->label('গ্রাহকের জন্য ট্র্যাকিং মেসেজ / নোট (Tracking Note)')
                            ->placeholder('যেমন: আপনার পার্সেলটি আজ বিকেলে Steadfast কুরিয়ারে বুকিং দেওয়া হয়েছে। আগামী ২৪-৪৮ ঘণ্টায় ডেলিভারি পাবেন ইনশাআল্লাহ।')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('👤 গ্রাহক ও ডেলিভারি ঠিকানা (Customer & Shipping Details)')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('গ্রাহকের নাম')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('মোবাইল নম্বর')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('ইমেইল')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('শহর / জেলা')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('পোস্টাল কোড')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->label('দেশ')
                            ->required()
                            ->maxLength(255)
                            ->default('Bangladesh'),
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('পূর্ণাঙ্গ ঠিকানা')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('গ্রাহকের নোট (Order Notes)')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('💰 বিলিং সারাংশ (Order Totals)')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('সাব-টোটাল (৳)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('shipping')
                            ->label('ডেলিভারি চার্জ (৳)')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('total')
                            ->label('সর্বমোট বিল (৳)')
                            ->required()
                            ->numeric(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('📦 অর্ডারকৃত পণ্যসমূহ (Ordered Items)')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('product_name')
                                    ->label('পণ্যের নাম')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('price')
                                    ->label('একক মূল্য (৳)')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('পরিমাণ')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('মোট (৳)')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->columns(5)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->weight('bold')
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total (৳)')
                    ->money('BDT')
                    ->weight('bold')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Tracking Status')
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
                    ->label('Courier / Tracking')
                    ->placeholder('—')
                    ->description(fn (Order $record): ?string => $record->tracking_code ? "Code: {$record->tracking_code}" : null)
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid', 'pending' => 'warning',
                        'refunded', 'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Order Status')
                    ->options([
                        'pending' => 'Pending (অর্ডার গৃহীত)',
                        'processing' => 'Processing (প্রস্তুত হচ্ছে)',
                        'shipped' => 'Shipped (ডেলিভারির পথে)',
                        'delivered' => 'Delivered (ডেলিভারি সম্পন্ন)',
                        'cancelled' => 'Cancelled (বাতিল)',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cod' => 'Cash on Delivery (COD)',
                        'bkash' => 'bKash',
                        'nagad' => 'Nagad',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('updateTracking')
                    ->label('ট্র্যাকিং আপডেট')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->modalHeading(fn (Order $record): string => "অর্ডার ট্র্যাকিং আপডেট — #{$record->order_number}")
                    ->modalDescription('অর্ডারের বর্তমান ধাপ, কুরিয়ার সার্ভিস এবং ট্র্যাকিং কোড আপডেট করুন।')
                    ->modalSubmitActionLabel('আপডেট সেভ করুন')
                    ->fillForm(fn (Order $record): array => [
                        'status' => $record->status,
                        'payment_status' => $record->payment_status,
                        'courier_name' => $record->courier_name,
                        'tracking_code' => $record->tracking_code,
                        'tracking_note' => $record->tracking_note,
                    ])
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('অর্ডার স্ট্যাটাস (Tracking Step)')
                                    ->options([
                                        'pending' => '⏳ Pending (অর্ডার গৃহীত)',
                                        'processing' => '⚙️ Processing (প্রস্তুত হচ্ছে)',
                                        'shipped' => '🚚 Shipped (ডেলিভারির পথে)',
                                        'delivered' => '✅ Delivered (ডেলিভারি সম্পন্ন)',
                                        'cancelled' => '❌ Cancelled (বাতিল)',
                                    ])
                                    ->required()
                                    ->native(false),
                                Forms\Components\Select::make('payment_status')
                                    ->label('পেমেন্ট স্ট্যাটাস')
                                    ->options([
                                        'unpaid' => 'Unpaid (বকেয়া)',
                                        'paid' => 'Paid (পরিশোধিত)',
                                        'refunded' => 'Refunded (ফেরত)',
                                    ])
                                    ->required()
                                    ->native(false),
                                Forms\Components\Select::make('courier_name')
                                    ->label('কুরিয়ার সার্ভিস (Courier Name)')
                                    ->options([
                                        'Steadfast Courier' => 'Steadfast Courier',
                                        'Pathao Courier' => 'Pathao Courier',
                                        'RedX Delivery' => 'RedX Delivery',
                                        'Sundarban Courier' => 'Sundarban Courier',
                                        'SA Paribahan' => 'SA Paribahan',
                                        'Paperfly' => 'Paperfly',
                                        'Own Delivery (নিজস্ব ডেলিভারি)' => 'Own Delivery (নিজস্ব ডেলিভারি)',
                                    ])
                                    ->searchable()
                                    ->placeholder('কুরিয়ার নির্বাচন করুন'),
                                Forms\Components\TextInput::make('tracking_code')
                                    ->label('কুরিয়ার ট্র্যাকিং কোড (Tracking ID)')
                                    ->placeholder('যেমন: SF-8934201'),
                            ]),
                        Forms\Components\Textarea::make('tracking_note')
                            ->label('গ্রাহকের জন্য ট্র্যাকিং মেসেজ / আপডেট নোট')
                            ->placeholder('যেমন: পার্সেলটি কুরিয়ারে পাঠানো হয়েছে, আগামীকাল ডেলিভারি হবে ইনশাআল্লাহ।')
                            ->rows(2),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update($data);

                        Notification::make()
                            ->title("অর্ডার #{$record->order_number} এর ট্র্যাকিং তথ্য আপডেট হয়েছে!")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('invoice')
                    ->label('PDF Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Order $record): string => route('orders.invoice.download', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
