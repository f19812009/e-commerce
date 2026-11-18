<div class="aq-export-btns">
    <a href="{{ route('admin.reports.pdf', array_merge(['type' => $type], $params)) }}" target="_blank" class="aq-btn aq-btn-pdf">
        <svg class="aq-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>PDF ডাউনলোড</span>
    </a>

    <a href="{{ route('admin.reports.pdf', array_merge(['type' => $type, 'stream' => 1], $params)) }}" target="_blank" class="aq-btn aq-btn-print">
        <svg class="aq-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        <span>প্রিন্ট ভিউ (Print)</span>
    </a>

    <a href="{{ route('admin.reports.csv', array_merge(['type' => $type], $params)) }}" class="aq-btn aq-btn-csv">
        <svg class="aq-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <span>CSV / Excel এক্সপোর্ট</span>
    </a>
</div>
