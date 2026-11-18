@php
    $aqTheme = \App\Models\DashboardSetting::current();
    $isLightSidebar = ($aqTheme['sidebar_style'] ?? 'dark') === 'light';
    $kpiCols = in_array((int) ($aqTheme['kpi_columns'] ?? 3), [2, 3, 4], true) ? (int) $aqTheme['kpi_columns'] : 3;
@endphp
<style>
/* ========================================================== */
/* 1. DYNAMIC CSS VARIABLES FROM DASHBOARD THEME SETTINGS     */
/* ========================================================== */
:root {
    --aq-sidebar-bg: {{ $aqTheme['sidebar_bg'] ?? '#0b2b22' }};
    --aq-sidebar-end: {{ $aqTheme['sidebar_bg_end'] ?? '#061c16' }};
    --aq-sidebar-active: {{ $aqTheme['sidebar_active_color'] ?? '#34d399' }};
    --aq-sidebar-group: {{ $aqTheme['sidebar_group_color'] ?? '#fbbf24' }};
    --aq-primary: {{ $aqTheme['primary_color'] ?? '#059669' }};
    --aq-secondary: {{ $aqTheme['secondary_color'] ?? '#d97706' }};
    --aq-body-bg: {{ $aqTheme['body_bg'] ?? '#f1f5f9' }};
    --aq-hero-start: {{ $aqTheme['hero_bg_start'] ?? '#06211a' }};
    --aq-hero-mid: {{ $aqTheme['hero_bg_mid'] ?? '#0d3b37' }};
    --aq-hero-end: {{ $aqTheme['hero_bg_end'] ?? '#0f5248' }};
    --aq-card-radius: {{ $aqTheme['card_radius'] ?? '16px' }};
    --aq-kpi-cols: {{ $kpiCols }};
}

aside.fi-sidebar,
.fi-sidebar {
    background: linear-gradient(180deg, var(--aq-sidebar-bg) 0%, var(--aq-sidebar-end) 100%) !important;
    border-right: 1px solid {{ $isLightSidebar ? '#e2e8f0' : 'rgba(255, 255, 255, 0.08)' }} !important;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05) !important;
}

/* Custom Sleek Sidebar Scrollbar */
.fi-sidebar-nav::-webkit-scrollbar,
aside.fi-sidebar *::-webkit-scrollbar {
    width: 5px !important;
    height: 5px !important;
}
.fi-sidebar-nav::-webkit-scrollbar-track,
aside.fi-sidebar *::-webkit-scrollbar-track {
    background: transparent !important;
}
.fi-sidebar-nav::-webkit-scrollbar-thumb,
aside.fi-sidebar *::-webkit-scrollbar-thumb {
    background: {{ $isLightSidebar ? 'rgba(15, 23, 42, 0.15)' : 'rgba(255, 255, 255, 0.2)' }} !important;
    border-radius: 9999px !important;
}

header.fi-sidebar-header,
.fi-sidebar header,
.fi-sidebar-header {
    background-color: var(--aq-sidebar-bg) !important;
    border-bottom: 1px solid {{ $isLightSidebar ? '#e2e8f0' : 'rgba(255, 255, 255, 0.08)' }} !important;
}

.fi-sidebar-header * {
    color: {{ $isLightSidebar ? '#0f172a' : '#ffffff' }} !important;
}

.fi-sidebar-header img.fi-logo {
    height: 3.25rem !important;
    max-width: 190px !important;
    width: auto !important;
    object-fit: contain !important;
    background: {{ $isLightSidebar ? 'transparent' : '#ffffff' }};
    padding: {{ $isLightSidebar ? '0' : '4px 8px' }};
    border-radius: 0.5rem;
}

/* Sidebar navigation links */
.fi-sidebar .fi-sidebar-item-button,
.fi-sidebar a.fi-sidebar-item-button,
aside.fi-sidebar .fi-sidebar-item-button {
    color: {{ $isLightSidebar ? '#334155' : '#cbd5e1' }} !important;
    border-radius: 0.6rem !important;
    background-color: transparent !important;
    padding: 0.52rem 0.75rem !important;
    transition: all 0.18s ease !important;
    border-left: 3px solid transparent !important;
}

.fi-sidebar .fi-sidebar-item-button span,
.fi-sidebar a.fi-sidebar-item-button span {
    color: {{ $isLightSidebar ? '#334155' : '#e2e8f0' }} !important;
    font-size: 0.84rem !important;
}

