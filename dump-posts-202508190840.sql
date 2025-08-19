-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: posts
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
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `avatar_url` text,
  `bio` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
INSERT INTO `authors` VALUES (1,'John Doe','johndoe','john@example.com','https://i.pravatar.cc/150?img=1','Tech enthusiast and blogger','2025-08-16 02:49:24'),(2,'Jane Smith','janesmith','jane@example.com','https://i.pravatar.cc/150?img=2','Travel lover and food blogger','2025-08-16 02:49:24'),(3,'Michael Lee','mikelee','mike@example.com','https://i.pravatar.cc/150?img=3','Photographer and writer','2025-08-16 02:49:24'),(4,'Michael Brown','michaelbrown','michael@example.com','https://randomuser.me/api/portraits/men/3.jpg','Photographer and storyteller','2025-08-16 02:59:29'),(5,'Emily Davis','emilydavis','emily@example.com','https://randomuser.me/api/portraits/women/4.jpg','Food lover and chef','2025-08-16 02:59:29'),(6,'David Wilson','davidwilson','david@example.com','https://randomuser.me/api/portraits/men/5.jpg','Business and finance writer','2025-08-16 02:59:29');
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `logo` varchar(500) DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'TechNova','technova','Leading technology brand for innovative gadgets','https://picsum.photos/200/100?random=31','https://technova.com',1,'TechNova - Innovation Technology','TechNova brand offers cutting-edge technology products','2025-08-18 04:40:13','2025-08-18 04:40:13'),(2,'StyleCraft','stylecraft','Premium fashion and lifestyle brand','https://picsum.photos/200/100?random=32','https://stylecraft.com',1,'StyleCraft - Premium Fashion','StyleCraft creates premium fashion and lifestyle products','2025-08-18 04:40:13','2025-08-18 04:40:13'),(3,'HomeComfort','homecomfort','Quality home and living products','https://picsum.photos/200/100?random=33','https://homecomfort.com',1,'HomeComfort - Quality Living','HomeComfort specializes in quality home products','2025-08-18 04:40:13','2025-08-18 04:40:13'),(4,'FitPro','fitpro','Professional fitness and sports equipment','https://picsum.photos/200/100?random=34','https://fitpro.com',1,'FitPro - Fitness Equipment','FitPro offers professional fitness and sports equipment','2025-08-18 04:40:13','2025-08-18 04:40:13'),(5,'BookWise','bookwise','Educational books and learning materials','https://picsum.photos/200/100?random=35','https://bookwise.com',1,'BookWise - Educational Books','BookWise publishes quality educational books','2025-08-18 04:40:13','2025-08-18 04:40:13'),(6,'BeautyLux','beautylux','Luxury beauty and skincare products','https://picsum.photos/200/100?random=36','https://beautylux.com',1,'BeautyLux - Luxury Beauty','BeautyLux creates luxury beauty and skincare products','2025-08-18 04:40:13','2025-08-18 04:40:13'),(7,'AutoParts Plus','autoparts-plus','Quality automotive parts and accessories','https://picsum.photos/200/100?random=37','https://autopartsplus.com',1,'AutoParts Plus - Car Parts','AutoParts Plus supplies quality automotive parts','2025-08-18 04:40:13','2025-08-18 04:40:13'),(8,'PlayTime','playtime','Fun toys and games for all ages','https://picsum.photos/200/100?random=38','https://playtime.com',1,'PlayTime - Toys & Games','PlayTime creates fun toys and games for families','2025-08-18 04:40:13','2025-08-18 04:40:13'),(9,'EcoGreen','ecogreen','Sustainable and eco-friendly products','https://picsum.photos/200/100?random=39','https://ecogreen.com',1,'EcoGreen - Sustainable Products','EcoGreen offers sustainable and eco-friendly products','2025-08-18 04:40:13','2025-08-18 04:40:13'),(10,'DigitalMax','digitalmax','Digital accessories and gadgets','https://picsum.photos/200/100?random=40','https://digitalmax.com',1,'DigitalMax - Digital Accessories','DigitalMax specializes in digital accessories','2025-08-18 04:40:13','2025-08-18 04:40:13'),(11,'FashionForward','fashionforward','Trendy fashion and accessories','https://picsum.photos/200/100?random=41','https://fashionforward.com',1,'FashionForward - Trendy Fashion','FashionForward creates trendy fashion items','2025-08-18 04:40:13','2025-08-18 04:40:13'),(12,'SmartHome','smarthome','Smart home automation products','https://picsum.photos/200/100?random=42','https://smarthome.com',1,'SmartHome - Home Automation','SmartHome offers intelligent home automation solutions','2025-08-18 04:40:13','2025-08-18 04:40:13'),(13,'OutdoorGear','outdoorgear','Outdoor and camping equipment','https://picsum.photos/200/100?random=43','https://outdoorgear.com',1,'OutdoorGear - Camping Equipment','OutdoorGear provides quality outdoor and camping equipment','2025-08-18 04:40:13','2025-08-18 04:40:13'),(14,'KitchenPro','kitchenpro','Professional kitchen appliances','https://picsum.photos/200/100?random=44','https://kitchenpro.com',1,'KitchenPro - Kitchen Appliances','KitchenPro manufactures professional kitchen appliances','2025-08-18 04:40:13','2025-08-18 04:40:13'),(15,'WellnessPlus','wellnessplus','Health and wellness products','https://picsum.photos/200/100?random=45','https://wellnessplus.com',1,'WellnessPlus - Health Products','WellnessPlus focuses on health and wellness products','2025-08-18 04:40:13','2025-08-18 04:40:13');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Technology','technology','2025-08-16 02:49:30'),(2,'Travel','travel','2025-08-16 02:49:30'),(3,'Food','food','2025-08-16 02:49:30'),(4,'Lifestyle','lifestyle','2025-08-16 02:49:30'),(5,'Business','business','2025-08-16 02:49:30');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_products`
--

