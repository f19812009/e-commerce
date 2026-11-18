@extends('layouts.app')
@section('title', 'সব পণ্য')

@section('content')
{{-- Hero Section --}}
@if(!request('search') && !request('category'))
<div class="mb-12 relative overflow-hidden rounded-[3rem] hero-gradient shadow-2xl group min-h-[350px] flex items-center">
    {{-- Decorative Shapes --}}
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>

    <div class="relative z-10 px-8 md:px-16 text-white max-w-2xl py-10">
        <span class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-6 shadow-lg">নতুন কালেকশন ২০২৪</span>
        <h1 class="text-4xl md:text-6xl font-black mb-6 leading-tight tracking-tighter">সেরা পণ্যে সেরা অফার — <br><span class="text-pink-200">সবচেয়ে কম দামে!</span></h1>
        <p class="text-lg opacity-90 mb-10 leading-relaxed font-medium">ShopBD-তে পাচ্ছেন ১০০০টিরও বেশি প্রিমিয়াম প্রোডাক্ট। আজই অর্ডার করুন এবং উপভোগ করুন ফ্রি ডেলিভারি।</p>
        <div class="flex flex-wrap gap-4">
            <a href="#products" class="bg-white text-pink-600 px-8 py-4 rounded-2xl font-black hover:shadow-xl transition-all duration-300 hover:-translate-y-1">শপিং শুরু করুন</a>
            <a href="#" class="bg-black/20 backdrop-blur-md border border-white/30 px-8 py-4 rounded-2xl font-black hover:bg-white/10 transition">অফারগুলো দেখুন</a>
        </div>
    </div>
    
    <div class="absolute right-12 top-1/2 -translate-y-1/2 hidden lg:block opacity-20 pointer-events-none">
        <span class="text-[18rem] select-none">🛍️</span>
    </div>
</div>
@endif