.fi-sidebar .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-group-icon,
.fi-sidebar svg {
    color: {{ $isLightSidebar ? '#64748b' : '#94a3b8' }} !important;
    transition: color 0.18s ease !important;
}

/* Hover state */
.fi-sidebar .fi-sidebar-item-button:hover,
aside.fi-sidebar .fi-sidebar-item-button:hover {
    background-color: {{ $isLightSidebar ? '#f1f5f9' : 'rgba(255, 255, 255, 0.07)' }} !important;
    color: {{ $isLightSidebar ? '#0f172a' : '#ffffff' }} !important;
    transform: translateX(2px);
}

.fi-sidebar .fi-sidebar-item-button:hover span,
.fi-sidebar .fi-sidebar-item-button:hover svg,
.fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
    color: var(--aq-sidebar-active) !important;
}

/* Active item */
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-button,
.fi-sidebar li.fi-active > a,
aside.fi-sidebar li.fi-active > a {
    background: {{ $isLightSidebar ? '#ecfdf5' : 'rgba(255, 255, 255, 0.12)' }} !important;
    border-left: 3px solid var(--aq-sidebar-active) !important;
    color: {{ $isLightSidebar ? '#0f172a' : '#ffffff' }} !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06) !important;
}

.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button span,
.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-button span,
.fi-sidebar li.fi-active > a span {
    color: {{ $isLightSidebar ? '#0f172a' : '#ffffff' }} !important;
    font-weight: 700 !important;
}

.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-icon,
.fi-sidebar li.fi-active > a svg {
    color: var(--aq-sidebar-active) !important;
}

/* Group Headers / Section Titles */
.fi-sidebar .fi-sidebar-group-label,
.fi-sidebar span.fi-sidebar-group-label {
    color: var(--aq-sidebar-group) !important;
    font-size: 0.68rem !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    opacity: 0.92 !important;
}

.fi-sidebar .fi-sidebar-group-button svg {
    color: {{ $isLightSidebar ? '#64748b' : '#94a3b8' }} !important;
}

/* Topbar Header */
header.fi-topbar,
.fi-topbar {
    background-color: rgba(255, 255, 255, 0.94) !important;
    backdrop-filter: blur(10px) !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03) !important;
}

/* Topbar Action Buttons (Visit Website & Theme) */
.aq-topbar-front-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.9rem;
    border-radius: 0.6rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: #ffffff !important;
    background: linear-gradient(135deg, var(--aq-primary) 0%, var(--aq-hero-mid) 100%);
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
    text-decoration: none;
    transition: all 0.18s ease;
    white-space: nowrap;
}

.aq-topbar-front-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
    filter: brightness(1.06);
}

.aq-topbar-theme-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.42rem 0.75rem;
    border-radius: 0.6rem;
    font-size: 0.76rem;
    font-weight: 700;
    color: #0b2b22 !important;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    text-decoration: none;
    transition: all 0.18s ease;
    white-space: nowrap;
}

.aq-topbar-theme-btn:hover {
    background: #d1fae5;
    border-color: #6ee7b7;
}

.aq-topbar-btn-icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.aq-topbar-ext-icon {
    width: 0.82rem;
    height: 0.82rem;
    opacity: 0.85;
    flex-shrink: 0;
}

.aq-topbar-btn-label {
    display: inline;
}

@media (max-width: 640px) {
    .aq-topbar-btn-label,
    .aq-topbar-ext-icon {
        display: none;
    }
    .aq-topbar-front-btn,
    .aq-topbar-theme-btn {
        padding: 0.45rem 0.6rem;
    }
}

/* Dashboard Body Background */
main.fi-main,
.fi-main {
    background-color: var(--aq-body-bg) !important;
}

/* Remove wasted top space on main Dashboard page */
.fi-page-dashboard .fi-header {
    display: none !important;
}

/* ========================================================== */
/* 2. SMART DASHBOARD OVERVIEW (HERO + MODERN KPI + PIPELINE) */
/* ========================================================== */
.aq-smart-dash {
    display: flex;
    flex-direction: column;
    gap: 1.15rem;
    margin-top: 0.25rem;
}

/* Executive Command Hero Banner */
.aq-hero-banner {
    background: linear-gradient(135deg, var(--aq-hero-start) 0%, var(--aq-hero-mid) 55%, var(--aq-hero-end) 100%);
    border-radius: var(--aq-card-radius);
    padding: 1.35rem 1.6rem;
    color: #ffffff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.14);
}

