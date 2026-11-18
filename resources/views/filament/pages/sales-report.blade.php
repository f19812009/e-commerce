<x-filament-panels::page>
    @php
        $s = $report['summary'];
        $exportParams = [
            'start_date'     => $this->startDate,
            'end_date'       => $this->endDate,
            'status'         => $this->status,
            'payment_method' => $this->paymentMethod,
        ];
    @endphp

    <div class="aq-report-wrapper">
        {{-- Top Report Switcher Tabs --}}
        @include('filament.pages.partials.report-tabs', ['active' => 'sales'])

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
                <div class="aq-filter-field">
                    <label>অর্ডার স্ট্যাটাস (Order Status)</label>
                    <select wire:model.live="status" class="aq-filter-input">
                        <option value="all">সকল স্ট্যাটাস (All Status)</option>
                        <option value="delivered">Delivered (ডেলিভারি সম্পন্ন)</option>
                        <option value="pending">Pending (পেন্ডিং)</option>
                        <option value="processing">Processing (প্রসেসিং)</option>
                        <option value="shipped">Shipped (শিপড)</option>
                        <option value="cancelled">Cancelled (বাতিল)</option>
                    </select>
                </div>
                <div class="aq-filter-field">
                    <label>পেমেন্ট মেথড (Payment Method)</label>
                    <select wire:model.live="paymentMethod" class="aq-filter-input">
                        <option value="all">সকল মেথড (All Methods)</option>
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="bkash">bKash</option>
                        <option value="nagad">Nagad</option>
                    </select>
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

                @include('filament.pages.partials.export-buttons', ['type' => 'sales', 'params' => $exportParams])
            </div>
        </div>

        {{-- Summary KPI Cards --}}
        <div class="aq-kpi-grid">
            <div class="aq-kpi-card aq-kpi-emerald">
                <div class="aq-kpi-label">ডেলিভারিকৃত বিক্রয় (Delivered)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['delivered_revenue'], 2) }}</div>
                <div class="aq-kpi-sub">সম্পন্ন অর্ডার: {{ $s['delivered_count'] }} টি</div>
            </div>

            <div class="aq-kpi-card aq-kpi-blue">
                <div class="aq-kpi-label">মোট অর্ডার মূল্য (Gross Sales)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['gross_order_amount'], 2) }}</div>
                <div class="aq-kpi-sub">মোট অর্ডার: {{ $s['total_orders_count'] }} টি (বাতিল বাদে)</div>
            </div>

            <div class="aq-kpi-card aq-kpi-amber">
                <div class="aq-kpi-label">চলমান অর্ডার (Pending / Active)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['pending_revenue'], 2) }}</div>
                <div class="aq-kpi-sub">প্রক্রিয়াধীন অর্ডার: {{ $s['pending_count'] }} টি</div>
            </div>

            <div class="aq-kpi-card aq-kpi-teal">
                <div class="aq-kpi-label">গড় অর্ডার মূল্য (Avg Order Value)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['average_order_val'], 2) }}</div>
                <div class="aq-kpi-sub">ডেলিভারি চার্জ আদায়: ৳{{ number_format($s['total_shipping'], 2) }}</div>
            </div>
        </div>

        {{-- Status & Payment Method Breakdown --}}
        <div class="aq-section-grid-2">
            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">অর্ডার স্ট্যাটাস ভিত্তিক সারাংশ (Order Status Summary)</h3>
                        <span class="aq-card-subtitle">{{ $report['period']['start'] }} হতে {{ $report['period']['end'] }}</span>
                    </div>
                </div>
                <div class="aq-table-wrap">
                    <table class="aq-table">
                        <thead>
                            <tr>
                                <th>স্ট্যাটাস (Status)</th>
                                <th style="text-align: center;">অর্ডার সংখ্যা</th>
                                <th style="text-align: right;">মোট টাকা (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report['status_breakdown'] as $key => $row)
                                <tr>
                                    <td>
                                        <span class="aq-badge {{ match($key) {
                                            'delivered' => 'aq-badge-success',
                                            'pending' => 'aq-badge-warning',
                                            'cancelled' => 'aq-badge-danger',
                                            default => 'aq-badge-info'
                                        } }}">
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">{{ $row['count'] }} টি</td>
                                    <td style="text-align: right; font-weight: 700;">৳{{ number_format($row['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="aq-card">
                <div class="aq-card-header">
                    <div>
                        <h3 class="aq-card-title">পেমেন্ট মেথড অনুযায়ী বিক্রয় (Payment Method Breakdown)</h3>
                        <span class="aq-card-subtitle">বাতিলকৃত অর্ডার ব্যতীত</span>
                    </div>
                </div>
                <div class="aq-table-wrap">
                    <table class="aq-table">
                        <thead>
                            <tr>
                                <th>পেমেন্ট মাধ্যম</th>
                                <th style="text-align: center;">অর্ডার সংখ্যা</th>
                                <th style="text-align: right;">মোট টাকা (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['payment_breakdown'] as $pm)
                                <tr>
                                    <td><span class="aq-badge aq-badge-info">{{ $pm['method'] }}</span></td>
                                    <td style="text-align: center; font-weight: 700;">{{ $pm['count'] }} টি</td>
                                    <td style="text-align: right; font-weight: 700;">৳{{ number_format($pm['total'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: #64748b; padding: 1.25rem;">কোনো পেমেন্ট তথ্য পাওয়া যায়নি</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Selling Products Table --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">টপ সেলিং প্রোডাক্ট ও লাভ বিশ্লেষণ (Top Selling Products & Margin)</h3>
                    <span class="aq-card-subtitle">সর্বাধিক বিক্রিত প্রোডাক্টের তালিকা, ক্রয়মূল্য (COGS) ও আনুমানিক লাভ</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>প্রোডাক্টের নাম</th>
                            <th>SKU</th>
                            <th style="text-align: center;">বিক্রিত সংখ্যা</th>
                            <th style="text-align: right;">মোট বিক্রয় (৳)</th>
                            <th style="text-align: right;">ক্রয়মূল্য / COGS (৳)</th>
                            <th style="text-align: right;">আনুমানিক লাভ (৳)</th>
                            <th style="text-align: center;">বর্তমান স্টক</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['top_products'] as $i => $prod)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td style="font-weight: 700;">{{ $prod['product_name'] }}</td>
                                <td><span class="aq-badge aq-badge-gray">{{ $prod['sku'] }}</span></td>
                                <td style="text-align: center; font-weight: 800; color: #0b2b22;">{{ $prod['total_qty'] }} টি</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($prod['total_revenue'], 2) }}</td>
                                <td style="text-align: right; color: #64748b;">৳{{ number_format($prod['total_cogs'], 2) }}</td>
                                <td style="text-align: right; font-weight: 700; color: {{ $prod['estimated_profit'] >= 0 ? '#059669' : '#e11d48' }};">
                                    ৳{{ number_format($prod['estimated_profit'], 2) }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="aq-badge {{ $prod['current_stock'] <= 5 ? 'aq-badge-danger' : 'aq-badge-success' }}">
                                        {{ $prod['current_stock'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 1.5rem;">নির্বাচিত সময়ে কোনো প্রোডাক্ট বিক্রয়ের তথ্য পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Daily Sales Summary --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">দৈনিক বিক্রয় সারাংশ (Daily Sales Breakdown)</h3>
                    <span class="aq-card-subtitle">তারিখ অনুযায়ী অর্ডার ও বিক্রয়ের হিসাব</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>তারিখ (Date)</th>
                            <th style="text-align: center;">মোট অর্ডার</th>
                            <th style="text-align: center;">ডেলিভারি সম্পন্ন</th>
                            <th style="text-align: right;">ডেলিভারিকৃত বিক্রয় (৳)</th>
                            <th style="text-align: right;">সর্বমোট অর্ডার মূল্য (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['daily_sales'] as $day)
                            <tr>
                                <td style="font-weight: 700;">{{ $day['date'] }}</td>
                                <td style="text-align: center;">{{ $day['orders_count'] }} টি</td>
                                <td style="text-align: center; color: #059669; font-weight: 700;">{{ $day['delivered_count'] }} টি</td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">৳{{ number_format($day['delivered_amount'], 2) }}</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($day['total_amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 1.5rem;">নির্বাচিত তারিখে কোনো অর্ডার পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Filtered Orders Detail Table --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">বিস্তারিত অর্ডার তালিকা (Filtered Order List)</h3>
                    <span class="aq-card-subtitle">মোট প্রদর্শিত অর্ডার: {{ count($report['orders']) }} টি</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>অর্ডার নং</th>
                            <th>তারিখ ও সময়</th>
                            <th>গ্রাহকের নাম ও ফোন</th>
                            <th>শহর</th>
                            <th style="text-align: center;">আইটেম</th>
                            <th>পেমেন্ট মেথড</th>
                            <th>স্ট্যাটাস</th>
                            <th style="text-align: right;">মোট টাকা (৳)</th>
                            <th style="text-align: center;">ইনভয়েস</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['orders'] as $order)
                            <tr>
                                <td style="font-weight: 700; color: #0b2b22;">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $order->customer_name }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $order->customer_phone }}</div>
                                </td>
                                <td>{{ $order->city }}</td>
                                <td style="text-align: center;">{{ $order->items_count }} টি</td>
                                <td><span class="aq-badge aq-badge-gray">{{ strtoupper($order->payment_method) }}</span></td>
                                <td>
                                    <span class="aq-badge {{ match($order->status) {
                                        'delivered' => 'aq-badge-success',
                                        'pending' => 'aq-badge-warning',
                                        'cancelled' => 'aq-badge-danger',
                                        default => 'aq-badge-info'
                                    } }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($order->total, 2) }}</td>
                                <td style="text-align: center;">
                                    <a href="{{ route('orders.invoice.download', $order) }}" target="_blank" style="color: #059669; font-weight: 700; text-decoration: none;">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; color: #64748b; padding: 1.5rem;">কোনো অর্ডার পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