<div class="flex flex-col md:flex-row gap-8" id="products">

    {{-- Sidebar: Categories --}}
    <aside class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-24">
            <h2 class="font-black text-gray-900 text-xl mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-pink-500 rounded-full"></span>
                ক্যাটাগরি
            </h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('products.index', request()->except('category')) }}"
                       class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 {{ !request('category') ? 'bg-pink-500 text-white shadow-lg shadow-pink-200 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span>সব পণ্য</span>
                        @if(!request('category')) <span class="text-xl">✨</span> @endif
                    </a>
                </li>
                @foreach($categories as $cat)
                <li>
                    <a href="{{ route('products.index', array_merge(request()->query(), ['category' => $cat->slug])) }}"
                       class="flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 {{ request('category') === $cat->slug ? 'bg-pink-600 text-white shadow-lg shadow-pink-200 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span>{{ $cat->name }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
            
            <div class="mt-10 p-5 bg-amber-50 rounded-2xl border border-amber-100">
                <p class="text-amber-800 text-xs font-bold uppercase mb-2">প্রোমো অফার</p>
                <p class="text-amber-900 text-sm font-medium mb-3">৳১০০০+ অর্ডারে পাচ্ছেন ফ্রি ডেলিভারি!</p>
                <div class="w-full h-1.5 bg-amber-200 rounded-full overflow-hidden">
                    <div class="w-2/3 h-full bg-amber-500"></div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1">
        {{-- Top bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900">
                    @if(request('search')) "<span>{{ request('search') }}</span>" এর ফলাফল
                    @elseif(request('category')) {{ $categories->where('slug', request('category'))->first()->name ?? '' }}
                    @else সকল পণ্য @endif
                </h2>
                <p class="text-sm text-gray-500">{{ $products->total() }} টি পণ্য পাওয়া গেছে</p>
            </div>
            <form method="GET" class="flex items-center gap-3">
                @foreach(request()->except('sort') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">সর্ট করুন:</span>
                <select name="sort" onchange="this.form.submit()"
                    class="bg-white border-none shadow-sm rounded-xl px-4 py-2.5 text-sm font-bold focus:ring-2 focus:ring-pink-400 cursor-pointer">
                    <option value="latest"     {{ request('sort') === 'latest'     ? 'selected' : '' }}>নতুন আগে</option>
                    <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>কম দামে</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>বেশি দামে</option>
                    <option value="name"       {{ request('sort') === 'name'       ? 'selected' : '' }}>নাম অনুযায়ী</option>
                </select>
            </form>
        </div>

        {{-- Product grid --}}
        @if($products->isEmpty())
            <div class="bg-white rounded-[2.5rem] p-20 text-center shadow-sm border border-gray-100">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl opacity-50">🔍</span>
                </div>
                <h3 class="text-2xl font-black text-gray-800 mb-2">দুঃখিত!</h3>
                <p class="text-gray-500">আপনার খোঁজা পণ্যটি আমাদের স্টকে পাওয়া যায়নি।</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-8 text-pink-600 font-bold hover:underline">সকল পণ্য দেখুন →</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($products as $product)
                <div data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}" class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-pink-100/50 transition-all duration-700 overflow-hidden flex flex-col h-full relative">
                    {{-- Decorative Glow --}}
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-pink-500/5 rounded-full blur-3xl group-hover:bg-pink-500/10 transition-all duration-700"></div>

                    <div class="relative overflow-hidden aspect-[4/5] m-3 rounded-[2rem]">
                        {{-- Image with Gradient Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 z-10"></div>
                        
                        <a href="{{ route('products.show', $product) }}" class="block w-full h-full">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-[1.5s] ease-out">
                        </a>
                        
                        {{-- Badges --}}
                        <div class="absolute top-4 left-4 flex flex-col gap-2 z-20">
                            @if($product->is_on_sale)
                                <div class="bg-rose-500 text-white text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-tighter shadow-xl shadow-rose-200 animate-pulse">
                                    {{ $product->discount_percent }}% ছাড়
                                </div>
                            @endif
                            @if($product->created_at > now()->subDays(7))
                                <div class="bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-tighter shadow-xl shadow-indigo-200">নতুন</div>
                            @endif
                        </div>

                        {{-- Floating Actions --}}
                        <div class="absolute -bottom-20 left-4 right-4 transition-all duration-500 group-hover:bottom-4 z-20">
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <button class="w-full bg-white text-gray-900 font-black py-4 rounded-2xl shadow-2xl hover:bg-gray-900 hover:text-white transition-all duration-300 transform active:scale-95 flex items-center justify-center gap-2">
                                    <span>কার্টে যোগ করুন</span>
                                    <span class="text-xl">🛒</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-8 pt-2 flex flex-col flex-1 relative z-10">
                        @php
                            $catColors = [
                                'fashion' => 'pink',
                                'gadgets' => 'blue',
                                'books' => 'amber',
                                'beauty' => 'rose',
                                'home-appliances' => 'emerald',
                                'health' => 'cyan',
                            ];
                            $color = $catColors[$product->category->slug ?? ''] ?? 'gray';
                        @endphp
                        
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-{{ $color }}-100/50">
                                {{ $product->category->name ?? 'অন্যান্য' }}
                            </span>
                        </div>

                        <a href="{{ route('products.show', $product) }}"
                           class="text-xl font-black text-gray-900 group-hover:text-pink-600 transition-colors duration-300 line-clamp-1 mb-2">
                            {{ $product->name }}
                        </a>
                        
                        <p class="text-sm text-gray-400 line-clamp-2 mb-8 leading-relaxed font-medium">
                            {{ $product->description }}
                        </p>
                        
                        <div class="mt-auto flex items-end justify-between">
                            <div class="flex flex-col">
                                @if($product->is_on_sale)
                                    <span class="text-xs text-gray-300 line-through mb-1">৳{{ number_format($product->price, 0) }}</span>
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-black text-pink-600 tracking-tighter">৳{{ number_format($product->effective_price, 0) }}</span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase">/-</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex text-amber-400 text-sm">
                                    ★★★★☆
                                </div>
                                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">(১২ রিভিউ)</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-16">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
