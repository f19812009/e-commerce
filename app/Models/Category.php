<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'variety_type',
        'image',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    protected $appends = ['image_url'];

    public static function varietyOptions(): array
    {
        return [
            'phone_tablet'   => '📱 Smartphones & Tablets (ফোন ও ট্যাবলেট)',
            'laptop_pc'      => '💻 Laptops & PC Components (ল্যাপটপ ও কম্পিউটার)',
            'monitor_tv'     => '🖥️ Monitors & Smart TV (মনিটর ও টিভি)',
            'audio_gadget'   => '🎧 Audio, Watch, Camera & Gaming (অডিও, ওয়াচ ও গ্যাজেট)',
            'fashion'        => '👕 Fashion, Clothing & Shoes (ফ্যাশন ও পোশাক)',
            'book'           => '📚 Books & Stationery (বই ও স্টেশনারি)',
            'grocery_beauty' => '🍯 Food, Grocery, Health & Beauty (খাবার ও প্রসাধনী)',
            'appliance'      => '🏠 Home & Kitchen Appliances (হোম অ্যাপ্লায়েন্স)',
            'general'        => '📦 General / Multi-purpose (সাধারণ পণ্য)',
        ];
    }

    public static function inferVarietyType(string $name, string $slug = ''): string
    {
        $haystack = strtolower($name . ' ' . $slug);

        if (str_contains($haystack, 'phone') || str_contains($haystack, 'tablet') || str_contains($haystack, 'ipad') || str_contains($haystack, 'mobile')) {
            return 'phone_tablet';
        }
        if (str_contains($haystack, 'laptop') || str_contains($haystack, 'computer') || str_contains($haystack, 'component') || str_contains($haystack, 'pc')) {
            return 'laptop_pc';
        }
        if (str_contains($haystack, 'monitor') || str_contains($haystack, 'tv') || str_contains($haystack, 'smart home') || str_contains($haystack, 'display')) {
            return 'monitor_tv';
        }
        if (str_contains($haystack, 'audio') || str_contains($haystack, 'watch') || str_contains($haystack, 'camera') || str_contains($haystack, 'drone') || str_contains($haystack, 'gaming') || str_contains($haystack, 'gadget')) {
            return 'audio_gadget';
        }
        if (str_contains($haystack, 'fashion') || str_contains($haystack, 'cloth') || str_contains($haystack, 'shoe') || str_contains($haystack, 'panjabi') || str_contains($haystack, 'shirt')) {
            return 'fashion';
        }
        if (str_contains($haystack, 'book') || str_contains($haystack, 'stationery') || str_contains($haystack, 'বই')) {
            return 'book';
        }
        if (str_contains($haystack, 'beauty') || str_contains($haystack, 'health') || str_contains($haystack, 'food') || str_contains($haystack, 'grocery') || str_contains($haystack, 'honey')) {
            return 'grocery_beauty';
        }
        if (str_contains($haystack, 'appliance')) {
            return 'appliance';
        }

        return 'general';
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return '';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