DROP TABLE IF EXISTS `category_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(500) DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text,
  `meta_keywords` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_slug` (`slug`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `category_products_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `category_products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_products`
--

LOCK TABLES `category_products` WRITE;
/*!40000 ALTER TABLE `category_products` DISABLE KEYS */;
INSERT INTO `category_products` VALUES (1,'Electronics','electronics','Latest electronic devices and gadgets','https://picsum.photos/400/300?random=1',NULL,1,1,'Electronics - Latest Gadgets & Devices','Shop the latest electronic devices, smartphones, laptops, and gadgets at best prices','electronics, gadgets, technology','2025-08-18 04:37:40','2025-08-18 04:37:40'),(2,'Fashion','fashion','Trendy clothing and accessories','https://picsum.photos/400/300?random=2',NULL,2,1,'Fashion - Trendy Clothing & Accessories','Discover latest fashion trends, clothing, shoes, and accessories for men and women','fashion, clothing, style','2025-08-18 04:37:40','2025-08-18 04:37:40'),(3,'Home & Garden','home-garden','Home improvement and garden supplies','https://picsum.photos/400/300?random=3',NULL,3,1,'Home & Garden - Furniture & Decor','Transform your home with furniture, decor, and garden supplies','home, furniture, garden','2025-08-18 04:37:40','2025-08-18 04:37:40'),(4,'Sports','sports','Sports equipment and fitness gear','https://picsum.photos/400/300?random=4',NULL,4,1,'Sports - Equipment & Fitness Gear','Quality sports equipment, fitness gear, and outdoor activities','sports, fitness, equipment','2025-08-18 04:37:40','2025-08-18 04:37:40'),(5,'Books','books','Books and educational materials','https://picsum.photos/400/300?random=5',NULL,5,1,'Books - Fiction, Non-fiction & Educational','Explore vast collection of books, novels, and educational materials','books, education, reading','2025-08-18 04:37:40','2025-08-18 04:37:40'),(6,'Health & Beauty','health-beauty','Health and beauty products','https://picsum.photos/400/300?random=6',NULL,6,1,'Health & Beauty - Skincare & Wellness','Premium health and beauty products for your wellness journey','health, beauty, skincare','2025-08-18 04:37:40','2025-08-18 04:37:40'),(7,'Automotive','automotive','Car parts and accessories','https://picsum.photos/400/300?random=7',NULL,7,1,'Automotive - Car Parts & Accessories','Quality automotive parts, accessories, and car care products','automotive, car parts, accessories','2025-08-18 04:37:40','2025-08-18 04:37:40'),(8,'Toys & Games','toys-games','Toys and games for all ages','https://picsum.photos/400/300?random=8',NULL,8,1,'Toys & Games - Fun for All Ages','Entertaining toys and games for kids and adults','toys, games, entertainment','2025-08-18 04:37:40','2025-08-18 04:37:40'),(9,'Smartphones','smartphones','Latest smartphones and mobile devices','https://picsum.photos/400/300?random=9',1,1,1,'Smartphones - Latest Mobile Devices','Browse latest smartphones with best features and prices','smartphones, mobile, phones','2025-08-18 04:38:30','2025-08-18 04:38:30'),(10,'Laptops','laptops','High-performance laptops and notebooks','https://picsum.photos/400/300?random=10',1,2,1,'Laptops - High Performance Computers','Professional laptops and notebooks for work and gaming','laptops, computers, notebooks','2025-08-18 04:38:30','2025-08-18 04:38:30'),(11,'Tablets','tablets','Tablets and e-readers','https://picsum.photos/400/300?random=11',1,3,1,'Tablets - Portable Computing Devices','Versatile tablets for work, entertainment, and creativity','tablets, ipad, e-readers','2025-08-18 04:38:30','2025-08-18 04:38:30'),(12,'Audio & Headphones','audio-headphones','Headphones, speakers, and audio equipment','https://picsum.photos/400/300?random=12',1,4,1,'Audio & Headphones - Premium Sound','High-quality headphones, speakers, and audio equipment','audio, headphones, speakers','2025-08-18 04:38:30','2025-08-18 04:38:30'),(13,'Gaming','gaming','Gaming consoles and accessories','https://picsum.photos/400/300?random=13',1,5,1,'Gaming - Consoles & Accessories','Gaming consoles, controllers, and gaming accessories','gaming, console, accessories','2025-08-18 04:38:30','2025-08-18 04:38:30'),(14,'Men\'s Clothing','mens-clothing','Men\'s fashion and clothing','https://picsum.photos/400/300?random=14',2,1,1,'Men\'s Clothing - Fashion & Style','Stylish men\'s clothing, shirts, pants, and formal wear','mens clothing, fashion, style','2025-08-18 04:38:53','2025-08-18 04:38:53'),(15,'Women\'s Clothing','womens-clothing','Women\'s fashion and clothing','https://picsum.photos/400/300?random=15',2,2,1,'Women\'s Clothing - Latest Fashion','Trendy women\'s clothing, dresses, tops, and accessories','womens clothing, fashion, dresses','2025-08-18 04:38:53','2025-08-18 04:38:53'),(16,'Shoes','shoes','Footwear for all occasions','https://picsum.photos/400/300?random=16',2,3,1,'Shoes - Footwear Collection','Comfortable and stylish shoes for men and women','shoes, footwear, sneakers','2025-08-18 04:38:53','2025-08-18 04:38:53'),(17,'Accessories','accessories','Fashion accessories and jewelry','https://picsum.photos/400/300?random=17',2,4,1,'Accessories - Fashion & Jewelry','Elegant accessories, jewelry, and fashion items','accessories, jewelry, fashion','2025-08-18 04:38:53','2025-08-18 04:38:53'),(18,'Furniture','furniture','Home and office furniture','https://picsum.photos/400/300?random=18',3,1,1,'Furniture - Home & Office','Quality furniture for home and office spaces','furniture, home decor, office','2025-08-18 04:39:06','2025-08-18 04:39:06'),(19,'Kitchen & Dining','kitchen-dining','Kitchen appliances and dining sets','https://picsum.photos/400/300?random=19',3,2,1,'Kitchen & Dining - Appliances & Sets','Modern kitchen appliances and elegant dining sets','kitchen, dining, appliances','2025-08-18 04:39:06','2025-08-18 04:39:06'),(20,'Bedroom','bedroom','Bedroom furniture and accessories','https://picsum.photos/400/300?random=20',3,3,1,'Bedroom - Furniture & Decor','Comfortable bedroom furniture and decor items','bedroom, furniture, decor','2025-08-18 04:39:06','2025-08-18 04:39:06'),(21,'Garden','garden','Garden tools and outdoor furniture','https://picsum.photos/400/300?random=21',3,4,1,'Garden - Tools & Outdoor Furniture','Garden tools, plants, and outdoor furniture','garden, outdoor, tools','2025-08-18 04:39:06','2025-08-18 04:39:06'),(22,'Fiction','fiction','Novels and fiction books','https://picsum.photos/400/300?random=25',5,1,1,'Fiction Books - Novels & Stories','Engaging fiction novels and story collections','fiction, novels, stories','2025-08-18 04:39:34','2025-08-18 04:39:34'),(23,'Non-Fiction','non-fiction','Educational and informative books','https://picsum.photos/400/300?random=26',5,2,1,'Non-Fiction - Educational Books','Informative non-fiction books and educational materials','non-fiction, education, learning','2025-08-18 04:39:34','2025-08-18 04:39:34'),(24,'Children\'s Books','childrens-books','Books for children and young adults','https://picsum.photos/400/300?random=27',5,3,1,'Children\'s Books - Stories & Learning','Engaging books for children and educational materials','children books, education, stories','2025-08-18 04:39:34','2025-08-18 04:39:34'),(25,'Skincare','skincare','Skincare products and treatments','https://picsum.photos/400/300?random=28',6,1,1,'Skincare - Beauty & Treatments','Premium skincare products and beauty treatments','skincare, beauty, treatments','2025-08-18 04:39:47','2025-08-18 04:39:47'),(26,'Makeup','makeup','Cosmetics and makeup products','https://picsum.photos/400/300?random=29',6,2,1,'Makeup - Cosmetics & Beauty','Professional makeup and cosmetic products','makeup, cosmetics, beauty','2025-08-18 04:39:47','2025-08-18 04:39:47'),(27,'Health Supplements','health-supplements','Vitamins and health supplements','https://picsum.photos/400/300?random=30',6,3,1,'Health Supplements - Vitamins & Wellness','Quality health supplements and vitamins','supplements, vitamins, health','2025-08-18 04:39:47','2025-08-18 04:39:47');
/*!40000 ALTER TABLE `category_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `author_email` varchar(150) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_stats`
--

DROP TABLE IF EXISTS `post_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_stats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `views` int DEFAULT '0',
  `likes` int DEFAULT '0',
  `shares` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `post_stats_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_stats`
--

LOCK TABLES `post_stats` WRITE;
/*!40000 ALTER TABLE `post_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `seo_keywords` text,
  `image_url` text,
  `category_id` int DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('Draft','Published','Deactived') NOT NULL DEFAULT 'Draft',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (81,'Exploring the Future of AI','exploring-the-future-of-ai','Content about AI...','Short excerpt about AI','Future of AI','Deep dive into the future of AI','AI, Future, Technology','https://picsum.photos/600/400?random=1',5,1,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 04:16:33','Published'),(82,'Top 10 Destinations to Visit in 2025','top-10-destinations-2025','Travel content...','Excerpt travel...','Best Destinations 2025','Discover top places to visit in 2025','Travel, Tourism, Adventure','https://picsum.photos/600/400?random=2',2,2,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:55:54','Published'),(83,'Healthy Recipes for Busy People','healthy-recipes-busy-people','Food content...','Quick and easy recipes...','Healthy Food Ideas','Best recipes for busy lifestyle','Food, Healthy, Lifestyle','https://picsum.photos/600/400?random=3',3,2,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:55:54','Published'),(84,'Productivity Hacks for Remote Workers','productivity-hacks-remote','Work from home content...','Quick productivity hacks...','Remote Work Tips','Boost your productivity while working remotely','Productivity, Remote Work, Business','https://picsum.photos/600/400?random=4',5,1,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:55:54','Published'),(85,'The Rise of Sustainable Fashion','sustainable-fashion-rise','Fashion and sustainability...','Why sustainable fashion matters...','Sustainable Fashion','Exploring eco-friendly fashion trends','Fashion, Lifestyle, Eco','https://picsum.photos/600/400?random=5',4,3,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(86,'Blockchain Beyond Cryptocurrency','blockchain-beyond-crypto','Blockchain applications...','Beyond Bitcoin...','Blockchain Innovation','Exploring blockchain use cases outside crypto','Blockchain, Tech, Innovation','https://picsum.photos/600/400?random=6',1,4,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(87,'Digital Nomad Lifestyle Guide','digital-nomad-lifestyle','How to live as a nomad...','Work while traveling...','Nomad Lifestyle','Guide to becoming a digital nomad','Travel, Remote Work, Lifestyle','https://picsum.photos/600/400?random=7',2,5,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(88,'Superfoods You Need in Your Diet','superfoods-diet','Food content...','Best superfoods...','Healthy Superfoods','Top superfoods for daily health','Food, Health, Nutrition','https://picsum.photos/600/400?random=8',3,3,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(89,'Startup Trends to Watch','startup-trends-to-watch','Business startup content...','Latest startup trends...','Startup Trends','Trends shaping startups in 2025','Startup, Business, Innovation','https://picsum.photos/600/400?random=9',5,4,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(90,'Minimalist Fashion Essentials','minimalist-fashion-essentials','Minimalist fashion...','Essential items...','Minimalist Wardrobe','Guide to minimalist fashion essentials','Fashion, Minimalist, Lifestyle','https://picsum.photos/600/400?random=10',4,2,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:56:50','Published'),(91,'The Future of Electric Cars','future-of-electric-cars','EV industry...','Electric vehicle growth...','Electric Cars 2025','Exploring the EV revolution','Cars, EV, Technology','https://picsum.photos/600/400?random=11',1,1,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:03','Published'),(92,'Top Hiking Trails in Europe','top-hiking-trails-europe','Hiking trails content...','Best trails Europe...','Europe Hiking Trails','Discover the best hiking spots in Europe','Travel, Hiking, Adventure','https://picsum.photos/600/400?random=12',2,2,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:50','Published'),(93,'Quick Vegan Meal Ideas','quick-vegan-meals','Vegan recipes...','Easy vegan meals...','Vegan Recipes','Simple vegan dishes for beginners','Food, Vegan, Healthy','https://picsum.photos/600/400?random=13',3,4,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:50','Published'),(94,'Mastering Work-Life Balance','mastering-work-life-balance','Content on balance...','Work-life balance tips...','Work-Life Balance','Tips for balancing work and personal life','Lifestyle, Productivity, Health','https://picsum.photos/600/400?random=14',5,5,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:50','Published'),(95,'Trendy Streetwear 2025','trendy-streetwear-2025','Streetwear trends...','Top streetwear picks...','Streetwear Trends','Explore 2025 streetwear fashion','Fashion, Streetwear, Lifestyle','https://picsum.photos/600/400?random=15',4,3,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:50','Published'),(96,'AI in Healthcare','ai-in-healthcare','AI healthcare...','How AI helps doctors...','AI Healthcare','AI applications in modern medicine','AI, Health, Technology','https://picsum.photos/600/400?random=16',1,2,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:57:50','Published'),(97,'Backpacking Asia Guide','backpacking-asia-guide','Asia backpacking tips...','Guide to backpacking...','Backpacking Asia','Tips for budget traveling in Asia','Travel, Backpacking, Asia','https://picsum.photos/600/400?random=17',2,5,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:58:22','Published'),(98,'Delicious Pasta Recipes','delicious-pasta-recipes','Pasta recipes...','Homemade pasta...','Pasta Dishes','Easy pasta recipes for everyone','Food, Pasta, Recipes','https://picsum.photos/600/400?random=18',3,1,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:58:22','Published'),(99,'Leadership Skills Every Manager Needs','leadership-skills-managers','Leadership skills...','Essential skills...','Leadership Guide','Top skills for successful managers','Leadership, Business, Management','https://picsum.photos/600/400?random=19',5,4,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:58:22','Published'),(100,'Eco-Friendly Fabrics to Try','eco-friendly-fabrics','Sustainable fabrics...','Best eco fabrics...','Eco Fabrics','Exploring eco-friendly clothing materials','Fashion, Eco, Lifestyle','https://picsum.photos/600/400?random=20',4,3,'2025-08-16 03:01:47','2025-08-16 03:01:47','2025-08-16 09:58:22','Published');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `attribute_value` text NOT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_attribute_name` (`attribute_name`),
  CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_category` (`product_id`,`category_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_category_id` (`category_id`),
  CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_faqs`
--

DROP TABLE IF EXISTS `product_faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_faqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `product_faqs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_faqs`
--

LOCK TABLES `product_faqs` WRITE;
/*!40000 ALTER TABLE `product_faqs` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_primary` (`is_primary`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_related`
--

DROP TABLE IF EXISTS `product_related`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_related` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `related_product_id` bigint unsigned NOT NULL,
  `relation_type` enum('related','upsell','cross_sell') DEFAULT 'related',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_relation` (`product_id`,`related_product_id`,`relation_type`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_related_product_id` (`related_product_id`),
  CONSTRAINT `product_related_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_related_ibfk_2` FOREIGN KEY (`related_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_related`
--

LOCK TABLES `product_related` WRITE;
/*!40000 ALTER TABLE `product_related` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_related` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `rating` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `is_verified` tinyint(1) DEFAULT '0',
  `is_approved` tinyint(1) DEFAULT '0',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_approved` (`is_approved`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_chk_1` CHECK (((`rating` >= 1) and (`rating` <= 5)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_tags`
--

DROP TABLE IF EXISTS `product_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_tag` (`product_id`,`tag_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_tag_id` (`tag_id`),
  CONSTRAINT `product_tags_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_tags`
--

LOCK TABLES `product_tags` WRITE;
/*!40000 ALTER TABLE `product_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variant_options`
--

DROP TABLE IF EXISTS `product_variant_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variant_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `variant_id` bigint unsigned NOT NULL,
  `option_id` bigint unsigned NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_variant_option` (`variant_id`,`option_id`),
  KEY `idx_variant_id` (`variant_id`),
  KEY `idx_option_id` (`option_id`),
  CONSTRAINT `product_variant_options_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variant_options_ibfk_2` FOREIGN KEY (`option_id`) REFERENCES `variant_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variant_options`
--

LOCK TABLES `product_variant_options` WRITE;
/*!40000 ALTER TABLE `product_variant_options` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variant_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `price` decimal(15,2) DEFAULT NULL,
  `compare_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `weight` decimal(8,3) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `position` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_sku` (`sku`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `compare_price` decimal(15,2) DEFAULT NULL,
  `cost_price` decimal(15,2) DEFAULT NULL,
  `track_quantity` tinyint(1) DEFAULT '1',
  `quantity` int DEFAULT '0',
  `min_quantity` int DEFAULT '0',
  `weight` decimal(8,3) DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `status` enum('active','inactive','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_digital` tinyint(1) DEFAULT '0',
  `requires_shipping` tinyint(1) DEFAULT '1',
  `taxable` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `canonical_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `og_description` text COLLATE utf8mb4_unicode_ci,
  `og_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_description` text COLLATE utf8mb4_unicode_ci,
  `twitter_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `focus_keyword` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_score` int DEFAULT '0',
  `readability_score` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `brand_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_slug` (`slug`),
  UNIQUE KEY `ux_sku` (`sku`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_price` (`price`),
  KEY `fk_products_brands` (`brand_id`),
  CONSTRAINT `fk_products_brands` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'TechNova Smartphone Pro Max','technova-smartphone-pro-max','Latest flagship smartphone with advanced AI camera system, 5G connectivity, and all-day battery life. Features include 108MP main camera, wireless charging, and premium build quality.','Latest flagship smartphone with AI camera and 5G','TNV-SPM-001','1234567890123',999.99,1199.99,650.00,1,50,5,0.200,15.50,7.50,0.80,'active',1,0,1,1,1,'TechNova Smartphone Pro Max - 5G Flagship Phone','Buy TechNova Smartphone Pro Max with AI camera, 5G, wireless charging. Latest flagship smartphone technology.','smartphone, 5g, ai camera, wireless charging','/products/technova-smartphone-pro-max','TechNova Smartphone Pro Max','Latest flagship smartphone with AI camera system','https://picsum.photos/800/600?random=101','TechNova Smartphone Pro Max','AI-powered flagship smartphone','https://picsum.photos/800/600?random=101','smartphone pro max',85,78,'2025-08-18 11:49:30','2025-08-18 14:54:50',1),(2,'TechNova Gaming Laptop Elite','technova-gaming-laptop-elite','High-performance gaming laptop with RTX graphics, Intel Core i9 processor, 32GB RAM, and RGB backlit keyboard. Perfect for gaming and content creation.','High-performance gaming laptop with RTX graphics','TNV-GLE-002','1234567890124',1899.99,2199.99,1200.00,1,25,3,2.500,35.00,25.00,2.00,'active',1,0,1,1,2,'TechNova Gaming Laptop Elite - RTX Gaming Laptop','TechNova Gaming Laptop Elite with RTX graphics, Core i9, 32GB RAM. Ultimate gaming performance.','gaming laptop, rtx, core i9, gaming','/products/technova-gaming-laptop-elite','TechNova Gaming Laptop Elite','Ultimate gaming laptop with RTX graphics','https://picsum.photos/800/600?random=102','TechNova Gaming Laptop Elite','RTX gaming laptop','https://picsum.photos/800/600?random=102','gaming laptop elite',88,82,'2025-08-18 11:49:30','2025-08-18 14:54:50',2),(3,'DigitalMax Wireless Earbuds Pro','digitalmax-wireless-earbuds-pro','Premium wireless earbuds with active noise cancellation, 30-hour battery life, and crystal clear sound quality. Includes wireless charging case.','Premium wireless earbuds with noise cancellation','DGM-WEP-003','1234567890125',199.99,249.99,95.00,1,100,10,0.050,6.00,4.00,2.50,'active',1,0,1,1,3,'DigitalMax Wireless Earbuds Pro - Premium Audio','DigitalMax Wireless Earbuds Pro with noise cancellation, 30hr battery, wireless charging case.','wireless earbuds, noise cancellation, bluetooth','/products/digitalmax-wireless-earbuds-pro','DigitalMax Wireless Earbuds Pro','Premium wireless earbuds with ANC','https://picsum.photos/800/600?random=103','DigitalMax Wireless Earbuds Pro','Premium ANC earbuds','https://picsum.photos/800/600?random=103','wireless earbuds pro',87,80,'2025-08-18 11:49:30','2025-08-18 14:54:50',3),(4,'TechNova 4K Smart TV 55\"','technova-4k-smart-tv-55','Ultra HD 4K Smart TV with HDR10+, built-in streaming apps, voice control, and slim bezel design. Perfect for home entertainment.','55\" 4K Smart TV with HDR and streaming apps','TNV-STV-004','1234567890126',799.99,999.99,450.00,1,15,2,18.000,123.00,71.00,8.00,'active',0,0,1,1,4,'TechNova 4K Smart TV 55\" - Ultra HD Television','TechNova 55\" 4K Smart TV with HDR10+, streaming apps, voice control. Premium home entertainment.','4k tv, smart tv, hdr, streaming','/products/technova-4k-smart-tv-55','TechNova 4K Smart TV 55\"','4K Smart TV with premium features','https://picsum.photos/800/600?random=104','TechNova 4K Smart TV','Premium 4K Smart TV','https://picsum.photos/800/600?random=104','4k smart tv',83,75,'2025-08-18 11:49:30','2025-08-18 14:54:50',4),(5,'DigitalMax Wireless Charger Pad','digitalmax-wireless-charger-pad','Fast wireless charging pad compatible with all Qi-enabled devices. LED indicator, overheating protection, and sleek design.','Fast wireless charging pad with LED indicator','DGM-WCP-005','1234567890127',29.99,39.99,15.00,1,200,20,0.150,10.00,10.00,1.00,'active',0,0,1,1,5,'DigitalMax Wireless Charger Pad - Fast Charging','DigitalMax Wireless Charger Pad with fast charging, LED indicator, Qi compatibility.','wireless charger, qi charging, fast charging','/products/digitalmax-wireless-charger-pad','DigitalMax Wireless Charger Pad','Fast wireless charging solution','https://picsum.photos/800/600?random=105','DigitalMax Wireless Charger','Fast wireless charging','https://picsum.photos/800/600?random=105','wireless charger pad',79,73,'2025-08-18 11:49:30','2025-08-18 14:54:50',4),(6,'StyleCraft Premium Leather Jacket','stylecraft-premium-leather-jacket','Genuine leather jacket with classic design, premium stitching, and comfortable fit. Perfect for casual and formal occasions.','Premium genuine leather jacket with classic design','STC-PLJ-006','1234567890128',299.99,399.99,150.00,1,30,3,1.200,60.00,50.00,5.00,'active',1,0,1,1,6,'StyleCraft Premium Leather Jacket - Genuine Leather','StyleCraft Premium Leather Jacket made from genuine leather with classic design and comfort.','leather jacket, genuine leather, fashion','/products/stylecraft-premium-leather-jacket','StyleCraft Premium Leather Jacket','Genuine leather jacket with premium quality','https://picsum.photos/800/600?random=106','StyleCraft Leather Jacket','Premium leather fashion','https://picsum.photos/800/600?random=106','premium leather jacket',86,81,'2025-08-18 11:49:30','2025-08-18 14:54:50',3),(7,'FashionForward Designer Dress','fashionforward-designer-dress','Elegant designer dress perfect for special occasions. Made from high-quality fabric with modern cut and sophisticated style.','Elegant designer dress for special occasions','FFW-DD-007','1234567890129',189.99,249.99,85.00,1,25,2,0.800,120.00,40.00,2.00,'active',1,0,1,1,7,'FashionForward Designer Dress - Elegant Fashion','FashionForward Designer Dress with elegant design, quality fabric, perfect for special occasions.','designer dress, elegant dress, fashion','/products/fashionforward-designer-dress','FashionForward Designer Dress','Elegant designer dress collection','https://picsum.photos/800/600?random=107','FashionForward Designer Dress','Elegant fashion dress','https://picsum.photos/800/600?random=107','designer dress',84,79,'2025-08-18 11:49:30','2025-08-18 14:54:50',5),(8,'StyleCraft Casual Sneakers','stylecraft-casual-sneakers','Comfortable casual sneakers with breathable material, cushioned sole, and modern design. Perfect for daily wear and light activities.','Comfortable casual sneakers with modern design','STC-CS-008','1234567890130',89.99,119.99,45.00,1,80,8,0.600,30.00,20.00,12.00,'active',0,0,1,1,8,'StyleCraft Casual Sneakers - Comfortable Footwear','StyleCraft Casual Sneakers with breathable material, cushioned sole, modern design for daily wear.','casual sneakers, comfortable shoes, footwear','/products/stylecraft-casual-sneakers','StyleCraft Casual Sneakers','Comfortable casual footwear','https://picsum.photos/800/600?random=108','StyleCraft Casual Sneakers','Comfortable casual shoes','https://picsum.photos/800/600?random=108','casual sneakers',81,76,'2025-08-18 11:49:30','2025-08-18 14:54:50',6),(9,'StyleCraft Luxury Watch','stylecraft-luxury-watch','Luxury timepiece with stainless steel case, automatic movement, and sapphire crystal. Water resistant and elegant design.','Luxury watch with automatic movement','STC-LW-009','1234567890131',499.99,699.99,220.00,1,20,2,0.180,4.50,4.50,1.20,'active',1,0,1,1,9,'StyleCraft Luxury Watch - Premium Timepiece','StyleCraft Luxury Watch with automatic movement, stainless steel, sapphire crystal, water resistant.','luxury watch, automatic watch, timepiece','/products/stylecraft-luxury-watch','StyleCraft Luxury Watch','Premium luxury timepiece','https://picsum.photos/800/600?random=109','StyleCraft Luxury Watch','Premium watch collection','https://picsum.photos/800/600?random=109','luxury watch',89,83,'2025-08-18 11:49:30','2025-08-18 14:54:50',7),(10,'FashionForward Handbag Collection','fashionforward-handbag-collection','Stylish handbag made from premium materials with multiple compartments, adjustable strap, and modern design. Perfect for work and casual use.','Stylish handbag with premium materials','FFW-HC-010','1234567890132',149.99,199.99,65.00,1,40,4,0.750,35.00,25.00,15.00,'active',0,0,1,1,10,'FashionForward Handbag Collection - Premium Bags','FashionForward Handbag Collection with premium materials, multiple compartments, modern design.','handbag, fashion bag, premium bag','/products/fashionforward-handbag-collection','FashionForward Handbag Collection','Premium handbag collection','https://picsum.photos/800/600?random=110','FashionForward Handbag','Premium fashion bags','https://picsum.photos/800/600?random=110','handbag collection',82,77,'2025-08-18 11:49:30','2025-08-18 14:54:50',8),(11,'HomeComfort Ergonomic Office Chair','homecomfort-ergonomic-office-chair','Professional ergonomic office chair with lumbar support, adjustable height, breathable mesh back, and 360-degree swivel. Perfect for long work hours.','Ergonomic office chair with lumbar support','HC-EOC-011','1234567890133',249.99,329.99,125.00,1,35,3,15.000,65.00,65.00,120.00,'active',0,0,1,1,11,'HomeComfort Ergonomic Office Chair - Professional Seating','HomeComfort Ergonomic Office Chair with lumbar support, adjustable height, breathable design.','office chair, ergonomic chair, professional seating','/products/homecomfort-ergonomic-office-chair','HomeComfort Ergonomic Office Chair','Professional ergonomic seating solution','https://picsum.photos/800/600?random=111','HomeComfort Office Chair','Ergonomic office seating','https://picsum.photos/800/600?random=111','ergonomic office chair',85,80,'2025-08-18 11:49:30','2025-08-18 14:54:50',9);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_redirects`
--

DROP TABLE IF EXISTS `seo_redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_redirects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `old_url` varchar(500) NOT NULL,
  `new_url` varchar(500) NOT NULL,
  `redirect_type` int DEFAULT '301',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_old_url` (`old_url`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_redirects`
--

LOCK TABLES `seo_redirects` WRITE;
/*!40000 ALTER TABLE `seo_redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(7) DEFAULT '#000000',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'New Arrival','new-arrival','#28a745','2025-08-18 04:40:00','2025-08-18 04:40:00'),(2,'Best Seller','best-seller','#dc3545','2025-08-18 04:40:00','2025-08-18 04:40:00'),(3,'Limited Edition','limited-edition','#6f42c1','2025-08-18 04:40:00','2025-08-18 04:40:00'),(4,'Sale','sale','#fd7e14','2025-08-18 04:40:00','2025-08-18 04:40:00'),(5,'Premium','premium','#ffc107','2025-08-18 04:40:00','2025-08-18 04:40:00'),(6,'Eco Friendly','eco-friendly','#20c997','2025-08-18 04:40:00','2025-08-18 04:40:00'),(7,'Wireless','wireless','#17a2b8','2025-08-18 04:40:00','2025-08-18 04:40:00'),(8,'Waterproof','waterproof','#007bff','2025-08-18 04:40:00','2025-08-18 04:40:00'),(9,'Organic','organic','#28a745','2025-08-18 04:40:00','2025-08-18 04:40:00'),(10,'Imported','imported','#6c757d','2025-08-18 04:40:00','2025-08-18 04:40:00'),(11,'Handmade','handmade','#e83e8c','2025-08-18 04:40:00','2025-08-18 04:40:00'),(12,'Vintage','vintage','#6f42c1','2025-08-18 04:40:00','2025-08-18 04:40:00'),(13,'Modern','modern','#495057','2025-08-18 04:40:00','2025-08-18 04:40:00'),(14,'Classic','classic','#343a40','2025-08-18 04:40:00','2025-08-18 04:40:00'),(15,'Luxury','luxury','#ffc107','2025-08-18 04:40:00','2025-08-18 04:40:00'),(16,'Budget Friendly','budget-friendly','#28a745','2025-08-18 04:40:00','2025-08-18 04:40:00'),(17,'Fast Shipping','fast-shipping','#17a2b8','2025-08-18 04:40:00','2025-08-18 04:40:00'),(18,'Free Shipping','free-shipping','#20c997','2025-08-18 04:40:00','2025-08-18 04:40:00'),(19,'Exclusive','exclusive','#dc3545','2025-08-18 04:40:00','2025-08-18 04:40:00'),(20,'Trending','trending','#fd7e14','2025-08-18 04:40:00','2025-08-18 04:40:00');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variant_options`
--

DROP TABLE IF EXISTS `variant_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `variant_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variant_options`
--

LOCK TABLES `variant_options` WRITE;
/*!40000 ALTER TABLE `variant_options` DISABLE KEYS */;
INSERT INTO `variant_options` VALUES (1,'Color','[\"Black\", \"White\", \"Red\", \"Blue\", \"Green\", \"Yellow\", \"Pink\", \"Purple\", \"Orange\", \"Gray\"]','2025-08-18 04:40:36','2025-08-18 04:40:36'),(2,'Size','[\"XS\", \"S\", \"M\", \"L\", \"XL\", \"XXL\", \"XXXL\"]','2025-08-18 04:40:36','2025-08-18 04:40:36'),(3,'Storage','[\"64GB\", \"128GB\", \"256GB\", \"512GB\", \"1TB\"]','2025-08-18 04:40:36','2025-08-18 04:40:36'),(4,'Material','[\"Cotton\", \"Polyester\", \"Leather\", \"Metal\", \"Plastic\", \"Wood\", \"Glass\"]','2025-08-18 04:40:36','2025-08-18 04:40:36'),(5,'Style','[\"Classic\", \"Modern\", \"Vintage\", \"Minimalist\", \"Bold\", \"Elegant\"]','2025-08-18 04:40:36','2025-08-18 04:40:36');
/*!40000 ALTER TABLE `variant_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'posts'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-19  8:40:30
