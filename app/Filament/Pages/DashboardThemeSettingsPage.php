<?php

namespace App\Filament\Pages;

use App\Models\DashboardSetting;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithFileUploads;

class DashboardThemeSettingsPage extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Settings & Appearance';

    protected static ?string $navigationLabel = 'Theme, Logo & Footer Settings';

    protected static ?string $title = '🎨 সাইট ব্র্যান্ডিং (Logo, Favicon, Footer) ও ড্যাশবোর্ড থিম সেটিংস';

    protected static ?string $slug = 'theme-settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dashboard-theme-settings';

    public array $form = [];

    public $logoUpload = null;

    public $faviconUpload = null;

    public function mount(): void
    {
        $this->form = DashboardSetting::current();
    }

    public function applyPreset(string $presetKey): void
    {
        $presets = DashboardSetting::presets();
        if (! isset($presets[$presetKey])) {
            return;
        }

        $presetData = $presets[$presetKey];
        unset($presetData['name'], $presetData['desc']);

        $this->form = array_merge($this->form, $presetData, ['preset' => $presetKey]);

        DashboardSetting::saveSettings($this->form);

        Notification::make()
            ->title('থিম প্রিসেট সফলভাবে প্রয়োগ করা হয়েছে!')
            ->body($presets[$presetKey]['name'])
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }

    public function clearCustomLogo(): void
    {
        $this->logoUpload = null;
        $this->form['site_logo'] = '';
        DashboardSetting::saveSettings($this->form);

        Notification::make()
            ->title('ডিফল্ট লোগো পুনরায় সেট করা হয়েছে!')
            ->info()
            ->send();
    }

    public function clearCustomFavicon(): void
    {
        $this->faviconUpload = null;
        $this->form['site_favicon'] = '';
        DashboardSetting::saveSettings($this->form);

        Notification::make()
            ->title('ডিফল্ট ফেভিকন পুনরায় সেট করা হয়েছে!')
            ->info()
            ->send();
    }

    public function saveTheme(): void
    {
        if ($this->logoUpload) {
            $path = $this->logoUpload->store('branding', 'public');
            $this->form['site_logo'] = $path;
            $this->logoUpload = null;
        }

        if ($this->faviconUpload) {
            $favPath = $this->faviconUpload->store('branding', 'public');
            $this->form['site_favicon'] = $favPath;
            $this->faviconUpload = null;
        }

        DashboardSetting::saveSettings($this->form);

        Notification::make()
            ->title('ব্র্যান্ডিং, লোগো, ফুটার ও ড্যাশবোর্ড থিম সেটিংস সংরক্ষিত হয়েছে!')
            ->success()
            ->send();

        $this->redirect(static::getUrl());
    }

    public function resetTheme(): void
    {
        $this->logoUpload = null;
        $this->faviconUpload = null;
        $this->form = DashboardSetting::resetToDefaults();

        Notification::make()
            ->title('সকল সেটিংস ডিফল্ট অবস্থায় রিসেট করা হয়েছে!')
            ->info()
            ->send();

        $this->redirect(static::getUrl());
    }

    public function getPresetsProperty(): array
    {
        return DashboardSetting::presets();
    }

    public function getCurrentLogoUrlProperty(): string
    {
        return DashboardSetting::resolveMediaUrl($this->form['site_logo'] ?? '', 'images/logo.png');
    }

    public function getCurrentFaviconUrlProperty(): string
    {
        $fav = trim((string) ($this->form['site_favicon'] ?? ''));
        if ($fav !== '') {
            return DashboardSetting::resolveMediaUrl($fav, 'images/logo.png');
        }

        return $this->currentLogoUrl;
    }
}
