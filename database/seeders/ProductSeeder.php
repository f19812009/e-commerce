<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'স্মার্টফোন ও গ্যাজেট', 'slug' => 'gadgets'],
            ['name' => 'ফ্যাশন ও লাইফস্টাইল', 'slug' => 'fashion'],
            ['name' => 'বই ও স্টেশনারি',    'slug' => 'books'],
            ['name' => 'হোম অ্যাপ্লায়েন্স',   'slug' => 'home-appliances'],
            ['name' => 'সৌন্দর্য ও যত্ন',    'slug' => 'beauty'],
            ['name' => 'স্বাস্থ্য ও সুরক্ষা',  'slug' => 'health'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $cats = Category::all()->keyBy('slug');

        $products = [
            // Gadgets
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'প্রিমিয়াম ওয়্যারলেস হেডফোন',
                'price' => 2500, 'sale_price' => 1999, 'stock' => 25,
                'description' => 'নয়েজ ক্যানসেলেশন সহ অসাধারণ সাউন্ড কোয়ালিটি। ১০ ঘণ্টা ব্যাটারি ব্যাকআপ।',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'
            ],
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'স্মার্ট ফিটনেস ব্যান্ড Pro',
                'price' => 1800, 'sale_price' => null, 'stock' => 50,
                'description' => 'হার্ট রেট এবং স্লিপ ট্র্যাকিং সহ ফুল টাচ ডিসপ্লে। ওয়াটারপ্রুফ বডি।',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80'
            ],
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'ফাস্ট চার্জিং পাওয়ার ব্যাংক ২০০০০mAh',
                'price' => 1500, 'sale_price' => 1250, 'stock' => 40,
                'description' => 'একসাথে তিনটি ডিভাইস চার্জ করার সুবিধা। ডিজিটাল ডিসপ্লে যুক্ত।',
                'image' => 'https://images.unsplash.com/photo-1609592424089-9890699b660c?w=500&q=80'
            ],
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'মেকানিক্যাল গেমিং কিবোর্ড',
                'price' => 3200, 'sale_price' => 2800, 'stock' => 15,
                'description' => 'RGB লাইটিং এবং স্মুথ টাইপিং এক্সপেরিয়েন্স। গেমারদের জন্য সেরা পছন্দ।',
                'image' => 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500&q=80'
            ],

            // Fashion
            [
                'category_id' => $cats['fashion']->id,
                'name' => 'প্রিমিয়াম কটন পাঞ্জাবি - নীল',
                'price' => 1200, 'sale_price' => 950, 'stock' => 30,
                'description' => 'উৎসবের আমেজে সেরা কোয়ালিটির কটন পাঞ্জাবি। আরামদায়ক এবং স্টাইলিশ।',
                'image' => 'https://images.unsplash.com/photo-1597983073493-88cd35cf93b0?w=500&q=80'
            ],
            [
                'category_id' => $cats['fashion']->id,
                'name' => 'ক্যাজুয়াল ডেনিম শার্ট',
                'price' => 1500, 'sale_price' => null, 'stock' => 20,
                'description' => 'মডার্ন ফিটিং ডেনিম শার্ট। সব ঋতুতে ব্যবহারের উপযোগী।',
                'image' => 'https://images.unsplash.com/photo-1541101767792-f9b2b1c4f127?w=500&q=80'
            ],
            [
                'category_id' => $cats['fashion']->id,
                'name' => 'লেদার মানিব্যাগ - কালো',
                'price' => 850, 'sale_price' => 699, 'stock' => 100,
                'description' => '১০০% খাঁটি চামড়ার মানিব্যাগ। দীর্ঘস্থায়ী এবং মজবুত।',
                'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&q=80'
            ],

            // Books
            [
                'category_id' => $cats['books']->id,
                'name' => 'লারাভেল ১০ মাস্টারি গাইড',
                'price' => 450, 'sale_price' => 380, 'stock' => 200,
                'description' => 'বাংলায় লারাভেল ফ্রেমওয়ার্ক শেখার জন্য সেরা বই। স্টেপ বাই স্টেপ গাইড।',
                'image' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=500&q=80'
            ],
            [
                'category_id' => $cats['books']->id,
                'name' => 'প্রোগ্রামিংয়ের হাতেখড়ি (পাইথন)',
                'price' => 350, 'sale_price' => null, 'stock' => 150,
                'description' => 'নতুনদের জন্য সহজ বাংলায় পাইথন শেখার বই।',
                'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=500&q=80'
            ],

            // Home Appliances
            [
                'category_id' => $cats['home-appliances']->id,
                'name' => 'ইলেকট্রিক রাইস কুকার ২.৮ লিটার',
                'price' => 2800, 'sale_price' => 2450, 'stock' => 12,
                'description' => 'অটোমেটিক কুকিং এবং ওয়ার্মিং সিস্টেম। দীর্ঘস্থায়ী অ্যালুমিনিয়াম পট।',
                'image' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=500&q=80'
            ],
            [
                'category_id' => $cats['home-appliances']->id,
                'name' => 'স্মার্ট এয়ার ফ্রায়ার',
                'price' => 8500, 'sale_price' => 7900, 'stock' => 5,
                'description' => 'তেল ছাড়া স্বাস্থ্যকর খাবার রান্নার জন্য আধুনিক এয়ার ফ্রায়ার।',
                'image' => 'https://images.unsplash.com/photo-1626075107426-514751574cbd?w=500&q=80'
            ],

            // Beauty
            [
                'category_id' => $cats['beauty']->id,
                'name' => 'অর্গানিক ফেস ওয়াশ',
                'price' => 550, 'sale_price' => 450, 'stock' => 60,
                'description' => 'সব ধরনের ত্বকের জন্য উপযোগী ভেষজ উপাদানে তৈরি ফেস ওয়াশ।',
                'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=500&q=80'
            ],
            [
                'category_id' => $cats['beauty']->id,
                'name' => 'প্রিমিয়াম পারফিউম - এম্বার গোল্ড',
                'price' => 1800, 'sale_price' => 1500, 'stock' => 18,
                'description' => 'দীর্ঘস্থায়ী এবং সতেজ ঘ্রাণ। আভিজাত্য প্রকাশে সেরা পারফিউম।',
                'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=500&q=80'
            ],
            
            // Adding more to fill up
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'স্মার্ট হোম ক্যামেরা (WiFi)',
                'price' => 2200, 'sale_price' => 1850, 'stock' => 22,
                'description' => 'ফুল এইচডি রেজোলিউশন এবং নাইট ভিশন সুবিধা। ফোনের মাধ্যমে কন্ট্রোল করা যায়।',
                'image' => 'https://images.unsplash.com/photo-1557324232-b8917d3c3dcb?w=500&q=80'
            ],
            [
                'category_id' => $cats['fashion']->id,
                'name' => 'স্নিকার্স জুতো - স্পোর্টস এডিশন',
                'price' => 2500, 'sale_price' => 2100, 'stock' => 15,
                'description' => 'হালকা ও টেকসই ম্যাটেরিয়াল। রানিং এবং ক্যাজুয়াল ব্যবহারের জন্য উপযুক্ত।',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80'
            ],
            [
                'category_id' => $cats['home-appliances']->id,
                'name' => 'ইউএসবি রিচার্জেবল মিনি ফ্যান',
                'price' => 850, 'sale_price' => null, 'stock' => 45,
                'description' => 'ভ্রমণের জন্য সেরা পোর্টেবল ফ্যান। শক্তিশালী বাতাস এবং দীর্ঘস্থায়ী ব্যাটারি।',
                'image' => 'https://images.unsplash.com/photo-1591123120675-6f7f1aae0e5b?w=500&q=80'
            ],
            [
                'category_id' => $cats['gadgets']->id,
                'name' => 'সোলার পাওয়ার ব্যাংক ১০০০০mAh',
                'price' => 1200, 'sale_price' => 999, 'stock' => 30,
                'description' => 'সূর্যের আলো থেকে চার্জ হওয়ার সুবিধা। ক্যাম্পিংয়ের জন্য উপযুক্ত।',
                'image' => 'https://images.unsplash.com/photo-1620288627223-53302f4e8c74?w=500&q=80'
            ],
            [
                'category_id' => $cats['beauty']->id,
                'name' => 'মেকআপ সেট - প্রো বক্স',
                'price' => 3500, 'sale_price' => 2950, 'stock' => 10,
                'description' => 'সব প্রয়োজনীয় মেকআপ আইটেম এক বক্সে। হাই কোয়ালিটি প্রোডাক্ট।',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&q=80'
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                array_merge($p, [
                    'slug' => Str::slug($p['name']),
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('✅ ' . count($products) . ' টি ডেমো প্রোডাক্ট এবং ' . count($categories) . ' টি ক্যাটাগরি তৈরি হয়েছে।');
    }
}
