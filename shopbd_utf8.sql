-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: shopbd
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('shopbd-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3','i:1;',1778431663),('shopbd-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer','i:1778431663;',1778431663);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'স্মার্টফোন ও গ্যাজেট','gadgets','2026-05-10 09:33:19','2026-05-10 09:33:19'),(2,'ফ্যাশন ও লাইফস্টাইল','fashion','2026-05-10 09:33:19','2026-05-10 09:33:19'),(3,'বই ও স্টেশনারি','books','2026-05-10 09:33:19','2026-05-10 09:33:19'),(4,'হোম অ্যাপ্লায়েন্স','home-appliances','2026-05-10 09:33:19','2026-05-10 09:33:19'),(5,'সৌন্দর্য ও যত্ন','beauty','2026-05-10 09:33:19','2026-05-10 09:33:19'),(6,'স্বাস্থ্য ও সুরক্ষা','health','2026-05-10 09:33:19','2026-05-10 09:33:19');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000001_create_products_table',1),(5,'2024_01_01_000002_create_orders_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,4,'মেকানিক্যাল গেমিং কিবোর্ড',2800.00,1,2800.00,'2026-05-10 10:48:07','2026-05-10 10:48:07');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Bangladesh',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `payment_status` enum('unpaid','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'ORD-6A00B6C721B51','processing',2800.00,0.00,2800.00,'Admin','admin@shopbd.com','01913162085','রাজশাহী','রাজশাহী',NULL,'Bangladesh','cod','unpaid',NULL,'2026-05-10 10:48:07','2026-05-10 10:56:02');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'প্রিমিয়াম ওয়্যারলেস হেডফোন','primiyam-wzarles-hedfon','নয়েজ ক্যানসেলেশন সহ অসাধারণ সাউন্ড কোয়ালিটি। ১০ ঘণ্টা ব্যাটারি ব্যাকআপ।',2500.00,1999.00,25,'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(2,1,'স্মার্ট ফিটনেস ব্যান্ড Pro','smart-fitnes-bzand-pro','হার্ট রেট এবং স্লিপ ট্র্যাকিং সহ ফুল টাচ ডিসপ্লে। ওয়াটারপ্রুফ বডি।',1800.00,NULL,50,'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(3,1,'ফাস্ট চার্জিং পাওয়ার ব্যাংক ২০০০০mAh','fast-carjing-pawar-bzangk-20000mah','একসাথে তিনটি ডিভাইস চার্জ করার সুবিধা। ডিজিটাল ডিসপ্লে যুক্ত।',1500.00,1250.00,40,'https://images.unsplash.com/photo-1609592424089-9890699b660c?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(4,1,'মেকানিক্যাল গেমিং কিবোর্ড','mekanikzal-geming-kibord','RGB লাইটিং এবং স্মুথ টাইপিং এক্সপেরিয়েন্স। গেমারদের জন্য সেরা পছন্দ।',3200.00,2800.00,14,'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 10:48:07'),(5,2,'প্রিমিয়াম কটন পাঞ্জাবি - নীল','primiyam-ktn-panjabi-neel','উৎসবের আমেজে সেরা কোয়ালিটির কটন পাঞ্জাবি। আরামদায়ক এবং স্টাইলিশ।',1200.00,950.00,30,'https://images.unsplash.com/photo-1597983073493-88cd35cf93b0?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(6,2,'ক্যাজুয়াল ডেনিম শার্ট','kzajuyal-denim-sart','মডার্ন ফিটিং ডেনিম শার্ট। সব ঋতুতে ব্যবহারের উপযোগী।',1500.00,NULL,20,'https://images.unsplash.com/photo-1541101767792-f9b2b1c4f127?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(7,2,'লেদার মানিব্যাগ - কালো','ledar-manibzag-kalo','১০০% খাঁটি চামড়ার মানিব্যাগ। দীর্ঘস্থায়ী এবং মজবুত।',850.00,699.00,100,'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(8,3,'লারাভেল ১০ মাস্টারি গাইড','laravel-10-mastari-gaid','বাংলায় লারাভেল ফ্রেমওয়ার্ক শেখার জন্য সেরা বই। স্টেপ বাই স্টেপ গাইড।',450.00,380.00,200,'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(9,3,'প্রোগ্রামিংয়ের হাতেখড়ি (পাইথন)','programingyer-hatekhri-paithn','নতুনদের জন্য সহজ বাংলায় পাইথন শেখার বই।',350.00,NULL,150,'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(10,4,'ইলেকট্রিক রাইস কুকার ২.৮ লিটার','ilektrik-rais-kukar-28-litar','অটোমেটিক কুকিং এবং ওয়ার্মিং সিস্টেম। দীর্ঘস্থায়ী অ্যালুমিনিয়াম পট।',2800.00,2450.00,12,'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(11,4,'স্মার্ট এয়ার ফ্রায়ার','smart-ezar-frazar','তেল ছাড়া স্বাস্থ্যকর খাবার রান্নার জন্য আধুনিক এয়ার ফ্রায়ার।',8500.00,7900.00,5,'https://images.unsplash.com/photo-1626075107426-514751574cbd?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(12,5,'অর্গানিক ফেস ওয়াশ','organik-fes-was','সব ধরনের ত্বকের জন্য উপযোগী ভেষজ উপাদানে তৈরি ফেস ওয়াশ।',550.00,450.00,60,'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(13,5,'প্রিমিয়াম পারফিউম - এম্বার গোল্ড','primiyam-parfium-embar-gold','দীর্ঘস্থায়ী এবং সতেজ ঘ্রাণ। আভিজাত্য প্রকাশে সেরা পারফিউম।',1800.00,1500.00,18,'https://images.unsplash.com/photo-1541643600914-78b084683601?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(14,1,'স্মার্ট হোম ক্যামেরা (WiFi)','smart-hom-kzamera-wifi','ফুল এইচডি রেজোলিউশন এবং নাইট ভিশন সুবিধা। ফোনের মাধ্যমে কন্ট্রোল করা যায়।',2200.00,1850.00,22,'https://images.unsplash.com/photo-1557324232-b8917d3c3dcb?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(15,2,'স্নিকার্স জুতো - স্পোর্টস এডিশন','snikars-juto-sports-edisn','হালকা ও টেকসই ম্যাটেরিয়াল। রানিং এবং ক্যাজুয়াল ব্যবহারের জন্য উপযুক্ত।',2500.00,2100.00,15,'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(16,4,'ইউএসবি রিচার্জেবল মিনি ফ্যান','iuesbi-ricarjebl-mini-fzan','ভ্রমণের জন্য সেরা পোর্টেবল ফ্যান। শক্তিশালী বাতাস এবং দীর্ঘস্থায়ী ব্যাটারি।',850.00,NULL,45,'https://images.unsplash.com/photo-1591123120675-6f7f1aae0e5b?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(17,1,'সোলার পাওয়ার ব্যাংক ১০০০০mAh','solar-paoozar-bzangk-10000mah','সূর্যের আলো থেকে চার্জ হওয়ার সুবিধা। ক্যাম্পিংয়ের জন্য উপযুক্ত।',1200.00,999.00,30,'https://images.unsplash.com/photo-1620288627223-53302f4e8c74?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19'),(18,5,'মেকআপ সেট - প্রো বক্স','mekap-set-pro-bks','সব প্রয়োজনীয় মেকআপ আইটেম এক বক্সে। হাই কোয়ালিটি প্রোডাক্ট।',3500.00,2950.00,10,'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&q=80',1,'2026-05-10 09:33:19','2026-05-10 09:33:19');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('S8D24G3C6pvC2dKe35tM3BwxkuJNZCw9EqeWdVYi',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0','eyJfdG9rZW4iOiJ1ZG1JbzFJUjJFUEk3TFBDWnRyaVM3Rno1NnRJTVl3YWh4cEhXQk1QIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiY2FydCI6eyIxMCI6eyJpZCI6MTAsIm5hbWUiOiJcdTA5ODdcdTA5YjJcdTA5YzdcdTA5OTVcdTA5OWZcdTA5Y2RcdTA5YjBcdTA5YmZcdTA5OTUgXHUwOWIwXHUwOWJlXHUwOTg3XHUwOWI4IFx1MDk5NVx1MDljMVx1MDk5NVx1MDliZVx1MDliMCBcdTA5ZTguXHUwOWVlIFx1MDliMlx1MDliZlx1MDk5Zlx1MDliZVx1MDliMCIsInByaWNlIjoyNDUwLCJpbWFnZSI6Imh0dHBzOlwvXC9pbWFnZXMudW5zcGxhc2guY29tXC9waG90by0xNTg0MjY5NjAwNDY0LTM3YjFiNThhOWZlNz93PTUwMCZxPTgwIiwicXVhbnRpdHkiOjIsInN1YnRvdGFsIjo0OTAwfX19',1778432626);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test User','test@example.com','2026-05-10 09:33:03','$2y$12$swa/sMvdbXPs4tqqAPet..x.7Q4U2woHarl4WBvqXqvMzJdDjtGQ6','UDOfzMCMo2','2026-05-10 09:33:03','2026-05-10 09:33:03'),(2,'Admin','admin@shopbd.com',NULL,'$2y$12$wN5HGATEjhreVUWVsKlFzOfmKjchUJTXKyWEfCc8oBgUPXffmmvVa',NULL,'2026-05-10 10:41:48','2026-05-10 10:46:37');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-10 23:21:01
