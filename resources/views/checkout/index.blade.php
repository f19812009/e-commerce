@extends('layouts.app')
@section('title', 'চেকআউট')

@section('content')
<h1 class="text-2xl font-bold mb-6">💳 চেকআউট</h1>

<form action="{{ route('checkout.place') }}" method="POST">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Delivery & Payment --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Delivery info --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold text-lg mb-4">ডেলিভারি তথ্য</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">আপনার নাম *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('customer_name') border-red-400 @enderror"
                        placeholder="পূর্ণ নাম লিখুন" required>
                    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">ইমেইল *</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('customer_email') border-red-400 @enderror"
                        placeholder="email@example.com" required>
                    @error('customer_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">ফোন নম্বর *</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="01700-000000" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">শহর *</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="ঢাকা / চট্টগ্রাম..." required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">পূর্ণ ঠিকানা *</label>
                    <textarea name="shipping_address" rows="2" required
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="বাড়ি নম্বর, রোড, এলাকা...">{{ old('shipping_address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">পোস্টাল কোড</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="1000">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">অর্ডার নোট</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="বিশেষ নির্দেশনা...">
                </div>
            </div>
        </div>

        {{-- Payment method --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-bold text-lg mb-4">পেমেন্ট পদ্ধতি</h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach(['cod' => '💵 ক্যাশ অন ডেলিভারি', 'bkash' => '📱 বিকাশ', 'nagad' => '📱 নগদ'] as $val => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="payment_method" value="{{ $val }}"
                           {{ old('payment_method', 'cod') === $val ? 'checked' : '' }}
                           class="peer hidden">
                    <div class="border-2 rounded-xl p-3 text-center text-sm peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition">
                        {{ $label }}
                    </div>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Order summary --}}
    <div class="bg-white rounded-xl shadow-sm p-6 h-fit sticky top-20">
        <h2 class="font-bold text-lg mb-4">আপনার অর্ডার</h2>
        <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-1">
            @foreach($items as $item)
            <div class="flex items-center gap-3">
                <img src="{{ $item['image'] }}" class="w-12 h-12 object-cover rounded-lg">
                <div class="flex-1">
                    <p class="text-sm font-medium line-clamp-1">{{ $item['name'] }}</p>
                    <p class="text-xs text-gray-500">{{ $item['quantity'] }}x ৳{{ number_format($item['price'], 0) }}</p>
                </div>
                <p class="text-sm font-bold">৳{{ number_format($item['subtotal'], 0) }}</p>
            </div>
            @endforeach
        </div>
        <hr class="mb-3">
        <div class="space-y-2 text-sm mb-4">
            <div class="flex justify-between"><span class="text-gray-500">সাবটোটাল</span><span>৳{{ number_format($subtotal, 0) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">ডেলিভারি</span><span class="{{ $shipping == 0 ? 'text-green-600' : '' }}">{{ $shipping == 0 ? 'ফ্রি' : '৳'.$shipping }}</span></div>
            <hr>
            <div class="flex justify-between font-bold text-base">
                <span>মোট</span>
                <span class="text-indigo-600">৳{{ number_format($total, 0) }}</span>
            </div>
        </div>
        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">
            অর্ডার দিন ✓
        </button>
    </div>

</div>
</form>
@endsection
