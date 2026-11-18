@extends('layouts.app')
@section('title', 'লগইন')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-2xl shadow-gray-200/50 overflow-hidden" data-aos="zoom-in">
        <div class="p-8 md:p-12">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-black text-gray-900 mb-2">স্বাগতম! 👋</h1>
                <p class="text-gray-400 font-medium">আপনার অ্যাকাউন্টে লগইন করুন</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">ইমেইল অ্যাড্রেস</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="example@mail.com">
                    @error('email') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest">পাসওয়ার্ড</label>
                        <a href="#" class="text-[10px] font-black text-indigo-600 hover:underline uppercase">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <input type="password" name="password" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center gap-3 ml-1">
                    <input type="checkbox" name="remember" id="remember" class="w-5 h-5 rounded-lg border-2 border-gray-200 text-indigo-600 focus:ring-0 transition cursor-pointer">
                    <label for="remember" class="text-sm font-bold text-gray-500 cursor-pointer">আমাকে মনে রাখুন</label>
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black text-lg shadow-xl shadow-gray-200 hover:bg-indigo-600 hover:-translate-y-1 transition duration-300">
                    লগইন করুন
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-gray-500 font-bold">নতুন ইউজার? <a href="{{ route('register') }}" class="text-pink-600 hover:underline">অ্যাকাউন্ট তৈরি করুন</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
