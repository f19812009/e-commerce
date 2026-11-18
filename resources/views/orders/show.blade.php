@extends('layouts.app')
@section('title', 'অর্ডার বিস্তারিত - #' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between" data-aos="fade-down">
        <div>
            <a href="{{ route('orders.index') }}" class="text-gray-400 font-bold hover:text-pink-600 transition flex items-center gap-2 mb-4">
                ← অর্ডার লিস্টে ফিরে যান
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">অর্ডার বিবরণ <span class="text-pink-600">#{{ $order->order_number }}</span></h1>
        </div>
        <div class="text-right">
            <span class="inline-block px-5 py-2 rounded-2xl text-xs font-black uppercase tracking-[0.2em] bg-{{ $order->status_badge }}-100 text-{{ $order->status_badge }}-700 shadow-sm mb-2">
                {{ $order->status }}
            </span>
            <p class="text-sm text-gray-400 font-medium">{{ $order->created_at->format('d F, Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-6" data-aos="fade-right">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8">
                <h2 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                    <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                    অর্ডার আইটেমসমূহ
                </h2>
                <div class="divide-y divide-gray-50">
                    @foreach($order->items as $item)
                    <div class="py-6 first:pt-0 last:pb-0 flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-24 rounded-2xl bg-gray-50 overflow-hidden shadow-sm">
                                <img src="{{ $item->product->image_url ?? 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&q=80' }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 text-lg mb-1">{{ $item->product_name }}</h3>
                                <p class="text-sm text-gray-400 font-medium">পরিমাণ: <span class="text-gray-900">{{ $item->quantity }}</span> × ৳{{ number_format($item->price, 0) }}</p>
                            </div>
                        </div>
                        <p class="text-xl font-black text-gray-900 leading-none">৳{{ number_format($item->subtotal, 0) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white">
                <h2 class="text-xl font-black mb-8 flex items-center gap-3">
                    <span class="w-2 h-6 bg-pink-500 rounded-full"></span>
                    ডেলিভারি তথ্য
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-4">শিপিং অ্যাড্রেস</p>
                        <div class="space-y-2 opacity-90 font-medium">
                            <p class="text-lg font-black text-white">{{ $order->customer_name }}</p>
                            <p>{{ $order->shipping_address }}</p>
                            <p>{{ $order->city }} - {{ $order->postal_code }}</p>
                            <p>{{ $order->country ?? 'Bangladesh' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mb-4">যোগাযোগ</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">📞</span>
                                <p class="font-bold">{{ $order->customer_phone }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">📧</span>
                                <p class="font-bold">{{ $order->customer_email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-1 space-y-6" data-aos="fade-left">
            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 sticky top-24">
                <h2 class="text-xl font-black text-gray-900 mb-8 flex items-center gap-3">
                    <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                    অর্ডার সামারি
                </h2>
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-gray-500 font-medium">
                        <span>আইটেম টোটাল</span>
                        <span>৳{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 font-medium">
                        <span>শিপিং খরচ</span>
                        <span>৳{{ number_format($order->shipping, 0) }}</span>
                    </div>
                    <hr class="border-gray-50">
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-lg font-black text-gray-900">সর্বমোট</span>
                        <span class="text-3xl font-black text-indigo-600 tracking-tighter">৳{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">পেমেন্ট মেথড</p>
                        <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">পেমেন্ট স্ট্যাটাস</p>
                        <span class="text-sm font-black {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $order->payment_status === 'paid' ? 'পরিশোধিত' : 'বকেয়া' }}
                        </span>
                    </div>
                </div>
                
                <button onclick="window.print()" class="w-full mt-8 border-2 border-gray-100 py-4 rounded-2xl font-black hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    🖨️ ইনভয়েস প্রিন্ট করুন
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
