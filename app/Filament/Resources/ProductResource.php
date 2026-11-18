<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // 1. General Information
                        Forms\Components\Section::make('সাধারণ তথ্য (General Information)')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('পণ্যের নাম (Product Name)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) =>
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->unique(Product::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\Select::make('category_id')
                                    ->label('ক্যাটাগরি নির্বাচন করুন (Category)')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $cat = Category::find($state);
                                            if ($cat) {
                                                $type = $cat->variety_type ?: Category::inferVarietyType($cat->name, $cat->slug ?? '');
                                                $set('product_type', $type);
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Category Name')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state) {
                                                    $slug = Str::slug($state);
                                                    $set('slug', $slug);
                                                    $set('variety_type', Category::inferVarietyType($state, $slug));
                                                }
                                            }),
                                        Forms\Components\TextInput::make('slug')
                                            ->required(),
                                        Forms\Components\Select::make('variety_type')
                                            ->label('Variety Template')
                                            ->options(Category::varietyOptions())
                                            ->default('general')
                                            ->required(),
                                    ]),

                                Forms\Components\Select::make('product_type')
                                    ->label('পণ্যের ভ্যারাইটি / ধরন (Variety Template)')
                                    ->options(Category::varietyOptions())
                                    ->default('general')
                                    ->required()
                                    ->live()
                                    ->helperText('ক্যাটাগরি সিলেক্ট করলে এটি স্বয়ংক্রিয়ভাবে সেট হবে এবং নিচে সেই পণ্যের ফিল্ডগুলো চলে আসবে'),

                                Forms\Components\TextInput::make('brand')
                                    ->label('ব্র্যান্ড / প্রস্তুতকারক (Brand)')
                                    ->placeholder('যেমন: Apple, Samsung, HP, Asus, Aarong')
                                    ->maxLength(120),

                                Forms\Components\TextInput::make('warranty')
                                    ->label('ওয়ারেন্টি / গ্যারান্টি (Warranty)')
                                    ->placeholder('যেমন: ১ বছর অফিশিয়াল ওয়ারেন্টি / ৭ দিনের রিপ্লেসমেন্ট')
                                    ->maxLength(150),

                                Forms\Components\Textarea::make('description')
                                    ->label('পণ্যের বিস্তারিত বিবরণ (Description)')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // 2. Dynamic Variety-Specific Fields
                        Forms\Components\Section::make('পণ্য ভ্যারাইটি অনুযায়ী বৈশিষ্ট্য (Variety-Specific Fields)')
                            ->description('নির্বাচিত ক্যাটাগরি ও পণ্যের ধরন অনুযায়ী নিচের স্পেসিফিকেশন ফিল্ডগুলো পূরণ করুন')
                            ->schema([
                                // A. Smartphones & Tablets (phone_tablet)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'phone_tablet')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.model_name')
                                            ->label('মডেল (Model Name)')
                                            ->placeholder('e.g. iPhone 15 Pro Max / Galaxy S24 Ultra'),
                                        Forms\Components\TextInput::make('specifications.ram')
                                            ->label('র‍্যাম (RAM)')
                                            ->placeholder('e.g. 6GB / 8GB / 12GB'),
                                        Forms\Components\TextInput::make('specifications.storage')
                                            ->label('স্টোরেজ / ROM (Internal Storage)')
                                            ->placeholder('e.g. 128GB / 256GB / 512GB'),
                                        Forms\Components\TextInput::make('specifications.display')
                                            ->label('ডিসপ্লে (Display Size & Type)')
                                            ->placeholder('e.g. 6.7" Super AMOLED 120Hz'),
                                        Forms\Components\TextInput::make('specifications.processor')
                                            ->label('প্রসেসর / চিপসেট (Processor / Chipset)')
                                            ->placeholder('e.g. Snapdragon 8 Gen 3 / Apple A17 Pro'),
                                        Forms\Components\TextInput::make('specifications.camera')
                                            ->label('ক্যামেরা (Rear & Front Camera)')
                                            ->placeholder('e.g. 50MP + 12MP Rear | 12MP Front'),
                                        Forms\Components\TextInput::make('specifications.battery')
                                            ->label('ব্যাটারি ও চার্জিং (Battery & Charging)')
                                            ->placeholder('e.g. 5000mAh, 45W Fast Charging'),
                                        Forms\Components\TextInput::make('specifications.sim_network')
                                            ->label('সিম ও নেটওয়ার্ক (SIM & Network)')
                                            ->placeholder('e.g. Dual Nano-SIM + eSIM (5G)'),
                                        Forms\Components\TextInput::make('specifications.os')
                                            ->label('অপারেটিং সিস্টেম (OS)')
                                            ->placeholder('e.g. Android 14 / iOS 18'),
                                        Forms\Components\TagsInput::make('specifications.colors')
                                            ->label('উপলভ্য কালারসমূহ (Available Colors)')
                                            ->placeholder('কালার লিখে Enter চাপুন (যেমন: Black, Silver, Blue)'),
                                    ]),

                                // B. Laptops & PC Components (laptop_pc)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'laptop_pc')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.model_name')
                                            ->label('মডেল (Model Name)')
                                            ->placeholder('e.g. MacBook Air M3 / HP Pavilion 15'),
                                        Forms\Components\TextInput::make('specifications.processor')
                                            ->label('প্রসেসর / CPU (Processor)')
                                            ->placeholder('e.g. Intel Core i5-13420H / AMD Ryzen 7 7730U'),
                                        Forms\Components\TextInput::make('specifications.ram')
                                            ->label('র‍্যাম (RAM Capacity & Type)')
                                            ->placeholder('e.g. 16GB DDR5 5200MHz'),
                                        Forms\Components\TextInput::make('specifications.storage')
                                            ->label('স্টোরেজ (SSD / HDD Storage)')
                                            ->placeholder('e.g. 512GB NVMe PCIe Gen4 SSD'),
                                        Forms\Components\TextInput::make('specifications.graphics')
                                            ->label('গ্রাফিক্স কার্ড (Graphics / GPU)')
                                            ->placeholder('e.g. NVIDIA RTX 4050 6GB / Intel Iris Xe'),
                                        Forms\Components\TextInput::make('specifications.display')
                                            ->label('ডিসপ্লে (Display Size & Resolution)')
                                            ->placeholder('e.g. 15.6" FHD (1920x1080) IPS 144Hz'),
                                        Forms\Components\TextInput::make('specifications.battery')
                                            ->label('ব্যাটারি / পাওয়ার (Battery & Adapter)')
                                            ->placeholder('e.g. 3-Cell 54Wh, 65W Type-C Adapter'),
                                        Forms\Components\TextInput::make('specifications.os')
                                            ->label('অপারেটিং সিস্টেম (Operating System)')
                                            ->placeholder('e.g. Windows 11 Home / macOS / FreeDOS'),
                                        Forms\Components\TextInput::make('specifications.keyboard_ports')
                                            ->label('কিবোর্ড ও পোর্টসমূহ (Keyboard & Ports)')
                                            ->placeholder('e.g. Backlit Keyboard, USB-C, HDMI 2.1, Wi-Fi 6E'),
                                        Forms\Components\TagsInput::make('specifications.colors')
                                            ->label('কালার (Available Colors)')
                                            ->placeholder('Silver, Space Gray, Black...'),
                                    ]),

                                // C. Monitors & Smart TV (monitor_tv)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'monitor_tv')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.model_name')
                                            ->label('মডেল (Model Name)')
                                            ->placeholder('e.g. LG UltraGear 27GR75Q / Sony Bravia 55"'),
                                        Forms\Components\TextInput::make('specifications.screen_size')
                                            ->label('স্ক্রিন সাইজ (Screen Size)')
                                            ->placeholder('e.g. 24 Inch / 27 Inch / 43 Inch / 55 Inch'),
                                        Forms\Components\TextInput::make('specifications.resolution')
                                            ->label('রেজোলিউশন (Resolution)')
                                            ->placeholder('e.g. FHD (1920x1080) / QHD 2K / 4K UHD'),
                                        Forms\Components\Select::make('specifications.panel_type')
                                            ->label('প্যানেল টাইপ (Panel Type)')
                                            ->options([
                                                'IPS'  => 'IPS Panel',
                                                'VA'   => 'VA Panel',
                                                'OLED' => 'OLED / AMOLED',
                                                'QLED' => 'QLED',
                                                'LED'  => 'LED / TN',
                                            ])
                                            ->searchable(),
                                        Forms\Components\TextInput::make('specifications.refresh_rate')
                                            ->label('রিফ্রেশ রেট (Refresh Rate)')
                                            ->placeholder('e.g. 75Hz / 100Hz / 144Hz / 165Hz / 240Hz'),
                                        Forms\Components\TextInput::make('specifications.response_time')
                                            ->label('রেসপন্স টাইম (Response Time)')
                                            ->placeholder('e.g. 1ms (GtG) / 5ms'),
                                        Forms\Components\TextInput::make('specifications.ports')
                                            ->label('পোর্ট ও কানেক্টিভিটি (Ports & Connectivity)')
                                            ->placeholder('e.g. 2x HDMI, 1x DisplayPort, Audio Out, Wi-Fi'),
                                        Forms\Components\TextInput::make('specifications.special_feature')
                                            ->label('বিশেষ ফিচার (Smart / Gaming Features)')
                                            ->placeholder('e.g. AMD FreeSync, G-Sync, HDR10, Google TV'),
                                    ]),

                                // D. Audio, Watch, Camera & Gaming (audio_gadget)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'audio_gadget')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.model_name')
                                            ->label('মডেল (Model Name)')
                                            ->placeholder('e.g. AirPods Pro 2 / Apple Watch Series 9 / DJI Mini 4'),
                                        Forms\Components\TextInput::make('specifications.connectivity')
                                            ->label('কানেক্টিভিটি (Connectivity)')
                                            ->placeholder('e.g. Bluetooth 5.3 / 2.4GHz Wireless / USB Type-C'),
                                        Forms\Components\TextInput::make('specifications.battery')
                                            ->label('ব্যাটারি ব্যাকআপ (Battery Life / Capacity)')
                                            ->placeholder('e.g. 30 Hours Playtime / 7 Days Battery'),
                                        Forms\Components\TextInput::make('specifications.special_feature')
                                            ->label('মূল ফিচার (Main Feature)')
                                            ->placeholder('e.g. Active Noise Cancellation (ANC) / AMOLED / 4K 60fps'),
                                        Forms\Components\TextInput::make('specifications.water_resistance')
                                            ->label('ওয়াটারপ্রুফ রেটিং (Water Resistance)')
                                            ->placeholder('e.g. IPX4 / IP68 / 5ATM Waterproof'),
                                        Forms\Components\TextInput::make('specifications.compatibility')
                                            ->label('কম্প্যাটিবিলিটি (Supported Devices)')
                                            ->placeholder('e.g. Android, iOS, Windows, Mac, PS5'),
                                        Forms\Components\TagsInput::make('specifications.colors')
                                            ->label('কালারসমূহ (Available Colors)')
                                            ->placeholder('Black, White, Red...')->columnSpanFull(),
                                    ]),

                                // E. Fashion, Clothing & Shoes (fashion)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'fashion')
                                    ->schema([
                                        Forms\Components\TagsInput::make('specifications.sizes')
                                            ->label('উপলভ্য সাইজসমূহ (Available Sizes)')
                                            ->placeholder('সাইজ লিখে Enter চাপুন (যেমন: S, M, L, XL, XXL বা 40, 41, 42)')
                                            ->suggestions(['S', 'M', 'L', 'XL', 'XXL', 'Free Size', '38', '40', '42', '44']),
                                        Forms\Components\TagsInput::make('specifications.colors')
                                            ->label('উপলভ্য কালারসমূহ (Available Colors)')
                                            ->placeholder('কালার লিখে Enter চাপুন (যেমন: কালো, নীল, সাদা)'),
                                        Forms\Components\TextInput::make('specifications.fabric_material')
                                            ->label('কাপড় / ম্যাটেরিয়াল (Fabric / Material)')
                                            ->placeholder('যেমন: ১০০% সুতি (Cotton), ডেনিম, জেনুইন লেদার'),
                                        Forms\Components\Select::make('specifications.gender')
                                            ->label('কাদের জন্য (Gender / Category)')
                                            ->options([
                                                'Men'    => 'Men (পুরুষ)',
                                                'Women'  => 'Women (নারী)',
                                                'Unisex' => 'Unisex (সবার জন্য)',
                                                'Kids'   => 'Kids (শিশু)',
                                            ]),
                                        Forms\Components\TextInput::make('specifications.fit_type')
                                            ->label('ফিটিং / ধরন (Fit / Style)')
                                            ->placeholder('যেমন: Regular Fit, Slim Fit, Semi-Long Panjabi'),
                                        Forms\Components\TextInput::make('specifications.care_instructions')
                                            ->label('যত্ন ও ধোয়ার নিয়ম (Wash Care)')
                                            ->placeholder('যেমন: Hand Wash / Gentle Machine Wash'),
                                    ]),

                                // F. Books & Stationery (book)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'book')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.author')
                                            ->label('লেখক / অনুবাদক (Author)')
                                            ->placeholder('যেমন: ড. খোন্দকার আব্দুল্লাহ জাহাঙ্গীর'),
                                        Forms\Components\TextInput::make('specifications.publisher')
                                            ->label('প্রকাশনী (Publisher)')
                                            ->placeholder('যেমন: আস-সুন্নাহ পাবলিকেশন্স / সমকালীন'),
                                        Forms\Components\TextInput::make('specifications.edition')
                                            ->label('সংস্করণ (Edition)')
                                            ->placeholder('যেমন: ৩য় সংস্করণ, ২০২৬'),
                                        Forms\Components\TextInput::make('specifications.pages')
                                            ->label('পৃষ্ঠা সংখ্যা (Total Pages)')
                                            ->numeric()
                                            ->placeholder('যেমন: 320'),
                                        Forms\Components\Select::make('specifications.cover_type')
                                            ->label('বাঁধাইয়ের ধরন (Binding / Cover)')
                                            ->options([
                                                'Hardcover (হার্ডকভার)' => 'Hardcover (হার্ডকভার)',
                                                'Paperback (পেপারব্যাক)' => 'Paperback (পেপারব্যাক)',
                                            ]),
                                        Forms\Components\TextInput::make('specifications.language')
                                            ->label('ভাষা (Language)')
                                            ->default('বাংলা')
                                            ->placeholder('যেমন: বাংলা, আরবি, ইংরেজি'),
                                        Forms\Components\TextInput::make('specifications.isbn')
                                            ->label('ISBN / Book Code')
                                            ->placeholder('e.g. 978-984-...'),
                                    ]),

                                // G. Food, Grocery, Health & Beauty (grocery_beauty)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'grocery_beauty')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.weight_volume')
                                            ->label('ওজন বা পরিমাণ (Net Weight / Volume)')
                                            ->placeholder('যেমন: ৫০০ গ্রাম / ১ কেজি / ১০০ মিলি'),
                                        Forms\Components\TextInput::make('specifications.origin')
                                            ->label('উৎস / দেশ (Origin)')
                                            ->placeholder('যেমন: সুন্দরবন, বাংলাদেশ / সৌদি আরব'),
                                        Forms\Components\TextInput::make('specifications.ingredients')
                                            ->label('উপাদানসমূহ (Ingredients / Key Elements)')
                                            ->placeholder('যেমন: ১০০% খাঁটি প্রাকৃতিক উপাদান'),
                                        Forms\Components\TextInput::make('specifications.certification')
                                            ->label('মান ও সার্টিফিকেশন (Quality / Type)')
                                            ->placeholder('যেমন: BSTI অনুমোদিত / ১০০% অর্গানিক ও হালাল'),
                                        Forms\Components\DatePicker::make('specifications.mfg_date')
                                            ->label('উৎপাদন তারিখ (Mfg Date)'),
                                        Forms\Components\DatePicker::make('specifications.expiry_date')
                                            ->label('মেয়াদ উত্তীর্ণের তারিখ (Expiry Date)'),
                                    ]),

                                // H. Home & Kitchen Appliances (appliance)
                                Forms\Components\Grid::make(2)
                                    ->visible(fn (Get $get): bool => $get('product_type') === 'appliance')
                                    ->schema([
                                        Forms\Components\TextInput::make('specifications.model_name')
                                            ->label('মডেল (Model Name)')
                                            ->placeholder('e.g. Philips HD9252 Air Fryer'),
                                        Forms\Components\TextInput::make('specifications.capacity')
                                            ->label('ক্যাপাসিটি / ধারণক্ষমতা (Capacity)')
                                            ->placeholder('e.g. 2.8 Liter / 4.1 Liter / 1.5 Ton'),
                                        Forms\Components\TextInput::make('specifications.power_wattage')
                                            ->label('পাওয়ার ও ভোল্টেজ (Power / Wattage)')
                                            ->placeholder('e.g. 1400W, 220V-240V'),
                                        Forms\Components\TextInput::make('specifications.material')
                                            ->label('বডি ম্যাটেরিয়াল (Material)')
                                            ->placeholder('e.g. Stainless Steel / Non-Stick Coating'),
                                    ]),

                                // Custom Key-Value Specifications (Available for ALL Product Types)
                                Forms\Components\KeyValue::make('specifications.custom')
                                    ->label('অতিরিক্ত কাস্টম স্পেসিফিকেশন (Custom Specifications - ঐচ্ছিক)')
                                    ->keyLabel('বৈশিষ্ট্যের নাম (Specification Name)')
                                    ->valueLabel('বিবরণ / মান (Value)')
                                    ->addActionLabel('+ নতুন বৈশিষ্ট্য যোগ করুন')
                                    ->helperText('উপরের ফিল্ডগুলোর বাইরে আরও কোনো স্পেসিফিকেশন দিতে চাইলে এখানে যোগ করুন')
                                    ->columnSpanFull(),
                            ]),

                        // 3. Product Variants Repeater (Color / Size / Storage Variations)
                        Forms\Components\Section::make('প্রোডাক্ট ভ্যারিয়েন্ট (Product Variants: কালার / সাইজ / স্টোরেজ অপশন)')
                            ->description('একই পণ্যের যদি একাধিক কালার, সাইজ বা স্টোরেজ ভ্যারিয়েন্ট থাকে (যেমন: 8GB/128GB বনাম 8GB/256GB অথবা সাইজ M, L, XL), তবে নিচে যোগ করুন।')
                            ->collapsed(fn (?Product $record) => empty($record?->variants))
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->label('ভ্যারিয়েন্ট তালিকা (Variants List)')
                                    ->addActionLabel('+ নতুন ভ্যারিয়েন্ট যোগ করুন (Add Variant)')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('ভ্যারিয়েন্ট নাম (Variant Title)')
                                            ->placeholder('যেমন: 8GB/256GB - Titanium Black বা Size: XL')
                                            ->required(),
                                        Forms\Components\TextInput::make('color')
                                            ->label('কালার (Color)')
                                            ->placeholder('যেমন: Black / Blue'),
                                        Forms\Components\TextInput::make('size_or_storage')
                                            ->label('সাইজ / স্টোরেজ / ওজন')
                                            ->placeholder('যেমন: 8GB/256GB, XL, বা 1 KG'),
                                        Forms\Components\TextInput::make('price')
                                            ->label('ভ্যারিয়েন্ট বিক্রয়মূল্য (৳)')
                                            ->numeric()
                                            ->prefix('৳')
                                            ->placeholder('খালি রাখলে মূল দাম প্রযোজ্য হবে'),
                                        Forms\Components\TextInput::make('stock')
                                            ->label('ভ্যারিয়েন্ট স্টক')
                                            ->numeric()
                                            ->default(10)
                                            ->required(),
                                        Forms\Components\TextInput::make('sku')
                                            ->label('Variant SKU')
                                            ->placeholder('e.g. AQ-001-BLK'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
                            ]),

                        // 4. Pricing & Financials
                        Forms\Components\Section::make('মূল্য ও লাভ হিসাব (Pricing & Financials)')
                            ->schema([
                                Forms\Components\TextInput::make('cost_price')
                                    ->label('Purchase / Cost Price (৳)')
                                    ->numeric()
                                    ->helperText('ক্রয়মূল্য (লাভ বা প্রফিট হিসাব করতে ব্যবহৃত হবে)')
                                    ->prefix('৳'),
                                Forms\Components\TextInput::make('price')
                                    ->label('Regular Selling Price (৳)')
                                    ->required()
                                    ->numeric()
                                    ->prefix('৳'),
                                Forms\Components\TextInput::make('sale_price')
                                    ->label('Discount / Sale Price (৳)')
                                    ->numeric()
                                    ->helperText('অফার বা ছাড় মূল্য')
                                    ->prefix('৳'),
                            ])->columns(3),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        // 5. Inventory & Stock
                        Forms\Components\Section::make('ইনভেন্টরি ও স্টক (Inventory & Stock)')
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU (Stock Keeping Unit)')
                                    ->placeholder('e.g. AQ-001')
                                    ->unique(Product::class, 'sku', ignoreRecord: true)
                                    ->maxLength(100),
                                Forms\Components\Select::make('unit')
                                    ->label('একক (Unit)')
                                    ->options([
                                        'pcs'  => 'Pcs (টি)',
                                        'box'  => 'Box (বক্স)',
                                        'set'  => 'Set (সেট)',
                                        'pair' => 'Pair (জোড়া)',
                                        'kg'   => 'KG (কেজি)',
                                        'gm'   => 'Gram (গ্রাম)',
                                        'ltr'  => 'Liter (লিটার)',
                                        'ml'   => 'ML (মিলি)',
                                    ])
                                    ->default('pcs'),
                                Forms\Components\TextInput::make('stock')
                                    ->label('Current Stock Quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('low_stock_threshold')
                                    ->label('Low Stock Alert Level')
                                    ->numeric()
                                    ->default(5)
                                    ->helperText('এই পরিমাণের নিচে নামলে অ্যালার্ট দেখাবে'),
                            ]),

                        // 6. Media & Status
                        Forms\Components\Section::make('মিডিয়া ও স্ট্যাটাস (Media & Status)')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('মূল ছবি (Main Thumbnail Image)')
                                    ->image()
                                    ->directory('products'),
                                Forms\Components\FileUpload::make('gallery')
                                    ->label('গ্যালারি ছবিসমূহ (Additional Gallery Images)')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->directory('products/gallery')
                                    ->helperText('একাধিক অ্যাঙ্গেলের ছবি আপলোড করতে পারেন'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Publish / Active')
                                    ->default(true),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record): string => $record->brand ? "Brand: {$record->brand}" : ''),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('product_type')
                    ->label('Variety')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'phone_tablet'   => 'Phone/Tablet',
                        'laptop_pc'      => 'Laptop/PC',
                        'monitor_tv'     => 'Monitor/TV',
                        'audio_gadget'   => 'Audio/Gadget',
                        'fashion'        => 'Fashion',
                        'book'           => 'Book',
                        'grocery_beauty' => 'Grocery/Beauty',
                        'appliance'      => 'Appliance',
                        default          => 'General',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Cost')
                    ->money('BDT')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Sale Price')
                    ->money('BDT')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (Product $record): string => match (true) {
                        $record->stock <= 0 => 'danger',
                        $record->stock <= $record->low_stock_threshold => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('product_type')
                    ->label('Variety Type')
                    ->options(Category::varietyOptions()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Products')
                    ->query(fn ($query) => $query->whereColumn('stock', '<=', 'low_stock_threshold')),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
