<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class DashboardSetting extends Model
{
    protected $fillable = [
        'key',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public static function defaults(): array
    {
        return [
            // 1. Theme & Layout Settings
            'preset' => 'emerald',
            'sidebar_style' => 'dark', // 'dark' | 'light'
            'sidebar_bg' => '#0b2b22',
            'sidebar_bg_end' => '#061c16',
            'sidebar_active_color' => '#34d399',
            'sidebar_group_color' => '#fbbf24',
            'primary_color' => '#059669',
            'secondary_color' => '#d97706',
            'body_bg' => '#f1f5f9',
            'hero_bg_start' => '#06211a',
            'hero_bg_mid' => '#0d3b37',
            'hero_bg_end' => '#0f5248',
            'kpi_card_style' => 'modern_white', // 'modern_white' | 'gradient_colorful' | 'minimal_outline'
            'kpi_columns' => '3', // '2' | '3' | '4'
            'card_radius' => '16px', // '8px' | '12px' | '16px' | '22px'
            'welcome_badge' => '⚡ LIVE STORE COMMAND CENTER',
            'welcome_title' => '',
            'welcome_subtitle' => 'আল-আকসা মার্টের প্রতিদিনের অর্ডার, ট্র্যাকিং, স্টক এবং লাভ-ক্ষতির স্মার্ট ড্যাশবোর্ড।',
            'show_welcome_banner' => true,
            'show_quick_actions' => true,
            'show_kpi_cards' => true,
            'show_pipeline_bar' => true,
            'show_sales_chart' => true,
            'show_order_chart' => true,
            'show_recent_orders' => true,
            'show_topbar_clock' => true,

            // 2. Company Branding, Logo & Favicon Settings
            'brand_name' => 'Al-Aqsa Mart',
            'company_primary_name' => 'AL-AQSA',
            'company_secondary_name' => 'MART',
            'company_tagline' => 'Islamic & Halal Shop',
            'site_logo' => '',     // relative storage path or full URL; falls back to /images/logo.png
            'site_favicon' => '',  // relative storage path or full URL; falls back to /images/logo.png
            'logo_height' => '68', // header logo height in px (e.g. 52, 60, 68, 76, 86)

            // 3. Topbar & Contact Information
            'topbar_hotline' => '16297',
            'topbar_hours' => 'সকাল ৯:০০ - রাত ১১:০০',
            'topbar_slogan' => '🌙 আল-আকসা ইসলামিক উদ্যোগ',
            'support_phone' => '01700-000000',
            'support_email' => 'support@alaqsamart.com',
            'company_address' => 'ঢাকা, বাংলাদেশ',

            // 4. Footer Settings
            'footer_bg_color' => '#07221f',
            'footer_description' => 'আপনার বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম। সেরা মানের খাঁটি, অর্গানিক ও হালাল পণ্য দ্রুততম সময়ে সারা বাংলাদেশে আপনার দোরগোড়ায় পৌঁছে দেওয়াই আমাদের লক্ষ্য।',
            'footer_badge_1' => '✓ ১০০% হালাল ও খাঁটি',
            'footer_badge_2' => '✓ ক্যাশ অন ডেলিভারি',
            'footer_service_1' => '৭ দিনের সহজ রিটার্ন পলিসি',
            'footer_service_2' => 'সারা বাংলাদেশে হোম ডেলিভারি',
            'footer_payment_methods' => 'Cash on Delivery, bKash, Nagad, Rocket',
            'footer_copyright' => '© ' . date('Y') . ' Al-Aqsa Mart. সর্বস্বত্ব সংরক্ষিত।',
            'footer_bottom_tagline' => '🌙 বিশ্বস্ততা ও হালাল বাণিজ্যের অঙ্গীকার',
            'facebook_url' => '',
            'youtube_url' => '',
            'whatsapp_number' => '',
        ];
    }

    public static function resolveMediaUrl(?string $path, string $fallback = '/images/logo.png'): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return asset($fallback);
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public static function logoUrl(): string
    {
        $settings = static::current();

        return static::resolveMediaUrl($settings['site_logo'] ?? '', 'images/logo.png');
    }

    public static function faviconUrl(): string
    {
        $settings = static::current();
        $fav = trim((string) ($settings['site_favicon'] ?? ''));
        if ($fav !== '') {
            return static::resolveMediaUrl($fav, 'images/logo.png');
        }

        return static::logoUrl();
    }

    public static function presets(): array
    {
        return [
            'emerald' => [
                'name' => '🌲 Al-Aqsa Emerald (ডিফল্ট গ্রিন)',
                'desc' => 'ডিপ এমেরাল্ড সাইডবার ও গোল্ডেন অ্যাকসেন্ট',
                'sidebar_style' => 'dark',
                'sidebar_bg' => '#0b2b22',
                'sidebar_bg_end' => '#061c16',
                'sidebar_active_color' => '#34d399',
                'sidebar_group_color' => '#fbbf24',
                'primary_color' => '#059669',
                'secondary_color' => '#d97706',
                'body_bg' => '#f1f5f9',
                'hero_bg_start' => '#06211a',
                'hero_bg_mid' => '#0d3b37',
                'hero_bg_end' => '#0f5248',
            ],
            'midnight_navy' => [
                'name' => '🌌 Midnight Royal Navy (রয়্যাল নেভি)',
                'desc' => 'আধুনিক কর্পোরেট ডার্ক নেভি ও ব্লু থিম',
                'sidebar_style' => 'dark',
                'sidebar_bg' => '#0f172a',
                'sidebar_bg_end' => '#090d16',
                'sidebar_active_color' => '#38bdf8',
                'sidebar_group_color' => '#93c5fd',
                'primary_color' => '#2563eb',
                'secondary_color' => '#4f46e5',
                'body_bg' => '#f1f5f9',
                'hero_bg_start' => '#0f172a',
                'hero_bg_mid' => '#1e293b',
                'hero_bg_end' => '#1d4ed8',
            ],
            'obsidian_gold' => [
                'name' => '👑 Luxury Obsidian Gold (লাক্সারি গোল্ড)',
                'desc' => 'প্রিমিয়াম চারকোল ব্ল্যাক ও গোল্ডেন থিম',
                'sidebar_style' => 'dark',
                'sidebar_bg' => '#18181b',
                'sidebar_bg_end' => '#09090b',
                'sidebar_active_color' => '#fbbf24',
                'sidebar_group_color' => '#f59e0b',
                'primary_color' => '#d97706',
                'secondary_color' => '#b45309',
                'body_bg' => '#f4f4f5',
                'hero_bg_start' => '#18181b',
                'hero_bg_mid' => '#27272a',
                'hero_bg_end' => '#78350f',
            ],
            'cyber_violet' => [
                'name' => '💜 Modern Indigo Violet (মডার্ন ভায়োলেট)',
                'desc' => 'স্মার্ট ইন্ডিগো-ভায়োলেট SaaS ড্যাশবোর্ড থিম',
                'sidebar_style' => 'dark',
                'sidebar_bg' => '#1e1b4b',
                'sidebar_bg_end' => '#0f0e26',
                'sidebar_active_color' => '#a78bfa',
                'sidebar_group_color' => '#c4b5fd',
                'primary_color' => '#7c3aed',
                'secondary_color' => '#ec4899',
                'body_bg' => '#f5f3ff',
                'hero_bg_start' => '#1e1b4b',
                'hero_bg_mid' => '#312e81',
                'hero_bg_end' => '#5b21b6',
            ],
            'ocean_teal' => [
                'name' => '🌊 Oceanic Cyan Teal (ওশান টিল)',
                'desc' => 'স্নিগ্ধ ডিপ সায়ান ও টিল অ্যাকসেন্ট থিম',
                'sidebar_style' => 'dark',
                'sidebar_bg' => '#134e4a',
                'sidebar_bg_end' => '#042f2e',
                'sidebar_active_color' => '#2dd4bf',
                'sidebar_group_color' => '#5eead4',
                'primary_color' => '#0d9488',
                'secondary_color' => '#0284c7',
                'body_bg' => '#f0fdfa',
                'hero_bg_start' => '#042f2e',
                'hero_bg_mid' => '#115e59',
                'hero_bg_end' => '#0f766e',
            ],
            'clean_light' => [
                'name' => '☀️ Clean Light Minimal (লাইট সাইডবার)',
                'desc' => 'সাদা পরিচ্ছন্ন সাইডবার ও এমেরাল্ড অ্যাকসেন্ট',
                'sidebar_style' => 'light',
                'sidebar_bg' => '#ffffff',
                'sidebar_bg_end' => '#f8fafc',
                'sidebar_active_color' => '#059669',
                'sidebar_group_color' => '#0f172a',
                'primary_color' => '#059669',
                'secondary_color' => '#d97706',
                'body_bg' => '#f8fafc',
                'hero_bg_start' => '#0d3b37',
                'hero_bg_mid' => '#065f46',
                'hero_bg_end' => '#047857',
            ],
        ];
    }

    public static function current(): array
    {
        try {
            return Cache::rememberForever('aq_dashboard_theme_settings', function () {
                if (! Schema::hasTable('dashboard_settings')) {
                    return static::defaults();
                }

                $record = static::where('key', 'default')->first();
                if (! $record || ! is_array($record->settings)) {
                    return static::defaults();
                }

                return array_merge(static::defaults(), $record->settings);
            });
        } catch (\Throwable $e) {
            return static::defaults();
        }
    }

    public static function saveSettings(array $newSettings): array
    {
        $sanitized = array_map(fn ($val) => $val === null ? '' : $val, $newSettings);
        $merged = array_merge(static::current(), $sanitized);

        static::updateOrCreate(
            ['key' => 'default'],
            ['settings' => $merged]
        );

        Cache::forget('aq_dashboard_theme_settings');

        return $merged;
    }

    public static function resetToDefaults(): array
    {
        $defaults = static::defaults();

        static::updateOrCreate(
            ['key' => 'default'],
            ['settings' => $defaults]
        );

        Cache::forget('aq_dashboard_theme_settings');

        return $defaults;
    }
}
