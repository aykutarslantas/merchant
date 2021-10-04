-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 04 Eki 2021, 05:17:29
-- Sunucu sürümü: 5.7.31
-- PHP Sürümü: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `company`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `merchant`
--

DROP TABLE IF EXISTS `merchant`;
CREATE TABLE IF NOT EXISTS `merchant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Tablo döküm verisi `merchant`
--

INSERT INTO `merchant` (`id`, `name`, `password`) VALUES
(1, 'a', 'a'),
(2, 'b', 'b'),
(3, 'c', 'c'),
(4, 'd', 'd'),
(5, 'c', 'c');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderCode` varchar(60) COLLATE utf8_bin NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `adress` text COLLATE utf8_bin NOT NULL,
  `shippingDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `orderCode`, `product_id`, `quantity`, `adress`, `shippingDate`) VALUES
(1, '1', 1, 20.45, 'Erzurum', '2021-11-09 07:33:55'),
(2, '1', 1, 30.45, 'Malatya', '2021-11-09 07:33:55'),
(3, '1', 1, 20.45, 'Malatya', '2021-11-09 07:33:55'),
(4, '1', 1, 10.45, 'Istanbul', '2021-10-02 21:00:00'),
(5, '1', 1, 10.45, 'Istanbul', '2021-10-02 21:00:00'),
(6, '1', 1, 10.45, 'Istanbul', '2021-10-02 21:00:00'),
(7, '1', 1, 10.36, 'Istanbul', NULL),
(8, '1', 1, 10.36, 'Istanbul', NULL),
(9, '1', 1, 10.36, 'Istanbul', NULL),
(10, '1', 1, 10.36, 'Istanbul', NULL),
(11, '1', 1, 10.36, 'Istanbul', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
