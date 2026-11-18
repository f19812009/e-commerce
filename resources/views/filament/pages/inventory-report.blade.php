<x-filament-panels::page>
    @php
        $s = $report['summary'];
        $exportParams = [
            'category_id'  => $this->categoryId,
            'stock_status' => $this->stockStatus,
            'start_date'   => $this->startDate,
            'end_date'     => $this->endDate,
        ];
    @endphp

    <div class="aq-report-wrapper">
        {{-- Top Report Switcher Tabs --}}
        @include('filament.pages.partials.report-tabs', ['active' => 'inventory'])

        {{-- Filter & Export Bar --}}
        <div class="aq-report-filter-bar">
            <div class="aq-filter-grid">
                <div class="aq-filter-field">
                    <label>ক্যাটাগরি (Category)</label>
                    <select wire:model.live="categoryId" class="aq-filter-input">
                        <option value="all">সকল ক্যাটাগরি (All Categories)</option>
                        @foreach($report['categories'] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="aq-filter-field">
                    <label>স্টক স্ট্যাটাস (Stock Status)</label>
                    <select wire:model.live="stockStatus" class="aq-filter-input">
                        <option value="all">সকল প্রোডাক্ট (All Products)</option>
                        <option value="in_stock">পর্যাপ্ত স্টক (Healthy Stock)</option>
                        <option value="low_stock">লো স্টক অ্যালার্ট (Low Stock)</option>
                        <option value="out_of_stock">স্টক আউট (Out of Stock)</option>
                    </select>
                </div>
                <div class="aq-filter-field">
                    <label>স্টক লগ শুরুর তারিখ</label>
                    <input type="date" wire:model.live="startDate" class="aq-filter-input" />
                </div>
                <div class="aq-filter-field">
                    <label>স্টক লগ শেষ তারিখ</label>
                    <input type="date" wire:model.live="endDate" class="aq-filter-input" />
                </div>
            </div>

            <div class="aq-preset-row">
                <div style="font-size: 0.8rem; color: #475569; font-weight: 600;">
                    স্টকের বর্তমান ভ্যালুয়েশন ও স্টক মুভমেন্ট রিপোর্ট
                </div>

                @include('filament.pages.partials.export-buttons', ['type' => 'inventory', 'params' => $exportParams])
            </div>
        </div>

        {{-- Summary KPI Cards --}}
        <div class="aq-kpi-grid">
            <div class="aq-kpi-card aq-kpi-dark">
                <div class="aq-kpi-label">মোট মজুদ ইউনিট (Total Stock Units)</div>
                <div class="aq-kpi-value">{{ number_format($s['total_stock_units']) }} টি</div>
                <div class="aq-kpi-sub">মোট প্রোডাক্ট: {{ $s['total_products'] }} টি (সক্রিয়: {{ $s['active_products'] }})</div>
            </div>

            <div class="aq-kpi-card aq-kpi-blue">
                <div class="aq-kpi-label">স্টকের ক্রয়মূল্য (Cost Valuation)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['total_cost_valuation'], 2) }}</div>
                <div class="aq-kpi-sub">গুদামে থাকা পণ্যের মোট ক্রয়মূল্য</div>
            </div>

            <div class="aq-kpi-card aq-kpi-emerald">
                <div class="aq-kpi-label">স্টকের বিক্রয়মূল্য (Retail Valuation)</div>
                <div class="aq-kpi-value">৳{{ number_format($s['total_retail_valuation'], 2) }}</div>
                <div class="aq-kpi-sub">সম্ভাব্য লাভ: ৳{{ number_format($s['potential_profit'], 2) }}</div>
            </div>

            <div class="aq-kpi-card aq-kpi-purple">
                <div class="aq-kpi-label">স্টক সতর্কতা (Low / Out of Stock)</div>
                <div class="aq-kpi-value">{{ $s['low_stock_count'] }} Low | {{ $s['out_of_stock_count'] }} Out</div>
                <div class="aq-kpi-sub">দ্রুত রিস্টক করা প্রয়োজন এমন পণ্য</div>
            </div>
        </div>

        {{-- Category-wise Stock Valuation --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">ক্যাটাগরি অনুযায়ী স্টক ভ্যালুয়েশন (Category-wise Stock Summary)</h3>
                    <span class="aq-card-subtitle">কোন ক্যাটাগরিতে কত টাকার মালামাল মজুদ আছে</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>ক্যাটাগরি (Category)</th>
                            <th style="text-align: center;">প্রোডাক্ট সংখ্যা</th>
                            <th style="text-align: center;">মোট স্টক ইউনিট</th>
                            <th style="text-align: right;">ক্রয়মূল্য ভ্যালুয়েশন (৳)</th>
                            <th style="text-align: right;">বিক্রয়মূল্য ভ্যালুয়েশন (৳)</th>
                            <th style="text-align: right;">সম্ভাব্য লাভ (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['category_valuation'] as $cat)
                            <tr>
                                <td style="font-weight: 700;">{{ $cat['category'] }}</td>
                                <td style="text-align: center;">{{ $cat['products_count'] }} টি</td>
                                <td style="text-align: center; font-weight: 700;">{{ number_format($cat['total_units']) }}</td>
                                <td style="text-align: right;">৳{{ number_format($cat['cost_valuation'], 2) }}</td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">৳{{ number_format($cat['retail_valuation'], 2) }}</td>
                                <td style="text-align: right; font-weight: 700; color: #0d9488;">৳{{ number_format($cat['expected_profit'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="text-align: center; padding: 1.25rem;">কোনো ক্যাটাগরি তথ্য নেই।</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Product-wise Stock & Valuation Table --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">প্রোডাক্ট ভিত্তিক স্টক ও ভ্যালুয়েশন তালিকা (Product Stock & Valuation)</h3>
                    <span class="aq-card-subtitle">মোট প্রদর্শিত প্রোডাক্ট: {{ count($report['products']) }} টি</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>প্রোডাক্টের নাম</th>
                            <th>SKU</th>
                            <th>ক্যাটাগরি</th>
                            <th style="text-align: center;">বর্তমান স্টক</th>
                            <th style="text-align: center;">মোট বিক্রিত</th>
                            <th style="text-align: right;">একক ক্রয়মূল্য</th>
                            <th style="text-align: right;">একক বিক্রয়মূল্য</th>
                            <th style="text-align: right;">মোট ক্রয় ভ্যালু</th>
                            <th style="text-align: right;">মোট বিক্রয় ভ্যালু</th>
                            <th>অবস্থা</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['products'] as $p)
                            @php
                                $cost = (float) ($p->cost_price ?? 0);
                                $sell = (float) $p->effective_price;
                                $qty = max(0, (int) $p->stock);
                            @endphp
                            <tr>
                                <td style="font-weight: 700;">{{ $p->name }}</td>
                                <td><span class="aq-badge aq-badge-gray">{{ $p->sku ?? 'N/A' }}</span></td>
                                <td>{{ $p->category?->name ?? '-' }}</td>
                                <td style="text-align: center; font-weight: 800;">{{ $p->stock }}</td>
                                <td style="text-align: center; color: #059669; font-weight: 700;">{{ (int) ($p->total_sold_qty ?? 0) }}</td>
                                <td style="text-align: right;">৳{{ number_format($cost, 2) }}</td>
                                <td style="text-align: right;">৳{{ number_format($sell, 2) }}</td>
                                <td style="text-align: right;">৳{{ number_format($qty * $cost, 2) }}</td>
                                <td style="text-align: right; font-weight: 700;">৳{{ number_format($qty * $sell, 2) }}</td>
                                <td>
                                    @if($p->stock <= 0)
                                        <span class="aq-badge aq-badge-danger">Stock Out</span>
                                    @elseif($p->stock <= $p->low_stock_threshold)
                                        <span class="aq-badge aq-badge-warning">Low Stock</span>
                                    @else
                                        <span class="aq-badge aq-badge-success">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; color: #64748b; padding: 1.5rem;">কোনো প্রোডাক্ট পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Stock Movements Log --}}
        <div class="aq-card">
            <div class="aq-card-header">
                <div>
                    <h3 class="aq-card-title">স্টক ইন / আউট এবং মুভমেন্ট লগ (Stock Movement History)</h3>
                    <span class="aq-card-subtitle">{{ $report['period']['start'] }} হতে {{ $report['period']['end'] }} পর্যন্ত স্টক পরিবর্তন</span>
                </div>
            </div>
            <div class="aq-table-wrap">
                <table class="aq-table">
                    <thead>
                        <tr>
                            <th>তারিখ ও সময়</th>
                            <th>প্রোডাক্ট</th>
                            <th>ধরন (Movement Type)</th>
                            <th style="text-align: center;">পরিবর্তন (Qty)</th>
                            <th style="text-align: center;">পূর্বের স্টক</th>
                            <th style="text-align: center;">বর্তমান স্টক</th>
                            <th>রেফারেন্স ও নোট</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['stock_movements'] as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                <td style="font-weight: 700;">{{ $log->product?->name ?? 'Deleted Product' }}</td>
                                <td>
                                    <span class="aq-badge {{ $log->quantity > 0 ? 'aq-badge-success' : 'aq-badge-danger' }}">
                                        {{ strtoupper($log->type) }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 800; color: {{ $log->quantity > 0 ? '#059669' : '#e11d48' }};">
                                    {{ $log->quantity > 0 ? '+' . $log->quantity : $log->quantity }}
                                </td>
                                <td style="text-align: center;">{{ $log->previous_stock }}</td>
                                <td style="text-align: center; font-weight: 700;">{{ $log->current_stock }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $log->reference ?? '-' }}</div>
                                    @if($log->note)
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $log->note }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #64748b; padding: 1.5rem;">নির্বাচিত সময়ে কোনো স্টক মুভমেন্ট লগ পাওয়া যায়নি।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
