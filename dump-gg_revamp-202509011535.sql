-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: gg_revamp
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `alamats`
--

DROP TABLE IF EXISTS `alamats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alamats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_penerima` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_pos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alamats_id_customer_foreign` (`id_customer`),
  CONSTRAINT `alamats_id_customer_foreign` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alamats`
--

LOCK TABLES `alamats` WRITE;
/*!40000 ALTER TABLE `alamats` DISABLE KEYS */;
/*!40000 ALTER TABLE `alamats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`),
  KEY `idx_blog_categories_parent_id` (`parent_id`),
  CONSTRAINT `fk_blog_categories_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES ('9fc568ad-8efd-4d2f-bee3-2bbcfbae4ef3',NULL,'General','general','general',NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-31 21:48:56','2025-08-31 21:48:56');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_images`
--

DROP TABLE IF EXISTS `blog_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_images` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '6b2213e4-b987-423a-9c98-c99244948998',
  `blog_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_images_blog_id_foreign` (`blog_id`),
  CONSTRAINT `blog_images_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_images`
--

LOCK TABLES `blog_images` WRITE;
/*!40000 ALTER TABLE `blog_images` DISABLE KEYS */;
INSERT INTO `blog_images` VALUES ('11fe002a-e1e7-4e2d-89fb-7705e5b47c19','9fc5a267-7c93-487c-8c2c-0a6bf40c9169','/storage/blogs/j1VMGyzH3sIvIWHAcql3nvxgSAmkiX6E2c9olyDg.jpg',NULL,NULL,1,0,'2025-09-01 00:30:20','2025-09-01 00:30:20'),('5089ce18-1819-4615-bf4f-53b9fe50789a','9fc5a096-92dd-4ed8-93c6-5c5a8790bf7d','/storage/blogs/p7kFKJ629eLrut0htBl0jzXdetPNVtO5NGno4ItL.jpg',NULL,NULL,1,0,'2025-09-01 00:25:16','2025-09-01 00:25:16');
/*!40000 ALTER TABLE `blog_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_tag`
--

DROP TABLE IF EXISTS `blog_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_tag` (
  `blog_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`blog_id`,`tag_id`),
  KEY `blog_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `blog_tag_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_tag`
--

LOCK TABLES `blog_tag` WRITE;
/*!40000 ALTER TABLE `blog_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `blog_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ae435c56-266c-4c12-9cce-a67f4fdff2ec',
  `category_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `reading_time` int NOT NULL DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structured_data` json DEFAULT NULL,
  `status` enum('draft','published','archived','scheduled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `author_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_count` int NOT NULL DEFAULT '0',
  `share_count` int NOT NULL DEFAULT '0',
  `average_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`),
  KEY `blogs_category_id_foreign` (`category_id`),
  KEY `blogs_status_published_at_index` (`status`,`published_at`),
  FULLTEXT KEY `blogs_title_content_excerpt_fulltext` (`title`,`content`,`excerpt`),
  CONSTRAINT `blogs_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES ('9fc5a096-92dd-4ed8-93c6-5c5a8790bf7d','9fc568ad-8efd-4d2f-bee3-2bbcfbae4ef3','9 Dampak Tidur Terlalu Lama: Dari Kesuburan ke Depresi','9-dampak-tidur-terlalu-lama-dari-kesuburan-ke-depresi','9 Dampak Tidur Terlalu Lama: Dari Kesuburan ke Depresi','<p><strong style=\"color: rgb(0, 0, 0);\">Tidur Terlalu Lama -</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;Oversleeping atau tidur terlalu lama adalah kebiasaan tidur lebih dari 10 jam setiap hari.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Meskipun tidur cukup itu penting, tapi kalau kamu sudah tidur lama tapi tetap merasa lelah, malas atau sulit bangun, itu bisa jadi tanda ada masalah pada tubuh atau kesehatanmu.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Artikel ini akan membahas penyebab, dampak dan cara mengatasi kebiasaan tidur lama dengan jalan dan mudah kamu pahami.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Langsung aja kita bahas!</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Apa Penyebab Tidur Terlalu Lama?</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Terdapat 5 penyebab umum yang sering membuat seseorang tidur terlalu lama, antara lain:</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">Hutang tidur</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Coba tanya ke diri sendiri deh, apakah kamu sering&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/efek-begadang-dan-cara-mengatasi-kebiasaan-ini\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>begadang</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;atau tidur larut malam? Kalau iya, begadang bisa jadi salah satu penyebab kamu tidur terlalu lama. Kenapa?</span></p><p><span style=\"color: rgb(0, 0, 0);\">Karena, tubuhmu akan mencoba “membalasnya” dengan tidur lebih lama di hari berikutnya.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Jika kebiasaan begadang dan tidur terlalu lama ini berlangsung terus menerus, jam tidur alami tubuhmu bisa jadi kacau dan kamu makin sulit bangun pagi.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/mengenal-apa-itu-revenge-bedtime-procrastination\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>Revenge Bedtime Procrastination - Fenomena Menunda Tidur</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">Gangguan medis/ tidur</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Penyebab selanjutnya adalah karena kamu mungkin mengalami sleep apnea atau gangguan pernapasan saat tidur.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Klik&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/sleep-apnea-gejala-penyebab-dan-cara-mengatasinya\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>di sini</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk mengetahui penyebab dan cara mengatasi sleep apnea ini.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Saat kamu mengalami sleep apnea, tidurmu akan menjadi tidak nyenyak.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Akibatnya, kamu akan merasa lelah dan ingin tidur lagi bahkan sehabis bangun tidur.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/bangun-tidur-badan-terasa-lelah-penyebab-dan-cara-mengatasinya\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>Bangun Tidur Badan Lemas? Ini Penyebab dan Cara Mengatasinya</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">Depresi dan kecemasan</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Apakah kamu sedang mengalami depresi? Apakah ada satu dua hal yang bikin kamu cemas? Jika iya, ini bisa jadi penyebab kamu tidur terlalu lama.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Karena, orang yang mengalami depresi atau stres berat sering merasa tidak punya semangat untuk beraktivitas, jadi memilih tidur sebagai pelarian.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Tidur terlalu lama ini bisa jadi salah satu tanda bahwa seseorang sedang tidak baik-baik saja secara emosional.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">Obesitas &amp; diabetes</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Berat badan berlebih atau kadar gula darah yang tidak stabil bisa membuat tubuh cepat capek.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Karena tubuh terasa berat dan lelah, kamu jadi mudah ngantuk dan ingin tidur lebih lama dari biasanya.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">Obat-obatan dan alkohol</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Beberapa jenis obat, seperti obat tidur atau penenang, serta minuman beralkohol bisa bikin tubuh jadi lebih rileks dan ngantuk.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Akibatnya, kamu bisa tidur lebih lama dari biasanya dan sulit bangun dengan segar.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:&nbsp;</strong><a href=\"https://www.yuureco.co.id/blog/manfaat-minum-air-putih-sebelum-tidur\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>5 Manfaat Minum Air Putih Sebelum Tidur Malam</u></a></p><h2><strong style=\"color: rgb(0, 0, 0);\">Apa Dampak Tidur Terlalu Lama?</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Di bagian ini kita akan membahas 9 dampak tidur terlalu lama atau tidur berlebihan, antara lain:</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">1. Sakit kepala dan badan pegal</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Saat kamu tidur terlalu lama, tubuhmu mungkin akan berada di posisi yang sama sehingga otot dan sendi tidak banyak bergerak.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kondisi ini bisa membuat otot menjadi tegang, terutama di leher, bahu dan punggung, serta dapat memicu sakit kepala karena aliran darah ke otak terhambat.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/cara-mengatasi-salah-bantal\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>Cara Mengatasi Salah Bantal - Penyebab &amp; Penyembuhannya</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">2. Jam biologis tubuh terganggu</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Tidur terlalu lama membuat ritme sirkadian (jam biologis) tubuh menjadi kacau, sehingga otak bingung kapan harus merasa aktif atau mengantuk.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Hal ini menyebabkan kamu jadi ngantuk di waktu yang seharusnya produktif, seperti siang hari atau saat kerja.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/manfaat-tidur-siang\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>5 Manfaat Tidur Siang - Berapa lama dan Jam Berapa</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">3. Imunitas melemah &amp; peradangan</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Ritme sirkadian tubuh juga berperan dalam mengatur sistem kekebalan pada tubuhmu.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Ketika ritme ini terganggu, produksi hormon anti-inflamasi menurun dan risiko peradangan meningkat, membuat tubuh lebih mudah sakit karena tidur berlebihan.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">4. Kesuburan bisa terganggu</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Jam tidur yang tidak teratur dan terlalu lama bisa memengaruhi keseimbangan hormon reproduksi, seperti estrogen dan progesteron.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Hal ini dapat mengganggu siklus haid, ovulasi dan menurunkan peluang kehamilan.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/manfaat-pillow-talk-solusi-komunikasi-pasangan\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>Pillow Talk, Solusi Komunikasi Pasangan Suami Istri</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">5. Memperparah depresi dan mood labil</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Tidur berlebihan bisa mengganggu keseimbangan hormon seperti serotonin dan dopamin yang berperan dalam menjaga suasana hati.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Akibatnya, kamu bisa merasa lebih mudah sedih, tidak bersemangat, sulit menikmati aktivitas sehari-hari dan bahkan dapat memperparah depresimu.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">6. Otak jadi lemot</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Tidur berlebihan bisa menyebabkan penurunan aktivitas otak karena otak tidak cukup terstimulasi saat kamu tidur terlalu lama.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Lama-kelamaan, ini bisa memengaruhi daya ingat, fokus, dan kecepatan berpikir, mirip dengan efek penuaan dini pada otak.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">7. Risiko penyakit meningkat</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Tidur terlalu lama dikaitkan dengan gangguan metabolisme, seperti resistensi insulin dan penumpukan lemak tubuh.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Hal ini meningkatkan risiko obesitas, diabetes tipe 2, tekanan darah tinggi dan penyakit jantung.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">8. Risiko kematian dini meningkat</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Penelitian menunjukkan bahwa terlalu sering tidur lebih dari 9–10 jam per hari berkaitan dengan peningkatan risiko kematian dari berbagai penyebab.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Ini diduga karena tidur berlebihan sering terjadi bersamaan dengan masalah kesehatan kronis yang tidak terdeteksi.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">9. Penyakit jantung lebih mungkin terjadi</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Tidur terlalu lama dapat meningkatkan peradangan dalam tubuh dan memperburuk tekanan darah serta kadar kolesterol.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kondisi ini bisa memperbesar risiko penyumbatan pembuluh darah dan serangan jantung, terutama pada wanita.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Bagaimana Cara Mengatasi Kebiasaan Tidur Terlalu Lama?</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Ada 5 cara mudah yang bisa kamu lakukan untuk mengatasi kebiasaan tidur terlalu lama ini, antara lain:</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">1. Tidur dan Bangun di Jam yang Sama Setiap Hari</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Terdengar klise, tapi dengan mengatur jadwal yang tetap bisa membuat tubuhmu terbiasa tidur dan bangun di jam yang sama setiap harinya.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Karena kalau kamu tidur dan bangun di waktu yang berbeda-beda, tubuhmu jadi bingung nih kapan harus istirahat kapan harus aktif untuk berkegiatan.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/cara-mudah-buat-tidurmu-lebih-nyenyak\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>7 Cara Mudah Buat Tidurmu Lebih Nyenyak</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">2. Hindari Kopi, HP dan TV Sebelum Tidur</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Kafein dan cahaya dari layar bisa membuat otak tetap aktif dan&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/cara-mengatasi-susah-tidur-atau-insomnia\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>sulit tidur</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;walaupun kamu merasa capek.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kalau kamu bisa tidur lebih cepat dan nyenyak di malam hari, kamu nggak perlu tidur lebih lama di pagi atau siang hari untuk merasa segar.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">3. Bangun dan Bergerak di Pagi Hari</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Begitu bangun, coba perlahan-lahan duduk, berdiri, jalan, buka jendela atau kena sinar matahari sebentar.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Aktivitas ini dapat memberi sinyal ke tubuh bahwa hari sudah dimulai, jadi kamu nggak akan tergoda untuk tidur lagi atau bermalas-malasan di kasur.</span></p><h3><strong style=\"color: rgb(0, 0, 0);\">4. Rutin Olahraga Ringan Setiap Hari</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Olahraga seperti jalan kaki atau stretching bikin tubuh jadi lebih aktif dan tidur malam jadi lebih berkualitas.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kalau tidur malammu, kamu nggak akan merasa lelah dan butuh tidur panjang keesokan harinya.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Baca Juga:</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/pentingnya-tidur-cukup-untuk-pemulihan-otot\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(0, 0, 0);\"><u>Pentingnya Tidur Cukup untuk Pemulihan Otot</u></a></p><h3><strong style=\"color: rgb(0, 0, 0);\">5. Catat Jam Tidur dan Bangun Setiap Hari</strong></h3><p><span style=\"color: rgb(0, 0, 0);\">Kadang kita nggak sadar kalau tidur kita terlalu lama atau terlalu sering ketiduran siang.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Dengan mencatat, kamu jadi tahu pola tidurmu sendiri dan bisa mulai mengatur agar waktunya cukup, bukan berlebihan.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Kesimpulan</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Tidur berlebihan sebenarnya bisa menjadi salah satu tanda bahwa ada yang ngga beres dengan tubuh atau pikiranmu.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Dampak tidur terlalu lama bukan hanya lesu seharian, tapi juga bisa menurunkan kesuburan, memperparah depresi dan bahkan meningkatkan risiko penyakit serius.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kamu bisa mengatasi kebiasaan tidur terlalu lama ini dengan memperbaiki mengatur ulang jadwal tidurmu. Bila perlu, konsultasikan permasalahan ini ke dokter atau psikolog.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Dan itu dia&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Penyebab, Dampak dan Cara Mengatasi Kebiasaan Tidur Terlalu Lama</strong><span style=\"color: rgb(0, 0, 0);\">. Share artikel ini sekarang juga ke teman kalian yang tidurnya lama banget!</span></p>',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'published',NULL,0,0,0.00,NULL,'2025-09-01 00:25:15','2025-09-01 00:25:15'),('9fc5a267-7c93-487c-8c2c-0a6bf40c9169','9fc568ad-8efd-4d2f-bee3-2bbcfbae4ef3','10 Merek Bantal yang Bagus: Rekomendasi & Harga Terbaru 2025','10-merek-bantal-yang-bagus-rekomendasi-harga-terbaru-2025','10 Merek Bantal yang Bagus: Rekomendasi & Harga Terbaru 2025','<p><strong style=\"color: rgb(0, 0, 0);\">Merek Bantal yang Bagus -</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;Lagi bingung cari merek bantal yang bagus? Di artikel ini, kamu akan mendapatkan ringkasan informasi tentang 10 merek bantal untuk kamu pertimbangkan.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Ada merek King Koil, Yuureco, Florence, Comforta, Dunlipillo dan masih banyak lagi. Semua punya kelebihan dan keunikan masing-masing.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Artikel ini juga di lengkapi dengan harga terbaru 2025 supaya kamu ada gambaran bantal mana yang sesuai sama budgetmu. Langsung aja kita bahas!</span></p><p><br></p><h2><strong style=\"color: rgb(0, 0, 0);\">1. King Koil</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Dari merek legendaris asal Amerika, King Koil punya banyak&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/mengenal-jenis-bantal-tidur\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>jenis bantal</u></strong></a><span style=\"color: rgb(0, 0, 0);\">, seperti Royale™ 7-Holes Fiberfill, NanoFiber™, Nano Down® Chamber, Bio-Organic™ Latex, FossFlakes, hingga Signature™ Goose Down.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harganya bervariasi, berkisar mulai dari&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp300,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;(Royale Pillow) sampai&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp5,400,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;lebih untuk jenis bulu angsa.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">2. Yuureco</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Yuureco adalah brand lokal yang punya banyak varian bantal, mulai dari dacron silikon sampai nanofiber.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Rekomendasi series bantal Yuureco antara lain:</span></p><ul><li><a href=\"https://www.yuureco.co.id/product/bantal-tidur-dakron-asli-bantal-sultan-dari-yuureco\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>Sultan series</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;(Dacron Silikon)</span></li><li><a href=\"https://www.yuureco.co.id/product/bantal-tidur-hotel-dacron-yuureco-royale\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>Royale series</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;(Dacron silikon + Kain knitting motif)</span></li><li><a href=\"https://www.yuureco.co.id/product/bantal-bulu-angsa-sintetis-microfiber-dacron-for-you-signature\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>Signature series</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;(Microfiber + Kain katun 220 TC)</span></li><li><a href=\"https://www.yuureco.co.id/product/bantal-hotel-premium-bantal-tidur-premium-bantal-premium-hotel-bintang-5-bantal-silicon-premium-bulu-angsa-sintesis-yuur\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>Splendor series</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;(Nanofiber + Kain katun 300 TC)</span></li><li>&nbsp;</li></ul><p><span style=\"color: rgb(0, 0, 0);\">Harga berkisar mulai dari&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp25,000 – Rp122,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk masing-masing&nbsp;</span><a href=\"https://www.yuureco.co.id/products/c/bantal\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>bantal merek yuureco</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;ini.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Bantal Yuureco ini bisa jadi&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">pilihan terbaik buat kamu</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;yang cari bantal berkualitas dengan harga yang terjangkau.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">3. Florence</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Merek ini punya varian seperti Fiber Gel, Lyocell Embossed, Durasoft, Anti Mosquito, serta Quillow (bantal selimut).</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">mulai Rp150 ribuan</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk jenis Lyocell Embossed, hingga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">sekitar Rp360–450 ribuan</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Fiber Gel,&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">bahkan hingga Rp900 ribuan</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk bantal selimut/balmut.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Materialnya lembut, breathable, dan anti-alergi.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">4. Comforta</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Dacron Pillow dan Elegant Pillow adalah 2 varian bantal yang diandalkan oleh Comforta.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harga merek bantal ini berkisar antara&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp59,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Dacron Pillow dan&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp129,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Elegant Pillow.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Comforta tidak banyak mengeluarkan varian bantal karena lebih berfokus ke kasur atau matras tidur.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">5. Dunlopillo</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Ada dua tipe terkenal: SnoreLess Latex Pillow (anti mendengkur, antialergi) dan Ergo Latex Pillow (bentuk bergelombang mengikuti posisi natural leher).</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harga bantalnya mulai dari&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp229,000 - Rp1,339,000</strong><span style=\"color: rgb(0, 0, 0);\">. Bantal ini berbahan latex dengan cover kain katun.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">6. DdPillow</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Merek ini fokus pada bantal bulu angsa (goose down) dan memory foam mewah.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Bantal merek ddpillow ini dijual mulai dari harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp549,000 sampai Rp3,099,000</strong><span style=\"color: rgb(0, 0, 0);\">.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Jika kamu tertarik mencoba sensasi bantal bulu angsa asli, kamu bisa coba&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/penjelasan-dan-rekomendasi-bantal-microfiber-terbaik\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>bantal microfiber</u></strong></a><span style=\"color: rgb(0, 0, 0);\">&nbsp;dengan harga yang lebih terjangkau.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">7. Therapedic</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Therapedic sebenarnya adalah merek kasur dari Amerika. Tapi mereka juga punya produk bantal, salah satunya adalah Anatomic X Gel Pillow dan Contour Pillow.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harganya mulai dari&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp122,500</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Contour Pillow dan&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp1,559,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Anatomic X Gel Pillow.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">8. Quantum</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Hampir sama seperti Comforta, Quantum juga berfokus pada produk kasur atau matras tidur. Tapi Quantum juga mengeluarkan produk bantal dakron sampai microfiber.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Varian bantal dacronnya di harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp113,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;dan bantal microfibernya di harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp235,000</strong><span style=\"color: rgb(0, 0, 0);\">. Bantal microfiber Quantum menggunakan kain microtex untuk covernya.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Cek harga bantal microfiber dengan 100% kain katun yang lebih murah&nbsp;</span><a href=\"https://www.yuureco.co.id/product/bantal-bulu-angsa-sintetis-microfiber-dacron-for-you-signature\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>di sini</u></strong></a><span style=\"color: rgb(0, 0, 0);\">.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">9. Clarissa</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Salah satu varian yang terlaris dan termurah dari Clarissa yaitu Bantal Restking dengan harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp45,000</strong><span style=\"color: rgb(0, 0, 0);\">.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Brand ini juga punya bantal Nano microfiber dengan harga&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp219,700</strong><span style=\"color: rgb(0, 0, 0);\">.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kamu bisa dapetin bantal nanofiber dengan harga yang lebih murah, cuma&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp122,040</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;merek Yuureco Splendor series&nbsp;</span><a href=\"https://www.yuureco.co.id/product/bantal-hotel-premium-bantal-tidur-premium-bantal-premium-hotel-bintang-5-bantal-silicon-premium-bulu-angsa-sintesis-yuur\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>di sini</u></strong></a><span style=\"color: rgb(0, 0, 0);\">.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">10. Willow Pillow</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Willow Pillow menawarkan 2 varian bantal antara lain&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">latex dan memory foam</strong><span style=\"color: rgb(0, 0, 0);\">.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Harga termurah untuk&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">bantal latex</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;brand ini adalah&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp179,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Side Sleeper dan yang termahal sebesar&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp699,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;untuk Long Luxury Latex.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Untuk varian&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">memory foam</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;harga termurah berkisar&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp375,000</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;dan yang termahal di&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Rp799,000</strong><span style=\"color: rgb(0, 0, 0);\">. Harga dan kualitasnya cukup bersaing dengan merek bantal dunlopillo.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Tips Pilih Bantal yang Pas Buat Kamu</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Mau baca lebih mendalam? Klik link di sini:&nbsp;</span><a href=\"https://www.yuureco.co.id/blog/memilih-bantal-tidur-yang-bagus\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>tips memilih bantal yang bagus</u></strong></a><span style=\"color: rgb(0, 0, 0);\">.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Berikut poin–poin ringkas:</span></p><p><strong style=\"color: rgb(0, 0, 0);\">1. Pelajari jenis bantal</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;- Ada beberapa jenis bantal seperti dacron silikon, memory foam, latex, microfiber, nanofiber atau bulu angsa. Tiap bahan punya keempukan &amp; ketahanan berbeda-beda.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">2. Tentukan ukuran bantal</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;– Sesuaikan ukuran bantal dengan ukuran ranjang dan kebiasaan tidurmu.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">3. Sesuaikan dengan posisi tidur</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;– Tidur telentang butuh bantal yang relatif rendah dan tidur menyamping perlu bantal yang tinggi dan padat.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">4. Pertimbangkan faktor kesehatan</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;– Alergi? Cari bahan hypoallergenic atau antitungau—seperti Yuureco, Florence atau Therapedic.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">5. Cari review bantal</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;– Testimoni di e-commerce atau sosial media bisa membantu kamu untuk mengetahui real experience dari pengguna merek-merek bantal tersebut.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Kesimpulan</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Nah, itu dia ulasan 10 merek bantal yang bisa kamu bandingin dari segi jenis isiannya, kualitas bahannya sampai harganya.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Dari merek premium seperti King Koil dan Therapedic, sampai opsi lokal dan ramah dompet seperti Yuureco dan Florence—semuanya punya kelebihan masing-masing.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Semoga membantu yah!</span></p>',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'published',NULL,0,0,0.00,NULL,'2025-09-01 00:30:20','2025-09-01 00:30:20');
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '01d8dac8-ccc5-4710-a1b2-d6330eddfe55',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES ('9fc1834a-59d2-44be-bc1a-18ec9c4e1219','Yuureco','yuureco','yuureco brand',NULL,NULL,1,NULL,NULL,NULL,'2025-08-29 23:20:02','2025-08-29 23:20:02'),('9fc1872c-31f0-4fd2-8da6-c4c885e5b5f5','Brand Yuureco','brand-yuureco','Brand Yuureco',NULL,NULL,1,NULL,NULL,NULL,'2025-08-29 23:30:53','2025-08-29 23:30:53');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` char(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
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
-- Table structure for table `i_error_applications`
--

DROP TABLE IF EXISTS `i_error_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `i_error_applications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modules` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `controller` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `function` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_line` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `param` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_error_applications_user_id_foreign` (`user_id`),
  CONSTRAINT `i_error_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `i_error_applications`
--

LOCK TABLES `i_error_applications` WRITE;
/*!40000 ALTER TABLE `i_error_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `i_error_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_date` datetime NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_user_id_foreign` (`user_id`),
  CONSTRAINT `logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cd10d7c6-9729-433d-8351-3b7389736875',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `width` int DEFAULT NULL,
  `height` int DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_type_index` (`type`),
  KEY `media_extension_index` (`extension`),
  KEY `media_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES ('9fc52aa8-ba5b-48d1-ad8f-f96981c1c44d','2025-04-24-6809b23c3b973-420x270.webp','2025-04-24-6809b23c3b973-420x270_1756691729_YQtaM0.webp','/storage/media/2025-04-24-6809b23c3b973-420x270_1756691729_YQtaM0.webp',19004,'image/webp','webp',420,270,NULL,NULL,'2025-08-31 18:55:30','2025-08-31 18:55:30',NULL);
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_levels`
--

DROP TABLE IF EXISTS `menu_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_levels`
--

LOCK TABLES `menu_levels` WRITE;
/*!40000 ALTER TABLE `menu_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_users`
--

DROP TABLE IF EXISTS `menu_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `created_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_time` timestamp NOT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_users_user_id_foreign` (`user_id`),
  CONSTRAINT `menu_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_users`
--

LOCK TABLES `menu_users` WRITE;
/*!40000 ALTER TABLE `menu_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_level` bigint unsigned NOT NULL,
  `menu_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (64,'2014_10_12_000000_create_users_table',1),(65,'2014_10_12_100000_create_password_resets_table',1),(66,'2019_08_19_000000_create_failed_jobs_table',1),(67,'2019_12_14_000001_create_personal_access_tokens_table',1),(68,'2020_11_20_100001_create_log_table',1),(69,'2023_08_18_072641_create_customers_table',1),(70,'2023_08_18_093214_create_alamats_table',1),(71,'2023_08_25_131402_create_menu_users_table',1),(72,'2023_08_25_131708_create_menu_levels_table',1),(73,'2023_08_25_131808_create_menus_table',1),(74,'2023_08_25_132240_create_user_photos_table',1),(75,'2023_08_25_132500_create_user_activitis_table',1),(76,'2023_08_25_132650_create_i_error_applications_table',1),(77,'2025_08_20_025246_alter_tokenable_id_to_uuid_in_personal_access_tokens',1),(78,'2025_08_20_035824_create_products_blogs_seo_tables',1),(79,'2025_08_23_041101_create_media_table',1),(80,'2025_08_29_080617_create_stores_table',1),(81,'2025_08_29_080701_add_store_id_to_products_table',1),(82,'2025_08_30_032430_create_pages_table',1),(83,'2025_08_30_034811_create_product_media',1),(84,'2025_08_30_035629_create_product_variant_media_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `banner_image_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `twitter_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_description` text COLLATE utf8mb4_unicode_ci,
  `twitter_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_card` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'summary_large_image',
  `structured_data` json DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `published_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`),
  KEY `pages_banner_image_id_foreign` (`banner_image_id`),
  CONSTRAINT `pages_banner_image_id_foreign` FOREIGN KEY (`banner_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES ('284bf315-05ae-4f89-945e-061d6b329a3c','return-policy','Return Policy','<h1><strong>Kebijakan Pengembalian Barang (Return Policy) - Yuureco</strong></h1><p><br></p><h2><strong style=\"color: rgb(0, 0, 0);\">1. Negara Berlaku</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Kebijakan ini berlaku untuk pembelian dan pengembalian produk di wilayah&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">Indonesia</strong><span style=\"color: rgb(0, 0, 0);\">.</span>&nbsp;</p><p>&nbsp;</p><h2><strong style=\"color: rgb(0, 0, 0);\">2. Produk yang Bisa Dikembalikan</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Kami menerima pengembalian untuk:</span></p><ul><li><span style=\"color: rgb(0, 0, 0);\">Produk yang rusak atau cacat (defective)</span></li><li><span style=\"color: rgb(0, 0, 0);\">Produk yang tidak rusak (non-defective)</span>&nbsp;&nbsp;</li><li>&nbsp;</li></ul><h2><strong style=\"color: rgb(0, 0, 0);\">3. Syarat Produk yang Dikembalikan</strong></h2><ul><li><strong style=\"color: rgb(0, 0, 0);\">Produk harus dalam kondisi</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;baru, belum dipakai dan masih dalam kemasan asli.</span></li><li><span style=\"color: rgb(0, 0, 0);\">Sertakan bukti pembelian saat mengajukan pengembalian.</span>&nbsp;</li><li>&nbsp;</li></ul><h2><strong style=\"color: rgb(0, 0, 0);\">4. Batas Waktu Pengembalian</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Pengajuan pengembalian dapat dilakukan&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">maksimal 5 hari kerja</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;setelah produk diterima oleh pelanggan.</span>&nbsp;</p><p>&nbsp;</p><h2><strong style=\"color: rgb(0, 0, 0);\">5. Pertukaran Produk (Exchanges)</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Kami juga menerima&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">penukaran produk</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;selama memenuhi syarat kondisi dan waktu pengembalian.</span>&nbsp;</p><p>&nbsp;</p><h2><strong style=\"color: rgb(0, 0, 0);\">6. Metode Pengembalian</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Pengembalian produk dapat dilakukan dengan cara:</span></p><ul><li><strong style=\"color: rgb(0, 0, 0);\">Mengantar langsung</strong><span style=\"color: rgb(0, 0, 0);\">&nbsp;ke lokasi drop-off yang telah kami tentukan. Silakan hubungi kami untuk informasi alamat drop-off.</span>&nbsp;</li><li>&nbsp;</li></ul><h2><strong style=\"color: rgb(0, 0, 0);\">7. Biaya Pengembalian</strong></h2><ul><li><span style=\"color: rgb(0, 0, 0);\">Tidak dikenakan biaya restocking&nbsp;</span><strong style=\"color: rgb(0, 0, 0);\">(gratis)</strong><span style=\"color: rgb(0, 0, 0);\">.</span></li><li><span style=\"color: rgb(0, 0, 0);\">Biaya pengiriman balik ditanggung oleh pembeli, kecuali jika produk terbukti cacat atau salah kirim dari pihak kami.</span>&nbsp;</li><li>&nbsp;</li></ul><h2><strong style=\"color: rgb(0, 0, 0);\">8. Proses Pengembalian Dana</strong></h2><ul><li><span style=\"color: rgb(0, 0, 0);\">Pengembalian dana akan diproses maksimal 5 hari kerja setelah produk diterima dan diperiksa oleh tim kami.</span>&nbsp;</li><li>&nbsp;</li></ul><h2><strong style=\"color: rgb(0, 0, 0);\">9. Cara Mengajukan Pengembalian</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Untuk mengajukan pengembalian, silakan hubungi tim kami melalui:</span></p><ul><li><span style=\"color: rgb(0, 0, 0);\">WhatsApp: +62 821-4298-2365</span></li><li><span style=\"color: rgb(0, 0, 0);\">Email:&nbsp;</span><a href=\"mailto:marketing@yuureco.co.id\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>marketing@yuureco.co.id</u></strong></a></li><li><span style=\"color: rgb(0, 0, 0);\">Form kontak di halaman Hubungi Kami&nbsp;</span><a href=\"https://www.yuureco.co.id/contacts\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(77, 77, 230);\"><strong><u>https://www.yuureco.co.id/contacts</u></strong></a></li></ul><p><br></p>',NULL,'Return Policy Gudang Grosiran','Return Policy Gudang Grosiran','Return Policy Gudang Grosiran',NULL,'Return Policy Gudang Grosiran','Return Policy Gudang Grosiran',NULL,'website','Return Policy Gudang Grosiran','Return Policy Gudang Grosiran',NULL,'summary_large_image',NULL,1,'2025-08-31 21:21:35',NULL,'2025-08-31 21:21:35','2025-09-01 01:09:45'),('3ddddbc9-0765-4c7d-ae05-621e16fa9026','kontak-kami','Kontak Kami','Kontak kami',NULL,'Kontak kami','Kontak kami','kontak kami',NULL,'kontak kami','kontak kami',NULL,'website','kontak kami','kontak kami',NULL,'summary_large_image',NULL,1,NULL,NULL,'2025-08-31 19:06:09','2025-08-31 20:49:24'),('e69d6856-6eab-4be9-9df7-58009d3d57bf','about','About','<h1><strong>Tentang Gudang Grosiran</strong></h1><p><br></p><p><span style=\"color: rgb(0, 0, 0);\">Kami adalah perusahaan yang berkomitmen untuk menciptakan produk-produk ramah lingkungan guna memenuhi kebutuhan rumah tangga hingga industri.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Dengan pendekatan zero waste manufacturing, kami berupaya meminimalkan dampak lingkungan dari setiap produk yang kami hasilkan.</span></p><h2><strong style=\"color: rgb(0, 0, 0);\">Komitmen Kami terhadap Lingkungan</strong></h2><p><span style=\"color: rgb(0, 0, 0);\">Di Yuureco Indonesia, kami menerapkan prinsip zero waste manufacturing dalam hampir semua proses produksi.</span></p><p><span style=\"color: rgb(0, 0, 0);\">Kami percaya bahwa inovasi yang berkelanjutan adalah kunci untuk menciptakan masa depan yang lebih bersih dan sehat.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Kami terus melakukan:</strong></p><ul><li><span style=\"color: rgb(0, 0, 0);\">Evaluasi proses produksi agar lebih efisien dan ramah lingkungan</span></li><li><span style=\"color: rgb(0, 0, 0);\">Penerapan teknologi terbaru untuk mendukung proses hijau</span></li><li><span style=\"color: rgb(0, 0, 0);\">Inovasi produk yang tidak hanya fungsional tetapi juga berkontribusi terhadap pelestarian lingkungan</span></li><li>&nbsp;</li></ul><p><strong style=\"color: rgb(0, 0, 0);\">Menginspirasi Lewat Aksi</strong></p><p><span style=\"color: rgb(0, 0, 0);\">Kami berharap setiap produk Yuureco bukan hanya bermanfaat secara praktis, tetapi juga dapat menginspirasi masyarakat luas untuk lebih peduli terhadap lingkungan. Mulai dari pilihan bahan baku, desain produk, hingga kemasan — semua dibuat dengan tanggung jawab dan cinta terhadap bumi.</span></p><p><strong style=\"color: rgb(0, 0, 0);\">Produk yang Dapat Disesuaikan</strong></p><p><span style=\"color: rgb(0, 0, 0);\">Kami memahami bahwa setiap kebutuhan berbeda. Oleh karena itu, seluruh produk kami dapat disesuaikan (custom) sesuai kebutuhan Anda, baik untuk pemakaian pribadi, bisnis, hingga skala industri.</span></p>','9fc52aa8-ba5b-48d1-ad8f-f96981c1c44d','About Page - Gudang Grosiran','About Page - Gudang Grosiran','About Page - Gudang Grosiran',NULL,'About Page - Gudang Grosiran','About Page - Gudang Grosiran','About Page - Gudang Grosiran','website','About Page - Gudang Grosiran','About Page - Gudang Grosiran','About Page - Gudang Grosiran','summary_large_image',NULL,1,'2025-08-31 20:42:22',NULL,'2025-08-31 18:59:24','2025-09-01 01:06:57');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (5,'App\\Models\\User','18cfa5aa-4055-43f8-81f9-fdead1240404','auth_token','08102c13e2b135426354aa86a78d797f2b3c6c6b7832c269efd8096ba3df58f7','[\"*\"]','2025-09-01 01:09:45',NULL,'2025-09-01 00:07:43','2025-09-01 01:09:45');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attribute_values` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '986774b5-3739-4e5f-be82-26bbabfa43dd',
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_attribute_values_product_id_attribute_id_unique` (`product_id`,`attribute_id`),
  KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`),
  CONSTRAINT `product_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_values_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_values`
--

LOCK TABLES `product_attribute_values` WRITE;
/*!40000 ALTER TABLE `product_attribute_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attribute_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attributes` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'e40bb9e5-c8ed-49b3-ace9-1baf3e34f044',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('text','number','select','multiselect','boolean','date') COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attributes`
--

LOCK TABLES `product_attributes` WRITE;
/*!40000 ALTER TABLE `product_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`),
  KEY `idx_product_categories_parent_id` (`parent_id`),
  CONSTRAINT `fk_product_categories_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES ('18cfa5aa-4055-43f8-81f9-fdead1240409',NULL,'General','general','-',NULL,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'b0026c44-9e37-4d53-a1a8-06f9ffc98850',
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_variant_id_foreign` (`variant_id`),
  KEY `product_images_product_id_sort_order_index` (`product_id`,`sort_order`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_images_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_media`
--

DROP TABLE IF EXISTS `product_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_media_product_id_media_id_unique` (`product_id`,`media_id`),
  KEY `product_media_media_id_foreign` (`media_id`),
  KEY `product_media_product_id_sort_order_index` (`product_id`,`sort_order`),
  CONSTRAINT `product_media_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_media_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_media`
--

LOCK TABLES `product_media` WRITE;
/*!40000 ALTER TABLE `product_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '020572e9-13f2-48d9-b124-f561311eec2a',
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` json DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_is_approved_rating_index` (`product_id`,`is_approved`,`rating`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_subcategories`
--

DROP TABLE IF EXISTS `product_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_subcategories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '19f1bc0f-4d81-43ae-8af0-839dde47b1e0',
  `category_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_subcategories_slug_unique` (`slug`),
  KEY `product_subcategories_category_id_is_active_index` (`category_id`,`is_active`),
  CONSTRAINT `product_subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_subcategories`
--

LOCK TABLES `product_subcategories` WRITE;
/*!40000 ALTER TABLE `product_subcategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tag`
--

DROP TABLE IF EXISTS `product_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_tag` (
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tag_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`product_id`,`tag_id`),
  KEY `product_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `product_tag_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tag`
--

LOCK TABLES `product_tag` WRITE;
/*!40000 ALTER TABLE `product_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variant_media`
--

DROP TABLE IF EXISTS `product_variant_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variant_media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_variant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variant_media_product_variant_id_media_id_unique` (`product_variant_id`,`media_id`),
  KEY `product_variant_media_media_id_foreign` (`media_id`),
  KEY `product_variant_media_product_variant_id_sort_order_index` (`product_variant_id`,`sort_order`),
  CONSTRAINT `product_variant_media_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variant_media_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variant_media`
--

LOCK TABLES `product_variant_media` WRITE;
/*!40000 ALTER TABLE `product_variant_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variant_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3a33326c-e7c0-4fcf-83c3-e526f80681dc',
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attributes` json NOT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `discount_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `weight` decimal(8,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_is_active_index` (`product_id`,`is_active`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '345c4af2-49e9-4ae4-b7ec-4ce4816fb798',
  `store_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subcategory_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `specifications` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canonical_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structured_data` json DEFAULT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '0',
  `track_stock` tinyint(1) NOT NULL DEFAULT '1',
  `allow_backorder` tinyint(1) NOT NULL DEFAULT '0',
  `weight` decimal(8,2) DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `shipping_class_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_digital` tinyint(1) NOT NULL DEFAULT '0',
  `is_downloadable` tinyint(1) NOT NULL DEFAULT '0',
  `requires_shipping` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('draft','published','archived','out_of_stock') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `visibility` enum('public','private','password','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `review_count` int NOT NULL DEFAULT '0',
  `view_count` int NOT NULL DEFAULT '0',
  `purchase_count` int NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_subcategory_id_foreign` (`subcategory_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_status_visibility_published_at_index` (`status`,`visibility`,`published_at`),
  KEY `products_category_id_status_index` (`category_id`,`status`),
  KEY `products_is_featured_status_index` (`is_featured`,`status`),
  KEY `products_average_rating_index` (`average_rating`),
  KEY `products_store_id_foreign` (`store_id`),
  FULLTEXT KEY `products_title_description_fulltext` (`title`,`description`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `product_subcategories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_redirects`
--

DROP TABLE IF EXISTS `seo_redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_redirects` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'e053c99b-2b9f-4b86-920e-aadd4f44cf0f',
  `old_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` int NOT NULL DEFAULT '301',
  `hits` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seo_redirects_old_url_unique` (`old_url`),
  KEY `seo_redirects_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_redirects`
--

LOCK TABLES `seo_redirects` WRITE;
/*!40000 ALTER TABLE `seo_redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sitemaps`
--

DROP TABLE IF EXISTS `sitemaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sitemaps` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '5bed721a-aa34-4688-a654-eb39022042bd',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_count` int NOT NULL,
  `last_generated` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sitemaps`
--

LOCK TABLES `sitemaps` WRITE;
/*!40000 ALTER TABLE `sitemaps` DISABLE KEYS */;
/*!40000 ALTER TABLE `sitemaps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique store code for reference',
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Registered legal name of the store entity',
  `description` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fax` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indonesia',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `tax_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NPWP/VAT/GST ID',
  `timezone` enum('Africa/Abidjan','Africa/Accra','Africa/Addis_Ababa','Africa/Algiers','Africa/Asmara','Africa/Bamako','Africa/Bangui','Africa/Banjul','Africa/Bissau','Africa/Blantyre','Africa/Brazzaville','Africa/Bujumbura','Africa/Cairo','Africa/Casablanca','Africa/Ceuta','Africa/Conakry','Africa/Dakar','Africa/Dar_es_Salaam','Africa/Djibouti','Africa/Douala','Africa/El_Aaiun','Africa/Freetown','Africa/Gaborone','Africa/Harare','Africa/Johannesburg','Africa/Juba','Africa/Kampala','Africa/Khartoum','Africa/Kigali','Africa/Kinshasa','Africa/Lagos','Africa/Libreville','Africa/Lome','Africa/Luanda','Africa/Lubumbashi','Africa/Lusaka','Africa/Malabo','Africa/Maputo','Africa/Maseru','Africa/Mbabane','Africa/Mogadishu','Africa/Monrovia','Africa/Nairobi','Africa/Ndjamena','Africa/Niamey','Africa/Nouakchott','Africa/Ouagadougou','Africa/Porto-Novo','Africa/Sao_Tome','Africa/Tripoli','Africa/Tunis','Africa/Windhoek','America/Adak','America/Anchorage','America/Anguilla','America/Antigua','America/Araguaina','America/Argentina/Buenos_Aires','America/Argentina/Catamarca','America/Argentina/Cordoba','America/Argentina/Jujuy','America/Argentina/La_Rioja','America/Argentina/Mendoza','America/Argentina/Rio_Gallegos','America/Argentina/Salta','America/Argentina/San_Juan','America/Argentina/San_Luis','America/Argentina/Tucuman','America/Argentina/Ushuaia','America/Aruba','America/Asuncion','America/Atikokan','America/Bahia','America/Bahia_Banderas','America/Barbados','America/Belem','America/Belize','America/Blanc-Sablon','America/Boa_Vista','America/Bogota','America/Boise','America/Cambridge_Bay','America/Campo_Grande','America/Cancun','America/Caracas','America/Cayenne','America/Cayman','America/Chicago','America/Chihuahua','America/Ciudad_Juarez','America/Costa_Rica','America/Creston','America/Cuiaba','America/Curacao','America/Danmarkshavn','America/Dawson','America/Dawson_Creek','America/Denver','America/Detroit','America/Dominica','America/Edmonton','America/Eirunepe','America/El_Salvador','America/Fort_Nelson','America/Fortaleza','America/Glace_Bay','America/Goose_Bay','America/Grand_Turk','America/Grenada','America/Guadeloupe','America/Guatemala','America/Guayaquil','America/Guyana','America/Halifax','America/Havana','America/Hermosillo','America/Indiana/Indianapolis','America/Indiana/Knox','America/Indiana/Marengo','America/Indiana/Petersburg','America/Indiana/Tell_City','America/Indiana/Vevay','America/Indiana/Vincennes','America/Indiana/Winamac','America/Inuvik','America/Iqaluit','America/Jamaica','America/Juneau','America/Kentucky/Louisville','America/Kentucky/Monticello','America/Kralendijk','America/La_Paz','America/Lima','America/Los_Angeles','America/Lower_Princes','America/Maceio','America/Managua','America/Manaus','America/Marigot','America/Martinique','America/Matamoros','America/Mazatlan','America/Menominee','America/Merida','America/Metlakatla','America/Mexico_City','America/Miquelon','America/Moncton','America/Monterrey','America/Montevideo','America/Montserrat','America/Nassau','America/New_York','America/Nome','America/Noronha','America/North_Dakota/Beulah','America/North_Dakota/Center','America/North_Dakota/New_Salem','America/Nuuk','America/Ojinaga','America/Panama','America/Paramaribo','America/Phoenix','America/Port-au-Prince','America/Port_of_Spain','America/Porto_Velho','America/Puerto_Rico','America/Punta_Arenas','America/Rankin_Inlet','America/Recife','America/Regina','America/Resolute','America/Rio_Branco','America/Santarem','America/Santiago','America/Santo_Domingo','America/Sao_Paulo','America/Scoresbysund','America/Sitka','America/St_Barthelemy','America/St_Johns','America/St_Kitts','America/St_Lucia','America/St_Thomas','America/St_Vincent','America/Swift_Current','America/Tegucigalpa','America/Thule','America/Tijuana','America/Toronto','America/Tortola','America/Vancouver','America/Whitehorse','America/Winnipeg','America/Yakutat','Antarctica/Casey','Antarctica/Davis','Antarctica/DumontDUrville','Antarctica/Macquarie','Antarctica/Mawson','Antarctica/McMurdo','Antarctica/Palmer','Antarctica/Rothera','Antarctica/Syowa','Antarctica/Troll','Antarctica/Vostok','Arctic/Longyearbyen','Asia/Aden','Asia/Almaty','Asia/Amman','Asia/Anadyr','Asia/Aqtau','Asia/Aqtobe','Asia/Ashgabat','Asia/Atyrau','Asia/Baghdad','Asia/Bahrain','Asia/Baku','Asia/Bangkok','Asia/Barnaul','Asia/Beirut','Asia/Bishkek','Asia/Brunei','Asia/Chita','Asia/Colombo','Asia/Damascus','Asia/Dhaka','Asia/Dili','Asia/Dubai','Asia/Dushanbe','Asia/Famagusta','Asia/Gaza','Asia/Hebron','Asia/Ho_Chi_Minh','Asia/Hong_Kong','Asia/Hovd','Asia/Irkutsk','Asia/Jakarta','Asia/Jayapura','Asia/Jerusalem','Asia/Kabul','Asia/Kamchatka','Asia/Karachi','Asia/Kathmandu','Asia/Khandyga','Asia/Kolkata','Asia/Krasnoyarsk','Asia/Kuala_Lumpur','Asia/Kuching','Asia/Kuwait','Asia/Macau','Asia/Magadan','Asia/Makassar','Asia/Manila','Asia/Muscat','Asia/Nicosia','Asia/Novokuznetsk','Asia/Novosibirsk','Asia/Omsk','Asia/Oral','Asia/Phnom_Penh','Asia/Pontianak','Asia/Pyongyang','Asia/Qatar','Asia/Qostanay','Asia/Qyzylorda','Asia/Riyadh','Asia/Sakhalin','Asia/Samarkand','Asia/Seoul','Asia/Shanghai','Asia/Singapore','Asia/Srednekolymsk','Asia/Taipei','Asia/Tashkent','Asia/Tbilisi','Asia/Tehran','Asia/Thimphu','Asia/Tokyo','Asia/Tomsk','Asia/Ulaanbaatar','Asia/Urumqi','Asia/Ust-Nera','Asia/Vientiane','Asia/Vladivostok','Asia/Yakutsk','Asia/Yangon','Asia/Yekaterinburg','Asia/Yerevan','Atlantic/Azores','Atlantic/Bermuda','Atlantic/Canary','Atlantic/Cape_Verde','Atlantic/Faroe','Atlantic/Madeira','Atlantic/Reykjavik','Atlantic/South_Georgia','Atlantic/St_Helena','Atlantic/Stanley','Australia/Adelaide','Australia/Brisbane','Australia/Broken_Hill','Australia/Darwin','Australia/Eucla','Australia/Hobart','Australia/Lindeman','Australia/Lord_Howe','Australia/Melbourne','Australia/Perth','Australia/Sydney','Europe/Amsterdam','Europe/Andorra','Europe/Astrakhan','Europe/Athens','Europe/Belgrade','Europe/Berlin','Europe/Bratislava','Europe/Brussels','Europe/Bucharest','Europe/Budapest','Europe/Busingen','Europe/Chisinau','Europe/Copenhagen','Europe/Dublin','Europe/Gibraltar','Europe/Guernsey','Europe/Helsinki','Europe/Isle_of_Man','Europe/Istanbul','Europe/Jersey','Europe/Kaliningrad','Europe/Kirov','Europe/Kyiv','Europe/Lisbon','Europe/Ljubljana','Europe/London','Europe/Luxembourg','Europe/Madrid','Europe/Malta','Europe/Mariehamn','Europe/Minsk','Europe/Monaco','Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Podgorica','Europe/Prague','Europe/Riga','Europe/Rome','Europe/Samara','Europe/San_Marino','Europe/Sarajevo','Europe/Saratov','Europe/Simferopol','Europe/Skopje','Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Tirane','Europe/Ulyanovsk','Europe/Vaduz','Europe/Vatican','Europe/Vienna','Europe/Vilnius','Europe/Volgograd','Europe/Warsaw','Europe/Zagreb','Europe/Zurich','Indian/Antananarivo','Indian/Chagos','Indian/Christmas','Indian/Cocos','Indian/Comoro','Indian/Kerguelen','Indian/Mahe','Indian/Maldives','Indian/Mauritius','Indian/Mayotte','Indian/Reunion','Pacific/Apia','Pacific/Auckland','Pacific/Bougainville','Pacific/Chatham','Pacific/Chuuk','Pacific/Easter','Pacific/Efate','Pacific/Fakaofo','Pacific/Fiji','Pacific/Funafuti','Pacific/Galapagos','Pacific/Gambier','Pacific/Guadalcanal','Pacific/Guam','Pacific/Honolulu','Pacific/Kanton','Pacific/Kiritimati','Pacific/Kosrae','Pacific/Kwajalein','Pacific/Majuro','Pacific/Marquesas','Pacific/Midway','Pacific/Nauru','Pacific/Niue','Pacific/Norfolk','Pacific/Noumea','Pacific/Pago_Pago','Pacific/Palau','Pacific/Pitcairn','Pacific/Pohnpei','Pacific/Port_Moresby','Pacific/Rarotonga','Pacific/Saipan','Pacific/Tahiti','Pacific/Tarawa','Pacific/Tongatapu','Pacific/Wake','Pacific/Wallis','UTC') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Jakarta',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_head_office` tinyint(1) NOT NULL DEFAULT '0',
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_by` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stores_code_unique` (`code`),
  KEY `stores_name_index` (`name`),
  KEY `stores_email_index` (`email`),
  KEY `stores_city_index` (`city`),
  KEY `stores_postal_code_index` (`postal_code`),
  KEY `stores_country_index` (`country`),
  KEY `stores_is_active_index` (`is_active`),
  KEY `stores_created_by_index` (`created_by`),
  KEY `stores_updated_by_index` (`updated_by`),
  KEY `stores_deleted_by_index` (`deleted_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES ('4b0a00d2-c3ec-40df-bb22-490f706dcf2e','S-002','Kupang',NULL,NULL,'','081130757550',NULL,'Jl. Terusan Timor Raya No.3, Oesapa, Kec. Klp. Lima, Kota Kupang, Nusa Tenggara Tim','Kelapa Lima','Kupang','Nusa Tenggara Timur','85228','Indonesia',-10.1576040,123.6369960,NULL,'Asia/Jakarta',1,0,NULL,NULL,NULL,NULL,NULL,'2025-08-29 21:40:29','2025-08-29 21:40:29',NULL),('d62dc0d1-b331-44b0-8333-f3990c07a389','S-001','Samarinda',NULL,NULL,'gudanggrosiran1.samarinda@gmail.com','081130776712',NULL,'Jl Kemakmuran no 71','Sungai Pindang Dalam','Kota Samarinda','Kalimantan Timur','75242','Indonesia',-0.4790710,117.1643020,NULL,'Asia/Jakarta',1,0,NULL,NULL,NULL,NULL,NULL,'2025-08-29 21:40:29','2025-08-29 21:40:29',NULL);
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0ad824a9-6218-4ea6-b8b5-c649e3cbe213',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_unique` (`name`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activitis`
--

DROP TABLE IF EXISTS `user_activitis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activitis` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discripsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` bigint unsigned NOT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activitis_user_id_foreign` (`user_id`),
  CONSTRAINT `user_activitis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activitis`
--

LOCK TABLES `user_activitis` WRITE;
/*!40000 ALTER TABLE `user_activitis` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activitis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_photos`
--

DROP TABLE IF EXISTS `user_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_photos` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `update_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_photos_user_id_foreign` (`user_id`),
  CONSTRAINT `user_photos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_photos`
--

LOCK TABLES `user_photos` WRITE;
/*!40000 ALTER TABLE `user_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_type_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delete_mark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('18cfa5aa-4055-43f8-81f9-fdead1240404','Superadmin','admin','$2y$10$c2tKDx49GqAjeB0tVbOIZum7SFsGtb5RhtbkpK32JFd0zK5TYW93G','admin@mail.com',NULL,'081234567890','081234567890','123456','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-08-19 22:47:46','2025-08-19 22:47:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'gg_revamp'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-01 15:35:20
