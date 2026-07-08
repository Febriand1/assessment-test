-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_test
CREATE DATABASE IF NOT EXISTS `db_test` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_test`;

-- Dumping structure for table db_test.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_test.orders: ~0 rows (approximately)
INSERT INTO `orders` (`id`, `customer_name`, `total_price`, `created_at`) VALUES
	(1, 'Pembeli Ke-1', 999.00, '2026-07-08 02:43:42'),
	(2, 'Pembeli Ke-2', 999.00, '2026-07-08 02:43:42'),
	(3, 'Pembeli Ke-3', 999.00, '2026-07-08 02:43:42'),
	(4, 'Pembeli Ke-4', 999.00, '2026-07-08 02:43:42'),
	(5, 'Pembeli Ke-5', 999.00, '2026-07-08 02:43:42'),
	(6, 'Pembeli Ke-6', 999.00, '2026-07-08 02:43:42'),
	(7, 'Pembeli Ke-7', 999.00, '2026-07-08 02:43:42'),
	(8, 'Pembeli Ke-8', 999.00, '2026-07-08 02:43:42'),
	(9, 'Pembeli Ke-9', 999.00, '2026-07-08 02:43:42'),
	(10, 'Pembeli Ke-10', 999.00, '2026-07-08 02:43:42');

-- Dumping structure for table db_test.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_test.order_items: ~0 rows (approximately)
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
	(1, 1, 1, 1, 999.00),
	(2, 2, 1, 1, 999.00),
	(3, 3, 1, 1, 999.00),
	(4, 4, 1, 1, 999.00),
	(5, 5, 1, 1, 999.00),
	(6, 6, 1, 1, 999.00),
	(7, 7, 1, 1, 999.00),
	(8, 8, 1, 1, 999.00),
	(9, 9, 1, 1, 999.00),
	(10, 10, 1, 1, 999.00);

-- Dumping structure for table db_test.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `inventory` int NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_inventory` CHECK ((`inventory` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_test.products: ~3 rows (approximately)
INSERT INTO `products` (`id`, `name`, `price`, `inventory`) VALUES
	(1, 'Super Gaming Laptop RTX 5090', 999.00, 0),
	(2, 'Wireless Gaming Mouse', 49.99, 100),
	(3, 'Mechanical Keyboard', 89.99, 50);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