.aq-hero-banner::before {
    content: '';
    position: absolute;
    right: -40px;
    top: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.16) 0%, transparent 70%);
    pointer-events: none;
}

.aq-hero-left {
    position: relative;
    z-index: 1;
}

.aq-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #fde68a;
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    margin-bottom: 0.45rem;
}

.aq-hero-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.25;
}

.aq-hero-sub {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.85);
    margin: 0.25rem 0 0 0;
}

.aq-hero-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.6rem;
    position: relative;
    z-index: 1;
}

.aq-qbtn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.55rem 0.95rem;
    border-radius: 0.65rem;
    font-size: 0.78rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.18s ease;
    white-space: nowrap;
}

.aq-qbtn-icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.aq-qbtn-gold {
    background: var(--aq-secondary);
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.aq-qbtn-gold:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
}

.aq-qbtn-glass {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(6px);
}

.aq-qbtn-glass:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.45);
    transform: translateY(-2px);
}

/* Smart KPI Dynamic Grid */
.aq-smart-kpi-grid {
    display: grid;
    grid-template-columns: repeat(var(--aq-kpi-cols), minmax(0, 1fr));
    gap: 1rem;
}

@media (max-width: 1100px) {
    .aq-smart-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .aq-smart-kpi-grid {
        grid-template-columns: 1fr;
    }
}

.aq-s-card {
    background: #ffffff;
    border-radius: var(--aq-card-radius);
    padding: 1.15rem 1.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 0.9rem;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: all 0.2s ease;
}

.aq-s-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.1);
    border-color: #cbd5e1;
}

