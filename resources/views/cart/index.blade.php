@extends('layouts.app')
@section('title', 'আমার কার্ট')

@section('content')
<div class="mb-10">
    <h1 class="text-4xl font-black text-gray-900 flex items-center gap-4">
        <span class="w-12 h-12 bg-pink-100 rounded-2xl flex items-center justify-center text-2xl">🛒</span>
        আমার কার্ট
    </h1>
    <p class="text-gray-500 mt-2">আপনার পছন্দের পণ্যগুলো এখানে রয়েছে।</p>
</div>

@if($items->isEmpty())
    <div class="text-center py-24 bg-white rounded-[3rem] shadow-sm border border-gray-100">
        <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8">
            <span class="text-6xl opacity-30">🛒</span>
        </div>
        <h2 class="text-2xl font-black text-gray-800 mb-2">আপনার কার্ট এখন খালি!</h2>
        <p class="text-gray-500 mb-10">পছন্দের কিছু পণ্য খুঁজে নিতে শপিং শুরু করুন।</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-pink-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-pink-700 shadow-xl shadow-pink-200 transition-all duration-300 hover:-translate-y-1">
            পণ্য দেখুন →
        </a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- Cart items --}}
        <div class="lg:col-span-2 space-y-6">
            @foreach($items as $item)
            <div class="group bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row gap-6 items-center hover:shadow-xl hover:shadow-gray-100 transition-all duration-500">
                <div class="relative overflow-hidden rounded-2xl w-28 h-28 flex-shrink-0">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                </div>
                
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-black text-gray-900 mb-1 group-hover:text-pink-600 transition">{{ $item['name'] }}</h3>
                    <p class="text-indigo-600 font-black text-xl">৳{{ number_format($item['price'], 0) }}</p>
                </div>

                {{-- Quantity update --}}
                <div class="flex items-center gap-4 bg-gray-50 p-2 rounded-2xl border border-gray-100">
                    <form action="{{ route('cart.update', $item['id']) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}"
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all font-bold text-lg">−</button>
                    </form>
                    
                    <span class="w-8 text-center font-black text-gray-900 text-lg">{{ $item['quantity'] }}</span>
                    
                    <form action="{{ route('cart.update', $item['id']) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}"
                            class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all font-bold text-lg">+</button>
                    </form>
                </div>

                <div class="text-center sm:text-right w-32">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-1">মোট</p>
                    <p class="text-xl font-black text-gray-900 tracking-tighter">৳{{ number_format($item['subtotal'], 0) }}</p>
                </div>

                <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="w-10 h-10 flex items-center justify-center text-red-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
            @endforeach

            {{-- Clear cart --}}
            <div class="flex justify-between items-center px-4">
                <a href="{{ route('products.index') }}" class="text-sm font-bold text-gray-500 hover:text-pink-600 transition flex items-center gap-2">
                    <span>←</span> কেনাকাটা চালিয়ে যান
                </a>
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-sm font-bold text-red-400 hover:text-red-600 hover:underline">কার্ট খালি করুন</button>
                </form>
            </div>
        </div>

        {{-- Order summary --}}
        <div class="lg:col-span-1">
            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl sticky top-24">
                <h2 class="text-2xl font-black mb-8 flex items-center gap-3">
                    অর্ডার সারসংক্ষেপ
                </h2>
                <div class="space-y-6 mb-8 text-sm opacity-80">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">সাবটোটাল</span>
                        <span class="font-bold text-lg">৳{{ number_format($subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium">ডেলিভারি চার্জ</span>
                        <span class="font-bold text-lg {{ $shipping == 0 ? 'text-green-400' : '' }}">
                            {{ $shipping == 0 ? 'ফ্রি' : '৳'.$shipping }}
                        </span>
                    </div>
                    @if($shipping > 0)
                        <div class="p-3 bg-white/5 rounded-xl border border-white/10 text-xs flex items-center gap-2">
                            <span class="text-amber-400">💡</span> ৳১০০০+ অর্ডারে ফ্রি ডেলিভারি পাবেন!
                        </div>
                    @endif
                    <div class="pt-6 border-t border-white/10 flex justify-between items-end">
                        <span class="font-bold text-lg uppercase tracking-wider">মোট পরিশোধ</span>
                        <span class="text-4xl font-black text-pink-500 tracking-tighter">৳{{ number_format($total, 0) }}</span>
                    </div>
                </div>
                
                <a href="{{ route('checkout.index') }}"
                   class="block w-full bg-pink-600 hover:bg-pink-700 text-white text-center py-5 rounded-[1.5rem] font-black text-lg shadow-xl shadow-pink-900/20 transition-all duration-300 hover:-translate-y-1 active:scale-95">
                    চেকআউট করুন →
                </a>
                
                <div class="mt-8 flex items-center justify-center gap-4 opacity-30">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4">
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
