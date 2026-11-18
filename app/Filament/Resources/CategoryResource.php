<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Categories & Submenus';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category & Submenu Configuration')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('মূল ক্যাটাগরি (Parent Category - সাব-ক্যাটাগরির জন্য)')
                            ->relationship(
                                'parent',
                                'name',
                                fn ($query, ?Category $record) => $query->whereNull('parent_id')->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('— মূল ক্যাটাগরি (Main / Root Category) —')
                            ->helperText('এটি যদি সাব-ক্যাটাগরি / সাব-মেনু হয়, তবে কোন মূল ক্যাটাগরির অধীনে থাকবে তা নির্বাচন করুন।')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $parent = Category::find($state);
                                    if ($parent && $parent->variety_type) {
                                        $set('variety_type', $parent->variety_type);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('name')
                            ->label('ক্যাটাগরি / সাব-ক্যাটাগরির নাম (Name)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set, Forms\Get $get) {
                                if ($operation === 'create' && $state) {
                                    $slug = Str::slug($state);
                                    $set('slug', $slug);
                                    if (!$get('parent_id')) {
                                        $set('variety_type', Category::inferVarietyType($state, $slug));
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->unique(Category::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('variety_type')
                            ->label('পণ্যের ভ্যারাইটি টেমপ্লেট (Product Variety Template)')
                            ->options(Category::varietyOptions())
                            ->default('general')
                            ->required()
                            ->helperText('এই ক্যাটাগরিতে প্রোডাক্ট যোগ করার সময় কোন ধরনের ডাইনামিক ফিল্ড আসবে তা নির্ধারণ করুন'),

                        Forms\Components\TextInput::make('icon')
                            ->label('Emoji or Icon Class')
                            ->placeholder('e.g. 📱, 💻, 📚, 👕')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('মেনু সিরিয়াল (Menu Sort Order)')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label('মেনুতে সক্রিয় (Active in Menu)')
                            ->default(true),

                        Forms\Components\FileUpload::make('image')
                            ->label('Category Image / Icon')
                            ->image()
                            ->directory('categories')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->label('Image'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Category / Submenu')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Category')
                    ->placeholder('Main Category (Root)')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variety_type')
                    ->label('Variety Template')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Category::varietyOptions()[$state ?? 'general'] ?? 'General')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('children_count')
                    ->counts('children')
                    ->label('Submenus')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('variety_type')
                    ->label('Variety Type')
                    ->options(Category::varietyOptions()),
                Tables\Filters\TernaryFilter::make('root_only')
                    ->label('Category Level')
                    ->placeholder('All Categories')
                    ->trueLabel('Main Categories Only')
                    ->falseLabel('Subcategories Only')
                    ->queries(
                        true: fn ($q) => $q->whereNull('parent_id'),
                        false: fn ($q) => $q->whereNotNull('parent_id'),
                    ),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