/* Style 1: Modern White with Top Gradient Bar */
.aq-style-modern_white.aq-s-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.aq-style-modern_white.aq-s-emerald::before { background: linear-gradient(90deg, #059669, #34d399); }
.aq-style-modern_white.aq-s-blue::before    { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.aq-style-modern_white.aq-s-teal::before    { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
.aq-style-modern_white.aq-s-purple::before  { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
.aq-style-modern_white.aq-s-rose::before    { background: linear-gradient(90deg, #e11d48, #fb7185); }
.aq-style-modern_white.aq-s-amber::before   { background: linear-gradient(90deg, #d97706, #fbbf24); }

/* Style 2: Full Gradient Colorful Cards */
.aq-style-gradient_colorful.aq-s-card {
    border: none !important;
    color: #ffffff !important;
}
.aq-style-gradient_colorful.aq-s-emerald { background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; }
.aq-style-gradient_colorful.aq-s-blue    { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; }
.aq-style-gradient_colorful.aq-s-teal    { background: linear-gradient(135deg, #0d9488 0%, #115e59 100%) !important; }
.aq-style-gradient_colorful.aq-s-purple  { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%) !important; }
.aq-style-gradient_colorful.aq-s-rose    { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important; }
.aq-style-gradient_colorful.aq-s-amber   { background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important; }

.aq-style-gradient_colorful .aq-s-label,
.aq-style-gradient_colorful .aq-s-value,
.aq-style-gradient_colorful .aq-s-meta {
    color: #ffffff !important;
}
.aq-style-gradient_colorful .aq-s-bottom {
    border-top-color: rgba(255, 255, 255, 0.2) !important;
}
.aq-style-gradient_colorful .aq-s-icon-box {
    background: rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
}
.aq-style-gradient_colorful .aq-s-pill {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

/* Style 3: Minimal Outline */
.aq-style-minimal_outline.aq-s-card {
    border: 1.5px solid #cbd5e1;
    box-shadow: none;
}

.aq-s-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
}

.aq-s-label {
    display: block;
    font-size: 0.76rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.3rem;
}

.aq-s-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    letter-spacing: -0.01em;
}

.aq-s-val-sep {
    color: #cbd5e1;
    font-weight: 400;
    margin: 0 0.15rem;
}

.aq-s-icon-box {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.aq-s-icon-box svg {
    width: 1.4rem;
    height: 1.4rem;
}

.aq-s-icon-emerald { background: #ecfdf5; color: #059669; }
.aq-s-icon-blue    { background: #eff6ff; color: #2563eb; }
.aq-s-icon-teal    { background: #f0fdfa; color: #0d9488; }
.aq-s-icon-purple  { background: #f5f3ff; color: #7c3aed; }
.aq-s-icon-rose    { background: #fff1f2; color: #e11d48; }
.aq-s-icon-amber   { background: #fffbeb; color: #d97706; }

.aq-s-bottom {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding-top: 0.65rem;
    border-top: 1px dashed #f1f5f9;
}

.aq-s-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.18rem 0.55rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
}

.aq-s-pill-emerald { background: #dcfce7; color: #15803d; }
.aq-s-pill-blue    { background: #dbeafe; color: #1d4ed8; }
.aq-s-pill-teal    { background: #ccfbf1; color: #0f766e; }
.aq-s-pill-purple  { background: #ede9fe; color: #6d28d9; }
.aq-s-pill-rose    { background: #ffe4e6; color: #be123c; }
.aq-s-pill-amber   { background: #fef3c7; color: #b45309; }

.aq-s-meta {
    font-size: 0.73rem;
    font-weight: 600;
    color: #475569;
}

/* Order Tracking Pipeline Strip */
.aq-pipeline-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: var(--aq-card-radius);
    padding: 0.75rem 1.15rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
}

.aq-pipeline-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: #0f172a;
}

.aq-pipeline-items {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
}

.aq-pipe-item {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.15s ease;
}

.aq-pipe-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.06);
}

.aq-pipe-dot {
    width: 0.45rem;
    height: 0.45rem;
    border-radius: 50%;
}

.aq-pipe-amber   { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.aq-pipe-amber .aq-pipe-dot { background: #f59e0b; }

.aq-pipe-blue    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.aq-pipe-blue .aq-pipe-dot { background: #3b82f6; }

.aq-pipe-indigo  { background: #eef2ff; color: #3730a3; border-color: #c7d2fe; }
.aq-pipe-indigo .aq-pipe-dot { background: #6366f1; }

.aq-pipe-emerald { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
.aq-pipe-emerald .aq-pipe-dot { background: #10b981; }

.aq-pipe-rose    { background: #fff1f2; color: #9f1239; border-color: #fecdd3; }
.aq-pipe-rose .aq-pipe-dot { background: #f43f5e; }

/* ========================================================== */
/* 3. DASHBOARD CHARTS & TABLES REFINEMENT                    */
/* ========================================================== */
.fi-wi-chart,
.fi-wi-table {
    border-radius: var(--aq-card-radius) !important;
    overflow: hidden !important;
    box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.04) !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
}

.fi-wi-chart .fi-section-header,
.fi-wi-table .fi-section-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 0.9rem 1.25rem !important;
}

.fi-wi-chart .fi-section-header-heading,
.fi-wi-table .fi-section-header-heading {
    font-size: 0.95rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
}

/* ========================================================== */
/* 4. ADMIN REPORTS & ANALYTICS MODULE STYLES                 */
/* ========================================================== */
.aq-report-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.aq-report-tabs-bar {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.75rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.875rem;
    padding: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

@media (max-width: 1024px) {
    .aq-report-tabs-bar {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .aq-report-tabs-bar {
        grid-template-columns: 1fr;
    }
}

.aq-report-tab {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.9rem;
    border-radius: 0.65rem;
    text-decoration: none;
    color: #334155;
    background-color: #f8fafc;
    border: 1px solid transparent;
    transition: all 0.15s ease;
}

.aq-report-tab:hover {
    background-color: #ecfdf5;
    border-color: #a7f3d0;
    color: #0b2b22;
}

.aq-report-tab.active {
    background: linear-gradient(135deg, var(--aq-hero-start) 0%, var(--aq-hero-end) 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.18);
}

.aq-tab-icon {
    width: 1.35rem;
    height: 1.35rem;
    flex-shrink: 0;
    color: var(--aq-primary);
}

.aq-report-tab.active .aq-tab-icon {
    color: var(--aq-sidebar-active);
}

.aq-report-tab-text {
    display: flex;
    flex-direction: column;
    line-height: 1.25;
    overflow: hidden;
}

.aq-report-tab-title {
    font-size: 0.82rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.aq-report-tab-sub {
    font-size: 0.72rem;
    opacity: 0.8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Filter Bar */
.aq-report-filter-bar {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.875rem;
    padding: 1.15rem 1.25rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}

.aq-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.aq-filter-field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.aq-filter-field label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.02em;
}

.aq-filter-input {
    width: 100% !important;
    height: 2.5rem !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 0.5rem !important;
    padding: 0.45rem 0.75rem !important;
    font-size: 0.85rem !important;
    color: #0f172a !important;
    background-color: #f8fafc !important;
    box-shadow: none !important;
    outline: none !important;
    transition: border-color 0.15s ease, background-color 0.15s ease !important;
}

.aq-filter-input:focus {
    border-color: var(--aq-primary) !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.15) !important;
}

select.aq-filter-input {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-color: #f8fafc !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.75' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
    background-position: right 0.7rem center !important;
    background-repeat: no-repeat !important;
    background-size: 1.15rem 1.15rem !important;
    padding-right: 2.25rem !important;
}

select.aq-filter-input:focus {
    background-color: #ffffff !important;
}

.aq-preset-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 0.9rem;
    border-top: 1px dashed #e2e8f0;
}

.aq-preset-btns,
.aq-export-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: center;
}

.aq-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.85rem;
    border-radius: 0.5rem;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    line-height: 1.2;
}

.aq-btn-icon {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.aq-btn-preset {
    background-color: #f1f5f9;
    color: #334155;
    border-color: #e2e8f0;
}

.aq-btn-preset:hover,
.aq-btn-preset.active {
    background-color: var(--aq-primary);
    color: #ffffff;
    border-color: var(--aq-primary);
}

.aq-btn-pdf {
    background-color: var(--aq-primary);
    color: #ffffff !important;
}

.aq-btn-pdf:hover {
    filter: brightness(0.92);
}

.aq-btn-print {
    background-color: #0f172a;
    color: #ffffff !important;
}

.aq-btn-print:hover {
    background-color: #1e293b;
}

.aq-btn-csv {
    background-color: #2563eb;
    color: #ffffff !important;
}

.aq-btn-csv:hover {
    background-color: #1d4ed8;
}

/* KPI Grid - 4 Columns on Desktop (For Reports Pages) */
.aq-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
}

@media (max-width: 1100px) {
    .aq-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .aq-kpi-grid {
        grid-template-columns: 1fr;
    }
}

.aq-kpi-card {
    border-radius: var(--aq-card-radius);
    padding: 1.2rem 1.25rem;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.06);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 122px;
}

.aq-kpi-card::after {
    content: '';
    position: absolute;
    right: -18px;
    top: -18px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    pointer-events: none;
}

.aq-kpi-emerald { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
.aq-kpi-teal    { background: linear-gradient(135deg, #0d9488 0%, #115e59 100%); }
.aq-kpi-blue    { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.aq-kpi-rose    { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }
.aq-kpi-amber   { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.aq-kpi-purple  { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); }
.aq-kpi-dark    { background: linear-gradient(135deg, #0b2b22 0%, #185243 100%); }

.aq-kpi-label {
    font-size: 0.78rem;
    font-weight: 700;
    opacity: 0.92;
    line-height: 1.3;
}

.aq-kpi-value {
    font-size: 1.55rem;
    font-weight: 800;
    margin: 0.35rem 0;
    line-height: 1.15;
}

.aq-kpi-sub {
    font-size: 0.75rem;
    opacity: 0.9;
}

/* 2-Column Section Grid */
.aq-section-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.25rem;
}

@media (max-width: 900px) {
    .aq-section-grid-2 {
        grid-template-columns: 1fr;
    }
}

.aq-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: var(--aq-card-radius);
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}

.aq-card-header {
    padding: 0.95rem 1.25rem;
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.aq-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.aq-card-subtitle {
    font-size: 0.75rem;
    color: #64748b;
}

.aq-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.aq-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.84rem;
}

.aq-table th {
    background-color: var(--aq-hero-start);
    color: #ffffff;
    font-weight: 600;
    text-align: left;
    padding: 0.65rem 1rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    white-space: nowrap;
}

.aq-table td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
}

.aq-table tr:hover td {
    background-color: #f8fafc;
}

.aq-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.18rem 0.55rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.aq-badge-success { background-color: #dcfce7; color: #15803d; }
.aq-badge-warning { background-color: #fef3c7; color: #b45309; }
.aq-badge-danger  { background-color: #ffe4e6; color: #be123c; }
.aq-badge-info    { background-color: #dbeafe; color: #1d4ed8; }
.aq-badge-gray    { background-color: #f1f5f9; color: #475569; }

@media print {
    aside.fi-sidebar,
    header.fi-topbar,
    .aq-report-tabs-bar,
    .aq-report-filter-bar {
        display: none !important;
    }
    main.fi-main {
        background: #ffffff !important;
        padding: 0 !important;
    }
}
</style>
