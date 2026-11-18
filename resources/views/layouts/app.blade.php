<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopBD') — আপনার অনলাইন শপ</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .gradient-text { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); }
    </style>
    
    {{-- AOS Animation Library --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-gray-800">

{{-- Navbar --}}
<nav class="glass sticky top-0 z-50 border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="text-3xl">🛍️</span>
                <span class="text-2xl font-black gradient-text tracking-tighter group-hover:opacity-80 transition">ShopBD</span>
            </a>

            {{-- Search --}}
            <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex flex-1 mx-12 max-w-lg">
                <div class="relative w-full group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="পণ্য বা ব্র্যান্ড খুঁজুন..."
                        class="w-full bg-gray-100 border-none rounded-2xl px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 transition-all duration-300 group-hover:bg-gray-200">
                    <button class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-pink-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>

            {{-- Actions --}}
            <div class="flex items-center gap-6">
                <a href="{{ route('cart.index') }}" class="relative group p-2 rounded-xl hover:bg-pink-50 transition duration-300">
                    <span class="text-2xl group-hover:scale-110 block transition">🛒</span>
                    @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                    @if($cartCount > 0)
                        <span class="absolute top-0 right-0 bg-pink-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-sm">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    <div class="relative group">
                        <button class="flex items-center gap-2 py-2 px-3 rounded-xl hover:bg-gray-100 transition duration-300">
                            <div class="w-8 h-8 rounded-full hero-gradient flex items-center justify-center text-white font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-medium hidden sm:block">{{ auth()->user()->name }}</span>
                        </button>
                        <div class="absolute right-0 hidden group-hover:block bg-white shadow-2xl rounded-2xl w-48 py-2 z-50 border border-gray-100 mt-1 origin-top-right transition-all">
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-600 hover:bg-pink-50 hover:text-pink-600 transition">📦 আমার অর্ডার</a>
                            <hr class="mx-4 my-1 border-gray-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left flex items-center gap-2 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition">🚪 লগআউট</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-gray-600 hover:text-pink-600 transition">লগইন</a>
                        <a href="{{ route('register') }}" class="text-sm bg-gray-900 text-white px-5 py-2.5 rounded-xl hover:bg-gray-800 shadow-md hover:shadow-lg transition duration-300">রেজিস্টার</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
<div class="max-w-7xl mx-auto px-4 mt-4">
    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-700 px-5 py-4 rounded-2xl mb-4 text-sm flex items-center gap-3 animate-bounce-short">
            <span class="text-xl">✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-700 px-5 py-4 rounded-2xl mb-4 text-sm flex items-center gap-3">
            <span class="text-xl">⚠️</span> {{ session('error') }}
        </div>
    @endif
</div>

{{-- Main content --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-gray-900 text-gray-400 mt-20 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
        <div class="col-span-1 md:col-span-1">
            <a href="{{ route('home') }}" class="flex items-center gap-2 mb-6">
                <span class="text-3xl">🛍️</span>
                <span class="text-2xl font-black text-white tracking-tighter">ShopBD</span>
            </a>
            <p class="text-sm leading-relaxed mb-6">বাংলাদেশের এক নম্বর অনলাইন শপিং গন্তব্য। আপনার নিত্যদিনের প্রয়োজনীয় সবকিছু এখন এক ঠিকানায়।</p>
            <div class="flex gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-indigo-600 transition text-white">f</a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-pink-600 transition text-white">i</a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-sky-500 transition text-white">t</a>
            </div>
        </div>
        
        <div>
            <h3 class="text-white font-bold text-lg mb-6">দ্রুত লিংক</h3>
            <ul class="text-sm space-y-4">
                <li><a href="{{ route('home') }}" class="hover:text-pink-500 transition">হোম</a></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-pink-500 transition">সব পণ্য</a></li>
                <li><a href="{{ route('cart.index') }}" class="hover:text-pink-500 transition">কার্ট</a></li>
                <li><a href="#" class="hover:text-pink-500 transition">আমাদের সম্পর্কে</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-white font-bold text-lg mb-6">কাস্টমার কেয়ার</h3>
            <ul class="text-sm space-y-4">
                <li><a href="#" class="hover:text-pink-500 transition">সহযোগিতা কেন্দ্র</a></li>
                <li><a href="#" class="hover:text-pink-500 transition">রিফান্ড পলিসি</a></li>
                <li><a href="#" class="hover:text-pink-500 transition">শিপিং তথ্য</a></li>
                <li><a href="#" class="hover:text-pink-500 transition">টার্মস ও কন্ডিশন</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-white font-bold text-lg mb-6">যোগাযোগ</h3>
            <div class="space-y-4 text-sm">
                <div class="flex items-start gap-3">
                    <span class="text-xl">📧</span>
                    <div>
                        <p class="text-white font-medium">ইমেইল করুন</p>
                        <p>hello@shopbd.com</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="text-xl">📞</span>
                    <div>
                        <p class="text-white font-medium">কল করুন</p>
                        <p>০৯৬-০০০-০০০০০</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 mt-16 pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
        <p>© {{ date('Y') }} ShopBD. সর্বস্বত্ব সংরক্ষিত।</p>
        <div class="flex gap-6">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-4 grayscale hover:grayscale-0 transition">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-4 grayscale hover:grayscale-0 transition">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4 grayscale hover:grayscale-0 transition">
        </div>
    </div>
</footer>


<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });
</script>


@stack('scripts')
</body>
</html>
