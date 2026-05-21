-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sistema_gestion
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `pendiente` tinyint(1) NOT NULL DEFAULT 1,
  `idCliente` int(11) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `numMatricula` varchar(50) NOT NULL,
  `numIdC` varchar(50) NOT NULL,
  `informacionAd` varchar(255) DEFAULT NULL,
  `tipoConsulta` enum('Cambio de Liquidos','cambio de ruedas','revisión','otros') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idCliente` (`idCliente`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,'marco',1,3,'345121212','marcoalvapa@gmail.com','4323CFV','12','hola','revisión','2026-05-18 17:19:34','2026-05-18 17:19:34'),(2,'juan',1,3,'586454545','juan@gmail.com','DGFH123','32','ASDasd','Cambio de Liquidos','2026-05-18 17:20:44','2026-05-18 17:20:44'),(3,'a',1,NULL,'123112112','a@gmail.com','DGFS435','1','a','revisión','2026-05-18 17:52:46','2026-05-18 17:52:46'),(4,'marco',1,3,'642242424','marcoalvapa@gmail.com','DFGA123','1','asdsad','revisión','2026-05-18 17:53:28','2026-05-18 17:53:28'),(5,'asd',1,NULL,'657676767','asd@gmail.com','DSGF123','1','asa','revisión','2026-05-18 18:08:25','2026-05-18 18:08:25'),(6,'a',1,3,'123122121','a@gmail.com','SADH453','1','a','revisión','2026-05-18 18:29:18','2026-05-18 18:29:18'),(7,'a',1,3,'642523353','a@gmail.com','ASDA123','1','a','revisión','2026-05-18 18:38:01','2026-05-18 18:38:01'),(8,'a',1,3,'879999999','a@gmail.com','GHJF213','1','pepe','revisión','2026-05-18 18:44:02','2026-05-18 18:44:02'),(9,'pepe',1,3,'465878787','hola@gmail.com','KJHF464','1','holaaaaaa','revisión','2026-05-19 16:16:34','2026-05-19 16:16:34');
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-19 21:36:02
