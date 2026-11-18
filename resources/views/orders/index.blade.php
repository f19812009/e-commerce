@extends('layouts.app')
@section('title', 'আমার অর্ডারসমূহ')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-10" data-aos="fade-down">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">আমার অর্ডারসমূহ</h1>
            <p class="text-gray-500 mt-1">আপনার করা সব অর্ডার এখান থেকে ট্র্যাক করুন।</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-pink-600 font-bold hover:underline flex items-center gap-2">
            ← কেনাকাটা চালিয়ে যান
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-[2.5rem] p-16 text-center border border-gray-100 shadow-sm" data-aos="fade-up">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-5xl opacity-40">📦</div>
            <h2 class="text-2xl font-black text-gray-800 mb-2">আপনি এখনো কোনো অর্ডার করেননি!</h2>
            <p class="text-gray-500 mb-8">সেরা সব অফারে কেনাকাটা করতে আজই আমাদের স্টোর ভিজিট করুন।</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-pink-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-pink-200 hover:-translate-y-1 transition">শপিং শুরু করুন</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition duration-500" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-3xl">📦</div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-lg font-black text-gray-900 uppercase">#{{ $order->order_number }}</h3>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $order->status_badge }}-100 text-{{ $order->status_badge }}-700">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">{{ $order->created_at->format('d M, Y • h:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-12 text-center md:text-left">
                        <div class="hidden sm:block">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">পেমেন্ট স্ট্যাটাস</p>
                            <span class="text-sm font-bold {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $order->payment_status === 'paid' ? 'পরিশোধিত' : 'বকেয়া' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">মোট টাকা</p>
                            <p class="text-xl font-black text-indigo-600 leading-none">৳{{ number_format($order->total, 0) }}</p>
                        </div>
                        <a href="{{ route('orders.show', $order) }}" class="bg-gray-900 text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-pink-600 transition shadow-lg">বিস্তারিত</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
