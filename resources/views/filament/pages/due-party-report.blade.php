<x-filament-panels::page>
    @php
        $s = $report['summary'];
        $exportParams = [
            'party_type' => $this->partyType,
            'search'     => $this->search,
            'only_due'   => $this->onlyDue ? 1 : 0,
        ];
    @endphp

    <div class="aq-report-wrapper">
        {{-- Top Report Switcher Tabs --}}
        @include('filament.pages.partials.report-tabs', ['active' => 'due-party'])

        {{-- Filter & Export Bar --}}
        <div class="aq-report-filter-bar">
            <div class="aq-filter-grid">
                <div class="aq-filter-field">
                    <label>পার্টির ধরন (Party Type)</label>
                    <select wire:model.live="partyType" class="aq-filter-input">
                        <option value="all">সকল পার্টি (Customer, Supplier & Other)</option>
                        <option value="customer">Customer (কাস্টমার বকেয়া)</option>
                        <option value="supplier">Supplier / Vendor (সাপ্লায়ার বকেয়া)</option>
                        <option value="other">Other / Internal (অন্যান্য)</option>
                    </select>
                </div>
                <div class="aq-filter-field">
                    <label>পার্টি বা রেফারেন্স খুঁজুন (Search)</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="নাম, বিবরণ বা ভাউচার নং..." class="aq-filter-input" />
                </div>
                <div class="aq-filter-field" style="justify-content: center;">
                    <label>ফিল্টার অপশন (Due Filter)</label>
                    <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; text-transform: none; cursor: pointer; height: 2.5rem;">
                        <input type="checkbox" wire:model.live="onlyDue" style="border-radius: 0.25rem; width: 1rem; height: 1rem;" />
                        <span>শুধুমাত্র বকেয়া (Due > 0) লেনদেন দেখান</span>
                    </label>
                </div>
            </div>

            <div class="aq-preset-row">
                <div style="font-size: 0.8rem; color: #475569; font-weight: 600;">
                    কাস্টমার, সাপ্লায়ার ও আনপেইড অর্ডারের বকেয়া হিসাব বিবরণী
                </div>

                @include('filament.pages.partials.export-buttons', ['type' => 'due-party', 'params' => $exportParams])
            </div>
        </div>

        {{-- Summary KPI Cards --}}
        <div class="aq-kpi-grid">
            <div class="aq-kpi-card aq-kpi-amber">
                <div class="aq-kpi-label">কাস্টমার লেজার বকেয়া (Customer Due)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['customer_due'], 2) }}</div>
                <div class="aq-kpi-sub">কাস্টমারদের নিকট হতে প্রাপ্য বকেয়া</div>
            </div>

            <div class="aq-kpi-card aq-kpi-rose">
                <div class="aq-kpi-label">সাপ্লায়ার বকেয়া (Supplier Payable)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['supplier_due'], 2) }}</div>
                <div class="aq-kpi-sub">মহাজন বা সাপ্লায়ারদের প্রদেয় বকেয়া</div>
            </div>

            <div class="aq-kpi-card aq-kpi-blue">
                <div class="aq-kpi-label">আনপেইড অর্ডার ভ্যালু (Unpaid COD)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['unpaid_orders_amount'], 2) }}</div>
                <div class="aq-kpi-sub">মোট আনপেইড অর্ডার: {{ $s['unpaid_orders_count'] }} টি</div>
            </div>

            <div class="aq-kpi-card aq-kpi-dark">
                <div class="aq-kpi-label">সর্বমোট লেজার বকেয়া (Total Due)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['total_ledger_due'], 2) }}</div>
                <div class="aq-kpi-sub">অন্যান্য বকেয়া: ৳{{ number_format($s['other_due'], 2) }}</div>
            </div>
        </div>

        {{-- Party-wise Aggregated Summary --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">পার্টি ভিত্তিক মোট লেনদেন ও বকেয়া সারাংশ (Party-wise Ledger Summary)</h3>
                    <span class="aq-card-subtitle">প্রতিটি কাস্টমার বা সাপ্লায়ারের মোট জমা/খরচ এবং বর্তমান বকেয়া</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>পার্টি / ব্যক্তির নাম</th>
                            <th>ধরন (Party Type)</th>
                            <th style="text-align: center;">লেনদেন সংখ্যা</th>
                            <th style="text-align: right;">মোট পরিশোধ / জমা (৳)</th>
                            <th style="text-align: right;">বর্তমান বকেয়া (Due ৳)</th>
                            <th>সর্বশেষ লেনদেন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['party_summary'] as $party)
                            <tr>
                                <td style="font-weight: 700;">{{ $party['party_name'] }}</td>
                                <td>
                                    <span class="aq-badge {{ $party['party_type'] === 'customer' ? 'aq-badge-warning' : ($party['party_type'] === 'supplier' ? 'aq-badge-danger' : 'aq-badge-gray') }}">
                                        {{ strtoupper($party['party_type']) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">{{ $party['transactions_count'] }} টি</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($party['total_paid'], 2) }}</td>
                                <td style="text-align: right; font-weight: 800; color: {{ $party['total_due'] > 0 ? '#e11d48' : '#059669' }};">
                                    ৳{{ number_format($party['total_due'], 2) }}
                                </td>
                                <td>{{ $party['last_date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 1.5rem;">কোনো পার্টি লেজার তথ্য পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Unpaid Orders Table --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">পেমেন্ট বকেয়া থাকা অর্ডারসমূহ (Unpaid / COD Orders)</h3>
                    <span class="aq-card-subtitle">যেসকল অর্ডারের পেমেন্ট স্ট্যাটাস এখনো Unpaid রয়েছে</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>অর্ডার নং</th>
                            <th>তারিখ</th>
                            <th>গ্রাহকের নাম</th>
                            <th>ফোন নম্বর</th>
                            <th>অর্ডার স্ট্যাটাস</th>
                            <th>পেমেন্ট মেথড</th>
                            <th>পেমেন্ট স্ট্যাটাস</th>
                            <th style="text-align: right;">মোট টাকা (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['unpaid_orders'] as $order)
                            <tr>
                                <td style="font-weight: 700; color: #0b2b22;">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                                <td style="font-weight: 600;">{{ $order->customer_name }}</td>
                                <td>{{ $order->customer_phone }}</td>
                                <td><span class="aq-badge aq-badge-info">{{ $order->status }}</span></td>
                                <td><span class="aq-badge aq-badge-gray">{{ strtoupper($order->payment_method) }}</span></td>
                                <td><span class="aq-badge aq-badge-warning">{{ strtoupper($order->payment_status) }}</span></td>
                                <td style="text-align: right; font-weight: 800; color: #d97706;">৳{{ number_format($order->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 1.5rem;">কোনো আনপেইড অর্ডার নেই।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Detailed Ledger Entries --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">পার্টি লেজার এন্ট্রি তালিকা (Detailed Party Transactions)</h3>
                    <span class="aq-card-subtitle">মোট প্রদর্শিত এন্ট্রি: {{ count($report['transactions']) }} টি</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>তারিখ</th>
                            <th>পার্টি নাম</th>
                            <th>পার্টি ধরন</th>
                            <th>বিবরণ (Title)</th>
                            <th>লেনদেন ধরন</th>
                            <th>ভাউচার / রেফারেন্স</th>
                            <th style="text-align: right;">জমা / পরিশোধ (৳)</th>
                            <th style="text-align: right;">বকেয়া (Due ৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['transactions'] as $tr)
                            <tr>
                                <td>{{ optional($tr->transaction_date)->format('d M, Y') }}</td>
                                <td style="font-weight: 700;">{{ $tr->party_name ?: 'Unnamed' }}</td>
                                <td><span class="aq-badge aq-badge-gray">{{ strtoupper($tr->party_type) }}</span></td>
                                <td>{{ $tr->title }}</td>
                                <td><span class="aq-badge aq-badge-info">{{ strtoupper($tr->type) }}</span></td>
                                <td>{{ $tr->reference_no ?? '-' }}</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($tr->amount, 2) }}</td>
                                <td style="text-align: right; font-weight: 800; color: {{ $tr->due_amount > 0 ? '#e11d48' : '#64748b' }};">
                                    ৳{{ number_format($tr->due_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 1.5rem;">কোনো লেনদেন পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
