<x-filament-panels::page>
    @php
        $s = $report['summary'];
        $exportParams = [
            'start_date' => $this->startDate,
            'end_date'   => $this->endDate,
        ];
    @endphp

    <div class="aq-report-wrapper">
        {{-- Top Report Switcher Tabs --}}
        @include('filament.pages.partials.report-tabs', ['active' => 'profit-loss'])

        {{-- Filter & Export Bar --}}
        <div class="aq-report-filter-bar">
            <div class="aq-filter-grid">
                <div class="aq-filter-field">
                    <label>শুরুর তারিখ (From Date)</label>
                    <input type="date" wire:model.live="startDate" class="aq-filter-input" />
                </div>
                <div class="aq-filter-field">
                    <label>শেষ তারিখ (To Date)</label>
                    <input type="date" wire:model.live="endDate" class="aq-filter-input" />
                </div>
            </div>

            <div class="aq-preset-row">
                <div class="aq-preset-btns">
                    <button type="button" wire:click="applyPreset('today')" class="aq-btn aq-btn-preset {{ $this->preset === 'today' ? 'active' : '' }}">আজ (Today)</button>
                    <button type="button" wire:click="applyPreset('last_7_days')" class="aq-btn aq-btn-preset {{ $this->preset === 'last_7_days' ? 'active' : '' }}">গত ৭ দিন</button>
                    <button type="button" wire:click="applyPreset('this_month')" class="aq-btn aq-btn-preset {{ $this->preset === 'this_month' ? 'active' : '' }}">চলতি মাস (This Month)</button>
                    <button type="button" wire:click="applyPreset('last_month')" class="aq-btn aq-btn-preset {{ $this->preset === 'last_month' ? 'active' : '' }}">গত মাস</button>
                    <button type="button" wire:click="applyPreset('this_year')" class="aq-btn aq-btn-preset {{ $this->preset === 'this_year' ? 'active' : '' }}">চলতি বছর</button>
                    <button type="button" wire:click="applyPreset('all_time')" class="aq-btn aq-btn-preset {{ $this->preset === 'all_time' ? 'active' : '' }}">সর্বমোট (All Time)</button>
                </div>

                @include('filament.pages.partials.export-buttons', ['type' => 'profit-loss', 'params' => $exportParams])
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="aq-kpi-grid">
            <div class="aq-kpi-card aq-kpi-emerald">
                <div class="aq-kpi-label">মোট বিক্রয় ও আয় (Revenue & Income)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['total_inflow'], 2) }}</div>
                <div class="aq-kpi-sub">ডেলিভারি: ৳{{ number_format($s['delivered_sales'], 0) }} | আয়: ৳{{ number_format($s['other_income'], 0) }}</div>
            </div>

            <div class="aq-kpi-card aq-kpi-rose">
                <div class="aq-kpi-label">মোট খরচ ও ক্রয় (Expenses & Purchase)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['total_outflow'], 2) }}</div>
                <div class="aq-kpi-sub">ক্রয়: ৳{{ number_format($s['total_purchases'], 0) }} | খরচ: ৳{{ number_format($s['total_expenses'], 0) }}</div>
            </div>

            <div class="aq-kpi-card {{ $s['net_operating_profit'] >= 0 ? 'aq-kpi-teal' : 'aq-kpi-rose' }}">
                <div class="aq-kpi-label">নিট অপারেটিং লাভ (Net Profit)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['net_operating_profit'], 2) }}</div>
                <div class="aq-kpi-sub">প্রফিট মার্জিন: {{ $s['profit_margin_pct'] }}%</div>
            </div>

            <div class="aq-kpi-card aq-kpi-dark">
                <div class="aq-kpi-label">প্রোডাক্ট মার্জিন নিট লাভ (COGS ভিত্তিক)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['net_margin_profit'], 2) }}</div>
                <div class="aq-kpi-sub">গ্রস প্রফিট: ৳{{ number_format($s['gross_product_profit'], 0) }} (COGS: ৳{{ number_format($s['delivered_cogs'], 0) }})</div>
            </div>
        </div>

        {{-- Financial Statement & Payment Method Cashflow --}}
        <div class="aq-section-grid-2">
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">আয়-ব্যয় ও লাভ-ক্ষতি বিবরণী (Income Statement)</h3>
                        <span class="aq-card-subtitle">{{ $report['period']['start'] }} — {{ $report['period']['end'] }}</span>
                    </div>
                </div>
                <div class="aq-table-wrap">
                    <table class="aq-table">
                        <thead>
                            <tr>
                                <th>হিসাবের খাত (Account Head)</th>
                                <th style="text-align: right;">পরিমাণ (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>১. ডেলিভারিকৃত অর্ডার বিক্রয় (Delivered Order Sales)</td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">+ ৳{{ number_format($s['delivered_sales'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>২. বিক্রিত পণ্যের ক্রয়মূল্য (Cost of Goods Sold - COGS)</td>
                                <td style="text-align: right; color: #64748b;">- ৳{{ number_format($s['delivered_cogs'], 2) }}</td>
                            </tr>
                            <tr style="background-color: #f0fdf4;">
                                <td style="font-weight: 700;">মোট প্রোডাক্ট গ্রস প্রফিট (Gross Profit from Sales)</td>
                                <td style="text-align: right; font-weight: 800; color: #059669;">৳{{ number_format($s['gross_product_profit'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>৩. অন্যান্য আয় (Other Income - Ledger)</td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">+ ৳{{ number_format($s['other_income'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>৪. পণ্য ক্রয় বাবদ ব্যয় (Product Purchases - Ledger)</td>
                                <td style="text-align: right; font-weight: 700; color: #e11d48;">- ৳{{ number_format($s['total_purchases'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>৫. অফিস ও পরিচালনা খরচ (Operating Expenses - Ledger)</td>
                                <td style="text-align: right; font-weight: 700; color: #e11d48;">- ৳{{ number_format($s['total_expenses'], 2) }}</td>
                            </tr>
                            <tr style="background-color: #f8fafc; border-top: 2px solid #0b2b22;">
                                <td style="font-weight: 800; color: #0b2b22;">নিট অপারেটিং লাভ / ক্ষতি (Sales + Income - Purchase - Expense)</td>
                                <td style="text-align: right; font-weight: 800; font-size: 1rem; color: {{ $s['net_operating_profit'] >= 0 ? '#059669' : '#e11d48' }};">
                                    ৳{{ number_format($s['net_operating_profit'], 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">পেমেন্ট মেথড অনুযায়ী ক্যাশফ্লো (Cashflow by Payment Method)</h3>
                        <span class="aq-card-subtitle">নগদ, ব্যাংক, বিকাশ ও নগদের লেনদেন চিত্র</span>
                    </div>
                </div>
                <div class="aq-table-wrap">
                    <table class="aq-table">
                        <thead>
                            <tr>
                                <th>মাধ্যম (Method)</th>
                                <th style="text-align: right;">অর্ডার বিক্রয়</th>
                                <th style="text-align: right;">লেজার জমা</th>
                                <th style="text-align: right;">খরচ/ক্রয়</th>
                                <th style="text-align: right;">নিট ব্যালেন্স</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['method_summary'] as $m)
                                <tr>
                                    <td style="font-weight: 700;">{{ $m['method'] }}</td>
                                    <td style="text-align: right;">৳{{ number_format($m['order_sales'], 2) }}</td>
                                    <td style="text-align: right; color: #059669;">৳{{ number_format($m['ledger_in'], 2) }}</td>
                                    <td style="text-align: right; color: #e11d48;">৳{{ number_format($m['ledger_out'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 800; color: {{ $m['net_balance'] >= 0 ? '#059669' : '#e11d48' }};">
                                        ৳{{ number_format($m['net_balance'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown by Category --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">খাত অনুযায়ী খরচ ও ক্রয়ের বিশ্লেষণ (Expense & Purchase by Category)</h3>
                    <span class="aq-card-subtitle">কোন খাতে কত টাকা ব্যয় হয়েছে তার সারসংক্ষেপ</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>খাতের নাম (Category / Head)</th>
                            <th>ধরন (Type)</th>
                            <th style="text-align: center;">লেনদেন সংখ্যা</th>
                            <th style="text-align: right;">প্রদত্ত টাকা (Paid ৳)</th>
                            <th style="text-align: right;">বকেয়া (Due ৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['expense_by_category'] as $cat)
                            <tr>
                                <td style="font-weight: 700;">{{ $cat['category'] }}</td>
                                <td>
                                    <span class="aq-badge {{ $cat['type'] === 'expense' ? 'aq-badge-danger' : 'aq-badge-warning' }}">
                                        {{ strtoupper($cat['type']) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">{{ $cat['count'] }} টি</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($cat['amount'], 2) }}</td>
                                <td style="text-align: right; color: {{ $cat['due_amount'] > 0 ? '#e11d48' : '#64748b' }}; font-weight: 600;">
                                    ৳{{ number_format($cat['due_amount'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 1.5rem;">নির্বাচিত সময়ে কোনো খরচ বা ক্রয়ের এন্ট্রি নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Detailed Ledger Transactions --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">বিস্তারিত লেজার লেনদেন (Account Ledger Entries)</h3>
                    <span class="aq-card-subtitle">মোট লেনদেন: {{ count($report['transactions']) }} টি</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>তারিখ</th>
                            <th>বিবরণ (Title)</th>
                            <th>ধরন</th>
                            <th>ক্যাটাগরি</th>
                            <th>পার্টি / ব্যক্তি</th>
                            <th>মেথড</th>
                            <th style="text-align: right;">টাকা (৳)</th>
                            <th style="text-align: right;">বকেয়া (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['transactions'] as $tr)
                            <tr>
                                <td>{{ optional($tr->transaction_date)->format('d M, Y') }}</td>
                                <td style="font-weight: 700;">{{ $tr->title }}</td>
                                <td>
                                    <span class="aq-badge {{ in_array($tr->type, ['income', 'sales_collection']) ? 'aq-badge-success' : ($tr->type === 'expense' ? 'aq-badge-danger' : 'aq-badge-warning') }}">
                                        {{ strtoupper($tr->type) }}
                                    </span>
                                </td>
                                <td>{{ $tr->category ?? '-' }}</td>
                                <td>{{ $tr->party_name ?? '-' }}</td>
                                <td><span class="aq-badge aq-badge-gray">{{ strtoupper($tr->payment_method) }}</span></td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($tr->amount, 2) }}</td>
                                <td style="text-align: right; font-weight: 700; color: {{ $tr->due_amount > 0 ? '#e11d48' : '#64748b' }};">
                                    ৳{{ number_format($tr->due_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 1.5rem;">নির্বাচিত সময়ে কোনো লেজার লেনদেন পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
