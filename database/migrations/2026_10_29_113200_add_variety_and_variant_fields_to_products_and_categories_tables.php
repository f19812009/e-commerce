<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'variety_type')) {
                $table->string('variety_type')->default('general')->after('slug');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('general')->after('category_id');
            }
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('pcs')->nullable()->after('low_stock_threshold');
            }
            if (!Schema::hasColumn('products', 'warranty')) {
                $table->string('warranty')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('products', 'gallery')) {
                $table->json('gallery')->nullable()->after('image');
            }
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('gallery');
            }
            if (!Schema::hasColumn('products', 'variants')) {
                $table->json('variants')->nullable()->after('specifications');
            }
        });

        // Auto-map existing categories to their appropriate variety_type
        $categories = DB::table('categories')->get();
        foreach ($categories as $cat) {
            $haystack = strtolower($cat->name . ' ' . $cat->slug);
            $type = 'general';

            if (str_contains($haystack, 'phone') || str_contains($haystack, 'tablet') || str_contains($haystack, 'ipad') || str_contains($haystack, 'mobile')) {
                $type = 'phone_tablet';
            } elseif (str_contains($haystack, 'laptop') || str_contains($haystack, 'computer') || str_contains($haystack, 'component') || str_contains($haystack, 'pc')) {
                $type = 'laptop_pc';
            } elseif (str_contains($haystack, 'monitor') || str_contains($haystack, 'tv') || str_contains($haystack, 'smart home') || str_contains($haystack, 'display')) {
                $type = 'monitor_tv';
            } elseif (str_contains($haystack, 'audio') || str_contains($haystack, 'watch') || str_contains($haystack, 'camera') || str_contains($haystack, 'drone') || str_contains($haystack, 'gaming') || str_contains($haystack, 'gadget')) {
                $type = 'audio_gadget';
            } elseif (str_contains($haystack, 'fashion') || str_contains($haystack, 'cloth') || str_contains($haystack, 'shoe')) {
                $type = 'fashion';
            } elseif (str_contains($haystack, 'book') || str_contains($haystack, 'stationery')) {
                $type = 'book';
            } elseif (str_contains($haystack, 'beauty') || str_contains($haystack, 'health') || str_contains($haystack, 'food') || str_contains($haystack, 'grocery')) {
                $type = 'grocery_beauty';
            } elseif (str_contains($haystack, 'appliance')) {
                $type = 'appliance';
            }

            DB::table('categories')->where('id', $cat->id)->update(['variety_type' => $type]);
            DB::table('products')->where('category_id', $cat->id)->update(['product_type' => $type]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_type',
                'brand',
                'unit',
                'warranty',
                'gallery',
                'specifications',
                'variants',
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('variety_type');
        });
    }
};
