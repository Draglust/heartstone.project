-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-04-2018 a las 21:31:25
-- Versión del servidor: 5.7.19
-- Versión de PHP: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `wow`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auction`
--

DROP TABLE IF EXISTS `auction`;
CREATE TABLE IF NOT EXISTS `auction` (
  `Id` bigint(20) NOT NULL,
  `Apuesta` int(20) NOT NULL,
  `Compra` int(20) NOT NULL,
  `Cantidad` int(20) NOT NULL,
  `Tiempo_restante` varchar(45) NOT NULL,
  `Json_id` bigint(20) UNSIGNED NOT NULL,
  `Owner_id` bigint(20) UNSIGNED NOT NULL,
  `Realm_id` bigint(20) UNSIGNED NOT NULL,
  `Item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_JSON_AUCTION` (`Json_id`),
  KEY `FK_OWNER_AUCTION` (`Owner_id`),
  KEY `FK_REALM_AUCTION` (`Realm_id`),
  KEY `FK_ITEM_AUCTION` (`Item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `Id` bigint(20) NOT NULL,
  `Nombre` varchar(70) NOT NULL,
  `Descripcion` varchar(200) NOT NULL,
  `Icono` varchar(45) NOT NULL,
  `Calidad` int(1) NOT NULL,
  `Nivel_objeto` int(4) NOT NULL,
  `Nive_requerido` int(4) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `json`
--

DROP TABLE IF EXISTS `json`;
CREATE TABLE IF NOT EXISTS `json` (
  `Id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Url` text NOT NULL,
  `Fecha` datetime NOT NULL,
  `Fecha_numerica` int(15) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `owner`
--

DROP TABLE IF EXISTS `owner`;
CREATE TABLE IF NOT EXISTS `owner` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) NOT NULL,
  `Faccion` varchar(45) NOT NULL,
  `Realm_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_REALM_OWNER` (`Realm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `price`
--

DROP TABLE IF EXISTS `price`;
CREATE TABLE IF NOT EXISTS `price` (
  `Id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Precio_medio` int(20) NOT NULL,
  `Precio_minimo` int(20) NOT NULL,
  `Precio_maximo` int(20) NOT NULL,
  `Faccion` varchar(15) NOT NULL,
  `Item_id` bigint(20) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ITEM_PRICE` (`Item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realm`
--

DROP TABLE IF EXISTS `realm`;
CREATE TABLE IF NOT EXISTS `realm` (
  `Id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
