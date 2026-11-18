@extends('layouts.app')
@section('title', 'রেজিস্ট্রেশন')

@section('content')
<div class="max-w-md mx-auto my-12">
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-2xl shadow-gray-200/50 overflow-hidden" data-aos="zoom-in">
        <div class="p-8 md:p-12">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-black text-gray-900 mb-2">নতুন অ্যাকাউন্ট ✨</h1>
                <p class="text-gray-400 font-medium">কেনাকাটা শুরু করতে রেজিস্ট্রেশন করুন</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">আপনার নাম</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="আপনার নাম লিখুন">
                    @error('name') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">ইমেইল অ্যাড্রেস</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="example@mail.com">
                    @error('email') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">পাসওয়ার্ড</label>
                    <input type="password" name="password" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="কমপক্ষে ৮ ডিজিট">
                    @error('password') <p class="text-red-500 text-xs mt-2 font-bold ml-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">পাসওয়ার্ড নিশ্চিত করুন</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full px-6 py-4 rounded-2xl bg-gray-50 border-2 border-transparent focus:border-indigo-600 focus:bg-white transition-all outline-none font-bold text-gray-900 shadow-inner"
                        placeholder="আবার লিখুন">
                </div>

                <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black text-lg shadow-xl shadow-gray-200 hover:bg-pink-600 hover:-translate-y-1 transition duration-300">
                    অ্যাকাউন্ট তৈরি করুন
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-gray-500 font-bold">ইতিমধ্যেই অ্যাকাউন্ট আছে? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">লগইন করুন</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
