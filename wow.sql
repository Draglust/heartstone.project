-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 12-07-2018 a las 12:06:04
-- Versión del servidor: 5.7.21
-- Versión de PHP: 5.6.35

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
-- Estructura de tabla para la tabla `class_subclass`
--

DROP TABLE IF EXISTS `class_subclass`;
CREATE TABLE IF NOT EXISTS `class_subclass` (
  `Id` bigint(20) NOT NULL,
  `Clase_nombre` varchar(100) NOT NULL,
  `Subclase_nombre` varchar(100) NOT NULL,
  `Clase_id` int(11) NOT NULL,
  `Subclase_id` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Uni_class_subclass` (`Clase_id`,`Subclase_id`)
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
  `Nivel_requerido` int(4) NOT NULL,
  `Expansion` varchar(100) NOT NULL,
  `Class_Subclass_Id` bigint(20) NOT NULL,
  `Tipo_inventario` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CS_Item` (`Class_Subclass_Id`)
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
  `Fecha_numerica` bigint(20) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=1192 DEFAULT CHARSET=utf8;

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
  `Fecha` datetime NOT NULL,
  `Total_objetos` int(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNIQUE_Item_Fecha` (`Fecha`,`Item_id`,`Faccion`),
  KEY `FK_ITEM_PRICE` (`Item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profession`
--

DROP TABLE IF EXISTS `profession`;
CREATE TABLE IF NOT EXISTS `profession` (
  `Id` bigint(20) NOT NULL,
  `Nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profession_item`
--

DROP TABLE IF EXISTS `profession_item`;
CREATE TABLE IF NOT EXISTS `profession_item` (
  `Profession_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  KEY `FK_PF_Profession` (`Profession_id`),
  KEY `FK_PF_Item` (`item_id`)
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `realm`
--

INSERT INTO `realm` (`Id`, `Nombre`) VALUES
(1, 'Shen\'dralar'),
(2, 'Sanguino'),
(3, 'Uldum'),
(4, 'Zul\'jin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
