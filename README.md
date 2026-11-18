# 🛍 ShopBD — Laravel ইকমার্স প্রোজেক্ট

একটি সম্পূর্ণ বাংলাদেশি ইকমার্স ওয়েবসাইট — প্রোডাক্ট লিস্ট, কার্ট এবং চেকআউট সহ।

---

## 📁 ফাইল কাঠামো

```
app/
├── Http/Controllers/
│   ├── ProductController.php   ← পণ্য তালিকা ও বিস্তারিত
│   ├── CartController.php      ← কার্ট যোগ/সরানো/আপডেট
│   ├── CheckoutController.php  ← অর্ডার প্লেস করা
│   └── AuthController.php      ← লগইন/রেজিস্টার
├── Models/
│   ├── Product.php             ← পণ্য মডেল + accessors
│   ├── Order.php               ← অর্ডার মডেল
│   └── Category.php            ← ক্যাটাগরি মডেল
└── Services/
    └── CartService.php         ← সেশন-ভিত্তিক কার্ট লজিক

database/
├── migrations/
│   ├── ..._create_products_table.php
│   └── ..._create_orders_table.php
└── seeders/
    └── ProductSeeder.php       ← ডেমো পণ্য ও ক্যাটাগরি

resources/views/
├── layouts/app.blade.php       ← মেইন লেআউট (Navbar, Footer)
├── products/
│   ├── index.blade.php         ← পণ্য তালিকা (ফিল্টার + সর্ট)
│   └── show.blade.php          ← পণ্য বিস্তারিত
├── cart/
│   └── index.blade.php         ← কার্ট পেজ
└── checkout/
    ├── index.blade.php         ← চেকআউট ফর্ম
    └── success.blade.php       ← অর্ডার সফল পেজ

routes/web.php                  ← সব রাউট
```

---

## ⚙️ ইনস্টলেশন (ধাপে ধাপে)

### ১. নতুন Laravel প্রোজেক্ট তৈরি করুন
```bash
composer create-project laravel/laravel shopbd
cd shopbd
```

### ২. এই প্রোজেক্টের ফাইলগুলো কপি করুন
উপরের ফাইল কাঠামো অনুযায়ী প্রতিটি ফাইল সঠিক জায়গায় রাখুন।

### ৩. `.env` কনফিগার করুন
```env
APP_NAME=ShopBD
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopbd
DB_USERNAME=root
DB_PASSWORD=your_password
```

### ৪. MySQL ডেটাবেস তৈরি করুন
```sql
CREATE DATABASE shopbd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### ৫. মাইগ্রেশন ও সিডার চালান
```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

### ৬. Storage লিংক তৈরি করুন (পণ্যের ছবির জন্য)
```bash
php artisan storage:link
```

### ৭. সার্ভার চালু করুন
```bash
php artisan serve
```
এখন ব্রাউজারে যান: **http://localhost:8000**

---

## ✨ ফিচার সমূহ

| ফিচার | বিবরণ |
|-------|--------|
| 🛍 প্রোডাক্ট লিস্ট | ক্যাটাগরি ফিল্টার, সার্চ, সর্টিং, পেজিনেশন |
| 🛒 কার্ট | সেশন-ভিত্তিক, পরিমাণ আপডেট, মুছে ফেলা |
| 💳 চেকআউট | ডেলিভারি ফর্ম, COD/বিকাশ/নগদ পেমেন্ট |
| 📦 অর্ডার | স্বয়ংক্রিয় অর্ডার নম্বর, স্টক কমানো |
| 🆓 ফ্রি ডেলিভারি | ৳১০০০+ অর্ডারে ফ্রি ডেলিভারি |
| 📱 রেসপন্সিভ | মোবাইল ও ডেস্কটপ উভয়ে কাজ করে |

---

## 🔜 পরবর্তী ধাপে যোগ করতে পারো

- [ ] অ্যাডমিন প্যানেল (পণ্য যোগ/সম্পাদনা)
- [ ] বিকাশ/নগদ পেমেন্ট API ইন্টিগ্রেশন
- [ ] ইমেইল নোটিফিকেশন (Laravel Mail)
- [ ] পণ্যের রিভিউ সিস্টেম
- [ ] কুপন কোড

---

## 🛠 প্রযুক্তি

- **Backend:** Laravel 11, PHP 8.2+
- **Database:** MySQL 8
- **Frontend:** Blade + Tailwind CSS
- **Cart:** Laravel Session
