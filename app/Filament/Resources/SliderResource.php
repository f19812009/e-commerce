<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Settings & Appearance';

    protected static ?string $navigationLabel = 'Sliders (Carousel)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Banner / Slider Image')
                    ->image()
                    ->directory('sliders')
                    ->helperText('প্রস্তাবিত সাইজ: ব্যানার হিসেবে ফুল গ্রাফিক্স ইমেজ (যেমন: 1200x450 বা 1920x600 px)')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label('Title (ঐচ্ছিক - ব্যানারে লেখা না থাকলে)')
                    ->placeholder('যেমন: বিশেষ ছাড় চলছে')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subtitle')
                    ->label('Subtitle (ঐচ্ছিক)')
                    ->placeholder('যেমন: সব পণ্যে ২০% পর্যন্ত ছাড়')
                    ->maxLength(255),
                Forms\Components\TextInput::make('offer_badge')
                    ->label('Offer Badge (ঐচ্ছিক)')
                    ->placeholder('যেমন: অফার ২০২৪')
                    ->maxLength(255),
                Forms\Components\TextInput::make('link')
                    ->label('Redirect Link (ঐচ্ছিক)')
                    ->placeholder('যেমন: /products?category=books বা https://...')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('সিরিয়াল / Sort Order')
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
                    ->label('Banner Preview')
                    ->width(120)
                    ->height(55),
                Tables\Columns\TextColumn::make('title')
                    ->placeholder('— কোনো টাইটেল নেই —')
                    ->searchable(),
                Tables\Columns\TextColumn::make('offer_badge')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
