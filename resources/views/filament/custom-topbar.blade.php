@php
    $user = filament()->auth()->user();
    $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    $unreadAlertsCount = 1;
    $aqTheme = \App\Models\DashboardSetting::current();
@endphp

<div class="flex items-center justify-between w-full py-1 gap-x-4">
    {{-- Left Section: Live Date & Clock --}}
    @if(!empty($aqTheme['show_topbar_clock']))
        <div 
            x-data="{
                time: '',
                date: '',
                updateClock() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                    this.date = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                }
            }"
            x-init="updateClock(); setInterval(() => updateClock(), 1000)"
            class="hidden lg:flex items-center gap-x-2.5 text-xs text-slate-700 dark:text-slate-200 font-medium"
        >
            {{-- Date --}}
            <div class="flex items-center gap-x-1.5">
                <svg class="w-4 h-4 text-[#0b2b22] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-text="date" class="font-semibold text-slate-800 dark:text-slate-100"></span>
            </div>

            <span class="text-slate-300 dark:text-slate-600">|</span>

            {{-- Time --}}
            <div class="flex items-center gap-x-1.5">
                <svg class="w-4 h-4 text-[#0b2b22] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span x-text="time" class="font-bold text-[#0b2b22] dark:text-emerald-400 font-mono tracking-wide"></span>
            </div>
        </div>
    @else
        <div></div>
    @endif

    {{-- Right Section: Actions --}}
    <div class="flex items-center ml-auto gap-x-2 sm:gap-x-3">
        {{-- 1. Prominent Frontpage / Visit Website Button --}}
        <a 
            href="{{ url('/') }}" 
            target="_blank" 
            rel="noopener noreferrer"
            title="ফ্রন্টএন্ড ওয়েবসাইট ভিজিট করুন (Open Storefront in New Tab)"
            class="aq-topbar-front-btn"
        >
            <svg class="aq-topbar-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
            </svg>
            <span class="aq-topbar-btn-label">Visit Website</span>
            <svg class="aq-topbar-ext-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        </a>

        {{-- 2. Dynamic Theme Customizer Button --}}
        <a 
            href="{{ url('/admin/theme-settings') }}" 
            title="ড্যাশবোর্ড থিম ও লেআউট সেটিংস (Theme Customizer)"
            class="aq-topbar-theme-btn"
        >
            <span>🎨</span>
            <span class="aq-topbar-btn-label">Theme</span>
        </a>

        {{-- 2. New Order / Cart Icon --}}
        <a 
            href="{{ url('/admin/orders/create') }}" 
            title="Create New Order"
            class="relative inline-flex items-center justify-center p-2 text-[#0b2b22] dark:text-emerald-300 hover:bg-slate-100/70 dark:hover:bg-gray-800 rounded-lg transition-colors"
        >
            <svg class="w-5 h-5 text-[#0b2b22] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            @if($pendingOrdersCount > 0)
                <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[17px] h-[17px] px-1 text-[10px] font-bold text-white bg-rose-500 rounded-full shadow-xs">
                    {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                </span>
            @endif
        </a>

        {{-- 3. Alert / Bell Icon --}}
        <a 
            href="{{ url('/admin/orders') }}" 
            title="Alerts & Notifications"
            class="relative inline-flex items-center justify-center p-2 text-[#0b2b22] dark:text-emerald-300 hover:bg-slate-100/70 dark:hover:bg-gray-800 rounded-lg transition-colors"
        >
            <svg class="w-5 h-5 text-[#0b2b22] dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[17px] h-[17px] px-1 text-[10px] font-bold text-white bg-sky-500 rounded-full shadow-xs">
                {{ $unreadAlertsCount }}
            </span>
        </a>

        {{-- 4. System Admin Avatar & Info Badge --}}
        @if($user)
            <div class="flex items-center gap-x-2 pl-2 border-l border-slate-200 dark:border-gray-700">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-[#0b2b22] text-white font-bold text-sm shadow-xs uppercase">
                    {{ substr($user->name ?? 'A', 0, 1) }}
                </div>
                <div class="hidden md:flex flex-col text-left leading-tight">
                    <span class="text-xs font-bold text-slate-900 dark:text-slate-100">
                        {{ $user->name ?? 'System Admin' }}
                    </span>
                    <span class="text-[11px] text-slate-500 dark:text-gray-400">
                        {{ $user->email ?? 'admin@company.com' }}
                    </span>
                </div>
            </div>
        @endif

    </div>
</div>
