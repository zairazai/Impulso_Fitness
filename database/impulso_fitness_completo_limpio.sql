DROP DATABASE IF EXISTS `impulso_fitness`;
CREATE DATABASE `impulso_fitness` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `impulso_fitness`;
-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: localhost    Database: impulso_fitness
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accesos`
--

DROP TABLE IF EXISTS `accesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int NOT NULL,
  `membresia_id` int DEFAULT NULL,
  `resultado` enum('permitido','denegado') COLLATE utf8mb4_general_ci NOT NULL,
  `motivo` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_accesos_membresia` (`membresia_id`),
  KEY `idx_socio_fecha` (`socio_id`,`fecha_hora`),
  CONSTRAINT `accesos_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `accesos_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`),
  CONSTRAINT `fk_accesos_membresia` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`),
  CONSTRAINT `fk_accesos_socio` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accesos`
--

LOCK TABLES `accesos` WRITE;
/*!40000 ALTER TABLE `accesos` DISABLE KEYS */;
INSERT INTO `accesos` VALUES (1,30,2,'permitido','Ingreso registrado','2026-05-25 02:10:35'),(2,30,2,'permitido','Ingreso registrado','2026-05-25 02:10:45'),(3,30,2,'permitido','Ingreso registrado','2026-05-25 02:14:30'),(4,12,2,'permitido','Ingreso registrado','2026-05-25 02:15:20'),(5,25,1,'permitido','Ingreso registrado','2026-05-25 02:16:34'),(6,20,3,'permitido','Ingreso registrado','2026-05-25 02:20:41'),(7,30,2,'permitido','Ingreso registrado','2026-05-25 13:57:01'),(8,20,3,'permitido','Ingreso registrado','2026-05-25 13:57:17'),(9,27,NULL,'denegado','Membresía vencida','2026-05-25 13:57:56'),(10,27,NULL,'denegado','Membresía vencida','2026-05-25 14:11:39'),(11,12,2,'permitido','Ingreso registrado','2026-05-25 16:54:34'),(13,28,NULL,'denegado','Membresía vencida','2026-05-25 18:39:05'),(14,30,2,'permitido','Ingreso registrado','2026-05-25 18:53:37'),(16,30,2,'permitido','Acceso autorizado con membresía activa','2026-05-25 19:20:45'),(17,12,2,'permitido','Acceso diario autorizado','2026-05-25 19:21:29'),(18,33,NULL,'denegado','Membresía vencida','2026-05-25 20:58:56'),(19,33,NULL,'denegado','Membresía vencida','2026-05-25 20:59:20'),(20,33,1,'permitido','Ingreso registrado','2026-05-25 20:59:37'),(21,33,1,'permitido','Ingreso registrado','2026-05-25 21:05:25');
/*!40000 ALTER TABLE `accesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `biometric_credentials`
--

DROP TABLE IF EXISTS `biometric_credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biometric_credentials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `credential_id` varchar(255) NOT NULL,
  `credential_data` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `biometric_credentials`
--

LOCK TABLES `biometric_credentials` WRITE;
/*!40000 ALTER TABLE `biometric_credentials` DISABLE KEYS */;
INSERT INTO `biometric_credentials` VALUES (1,3,'demo123','demo','2026-05-21 02:48:10',NULL);
/*!40000 ALTER TABLE `biometric_credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fingerprints`
--

DROP TABLE IF EXISTS `fingerprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fingerprints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fingerprint_data` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fingerprints`
--

LOCK TABLES `fingerprints` WRITE;
/*!40000 ALTER TABLE `fingerprints` DISABLE KEYS */;
/*!40000 ALTER TABLE `fingerprints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructor_horarios`
--

DROP TABLE IF EXISTS `instructor_horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instructor_horarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `instructor_id` int NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `instructor_id` (`instructor_id`),
  CONSTRAINT `instructor_horarios_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructor_horarios`
--

LOCK TABLES `instructor_horarios` WRITE;
/*!40000 ALTER TABLE `instructor_horarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `instructor_horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instructores`
--

DROP TABLE IF EXISTS `instructores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instructores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `especialidad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `horas_diarias` int DEFAULT '8',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `instructores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instructores`
--

LOCK TABLES `instructores` WRITE;
/*!40000 ALTER TABLE `instructores` DISABLE KEYS */;
/*!40000 ALTER TABLE `instructores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario_movimientos`
--

DROP TABLE IF EXISTS `inventario_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste') COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referencia` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `inventario_movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario_movimientos`
--

LOCK TABLES `inventario_movimientos` WRITE;
/*!40000 ALTER TABLE `inventario_movimientos` DISABLE KEYS */;
INSERT INTO `inventario_movimientos` VALUES (1,1,1,'entrada',2,'2026-05-09 00:28:21','Compra proveedor','Entrada de recepción 08-05-2026'),(2,1,1,'entrada',3,'2026-05-16 08:58:37','PRUEBA-ENTRADA','Prueba de entrada manual'),(3,1,1,'salida',1,'2026-05-16 08:58:54','PRUEBA-SALIDA','Prueba de salida manual'),(4,4,1,'entrada',10,'2026-05-16 18:21:22','Ajuste fisico','Prueba'),(5,3,1,'salida',5,'2026-05-16 18:28:39','Ajuste fisico',''),(6,8,1,'entrada',90,'2026-05-16 21:25:31','Ajuste fisico',''),(7,4,1,'ajuste',2,'2026-05-16 22:18:23','Producto caducado','Se hizo revisión mensual de caducidades y dos productos caducan este mes.'),(8,7,1,'salida',5,'2026-05-17 19:58:12',NULL,'45'),(9,2,1,'salida',1,'2026-05-17 20:15:00','Transferencia sucursal','Tranferenciaa a sucursal 89'),(10,10,2,'salida',1,'2026-05-24 09:39:33','Venta #1','Salida por venta'),(11,7,2,'salida',2,'2026-05-24 09:39:33','Venta #1','Salida por venta'),(12,5,2,'salida',1,'2026-05-24 09:39:33','Venta #1','Salida por venta'),(13,4,2,'salida',1,'2026-05-24 09:39:33','Venta #1','Salida por venta'),(14,3,2,'salida',1,'2026-05-24 18:10:45','Venta #2','Salida por venta'),(15,10,2,'salida',1,'2026-05-24 18:10:45','Venta #2','Salida por venta'),(16,7,2,'salida',2,'2026-05-24 18:10:45','Venta #2','Salida por venta'),(17,4,2,'salida',1,'2026-05-24 18:10:45','Venta #2','Salida por venta'),(18,8,2,'salida',1,'2026-05-24 18:52:33','Venta #3','Salida por venta'),(19,2,2,'salida',1,'2026-05-24 18:52:33','Venta #3','Salida por venta'),(22,8,2,'salida',1,'2026-05-25 14:13:21','Venta #5','Salida por venta'),(23,7,2,'salida',1,'2026-05-25 14:13:21','Venta #5','Salida por venta'),(24,3,2,'salida',1,'2026-05-25 14:13:21','Venta #5','Salida por venta'),(27,5,2,'salida',8,'2026-05-25 18:53:31','Transferencia sucursal',''),(30,8,2,'salida',2,'2026-05-25 19:32:21','Venta #10','Salida por venta'),(31,3,2,'salida',1,'2026-05-25 19:43:21','Venta #11','Salida por venta'),(32,5,2,'salida',1,'2026-05-25 19:43:21','Venta #11','Salida por venta'),(33,3,2,'salida',8,'2026-05-25 19:48:08','Venta #12','Salida por venta');
/*!40000 ALTER TABLE `inventario_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membresias`
--

DROP TABLE IF EXISTS `membresias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membresias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `duracion_dias` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membresias`
--

LOCK TABLES `membresias` WRITE;
/*!40000 ALTER TABLE `membresias` DISABLE KEYS */;
INSERT INTO `membresias` VALUES (1,'Pase Diario',1,50.00,'Acceso por 1 día'),(2,'Pase Semanal',7,200.00,'Acceso por 7 días'),(3,'Pase Mensual',30,500.00,'Acceso por 30 días');
/*!40000 ALTER TABLE `membresias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int DEFAULT NULL,
  `tipo` enum('membresia_por_vencer','membresia_vencida','inventario_bajo') COLLATE utf8mb4_general_ci NOT NULL,
  `mensaje` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `socio_id` (`socio_id`),
  CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_membresia`
--

DROP TABLE IF EXISTS `pagos_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_membresia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int NOT NULL,
  `membresia_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referencia` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `socio_id` (`socio_id`),
  KEY `membresia_id` (`membresia_id`),
  CONSTRAINT `pagos_membresia_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `pagos_membresia_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_membresia`
--

LOCK TABLES `pagos_membresia` WRITE;
/*!40000 ALTER TABLE `pagos_membresia` DISABLE KEYS */;
INSERT INTO `pagos_membresia` VALUES (1,2,1,50.00,'efectivo','2026-04-20 04:18:48',NULL),(2,9,1,50.00,'tarjeta','2026-04-20 04:38:29',NULL),(3,7,3,500.00,'transferencia','2026-04-20 04:41:30',NULL),(4,10,3,500.00,'transferencia','2026-04-21 00:20:14',NULL),(5,11,2,200.00,'efectivo','2026-04-21 01:15:14',NULL),(6,12,1,50.00,'efectivo','2026-04-21 02:09:06',NULL),(7,13,1,50.00,'transferencia','2026-04-21 03:22:03',NULL),(8,14,3,500.00,'transferencia','2026-04-27 07:24:05',NULL),(9,15,1,50.00,'efectivo','2026-04-27 08:28:18',NULL),(10,16,3,500.00,'transferencia','2026-04-27 08:30:26',NULL),(11,5,2,200.00,'tarjeta','2026-04-27 08:31:20',NULL),(12,17,2,200.00,'transferencia','2026-04-27 15:27:42',NULL),(13,12,1,50.00,'efectivo','2026-04-27 20:40:29',NULL),(14,11,1,50.00,'tarjeta','2026-04-27 20:41:29',NULL),(15,13,3,500.00,'transferencia','2026-04-27 20:42:16',NULL),(16,12,1,50.00,'tarjeta','2026-04-27 20:42:58',NULL),(17,4,1,50.00,'efectivo','2026-04-27 20:45:20',NULL),(18,11,1,50.00,'efectivo','2026-04-27 21:25:44','REC-20260427-000018'),(19,11,3,500.00,'transferencia','2026-05-09 04:04:38','REC-20260508-000019'),(20,20,3,500.00,'transferencia','2026-05-14 07:55:31','REC-20260514-000020'),(21,12,1,50.00,'efectivo','2026-05-15 04:21:08','REC-20260514-000021'),(22,21,2,200.00,'transferencia','2026-05-15 04:25:32','REC-20260514-000022'),(23,22,1,50.00,'tarjeta','2026-05-15 06:41:05','REC-20260514-000023'),(24,23,1,50.00,'tarjeta','2026-05-15 17:37:19','REC-20260515-000024'),(25,23,1,50.00,'tarjeta','2026-05-16 06:29:07','REC-20260515-000025'),(26,20,1,50.00,'tarjeta','2026-05-16 07:04:47','REC-20260516-000026'),(27,21,2,200.00,'tarjeta','2026-05-16 07:14:06','REC-20260516-000027'),(28,6,1,50.00,'efectivo','2026-05-16 07:29:15','REC-20260516-000028'),(29,19,1,50.00,'transferencia','2026-05-16 07:51:21','REC-20260516-000029'),(30,18,1,50.00,'efectivo','2026-05-16 07:54:40','REC-20260516-000030'),(31,24,2,200.00,'transferencia','2026-05-17 19:48:15','REC-20260517-000031'),(32,26,2,200.00,'efectivo','2026-05-17 21:04:27','REC-20260517-000032'),(33,28,2,200.00,'efectivo','2026-05-17 22:39:35','REC-20260517-000033'),(34,12,3,500.00,'efectivo','2026-05-17 22:47:57','REC-20260517-000034'),(35,26,1,50.00,'transferencia','2026-05-17 22:50:00','REC-20260517-000035'),(36,15,1,50.00,'efectivo','2026-05-17 22:56:43','REC-20260517-000036'),(37,29,1,50.00,'efectivo','2026-05-17 23:13:52','REC-20260517-000037'),(38,6,1,50.00,'transferencia','2026-05-17 23:14:22','REC-20260517-000038'),(39,12,1,50.00,'efectivo','2026-05-24 07:41:08','REC-20260524-000039'),(40,18,2,200.00,'transferencia','2026-05-24 17:37:43','REC-20260524-000040'),(41,12,2,200.00,'tarjeta','2026-05-24 18:00:58','REC-20260524-000041'),(42,12,2,200.00,'transferencia','2026-05-24 18:18:01','REC-20260524-000042'),(43,30,2,200.00,'transferencia','2026-05-24 18:26:55','REC-20260524-000043'),(44,12,2,200.00,'tarjeta','2026-05-24 19:13:07','REC-20260524-000044'),(45,19,3,500.00,'tarjeta','2026-05-24 19:13:45','REC-20260524-000045'),(46,30,2,200.00,'efectivo','2026-05-24 22:52:22','REC-20260524-000046'),(47,30,2,200.00,'tarjeta','2026-05-24 23:11:08','REC-20260524-000047'),(48,25,1,50.00,'efectivo','2026-05-25 02:15:04','REC-20260524-000048'),(49,18,2,200.00,'efectivo','2026-05-25 20:59:13','REC-20260525-000049'),(50,33,1,50.00,'tarjeta','2026-05-25 20:59:32','REC-20260525-000050');
/*!40000 ALTER TABLE `pagos_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `costo_compra` decimal(10,2) DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL,
  `stock` int DEFAULT '0',
  `stock_minimo` int DEFAULT '5',
  `icono` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'bi-box-seam',
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'PR001','Proteína Whey','Suplementos','',600.00,1200.00,8,2,'bi-capsule',1,'2026-05-08 16:55:12','2026-05-16 08:58:54'),(2,'PR002','Proteína Itholate','Suplementos','',800.00,1300.00,1,1,'bi-capsule',1,'2026-05-09 00:32:12','2026-05-24 18:52:33'),(3,'PR003','Agua','Bebidas','',15.00,29.00,84,9,'bi-lightning-charge',1,'2026-05-09 04:10:08','2026-05-25 19:48:08'),(4,'PR004','Producto prueba','Prueba','Producto temporal para validar SP',10.00,20.00,0,2,'bi-box-seam',1,'2026-05-16 08:57:59','2026-05-24 18:10:45'),(5,'PR005','Monster Energy','Bebidas','',35.00,45.00,2,6,'bi-lightning-charge',1,'2026-05-16 16:52:12','2026-05-25 19:43:21'),(6,'PR006','Producto equis','Suplementos','',100.00,200.00,8,2,'bi-capsule',0,'2026-05-16 18:31:08','2026-05-16 18:31:25'),(7,'PR007','Galleta Amaranto','Snacks','hola',10.00,25.00,0,6,'bi-box-seam',1,'2026-05-16 21:22:29','2026-05-25 14:13:21'),(8,'PR008','Gatorade','Bebidas','hola',20.00,35.00,98,2,'bi-cup-straw',1,'2026-05-16 21:24:37','2026-05-25 19:32:21'),(9,'PR009','Cold Brew','Bebidas','Cafe frío',20.00,45.00,24,12,'bi-lightning-charge',0,'2026-05-17 19:57:05','2026-05-17 19:57:26'),(10,'PR010','Chocolate amargo','Snacks','',21.00,45.00,18,5,'bi-box-seam',0,'2026-05-17 20:32:19','2026-05-24 18:55:49'),(11,'PR011','Toalla','Ropa','',15.00,50.00,10,5,'bi-box-seam',1,'2026-05-24 08:30:46','2026-05-24 08:30:46'),(12,'PR012','Toalla grande','Ropa','Toalla grande para sudor',80.00,100.00,12,5,'bi-box-seam',1,'2026-05-24 18:55:37','2026-05-24 18:55:37');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socio_biometria`
--

DROP TABLE IF EXISTS `socio_biometria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socio_biometria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int NOT NULL,
  `huella_hash` varbinary(512) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `socio_id` (`socio_id`),
  CONSTRAINT `socio_biometria_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socio_biometria`
--

LOCK TABLES `socio_biometria` WRITE;
/*!40000 ALTER TABLE `socio_biometria` DISABLE KEYS */;
INSERT INTO `socio_biometria` VALUES (1,4,_binary '258b7015bf763536bbf12bcefcf8e77fd79f71191ac60130e51b1f8be672b919','2026-04-19 21:27:42'),(2,5,_binary '44b2402a1bebc35a318b16f81476cb428f7fe161165d4f5f3093ed833ed92e97','2026-04-20 01:09:39'),(3,6,_binary '5e94dee675dbd9d889162bd01c916f37d3a85a603e364f9cef8538ae2964892e','2026-04-19 23:33:10'),(4,7,_binary '2b589a1cc9809ff3eb6f16be5a629726b35d22f88e641f578a476055ce41c243','2026-04-19 23:59:00'),(5,8,_binary '5e507365e6ca0d2381328c4ec519d6d36c650b69db824362c905a5d004444e57','2026-04-20 00:07:07'),(7,2,_binary '65e27348e178d62babc31094445e956862eed5b683d9b82d4c50c9f2945f85f2','2026-04-20 02:08:14'),(8,9,_binary '4e1bd345b42643a5f429e78decfc33006774bbd266779dc56cc1f1cc0001bb27','2026-04-20 04:37:37'),(9,10,_binary 'b25216eafc37d156a3d6f49ec8598018356fddac1af08f82daa158b7cc6bc2b5','2026-04-21 00:19:51'),(10,11,_binary '1ca92162823097a53581620a71b9739be0bb7068aa2a00850ac1a5373d4175b1','2026-04-21 01:14:49'),(11,12,_binary 'a3c3f5f6f60cad6b59ed031af9f0fd73fb2f1999210b1af9c94db23b7ddb1922','2026-04-21 02:07:46'),(12,13,_binary 'c04a9aa5c49d7da1b9799f3a80762fc3fe7194104c7b2f00b75493d36a3bd590','2026-04-21 03:21:23'),(13,14,_binary 'e8f48bdc834540b4971e1f46e20004fc0528056ea8bd14672e56148dded635f2','2026-04-27 07:23:48'),(14,15,_binary 'c0ba2d8f8e1842f6c0ac741fee0e6400eb265c734a631120bd2a67928f9a5028','2026-04-27 08:01:39'),(15,16,_binary 'f4808ec321e936e7204ae8812a7be601e8ddc2cfe6ac81b175fdd4738cde5edd','2026-04-27 08:30:15'),(16,17,_binary 'c8150d2c3271bf74d795bb120348af42fa6e88491373b42db99733f970c7ead9','2026-04-27 15:27:26'),(17,19,_binary '7aa5884717528639f31fbb058b1ba8c53f86b2050aa00b90a51138631710a9f1','2026-05-14 07:45:44'),(18,20,_binary 'b7de045c84c0681f180cfae2176d5a8016d2615daaf0558937f566e07aaa61c3','2026-05-14 07:54:56'),(19,21,_binary '5f294c033aa930f9211379b5b6fc7237def85d35c3d077f87375c27735e740a5','2026-05-15 04:24:57'),(20,22,_binary '76f9caa6a703c03394ccb1a2e209650e1052766197d05e7233af3347f159e8b3','2026-05-15 06:40:39'),(21,23,_binary '1dae0cd44e9e89bff8f0e255b202fd313d0136e2ef053027cfe6aad6b56139f7','2026-05-15 17:36:57'),(22,24,_binary 'c837347f46662bfb2beafb1f707f7d91871144a8c4c2ed355b14f6aa3e552604','2026-05-17 19:47:42'),(23,25,_binary '22e1d5f37ba769691bbe8cdeaa1baaaf53493e55758da90d36627b774217fc87','2026-05-17 21:02:39'),(24,26,_binary '76690de3aca1b79e771f8831dd5eb68ec363b3e14965f692409c6e98bf0598ed','2026-05-17 21:04:16'),(25,27,_binary '64edd634b32dc4d420542a8a93ccd9b7732949697acb8a867b0e026d3ba54542','2026-05-17 22:25:45'),(26,28,_binary 'c852f7f306ca877cabe55b240acdfcae073744c43174551c34f39c25fc0e2c4a','2026-05-17 22:39:13'),(27,29,_binary '76a993531e5f2246699c9cd4fabf2887781eeff1a3f268a63a3f320f070ee689','2026-05-17 23:13:42');
/*!40000 ALTER TABLE `socio_biometria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socio_instructor_sesiones`
--

DROP TABLE IF EXISTS `socio_instructor_sesiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socio_instructor_sesiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int NOT NULL,
  `instructor_id` int NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `asistio` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `socio_id` (`socio_id`,`fecha`),
  KEY `instructor_id` (`instructor_id`),
  CONSTRAINT `socio_instructor_sesiones_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `socio_instructor_sesiones_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socio_instructor_sesiones`
--

LOCK TABLES `socio_instructor_sesiones` WRITE;
/*!40000 ALTER TABLE `socio_instructor_sesiones` DISABLE KEYS */;
/*!40000 ALTER TABLE `socio_instructor_sesiones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socio_membresia`
--

DROP TABLE IF EXISTS `socio_membresia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socio_membresia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `socio_id` int NOT NULL,
  `membresia_id` int NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `socio_id` (`socio_id`),
  KEY `membresia_id` (`membresia_id`),
  CONSTRAINT `socio_membresia_ibfk_1` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`),
  CONSTRAINT `socio_membresia_ibfk_2` FOREIGN KEY (`membresia_id`) REFERENCES `membresias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socio_membresia`
--

LOCK TABLES `socio_membresia` WRITE;
/*!40000 ALTER TABLE `socio_membresia` DISABLE KEYS */;
INSERT INTO `socio_membresia` VALUES (1,2,1,'2026-04-20 00:00:00','2026-04-21 00:00:00',0),(2,9,1,'2026-04-21 00:00:00','2026-04-22 00:00:00',0),(3,7,3,'2026-04-21 00:00:00','2026-05-21 00:00:00',0),(4,10,3,'2026-04-22 00:00:00','2026-05-22 00:00:00',0),(5,11,2,'2026-04-20 00:00:00','2026-04-27 00:00:00',0),(6,12,1,'2026-04-20 00:00:00','2026-04-21 00:00:00',0),(7,13,1,'2026-04-20 00:00:00','2026-04-21 00:00:00',0),(8,14,3,'2026-04-27 00:00:00','2026-05-27 00:00:00',1),(9,15,1,'2026-04-27 00:00:00','2026-04-28 00:00:00',0),(10,16,3,'2026-04-27 00:00:00','2026-05-27 00:00:00',1),(11,5,2,'2026-04-27 00:00:00','2026-05-04 00:00:00',0),(12,17,2,'2026-04-27 00:00:00','2026-05-04 00:00:00',0),(13,12,1,'2026-04-26 05:00:00','2026-04-27 05:00:00',0),(14,11,1,'2026-04-27 22:41:00','2026-04-28 22:41:00',0),(15,13,3,'2026-04-26 10:41:00','2026-05-26 10:41:00',1),(16,12,1,'2026-04-27 13:42:00','2026-04-28 13:42:00',0),(17,4,1,'2026-04-27 13:30:00','2026-04-28 13:30:00',0),(18,11,1,'2026-04-27 08:25:00','2026-04-28 08:25:00',0),(19,11,3,'2026-05-09 06:04:00','2026-06-08 06:04:00',1),(20,20,3,'2026-05-14 09:54:00','2026-06-13 09:54:00',1),(21,12,1,'2026-05-15 21:20:00','2026-05-16 21:20:00',0),(22,21,2,'2026-05-15 21:25:00','2026-05-22 21:25:00',0),(23,22,1,'2026-05-14 08:40:00','2026-05-15 08:40:00',0),(24,23,1,'2026-05-15 09:36:00','2026-05-16 09:36:00',0),(25,23,1,'2026-05-15 20:28:00','2026-05-16 20:28:00',0),(26,20,1,'2026-06-13 09:54:00','2026-06-14 09:54:00',0),(27,21,2,'2026-05-22 21:25:00','2026-05-29 21:25:00',0),(28,6,1,'2026-05-16 00:29:00','2026-05-17 00:29:00',0),(29,19,1,'2026-05-16 00:51:00','2026-05-17 00:51:00',0),(30,18,1,'2026-05-16 00:00:00','2026-05-17 00:00:00',0),(31,24,2,'2026-05-17 09:47:00','2026-05-24 09:47:00',0),(32,26,2,'2026-05-17 23:04:00','2026-05-24 23:04:00',0),(33,28,2,'2026-05-17 00:39:00','2026-05-24 00:39:00',0),(34,12,3,'2026-05-18 00:47:00','2026-06-17 00:47:00',0),(35,26,1,'2026-05-17 00:49:00','2026-05-18 00:49:00',0),(36,15,1,'2026-05-17 13:57:00','2026-05-18 13:57:00',0),(37,29,1,'2026-05-18 01:13:00','2026-05-19 01:13:00',0),(38,6,1,'2026-05-17 01:14:00','2026-05-18 01:14:00',0),(39,12,1,'2026-05-24 09:40:00','2026-05-25 09:40:00',0),(40,18,2,'2026-05-24 19:37:00','2026-05-31 19:37:00',0),(41,12,2,'2026-05-24 20:00:00','2026-05-31 20:00:00',0),(42,12,2,'2026-05-24 20:17:00','2026-05-31 20:17:00',0),(43,30,2,'2026-05-24 20:26:00','2026-05-31 20:26:00',0),(44,12,2,'2026-05-24 21:13:00','2026-05-31 21:13:00',1),(45,19,3,'2026-05-24 21:13:00','2026-06-23 21:13:00',1),(46,30,2,'2026-05-25 00:52:00','2026-06-01 00:52:00',1),(47,30,2,'2026-06-01 00:52:00','2026-06-08 00:52:00',1),(48,25,1,'2026-05-25 04:14:00','2026-05-26 04:14:00',1),(49,18,2,'2026-05-25 22:59:00','2026-06-01 22:59:00',1),(50,33,1,'2026-05-25 22:59:00','2026-05-26 22:59:00',1);
/*!40000 ALTER TABLE `socio_membresia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socios`
--

DROP TABLE IF EXISTS `socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nombres` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido_paterno` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `apellido_materno` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` enum('activo','inactivo','suspendido') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactivo',
  `fecha_registro` date NOT NULL,
  `genero` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contacto_emergencia_nombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contacto_emergencia_telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `calle` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `colonia` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_postal` char(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `socios_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socios`
--

LOCK TABLES `socios` WRITE;
/*!40000 ALTER TABLE `socios` DISABLE KEYS */;
INSERT INTO `socios` VALUES (1,NULL,'Juan Perez López',NULL,NULL,'6691272667','juanperez@gmail.com','2000-09-10','inactivo','2026-04-16','Masculino','Juan Ramos','4444444444','San Matias',NULL,NULL,NULL,'San Matias','ninguna'),(2,NULL,'Miguel Lizarraga Osuna',NULL,NULL,'6692481620','cris00@gmail.com','2006-03-19','inactivo','2026-04-17','Masculino','Alondra Hernandez','6691666339','San Juan 23',NULL,NULL,NULL,'San Juan 23','ninguna'),(3,NULL,'Juan','Ramos','Hernandez','8889001231','juaito@live.com.mx','1966-07-16','inactivo','2026-04-18','Masculino','Fanny Cañedo','3784920204','San Matias',NULL,NULL,NULL,'San Matias','OKK'),(4,NULL,'Gabriela','Mora','Ramirez','6690000000','gabo@gmail.com','2005-10-19','inactivo','2026-04-19','Femenino','6690000001','6690000001','San Jose #1244 Azul Marino, Mazatlan',NULL,NULL,NULL,'San Jose #1244 Azul Marino, Mazatlan','Este socio no registro ninguna nota adicional.'),(5,NULL,'Maria Navarro Lopez',NULL,NULL,'6691666335','mari@live.com.mx','1980-07-08','inactivo','2026-04-19','Femenino','Alondra Hernandez','6691666335','San Martin 89 Real Pacifico, Mazatlan',NULL,NULL,NULL,'San Martin 89 Real Pacifico, Mazatlan','Este socio tiene una lesión en el hombro derecho'),(6,NULL,'Juan Manuel Silva',NULL,NULL,'0000000000','juan2@outlook.com','2002-04-12','inactivo','2026-04-19','Masculino','7892289202','7892289202','Calle 78 #456 Terrranova, Mazatlan',NULL,NULL,NULL,'Calle 78 #456 Terrranova, Mazatlan','Sin notas adicionales'),(7,NULL,'Jose Hernandez Cañedo',NULL,NULL,'1111111111','jose@live.com','2000-02-10','suspendido','2026-04-19','Masculino','Alondra Hernandez','2222222222','Calle Juan Grijalva #389',NULL,NULL,NULL,'Calle Juan Grijalva #389','Ninguna'),(8,NULL,'Ivana','Ramos','De la Cruz','6691666339','ivana@gmail.com','1990-09-13','inactivo','2026-04-19','Femenino','Juan Ramos','6691272560','San Matias','67','Real Pacifico','82124','San Matias 67 Real Pacifico','NINGUNA'),(9,NULL,'Oscar Torres Olivier',NULL,NULL,'6692497367','oscar3@outlook.com','2006-01-23','inactivo','2026-04-19','Masculino','Cristobal Lizarraga','6699248162','San Marcos #45 Real Del Valle',NULL,NULL,NULL,'San Marcos #45 Real Del Valle','niguno'),(10,NULL,'Zaira','Ramos','','6682345678','zai@gmail.com','2002-06-23','inactivo','2026-04-20','Femenino','Juan Ramos','1111111111','San Matias','89','jasfc','29034','San Matias #123 Colinas','ola'),(11,NULL,'Carlos','Camacho','Sanchez','3333333333','carcar@outlook.com','1990-07-30','activo','2026-04-20','Masculino','Maria Tejeda','9999999999','Calle Ola #345, Fracc. Haciendas, Mazatlán',NULL,NULL,NULL,'Calle Ola #345, Fracc. Haciendas, Mazatlán','Este socio tiene una lesión en el hombro izquierdo'),(12,NULL,'Angel Urias Lopez','Urias','Lopez','0000000000','angel1@hotmail.com','2001-01-01','activo','2026-04-20','Masculino','Zaira Ramos','2222222222','CALLE 2 , FRACC REAL , MOCHIS','1929','TERRANOVA','81270','CALLE 2 , FRACC REAL , MOCHIS','NINGUNA'),(13,NULL,'Fanny de la Cruz',NULL,NULL,'8888888888','fan@hotmail.com','2005-01-01','activo','2026-04-20','Femenino','Juan Rene','4444444458','Calle 78 San Rafael',NULL,NULL,NULL,'Calle 78 San Rafael','niguna'),(14,NULL,'Doralin','Zavala','Partida','7777777777','dora@hotmail.com','2000-05-12','activo','2026-04-27','Femenino','Juan Rene','0000000000','morelos','893','hagdkcjhcd','91038','Calle 3 #1344 San Rafael','Todo ok'),(15,NULL,'Eduardo','Osuna','Roa','6692543219','edu@hotmail.com','1980-02-01','inactivo','2026-04-27','Otro','Maria Rendon','4444444444','Calle 2','234','Real Pacifico','98819','Calle 2 #345 Real Pacifico','NINGUNA'),(16,NULL,'Roberto Juarez Mojica',NULL,NULL,'4444444444','rober@live.com.mx','1998-11-30','activo','2026-04-27','Masculino','Ivana Ramos','5555555555','Calle 6 #45 Colinas, Mazatlán',NULL,NULL,NULL,'Calle 6 #45 Colinas, Mazatlán','NINGUNA'),(17,NULL,'Zaira','Ra','C','6687123646','zaizair@live.com.mx','2002-08-23','inactivo','2026-04-27','Femenino','Juan Perez','6691666335','San Matias 78 Lagos',NULL,NULL,NULL,'San Matias 78 Lagos','ninguna'),(18,NULL,'Alejandro','Bustamante','Hernandez','1234567890','alejandro.30@hotmail.com','1990-09-19','activo','2026-05-14','Masculino','Zaira Ramos','0000000000','San Jose','109','Real del Valle','82124',NULL,'Este socio es entrenador personal.'),(19,NULL,'Alfredo','Gomez','Díaz','7388278492','alf345@outlook.com','1980-11-04','activo','2026-05-14','Masculino','Sheyla Gomez','2222222222','Insurgentes','89','Centro','82100',NULL,''),(20,NULL,'Gael','Muñoz','Estrada','0000000000','gaelm34@outlook.com','2026-05-15','suspendido','2026-05-14','Otro','Esthela Zamora','2874207478','Catalina','jjuimh','hgg','82124',NULL,'jhhj'),(21,NULL,'Zai','Ramos','De la Cruz','6691272667','arizazaira23@gmail.com','2002-06-23','suspendido','2026-05-14','Femenino','Ivana Ramos de la Cruz','6692543213','San Matías','hola','Real del Valle','82124',NULL,''),(22,NULL,'Olivier','Juarez','Perez','6692218518','ollie69@gmail.com','2006-06-06','inactivo','2026-05-14','Femenino','Oscar Armando Lizarraga','3679204082','Catalina','2874','Hacienda de Seminario','82242',NULL,'lol'),(23,NULL,'Oscar','Liz','uhfnv','5555555555','jashsis@gamil.com','2007-02-11','inactivo','2026-05-15','Masculino','Maria Rendon','4444444444','Palos Prietos','234','Francisco Villa','53522',NULL,''),(24,NULL,'Rene','Falcon','Rivera','2848502019','rane23@live.com.mx','2006-03-07','inactivo','2026-05-17','Masculino','Xenia Dorcas','6743902059','Insurgentes','3456','Centro','34534',NULL,'HOLA'),(25,NULL,'Lola','Ortega','Espinoza','2345213452','loloa@gamil.com','1990-08-02','activo','2026-05-17','Femenino','Maria Rendon','3728949011','Catalina','124','Fraccionamiento Real del Valle','22345',NULL,'OK'),(26,NULL,'Javier','Lopez','de','6691666335','loahhde23@gmail.com','2000-12-12','inactivo','2026-05-17','Otro','Eva DIaz','1111111111','francisco villa','677','Fraccionamiento Real del Valle','45654',NULL,'niguna'),(27,NULL,'Karina','Mendoza','','1111111111','kari_23@gmail.com','1990-08-19','inactivo','2026-05-17','Femenino','Juan Rene','1111111111','La marin','230','Cerritos','45663',NULL,'niguna'),(28,NULL,'Cristopher','Rodriguez','','5567902311','criscris45@gmail.com','1985-11-20','inactivo','2026-05-17','Masculino','Uriel Barajas','2789746267','Insurgentes','123','Oscar Escoobeod','89033',NULL,'NIGUNA'),(29,NULL,'Prubea','Socio','','1111111111','ejemplo.23@gmail.com','1978-09-12','inactivo','2026-05-17','Femenino','Prueba dos','2222222222','hola','124','fra','24522',NULL,'niguna'),(30,NULL,'Kaleigh','Preciado','Lopez','6688829263','hanzelfive06@gmail.com','1992-12-13','activo','2026-05-24','Femenino','Kaleigh Preciado','6681696276','Salvador Alvarado','2355','Villas del Sol','81249',NULL,''),(31,NULL,'Maria del Carmen','Cinco','Araya','6688829263','hanzelfive06@gmail.com','1999-12-12','inactivo','2026-05-24','Femenino','Kaleigh Preciado','6681361752','Salvador Alvarado','2355','Villas del Sol','81249',NULL,''),(32,NULL,'Cliente general',NULL,NULL,NULL,NULL,'1999-10-12','inactivo','2026-05-25','Otro','wer','2222222222','Rio santa catalina','206','TERRANOVA','25616',NULL,''),(33,NULL,'Angel de Jesus','Urias','Lopez','6681361752','urias.angel.de.jesus@aptiv.com','1992-10-12','activo','2026-05-25','Masculino','Kaleigh Preciado','6681361752','Rio santa catalina','1929','TERRANOVA','81270',NULL,'');
/*!40000 ALTER TABLE `socios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_biometrics`
--

DROP TABLE IF EXISTS `user_biometrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_biometrics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `biometric_hash` varbinary(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_biometrics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_biometrics`
--

LOCK TABLES `user_biometrics` WRITE;
/*!40000 ALTER TABLE `user_biometrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_biometrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('Admin','Recepcion','Instructor','Socio') COLLATE utf8mb4_general_ci NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$mj38z2gP55q6owo.DG/yuej.tkqxWhtHtPd30fbFOHctIxqdkG3ge','admin@impulso.com','Admin',1,'2026-04-17 05:52:38'),(2,'a.uriasl','$2y$10$jPnDxPDfye6kKa.M3jV1FOHjMYAnRv4hfGfYdfgM.1K6W3e/S7wRm','urias.angel.de.jesus@aptiv.com','Admin',1,'2026-05-21 02:30:47'),(3,'angel','$2y$10$HtQlOKnqAieadnm61ZH6cuIieRV1W/F75OIVwzLMT0wDXZz.0OlqO','angel@angel.com','Admin',1,'2026-05-21 02:48:03'),(4,'a.uriaslopez','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9C5kz1kBzRZV3Q1rC4dT5a','aurias@impulso.com','Admin',1,'2026-05-24 06:34:43'),(5,'qwerty','$2y$10$om9rUbR/HhvVlbTDjjHl1.ZbyOKgmL.W5E5XLm4/qq1K6UZZgEy6e',NULL,'Admin',1,'2026-05-24 06:57:47'),(6,'AngelUrias','$2y$10$f51ba31YRd3dZithjWtp0ubRYRmp5e5303Iz/cZIXtq6/iIumiC2y','angel@urias.com','Admin',1,'2026-05-25 13:59:06');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venta_detalle`
--

DROP TABLE IF EXISTS `venta_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venta_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `venta_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `venta_detalle_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`),
  CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venta_detalle`
--

LOCK TABLES `venta_detalle` WRITE;
/*!40000 ALTER TABLE `venta_detalle` DISABLE KEYS */;
INSERT INTO `venta_detalle` VALUES (1,1,10,1,45.00),(2,1,7,2,25.00),(3,1,5,1,45.00),(4,1,4,1,20.00),(5,2,3,1,29.00),(6,2,10,1,45.00),(7,2,7,2,25.00),(8,2,4,1,20.00),(9,3,8,1,35.00),(10,3,2,1,1300.00),(14,5,8,1,35.00),(15,5,7,1,25.00),(16,5,3,1,29.00),(25,10,8,2,35.00),(26,11,3,1,29.00),(27,11,5,1,45.00),(28,12,3,8,29.00);
/*!40000 ALTER TABLE `venta_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `socio_id` int DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'efectivo',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `socio_id` (`socio_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,2,16,160.00,'efectivo','2026-05-24 09:39:33'),(2,2,NULL,144.00,'efectivo','2026-05-24 18:10:45'),(3,2,11,1335.00,'tarjeta','2026-05-24 18:52:33'),(5,2,NULL,89.00,'efectivo','2026-05-25 14:13:21'),(10,2,12,70.00,'efectivo','2026-05-25 19:32:21'),(11,2,NULL,74.00,'efectivo','2026-05-25 19:43:21'),(12,2,32,232.00,'transferencia','2026-05-25 19:48:08');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-25 17:43:16

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- Tomados del Script viejo y compatibles sin DEFINER
-- =====================================================

DELIMITER $$
DROP PROCEDURE IF EXISTS `sp_actualizar_estados_membresias`$$
DROP PROCEDURE IF EXISTS `sp_actualizar_socio_completo`$$
DROP PROCEDURE IF EXISTS `sp_buscar_socios_para_membresia`$$
DROP PROCEDURE IF EXISTS `sp_desactivar_socio`$$
DROP PROCEDURE IF EXISTS `sp_historial_pagos_membresia`$$
DROP PROCEDURE IF EXISTS `sp_insertar_socio_completo`$$
DROP PROCEDURE IF EXISTS `sp_inventario_movimientos_listar`$$
DROP PROCEDURE IF EXISTS `sp_inventario_registrar_movimiento`$$
DROP PROCEDURE IF EXISTS `sp_listar_membresias`$$
DROP PROCEDURE IF EXISTS `sp_listar_socios`$$
DROP PROCEDURE IF EXISTS `sp_obtener_historial_reciente_pagos_socio`$$
DROP PROCEDURE IF EXISTS `sp_obtener_huella_socio`$$
DROP PROCEDURE IF EXISTS `sp_obtener_membresia_activa_socio`$$
DROP PROCEDURE IF EXISTS `sp_obtener_membresia_por_nombre`$$
DROP PROCEDURE IF EXISTS `sp_obtener_recibo_membresia`$$
DROP PROCEDURE IF EXISTS `sp_obtener_socio_por_id`$$
DROP PROCEDURE IF EXISTS `sp_productos_listar`$$
DROP PROCEDURE IF EXISTS `sp_productos_stock_bajo`$$
DROP PROCEDURE IF EXISTS `sp_producto_baja_logica`$$
DROP PROCEDURE IF EXISTS `sp_producto_guardar`$$
DROP PROCEDURE IF EXISTS `sp_producto_obtener`$$
DROP PROCEDURE IF EXISTS `sp_registrar_huella_socio`$$
DROP PROCEDURE IF EXISTS `sp_registrar_pago`$$
--
-- Procedimientos
--
CREATE PROCEDURE `sp_actualizar_estados_membresias` ()   BEGIN
    UPDATE socio_membresia
    SET activa = 0
    WHERE fecha_fin <= NOW();

    UPDATE socios s
    SET s.estado = 'inactivo'
    WHERE s.estado <> 'suspendido'
      AND NOT EXISTS (
        SELECT 1
        FROM socio_membresia sm
        WHERE sm.socio_id = s.id
          AND sm.activa = 1
          AND sm.fecha_inicio <= NOW()
          AND sm.fecha_fin > NOW()
    );

    UPDATE socios s
    SET s.estado = 'activo'
    WHERE s.estado <> 'suspendido'
      AND EXISTS (
        SELECT 1
        FROM socio_membresia sm
        WHERE sm.socio_id = s.id
          AND sm.activa = 1
          AND sm.fecha_inicio <= NOW()
          AND sm.fecha_fin > NOW()
    );
END$$

CREATE PROCEDURE `sp_actualizar_socio_completo` (IN `p_id` INT, IN `p_nombres` VARCHAR(100), IN `p_apellido_paterno` VARCHAR(80), IN `p_apellido_materno` VARCHAR(80), IN `p_fecha_nacimiento` DATE, IN `p_telefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_genero` VARCHAR(20), IN `p_contacto_emergencia_nombre` VARCHAR(100), IN `p_contacto_emergencia_telefono` VARCHAR(20), IN `p_calle` VARCHAR(120), IN `p_numero` VARCHAR(20), IN `p_colonia` VARCHAR(120), IN `p_codigo_postal` CHAR(5), IN `p_notas` TEXT)   BEGIN
    UPDATE socios
    SET
        nombres = p_nombres,
        apellido_paterno = p_apellido_paterno,
        apellido_materno = p_apellido_materno,
        fecha_nacimiento = p_fecha_nacimiento,
        telefono = p_telefono,
        email = p_email,
        genero = p_genero,
        contacto_emergencia_nombre = p_contacto_emergencia_nombre,
        contacto_emergencia_telefono = p_contacto_emergencia_telefono,
        calle = p_calle,
        numero = p_numero,
        colonia = p_colonia,
        codigo_postal = p_codigo_postal,
        notas = p_notas
    WHERE id = p_id;
END$$

CREATE PROCEDURE `sp_buscar_socios_para_membresia` (IN `p_busqueda` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)   BEGIN
    DECLARE v_busqueda VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    SET v_busqueda = IFNULL(p_busqueda, _utf8mb4'' COLLATE utf8mb4_general_ci);

    SELECT 
        s.id,
        CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno) AS nombre,
        s.telefono,
        s.email,
        s.estado
    FROM socios s
    WHERE 
        v_busqueda = _utf8mb4'' COLLATE utf8mb4_general_ci
        OR CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR s.email
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR CAST(s.id AS CHAR)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
    ORDER BY nombre ASC;
END$$

CREATE PROCEDURE `sp_desactivar_socio` (IN `p_id` INT)   BEGIN
    UPDATE socios
    SET estado = 'suspendido'
    WHERE id = p_id;
END$$

CREATE PROCEDURE `sp_historial_pagos_membresia` (IN `p_busqueda` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci, IN `p_fecha_inicio` DATE, IN `p_fecha_fin` DATE)   BEGIN
    DECLARE v_busqueda VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

    SET v_busqueda = IFNULL(p_busqueda, _utf8mb4'' COLLATE utf8mb4_general_ci);

    SELECT 
        pm.id,
        CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno) AS socio_nombre,
        pm.fecha_pago,
        pm.metodo_pago,
        m.nombre AS membresia_nombre,
        pm.monto
    FROM pagos_membresia pm
    INNER JOIN socios s ON pm.socio_id = s.id
    INNER JOIN membresias m ON pm.membresia_id = m.id
    WHERE (
        v_busqueda = _utf8mb4'' COLLATE utf8mb4_general_ci
        OR CONCAT_WS(_utf8mb4' ' COLLATE utf8mb4_general_ci, s.nombres, s.apellido_paterno, s.apellido_materno)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR s.email
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
        OR CAST(s.id AS CHAR)
            LIKE CONCAT(_utf8mb4'%' COLLATE utf8mb4_general_ci, v_busqueda, _utf8mb4'%' COLLATE utf8mb4_general_ci)
    )
    AND (p_fecha_inicio IS NULL OR DATE(pm.fecha_pago) >= p_fecha_inicio)
    AND (p_fecha_fin IS NULL OR DATE(pm.fecha_pago) <= p_fecha_fin)
    ORDER BY pm.fecha_pago DESC;
END$$

CREATE PROCEDURE `sp_insertar_socio_completo` (IN `p_nombres` VARCHAR(100), IN `p_apellido_paterno` VARCHAR(80), IN `p_apellido_materno` VARCHAR(80), IN `p_fecha_nacimiento` DATE, IN `p_telefono` VARCHAR(20), IN `p_email` VARCHAR(100), IN `p_genero` VARCHAR(20), IN `p_contacto_emergencia_nombre` VARCHAR(100), IN `p_contacto_emergencia_telefono` VARCHAR(20), IN `p_calle` VARCHAR(120), IN `p_numero` VARCHAR(20), IN `p_colonia` VARCHAR(120), IN `p_codigo_postal` CHAR(5), IN `p_notas` TEXT)   BEGIN

    INSERT INTO socios (
        nombres,
        apellido_paterno,
        apellido_materno,
        fecha_nacimiento,
        telefono,
        email,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        notas,
        estado,
        fecha_registro
    )
    VALUES (
        p_nombres,
        p_apellido_paterno,
        p_apellido_materno,
        p_fecha_nacimiento,
        p_telefono,
        p_email,
        p_genero,
        p_contacto_emergencia_nombre,
        p_contacto_emergencia_telefono,
        p_calle,
        p_numero,
        p_colonia,
        p_codigo_postal,
        p_notas,
        'inactivo',
        CURDATE()
    );

    SELECT LAST_INSERT_ID() AS id;

END$$

CREATE PROCEDURE `sp_inventario_movimientos_listar` (IN `p_busqueda` VARCHAR(100))   BEGIN
    SELECT 
        im.id,
        im.producto_id,
        p.nombre AS producto,
        p.codigo,
        p.categoria,
        im.tipo,
        im.cantidad,
        im.fecha,
        im.referencia,
        im.observaciones
    FROM inventario_movimientos im
    INNER JOIN productos p ON p.id = im.producto_id
    WHERE 
        p_busqueda IS NULL OR p_busqueda = ''
        OR p.nombre LIKE CONCAT('%', p_busqueda, '%')
        OR p.codigo LIKE CONCAT('%', p_busqueda, '%')
        OR im.tipo LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY im.fecha DESC;
END$$

CREATE PROCEDURE `sp_inventario_registrar_movimiento` (IN `p_producto_id` INT, IN `p_usuario_id` INT, IN `p_tipo` ENUM('entrada','salida','ajuste'), IN `p_cantidad` INT, IN `p_referencia` VARCHAR(100), IN `p_observaciones` TEXT)   BEGIN
    DECLARE v_stock_actual INT DEFAULT 0;
    DECLARE v_activo TINYINT DEFAULT 0;

    SELECT stock, activo
    INTO v_stock_actual, v_activo
    FROM productos
    WHERE id = p_producto_id
    LIMIT 1;

    IF v_activo IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto no existe.';
    END IF;

    IF v_activo = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se pueden registrar movimientos de un producto inactivo.';
    END IF;

    IF p_tipo NOT IN ('entrada', 'salida', 'ajuste') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Tipo de movimiento inválido.';
    END IF;

    IF p_tipo IN ('entrada', 'salida') AND p_cantidad <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La cantidad debe ser mayor a 0.';
    END IF;

    IF p_tipo = 'ajuste' AND p_cantidad < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El ajuste no puede dejar stock negativo.';
    END IF;

    IF p_tipo = 'salida' AND v_stock_actual < p_cantidad THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida.';
    END IF;

    IF p_tipo = 'entrada' THEN
        UPDATE productos
        SET stock = stock + p_cantidad
        WHERE id = p_producto_id;

    ELSEIF p_tipo = 'salida' THEN
        UPDATE productos
        SET stock = stock - p_cantidad
        WHERE id = p_producto_id;

    ELSEIF p_tipo = 'ajuste' THEN
        UPDATE productos
        SET stock = p_cantidad
        WHERE id = p_producto_id;
    END IF;

    INSERT INTO inventario_movimientos (
        producto_id,
        usuario_id,
        tipo,
        cantidad,
        referencia,
        observaciones
    )
    VALUES (
        p_producto_id,
        p_usuario_id,
        p_tipo,
        p_cantidad,
        NULLIF(TRIM(p_referencia), ''),
        p_observaciones
    );

    SELECT LAST_INSERT_ID() AS movimiento_id;
END$$

CREATE PROCEDURE `sp_listar_membresias` ()   BEGIN
    SELECT 
        id,
        nombre,
        duracion_dias,
        precio,
        descripcion
    FROM membresias
    ORDER BY precio ASC;
END$$

CREATE PROCEDURE `sp_listar_socios` ()   BEGIN
    SELECT
        id,
        nombres,
        apellido_paterno,
        apellido_materno,
        CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno) AS nombre,
        telefono,
        email,
        fecha_nacimiento,
        estado,
        fecha_registro,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        CONCAT_WS(' ', calle, numero, colonia, codigo_postal) AS direccion,
        notas
    FROM socios
    ORDER BY id DESC;
END$$

CREATE PROCEDURE `sp_obtener_historial_reciente_pagos_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT 
        pm.id,
        pm.socio_id,
        pm.membresia_id,
        m.nombre AS membresia_nombre,
        pm.monto,
        pm.metodo_pago,
        pm.fecha_pago,
        pm.referencia
    FROM pagos_membresia pm
    INNER JOIN membresias m 
        ON pm.membresia_id = m.id
    WHERE pm.socio_id = p_socio_id
    ORDER BY pm.fecha_pago DESC
    LIMIT 5;
END$$

CREATE PROCEDURE `sp_obtener_huella_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT id, socio_id, huella_hash, fecha_registro
    FROM socio_biometria
    WHERE socio_id = p_socio_id
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_obtener_membresia_activa_socio` (IN `p_socio_id` INT)   BEGIN
    SELECT 
        sm.id,
        sm.socio_id,
        sm.membresia_id,
        sm.fecha_inicio,
        sm.fecha_fin,
        sm.activa,
        m.nombre AS membresia_nombre,
        m.precio,
        m.duracion_dias,
        DATEDIFF(sm.fecha_fin, NOW()) AS dias_restantes
    FROM socio_membresia sm
    INNER JOIN membresias m ON sm.membresia_id = m.id
    WHERE sm.socio_id = p_socio_id
      AND sm.activa = 1
      AND sm.fecha_inicio <= NOW()
      AND sm.fecha_fin > NOW()
    ORDER BY sm.fecha_fin DESC
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_obtener_membresia_por_nombre` (IN `p_nombre` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci)   BEGIN
    SELECT 
        id,
        nombre,
        duracion_dias,
        precio,
        descripcion
    FROM membresias
    WHERE nombre COLLATE utf8mb4_general_ci = p_nombre
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_obtener_recibo_membresia` (IN `p_pago_id` INT)   BEGIN
    SELECT 
        pm.id AS pago_id,
        pm.fecha_pago,
        pm.metodo_pago,
        pm.monto,
        pm.referencia,

        s.id AS socio_id,
        CONCAT_WS(' ', s.nombres, s.apellido_paterno, s.apellido_materno) AS socio,
        s.telefono,
        s.email,
        s.estado,

        m.nombre AS membresia,
        m.duracion_dias,
        m.precio,

        sm.fecha_inicio,
        sm.fecha_fin,
        sm.activa
    FROM pagos_membresia pm
    INNER JOIN socios s 
        ON pm.socio_id = s.id
    INNER JOIN membresias m 
        ON pm.membresia_id = m.id
    LEFT JOIN socio_membresia sm
        ON sm.socio_id = pm.socio_id
        AND sm.membresia_id = pm.membresia_id
    WHERE pm.id = p_pago_id
    ORDER BY sm.id DESC
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_obtener_socio_por_id` (IN `p_id` INT)   BEGIN
    SELECT
        id,
        nombres,
        apellido_paterno,
        apellido_materno,
        CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno) AS nombre,
        fecha_nacimiento,
        telefono,
        email,
        estado,
        fecha_registro,
        genero,
        contacto_emergencia_nombre,
        contacto_emergencia_telefono,
        calle,
        numero,
        colonia,
        codigo_postal,
        CONCAT_WS(' ', calle, numero, colonia, codigo_postal) AS direccion,
        notas
    FROM socios
    WHERE id = p_id
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_productos_listar` (IN `p_busqueda` VARCHAR(100))   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        descripcion,
        costo_compra,
        precio_venta,
        stock,
        stock_minimo,
        icono,
        activo,
        CASE 
            WHEN stock <= 0 THEN 'Agotado'
            WHEN stock <= stock_minimo THEN 'Por agotarse'
            ELSE 'Stock OK'
        END AS estado_stock
    FROM productos
    WHERE activo = 1
      AND (
        p_busqueda IS NULL OR p_busqueda = ''
        OR nombre LIKE CONCAT('%', p_busqueda, '%')
        OR codigo LIKE CONCAT('%', p_busqueda, '%')
        OR categoria LIKE CONCAT('%', p_busqueda, '%')
      )
    ORDER BY nombre ASC;
END$$

CREATE PROCEDURE `sp_productos_stock_bajo` ()   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        stock,
        stock_minimo,
        icono,
        CASE 
            WHEN stock <= 0 THEN 'Agotado'
            ELSE 'Por agotarse'
        END AS estado_stock
    FROM productos
    WHERE activo = 1
      AND stock <= stock_minimo
    ORDER BY stock ASC;
END$$

CREATE PROCEDURE `sp_producto_baja_logica` (IN `p_id` INT)   BEGIN
    DECLARE v_existe INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_existe
    FROM productos
    WHERE id = p_id
      AND activo = 1;

    IF v_existe = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El producto no existe o ya está inactivo.';
    END IF;

    UPDATE productos
    SET activo = 0
    WHERE id = p_id;
END$$

CREATE PROCEDURE `sp_producto_guardar` (IN `p_id` INT, IN `p_codigo` VARCHAR(30), IN `p_nombre` VARCHAR(100), IN `p_categoria` VARCHAR(100), IN `p_descripcion` TEXT, IN `p_costo_compra` DECIMAL(10,2), IN `p_precio_venta` DECIMAL(10,2), IN `p_stock` INT, IN `p_stock_minimo` INT, IN `p_icono` VARCHAR(50))   BEGIN
    DECLARE v_nuevo_codigo VARCHAR(30);
    DECLARE v_existe INT DEFAULT 0;

    IF p_nombre IS NULL OR TRIM(p_nombre) = '' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El nombre del producto es obligatorio.';
    END IF;

    IF p_costo_compra < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El costo de compra no puede ser negativo.';
    END IF;

    IF p_precio_venta <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El precio de venta debe ser mayor a 0.';
    END IF;

    IF p_stock_minimo < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El stock mínimo no puede ser negativo.';
    END IF;

    IF p_id IS NULL OR p_id = 0 THEN

        IF p_stock < 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El stock inicial no puede ser negativo.';
        END IF;

        SELECT CONCAT('PR', LPAD(COALESCE(MAX(id), 0) + 1, 3, '0'))
        INTO v_nuevo_codigo
        FROM productos;

        INSERT INTO productos (
            codigo,
            nombre,
            categoria,
            descripcion,
            costo_compra,
            precio_venta,
            stock,
            stock_minimo,
            icono,
            activo
        )
        VALUES (
            v_nuevo_codigo,
            TRIM(p_nombre),
            NULLIF(TRIM(p_categoria), ''),
            p_descripcion,
            p_costo_compra,
            p_precio_venta,
            p_stock,
            p_stock_minimo,
            IFNULL(NULLIF(TRIM(p_icono), ''), 'bi-box-seam'),
            1
        );

        SELECT LAST_INSERT_ID() AS producto_id;

    ELSE

        SELECT COUNT(*)
        INTO v_existe
        FROM productos
        WHERE id = p_id;

        IF v_existe = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El producto no existe.';
        END IF;

        UPDATE productos
        SET 
            nombre = TRIM(p_nombre),
            categoria = NULLIF(TRIM(p_categoria), ''),
            descripcion = p_descripcion,
            costo_compra = p_costo_compra,
            precio_venta = p_precio_venta,
            stock_minimo = p_stock_minimo,
            icono = IFNULL(NULLIF(TRIM(p_icono), ''), 'bi-box-seam')
        WHERE id = p_id;

        SELECT p_id AS producto_id;

    END IF;
END$$

CREATE PROCEDURE `sp_producto_obtener` (IN `p_id` INT)   BEGIN
    SELECT 
        id,
        codigo,
        nombre,
        categoria,
        descripcion,
        costo_compra,
        precio_venta,
        stock,
        stock_minimo,
        icono,
        activo,
        fecha_creacion,
        fecha_actualizacion
    FROM productos
    WHERE id = p_id
    LIMIT 1;
END$$

CREATE PROCEDURE `sp_registrar_huella_socio` (IN `p_socio_id` INT, IN `p_huella_hash` VARBINARY(512))   BEGIN
    INSERT INTO socio_biometria (socio_id, huella_hash)
    VALUES (p_socio_id, p_huella_hash)
    ON DUPLICATE KEY UPDATE
        huella_hash = p_huella_hash,
        fecha_registro = CURRENT_TIMESTAMP;
END$$

CREATE PROCEDURE `sp_registrar_pago` (IN `p_socio_id` INT, IN `p_membresia_id` INT, IN `p_fecha_inicio` DATETIME, IN `p_monto` DECIMAL(10,2), IN `p_metodo` VARCHAR(50))   BEGIN
    DECLARE v_duracion INT;
    DECLARE v_fecha_inicio DATETIME;
    DECLARE v_fecha_fin DATETIME;
    DECLARE v_fecha_fin_actual DATETIME;
    DECLARE v_estado_socio VARCHAR(20);
    DECLARE v_pago_id INT;
    DECLARE v_referencia VARCHAR(50);

    SELECT estado
    INTO v_estado_socio
    FROM socios
    WHERE id = p_socio_id
    LIMIT 1;

    IF v_estado_socio IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El socio no existe.';
    END IF;

    IF v_estado_socio = 'suspendido' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El socio está suspendido. No se puede registrar pago.';
    END IF;

    SELECT duracion_dias
    INTO v_duracion
    FROM membresias
    WHERE id = p_membresia_id
    LIMIT 1;

    IF v_duracion IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La membresía no existe.';
    END IF;

    SELECT MAX(fecha_fin)
    INTO v_fecha_fin_actual
    FROM socio_membresia
    WHERE socio_id = p_socio_id
      AND activa = 1
      AND fecha_fin > NOW();

    SET v_fecha_inicio = IFNULL(p_fecha_inicio, NOW());

    IF v_fecha_fin_actual IS NOT NULL AND v_fecha_fin_actual > v_fecha_inicio THEN
        SET v_fecha_inicio = v_fecha_fin_actual;
    END IF;

    SET v_fecha_fin = DATE_ADD(v_fecha_inicio, INTERVAL v_duracion DAY);

    IF v_fecha_inicio <= NOW() THEN
        UPDATE socio_membresia
        SET activa = 0
        WHERE socio_id = p_socio_id
          AND activa = 1;
    END IF;

    INSERT INTO pagos_membresia (
        socio_id,
        membresia_id,
        monto,
        metodo_pago
    )
    VALUES (
        p_socio_id,
        p_membresia_id,
        p_monto,
        p_metodo
    );

    SET v_pago_id = LAST_INSERT_ID();

    SET v_referencia = CONCAT(
        'REC-',
        DATE_FORMAT(NOW(), '%Y%m%d'),
        '-',
        LPAD(v_pago_id, 6, '0')
    );

    UPDATE pagos_membresia
    SET referencia = v_referencia
    WHERE id = v_pago_id;

    INSERT INTO socio_membresia (
        socio_id,
        membresia_id,
        fecha_inicio,
        fecha_fin,
        activa
    )
    VALUES (
        p_socio_id,
        p_membresia_id,
        v_fecha_inicio,
        v_fecha_fin,
        CASE
            WHEN v_fecha_inicio <= NOW() AND v_fecha_fin > NOW()
            THEN 1
            ELSE 0
        END
    );

    UPDATE socios
    SET estado = CASE
        WHEN v_fecha_inicio <= NOW() AND v_fecha_fin > NOW()
        THEN 'activo'
        ELSE estado
    END
    WHERE id = p_socio_id;

END$$

DELIMITER ;
