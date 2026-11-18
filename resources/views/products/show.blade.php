@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Breadcrumbs --}}
    <nav class="flex mb-8 text-sm font-bold uppercase tracking-widest" data-aos="fade-down">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-pink-600 transition">হোম</a>
        <span class="mx-3 text-gray-300">/</span>
        <a href="{{ route('products.index', ['category' => $product->category->slug ?? '']) }}" class="text-gray-400 hover:text-pink-600 transition">{{ $product->category->name ?? 'অন্যান্য' }}</a>
        <span class="mx-3 text-gray-300">/</span>
        <span class="text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">
        {{-- Product Images --}}
        <div class="space-y-6" data-aos="fade-right">
            <div class="bg-white rounded-[3rem] p-4 border border-gray-100 shadow-sm overflow-hidden group">
                <div class="relative aspect-square rounded-[2.5rem] overflow-hidden bg-gray-50">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-1000">
                    
                    @if($product->is_on_sale)
                        <div class="absolute top-6 left-6 bg-rose-500 text-white text-xs font-black px-4 py-2 rounded-xl shadow-xl shadow-rose-200 uppercase tracking-widest">
                            {{ $product->discount_percent }}% অফ
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-4 gap-4">
                @for($i=0; $i<4; $i++)
                <div class="aspect-square rounded-2xl border-2 border-gray-100 overflow-hidden cursor-pointer hover:border-pink-500 transition shadow-sm">
                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover opacity-60 hover:opacity-100 transition">
                </div>
                @endfor
            </div>
        </div>

        {{-- Product Info --}}
        <div class="space-y-10" data-aos="fade-left">
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <span class="px-4 py-1.5 bg-pink-50 text-pink-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-pink-100">
                        {{ $product->category->name ?? 'অন্যান্য' }}
                    </span>
                    <div class="flex text-amber-400 text-sm">★★★★☆ <span class="text-gray-400 text-xs ml-2 font-bold">(১২ কাস্টমার রিভিউ)</span></div>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-6 leading-tight tracking-tighter">
                    {{ $product->name }}
                </h1>
                
                <div class="flex items-baseline gap-4 mb-8">
                    <span class="text-4xl font-black text-pink-600 tracking-tighter">৳{{ number_format($product->effective_price, 0) }}</span>
                    @if($product->is_on_sale)
                        <span class="text-xl text-gray-300 line-through font-bold">৳{{ number_format($product->price, 0) }}</span>
                    @endif
                </div>

                <p class="text-gray-500 text-lg leading-relaxed font-medium">
                    {{ $product->description }}
                </p>
            </div>

            <div class="space-y-6 pt-6 border-t border-gray-100">
                <div class="flex items-center gap-6">
                    <div class="flex items-center bg-gray-50 rounded-2xl p-1 border border-gray-100">
                        <button onclick="updateQty(-1)" class="w-12 h-12 flex items-center justify-center font-black text-xl hover:bg-white hover:shadow-sm rounded-xl transition">-</button>
                        <input type="number" id="qty" value="1" min="1" class="w-16 bg-transparent border-none text-center font-black text-lg focus:ring-0">
                        <button onclick="updateQty(1)" class="w-12 h-12 flex items-center justify-center font-black text-xl hover:bg-white hover:shadow-sm rounded-xl transition">+</button>
                    </div>
                    <p class="text-sm font-black {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $product->stock > 0 ? 'স্টকে আছে (' . $product->stock . ' টি)' : 'আউট অফ স্টক' }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="quantity" id="form-qty" value="1">
                        <button class="w-full bg-gray-900 text-white py-5 rounded-[1.5rem] font-black text-lg shadow-2xl shadow-gray-200 hover:bg-pink-600 hover:-translate-y-1 transition duration-300 flex items-center justify-center gap-3">
                            কার্টে যোগ করুন 🛒
                        </button>
                    </form>
                    <button class="w-full sm:w-auto bg-white border-2 border-gray-100 px-8 py-5 rounded-[1.5rem] font-black hover:bg-gray-50 transition shadow-sm">
                        উইশলিস্ট ❤️
                    </button>
                </div>
            </div>

            {{-- Product Features --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-5 bg-pink-50/50 rounded-3xl border border-pink-100/50 flex items-center gap-4">
                    <span class="text-3xl">🚚</span>
                    <div>
                        <p class="text-xs font-black text-pink-900 uppercase">ফ্রি ডেলিভারি</p>
                        <p class="text-[10px] text-pink-600 font-bold uppercase mt-1">৳১০০০+ কেনাকাটায়</p>
                    </div>
                </div>
                <div class="p-5 bg-pink-50/50 rounded-3xl border border-pink-100/50 flex items-center gap-4">
                    <span class="text-3xl">🛡️</span>
                    <div>
                        <p class="text-xs font-black text-pink-900 uppercase">৭ দিনের গ্যারান্টি</p>
                        <p class="text-[10px] text-pink-600 font-bold uppercase mt-1">সহজ রিটার্ন পলিসি</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs / Extra Info --}}
    <div class="mt-24 border-t border-gray-100 pt-16">
        <div class="flex flex-col md:flex-row gap-12">
            <div class="md:w-1/3">
                <h2 class="text-3xl font-black text-gray-900 mb-6 tracking-tight">পণ্যটির বৈশিষ্ট্যসমূহ</h2>
                <p class="text-gray-500 font-medium leading-relaxed">এই পণ্যটি অত্যন্ত গুণগত মানসম্পন্ন উপাদানে তৈরি। আমরা কাস্টমারদের সর্বোচ্চ সন্তুষ্টি নিশ্চিত করতে প্রতিশ্রুতিবদ্ধ।</p>
            </div>
            <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">পণ্যের কোড</p>
                    <p class="text-lg font-bold text-gray-900 uppercase">BD-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">ম্যাটেরিয়াল</p>
                    <p class="text-lg font-bold text-gray-900">উন্নত মানের টেকসই উপাদান</p>
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">ডেলিভারি সময়</p>
                    <p class="text-lg font-bold text-gray-900">২-৩ কর্মদিবস (সারাদেশে)</p>
                </div>
                <div class="space-y-2">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">পেমেন্ট অপশন</p>
                    <p class="text-lg font-bold text-gray-900">ক্যাশ অন ডেলিভারি, বিকাশ, নগদ</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateQty(val) {
        let qty = document.getElementById('qty');
        let formQty = document.getElementById('form-qty');
        let newVal = parseInt(qty.value) + val;
        if (newVal >= 1) {
            qty.value = newVal;
            formQty.value = newVal;
        }
    }
</script>
@endsection
