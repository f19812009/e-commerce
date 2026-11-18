@php
    $activeTab = $active ?? 'sales';
    $tabs = [
        'sales' => [
            'label' => 'Sales & Order Report',
            'bn'    => 'বিক্রয় ও অর্ডার রিপোর্ট',
            'url'   => url('/admin/reports/sales'),
            'icon'  => '<svg class="aq-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 14l4-4 4 4 5-6"/></svg>',
        ],
        'profit-loss' => [
            'label' => 'Profit & Loss Statement',
            'bn'    => 'লাভ-ক্ষতি ও একাউন্টস',
            'url'   => url('/admin/reports/profit-loss'),
            'icon'  => '<svg class="aq-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
        ],
        'inventory' => [
            'label' => 'Stock & Valuation Report',
            'bn'    => 'স্টক ও ইনভেন্টরি ভ্যালুয়েশন',
            'url'   => url('/admin/reports/inventory'),
            'icon'  => '<svg class="aq-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
        ],
        'due-party' => [
            'label' => 'Due & Party Ledger',
            'bn'    => 'বকেয়া ও পার্টি লেজার',
            'url'   => url('/admin/reports/due-party'),
            'icon'  => '<svg class="aq-tab-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
        ],
    ];
@endphp

<div class="aq-report-tabs-bar">
    @foreach($tabs as $key => $tab)
        <a href="{{ $tab['url'] }}" class="aq-report-tab {{ $activeTab === $key ? 'active' : '' }}">
            {!! $tab['icon'] !!}
            <div class="aq-report-tab-text">
                <span class="aq-report-tab-title">{{ $tab['label'] }}</span>
                <span class="aq-report-tab-sub">{{ $tab['bn'] }}</span>
            </div>
        </a>
    @endforeach
</div>
