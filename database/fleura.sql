-- =====================================================
-- FLEURA — Base de données MySQL
-- Boutique de mode féminine — Koléa, Algérie
-- Compatible WAMP / phpMyAdmin
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- -----------------------------------------------------
-- Base de données: fleura
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS `fleura` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fleura`;

-- -----------------------------------------------------
-- Table: admins
-- -----------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: categories
-- -----------------------------------------------------
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: products
-- -----------------------------------------------------
CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT(11) NOT NULL DEFAULT 0,
  `sizes` VARCHAR(255) DEFAULT NULL,
  `colors` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `is_new` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: product_images
-- -----------------------------------------------------
CREATE TABLE `product_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: customers
-- -----------------------------------------------------
CREATE TABLE `customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: orders
-- -----------------------------------------------------
CREATE TABLE `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `order_number` VARCHAR(50) NOT NULL,
  `delivery_type` ENUM('domicile','bureau') NOT NULL DEFAULT 'domicile',
  `wilaya` VARCHAR(100) DEFAULT NULL,
  `commune` VARCHAR(100) DEFAULT NULL,
  `address` TEXT,
  `address_complement` TEXT,
  `company_name` VARCHAR(200) DEFAULT NULL,
  `office_address` TEXT,
  `office_phone` VARCHAR(20) DEFAULT NULL,
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'especes',
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('en_attente','confirmee','en_preparation','expediee','livree','annulee') NOT NULL DEFAULT 'en_attente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: order_items
-- -----------------------------------------------------
CREATE TABLE `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT 1,
  `size` VARCHAR(20) DEFAULT NULL,
  `color` VARCHAR(50) DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Données initiales
-- =====================================================

-- Admin par défaut: admin / admin123 (mot de passe hashé avec password_hash PHP)
INSERT INTO `admins` (`name`, `email`, `password`) VALUES
('Administrateur', 'admin@fleura.dz', '$2y$12$FLByOeXE6Fpdzn6rZAyXn.YnLPB3FiXTP.CHV.WxGKnxQ8Zhq/43a');

-- Catégories
INSERT INTO `categories` (`name`, `description`, `image`) VALUES
('Robes', 'Robes élégantes pour toutes les occasions', 'https://images.pexels.com/photos/35193727/pexels-photo-35193727.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Vêtements', 'Vêtements modernes et raffinés', 'https://images.pexels.com/photos/27333313/pexels-photo-27333313.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Sacs', 'Sacs à main en cuir de qualité', 'https://images.pexels.com/photos/7953286/pexels-photo-7953286.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Foulards', 'Foulards en soie pour sublimer vos tenues', 'https://images.pexels.com/photos/36455711/pexels-photo-36455711.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Chaussures', 'Chaussures élégantes et confortables', 'https://images.pexels.com/photos/31450733/pexels-photo-31450733.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Accessoires', 'Accessoires pour compléter votre style', 'https://images.pexels.com/photos/13924051/pexels-photo-13924051.jpeg?auto=compress&cs=tinysrgb&h=650&w=940');

-- Produits
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `stock`, `sizes`, `colors`, `image`, `is_new`) VALUES
(1, 'Robe Rouge Élégante', 'Robe longue rouge parfaite pour les occasions spéciales. Tissu fluide et confortable.', 8500.00, 15, 'S,M,L,XL', 'Rouge', 'https://images.pexels.com/photos/35193727/pexels-photo-35193727.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(1, 'Robe Blanche Florale', 'Robe blanche à motifs floraux, idéale pour les beaux jours.', 7200.00, 10, 'S,M,L', 'Blanc', 'https://images.pexels.com/photos/5582423/pexels-photo-5582423.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(1, 'Robe Noire de Soirée', 'Robe noire élégante pour vos soirées. Coupe ajustée et raffinée.', 9800.00, 8, 'S,M,L,XL', 'Noir', 'https://images.pexels.com/photos/32251059/pexels-photo-32251059.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(1, 'Robe Argentée Studio', 'Robe argentée scintillante pour un look moderne et audacieux.', 11500.00, 5, 'S,M,L', 'Argent', 'https://images.pexels.com/photos/7778891/pexels-photo-7778891.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(2, 'Manteau Noir Élégant', 'Manteau noir structuré, parfait pour un look chic en hiver.', 12500.00, 12, 'S,M,L,XL', 'Noir', 'https://images.pexels.com/photos/27333313/pexels-photo-27333313.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(2, 'Veste en Cuir Noir', 'Veste en cuir noir pour un look rock et élégant.', 9900.00, 7, 'S,M,L', 'Noir', 'https://images.pexels.com/photos/6497713/pexels-photo-6497713.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(2, 'Blouse Blanche Chic', 'Blouse blanche en soie, élégante et intemporelle.', 5500.00, 20, 'S,M,L,XL', 'Blanc', 'https://images.pexels.com/photos/18220443/pexels-photo-18220443.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(2, 'Manteau Fourrure Hiver', 'Manteau à col fourrure pour un hiver élégant et chaleureux.', 14000.00, 6, 'S,M,L', 'Beige', 'https://images.pexels.com/photos/19441381/pexels-photo-19441381.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(3, 'Sac à Main Cuir Marron', 'Sac à main en cuir marron, élégant et fonctionnel.', 7800.00, 14, 'Unique', 'Marron', 'https://images.pexels.com/photos/19354613/pexels-photo-19354613.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(3, 'Sac Beige Moderne', 'Sac à main beige au design moderne et épuré.', 6500.00, 9, 'Unique', 'Beige', 'https://images.pexels.com/photos/22432991/pexels-photo-22432991.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(3, 'Sac Cuir Studio', 'Sac à main en cuir affiché en studio, qualité premium.', 8900.00, 5, 'Unique', 'Marron', 'https://images.pexels.com/photos/7953286/pexels-photo-7953286.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(4, 'Foulard Soie Jaune', 'Foulard en soie jaune aux motifs élégants.', 3200.00, 25, 'Unique', 'Jaune', 'https://images.pexels.com/photos/36455711/pexels-photo-36455711.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(4, 'Foulard Soie Géométrique', 'Foulard en soie aux motifs géométriques colorés.', 3500.00, 18, 'Unique', 'Multicolore', 'https://images.pexels.com/photos/36455708/pexels-photo-36455708.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(5, 'Escarpins Cuir Marron', 'Escarpins en cuir marron, élégance et confort.', 6800.00, 11, '36,37,38,39,40', 'Marron', 'https://images.pexels.com/photos/31450733/pexels-photo-31450733.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(5, 'Talons Noirs Chaîne', 'Talons noirs avec détail chaîne, design moderne.', 7200.00, 8, '36,37,38,39', 'Noir', 'https://images.pexels.com/photos/17826424/pexels-photo-17826424.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0),
(5, 'Talons Nudes Élégants', 'Talons nudes élégants pour toutes les occasions.', 6500.00, 13, '36,37,38,39,40', 'Nude', 'https://images.pexels.com/photos/29393718/pexels-photo-29393718.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(6, 'Collier Doré Élégant', 'Collier doré raffiné pour sublimer vos tenues.', 2800.00, 30, 'Unique', 'Or', 'https://images.pexels.com/photos/13924051/pexels-photo-13924051.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 1),
(6, 'Pendentif Or Minimaliste', 'Pendentif doré minimaliste, parfait pour tous les jours.', 2500.00, 22, 'Unique', 'Or', 'https://images.pexels.com/photos/4889719/pexels-photo-4889719.jpeg?auto=compress&cs=tinysrgb&h=650&w=940', 0);

-- Images supplémentaires de produits
INSERT INTO `product_images` (`product_id`, `image`) VALUES
(1, 'https://images.pexels.com/photos/29843471/pexels-photo-29843471.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(1, 'https://images.pexels.com/photos/34160661/pexels-photo-34160661.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(2, 'https://images.pexels.com/photos/5582678/pexels-photo-5582678.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(5, 'https://images.pexels.com/photos/30676585/pexels-photo-30676585.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(6, 'https://images.pexels.com/photos/10955950/pexels-photo-10955950.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(9, 'https://images.pexels.com/photos/12643950/pexels-photo-12643950.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(14, 'https://images.pexels.com/photos/18935118/pexels-photo-18935118.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
(15, 'https://images.pexels.com/photos/27023941/pexels-photo-27023941.jpeg?auto=compress&cs=tinysrgb&h=650&w=940');
