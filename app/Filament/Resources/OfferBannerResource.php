<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferBannerResource\Pages;
use App\Models\OfferBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfferBannerResource extends Resource
{
    protected static ?string $model = OfferBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Settings & Appearance';

    protected static ?string $navigationLabel = 'Offer Banners (অফার ব্যানার)';

    protected static ?string $modelLabel = 'Offer Banner';

    protected static ?string $pluralModelLabel = 'Offer Banners';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('ব্যানার ইমেজ (Offer Banner Image)')
                    ->image()
                    ->directory('offer-banners')
                    ->helperText('প্রস্তাবিত সাইজ: হাই-কোয়ালিটি ব্যানার (যেমন: 1200x250 বা 1200x300 px)')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label('ব্যানার নাম / টাইটেল (ঐচ্ছিক)')
                    ->placeholder('যেমন: রামাদান মেগা অফার বা ৫০% ডিসকাউন্ট')
                    ->maxLength(255),
                Forms\Components\Select::make('after_category_id')
                    ->label('কোন ক্যাটাগরির নিচে দেখাবে? (Display Position)')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('ক্যাটাগরি নির্বাচন করুন (বা খালি রাখুন)')
                    ->helperText('হোমপেজে যে ক্যাটাগরির সেকশনের নিচে এই অফার ব্যানার দেখাতে চান সেটি নির্বাচন করুন। খালি রাখলে হোমপেজের ক্যাটাগরিগুলোর শেষে দেখাবে।'),
                Forms\Components\TextInput::make('link')
                    ->label('Redirect Link (ঐচ্ছিক)')
                    ->placeholder('যেমন: /products?category=perfume বা https://...')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('সিরিয়াল নম্বর (Sort Order)')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('সক্রিয় / Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('ব্যানার প্রিভিউ')
                    ->width(140)
                    ->height(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('টাইটেল')
                    ->placeholder('— কোনো নাম নেই —')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('ক্যাটাগরির নিচে')
                    ->badge()
                    ->color('info')
                    ->placeholder('ক্যাটাগরি তালিকার শেষে'),
                Tables\Columns\TextColumn::make('link')
                    ->label('লিংক')
                    ->limit(25)
                    ->placeholder('— নেই —')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('সিরিয়াল')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('সক্রিয়')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferBanners::route('/'),
            'create' => Pages\CreateOfferBanner::route('/create'),
            'edit' => Pages\EditOfferBanner::route('/{record}/edit'),
        ];
    }
}