@php
    $theme = \App\Models\DashboardSetting::current();
    $cardStyleClass = 'aq-style-' . ($theme['kpi_card_style'] ?? 'modern_white');
    $customTitle = trim((string) ($theme['welcome_title'] ?? ''));
    $bannerTitle = $customTitle !== '' ? $customTitle : ('স্বাগতম, ' . (filament()->auth()->user()->name ?? 'Admin') . '! 👋');
@endphp

<x-filament-widgets::widget>
    <div class="aq-smart-dash">
        {{-- 1. Executive Welcome & Quick Actions Banner --}}
        @if(!empty($theme['show_welcome_banner']))
            <div class="aq-hero-banner">
                <div class="aq-hero-left">
                    @if(!empty($theme['welcome_badge']))
                        <div class="aq-hero-badge">
                            <span>{{ $theme['welcome_badge'] }}</span>
                        </div>
                    @endif
                    <h2 class="aq-hero-title">{{ $bannerTitle }}</h2>
                    @if(!empty($theme['welcome_subtitle']))
                        <p class="aq-hero-sub">{{ $theme['welcome_subtitle'] }}</p>
                    @endif
                </div>

                @if(!empty($theme['show_quick_actions']))
                    <div class="aq-hero-actions">
                        <a href="{{ url('/admin/products/create') }}" class="aq-qbtn aq-qbtn-gold">
                            <svg class="aq-qbtn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>নতুন প্রোডাক্ট</span>
                        </a>
                        <a href="{{ url('/admin/orders') }}" class="aq-qbtn aq-qbtn-glass">
                            <svg class="aq-qbtn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            <span>অর্ডার ও ট্র্যাকিং</span>
                        </a>
                        <a href="{{ url('/admin/account-transactions/create') }}" class="aq-qbtn aq-qbtn-glass">
                            <svg class="aq-qbtn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>আয়-ব্যয় এন্ট্রি</span>
                        </a>
                        <a href="{{ url('/admin/reports/sales') }}" class="aq-qbtn aq-qbtn-glass">
                            <svg class="aq-qbtn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>সকল রিপোর্ট</span>
                        </a>
                        <a href="{{ url('/admin/theme-settings') }}" class="aq-qbtn aq-qbtn-glass" title="ড্যাশবোর্ড থিম ও লেআউট পরিবর্তন করুন">
                            <span>🎨 থিম সেটিংস</span>
                        </a>
                        <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="aq-qbtn aq-qbtn-glass" title="ফ্রন্টপেজ ওয়েবসাইট নতুন ট্যাবে ওপেন করুন">
                            <svg class="aq-qbtn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <span>ওয়েবসাইট দেখুন ↗</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- 2. Modern Smart KPI Cards Grid --}}
        @if(!empty($theme['show_kpi_cards']))
            <div class="aq-smart-kpi-grid">
                {{-- Card 1: Total Sales & Order Value --}}
                <a href="{{ url('/admin/reports/sales') }}" class="aq-s-card aq-s-emerald {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">মোট বিক্রয় (Delivered Revenue)</span>
                            <div class="aq-s-value">৳{{ number_format($deliveredSales, 2) }}</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-emerald">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-emerald">আজকের অর্ডার: ৳{{ number_format($todaySales, 0) }}</span>
                        <span class="aq-s-meta">চলমান অর্ডার মূল্য: ৳{{ number_format($activeOrderValue, 0) }} →</span>
                    </div>
                </a>

                {{-- Card 2: Active & Running Orders --}}
                <a href="{{ url('/admin/orders') }}" class="aq-s-card aq-s-blue {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">চলমান অর্ডার (Orders Pipeline)</span>
                            <div class="aq-s-value">{{ $pendingOrders }} Pending <span class="aq-s-val-sep">|</span> {{ $processingOrders + $shippedOrders }} Active</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-blue">সর্বমোট অর্ডার: {{ $totalOrders }} টি</span>
                        <span class="aq-s-meta">ডেলিভারি সম্পন্ন: {{ $deliveredOrders }} টি →</span>
                    </div>
                </a>

                {{-- Card 3: Estimated Net Profit --}}
                <a href="{{ url('/admin/reports/profit-loss') }}" class="aq-s-card aq-s-teal {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">আনুমানিক নিট মুনাফা (Net Profit)</span>
                            <div class="aq-s-value">{{ $estimatedProfit < 0 ? '-' : '' }}৳{{ number_format(abs($estimatedProfit), 2) }}</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-teal">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-teal">{{ $estimatedProfit >= 0 ? '✓ লাভে আছে' : '⚠ ঘাটতি' }}</span>
                        <span class="aq-s-meta">বিক্রয় ও আয় - খরচ ও ক্রয় →</span>
                    </div>
                </a>

                {{-- Card 4: Products & Inventory Valuation --}}
                <a href="{{ url('/admin/products') }}" class="aq-s-card aq-s-purple {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">মোট প্রোডাক্ট ও স্টক (Catalog)</span>
                            <div class="aq-s-value">{{ $totalProducts }} টি প্রোডাক্ট</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-purple">🔥 অফারে আছে: {{ $onSaleCount }} টি</span>
                        <span class="aq-s-meta">স্টক মূল্য: ৳{{ number_format($stockValue, 0) }} →</span>
                    </div>
                </a>

                {{-- Card 5: Total Expenses & Purchases --}}
                <a href="{{ url('/admin/account-transactions') }}" class="aq-s-card aq-s-rose {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">মোট ক্রয় ও খরচ (Expenses)</span>
                            <div class="aq-s-value">৳{{ number_format($totalPurchases + $totalExpenses, 2) }}</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-rose">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-rose">ক্রয়: ৳{{ number_format($totalPurchases, 0) }}</span>
                        <span class="aq-s-meta">অন্যান্য খরচ: ৳{{ number_format($totalExpenses, 0) }} →</span>
                    </div>
                </a>

                {{-- Card 6: Low Stock & Customer Due --}}
                <a href="{{ url('/admin/reports/inventory') }}" class="aq-s-card aq-s-amber {{ $cardStyleClass }}">
                    <div class="aq-s-top">
                        <div>
                            <span class="aq-s-label">স্টক অ্যালার্ট ও বকেয়া (Alerts & Due)</span>
                            <div class="aq-s-value">{{ $lowStockCount }} লো-স্টক <span class="aq-s-val-sep">|</span> ৳{{ number_format($customerDue, 0) }}</div>
                        </div>
                        <div class="aq-s-icon-box aq-s-icon-amber">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="aq-s-bottom">
                        <span class="aq-s-pill aq-s-pill-amber">স্টক আউট: {{ $outOfStockCount }} টি</span>
                        <span class="aq-s-meta">কাস্টমার বকেয়া: ৳{{ number_format($customerDue, 0) }} →</span>
                    </div>
                </a>
            </div>
        @endif

        {{-- 3. Order Tracking Status Quick-Filter Bar --}}
        @if(!empty($theme['show_pipeline_bar']))
            <div class="aq-pipeline-bar">
                <div class="aq-pipeline-label">
                    <span>🚚 অর্ডার ট্র্যাকিং পাইপলাইন:</span>
                </div>
                <div class="aq-pipeline-items">
                    <a href="{{ url('/admin/orders?tableFilters[status][value]=pending') }}" class="aq-pipe-item aq-pipe-amber">
                        <span class="aq-pipe-dot"></span>
                        <span>Pending (অপেক্ষমান)</span>
                        <strong>{{ $pendingOrders }}</strong>
                    </a>
                    <a href="{{ url('/admin/orders?tableFilters[status][value]=processing') }}" class="aq-pipe-item aq-pipe-blue">
                        <span class="aq-pipe-dot"></span>
                        <span>Processing (প্রস্তুত হচ্ছে)</span>
                        <strong>{{ $processingOrders }}</strong>
                    </a>
                    <a href="{{ url('/admin/orders?tableFilters[status][value]=shipped') }}" class="aq-pipe-item aq-pipe-indigo">
                        <span class="aq-pipe-dot"></span>
                        <span>Shipped (পথে আছে)</span>
                        <strong>{{ $shippedOrders }}</strong>
                    </a>
                    <a href="{{ url('/admin/orders?tableFilters[status][value]=delivered') }}" class="aq-pipe-item aq-pipe-emerald">
                        <span class="aq-pipe-dot"></span>
                        <span>Delivered (সম্পন্ন)</span>
                        <strong>{{ $deliveredOrders }}</strong>
                    </a>
                    <a href="{{ url('/admin/orders?tableFilters[status][value]=cancelled') }}" class="aq-pipe-item aq-pipe-rose">
                        <span class="aq-pipe-dot"></span>
                        <span>Cancelled (বাতিল)</span>
                        <strong>{{ $cancelledOrders }}</strong>
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
