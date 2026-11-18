@extends('layouts.app')
@section('title', 'অর্ডার সফল')

@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="text-7xl mb-6">🎉</div>
    <h1 class="text-3xl font-bold text-green-600 mb-2">অর্ডার সফলভাবে দেওয়া হয়েছে!</h1>
    <p class="text-gray-500 mb-6">আপনার অর্ডার নম্বর: <strong class="text-gray-800">{{ $order->order_number }}</strong></p>

    <div class="bg-white rounded-xl shadow-sm p-6 text-left mb-6">
        <h2 class="font-bold mb-3">অর্ডার সারসংক্ষেপ</h2>
        @foreach($order->items as $item)
        <div class="flex justify-between text-sm py-1.5 border-b last:border-0">
            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
            <span>৳{{ number_format($item->subtotal, 0) }}</span>
        </div>
        @endforeach
        <div class="flex justify-between font-bold mt-3">
            <span>মোট</span>
            <span class="text-indigo-600">৳{{ number_format($order->total, 0) }}</span>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800 mb-8">
        📧 আপনার ইমেইলে (<strong>{{ $order->customer_email }}</strong>) অর্ডার নিশ্চিতকরণ পাঠানো হবে।
    </div>

    <div class="flex gap-3 justify-center">
        <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700">
            কেনাকাটা চালিয়ে যান
        </a>
        @auth
        <a href="{{ route('orders.index') }}" class="border border-gray-300 px-6 py-2.5 rounded-lg hover:bg-gray-50">
            আমার অর্ডার
        </a>
        @endauth
    </div>
</div>
@endsection
