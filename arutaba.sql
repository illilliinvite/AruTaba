-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: arutaba
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.2

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
-- Table structure for table `calender`
--

DROP TABLE IF EXISTS `calender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calender` (
  `user_id` varchar(20) NOT NULL,
  `osake_drinking` date DEFAULT NULL,
  `smoke_day` date DEFAULT NULL,
  `alcohol_consumption` int DEFAULT NULL,
  `ciggarette_consumption` int DEFAULT NULL,
  `score` int NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `alcohol_degree` decimal(4,1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `calender_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `profile` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calender`
--

LOCK TABLES `calender` WRITE;
/*!40000 ALTER TABLE `calender` DISABLE KEYS */;
INSERT INTO `calender` VALUES ('user_6a0e781eaff7e','2026-05-21','2026-05-21',10,12,4850,NULL,NULL),('user_6a15251201dcf','2026-05-26','2026-05-26',100,1,500,NULL,NULL),('user_6a1906899955d','2026-06-02','2026-06-02',123,123,50430,'asdf',10.0),('user_6a19262b8b4a7','2026-05-28','2026-05-28',1000,12,5800,NULL,NULL);
/*!40000 ALTER TABLE `calender` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forum` (
  `user_name` varchar(20) NOT NULL,
  `forum_history` varchar(100) NOT NULL,
  `day` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forum`
--

LOCK TABLES `forum` WRITE;
/*!40000 ALTER TABLE `forum` DISABLE KEYS */;
INSERT INTO `forum` VALUES ('adsfa','adfa','2026-06-05 00:00:00'),('bananaa','adfafs','2026-06-05 03:14:51'),('bananaa','zzzzzzz','2026-06-05 03:15:24'),('bananaa','adsf','2026-06-05 03:15:52'),('bananaa','adsfad','2026-06-05 03:17:30'),('bananaa','zxcvzcxv','2026-06-05 03:17:38'),('bananaa','kintamawwwwww','2026-06-05 03:20:29'),('bananaa','asdfa','2026-06-05 04:58:58'),('bananaa','asdfsadoaof','2026-06-05 05:02:11'),('bananaa','asdfdas','2026-06-05 05:02:16'),('bananaa','adfdas','2026-06-05 05:02:17'),('bananaa','asdf','2026-06-09 00:55:07'),('bananaa','zxcv','2026-06-09 01:17:45'),('bananaa','ttt','2026-06-09 01:25:18'),('bananaa','adsfa','2026-06-09 01:31:59'),('bananaa','banana','2026-06-09 01:54:23'),('bananaa','zakow','2026-06-09 04:49:25'),('bananaa','asdfasd','2026-06-09 05:14:27'),('admin','asdf','2026-06-09 05:27:01'),('katou','こんにちは！','2026-06-18 01:05:13'),('katou','あｓｆだｓｆｄｓ','2026-06-18 01:05:15'),('takeya','adfdasfds','2026-06-19 04:32:34'),('takeya','<script>alert(1)</script>','2026-06-19 04:33:25'),('takeya','<h2>asdfa</h2>','2026-06-19 04:34:07'),('takeya','<a href=\"example.com\">click</a>','2026-06-19 04:34:47');
/*!40000 ALTER TABLE `forum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friend`
--

DROP TABLE IF EXISTS `friend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friend` (
  `user_id` varchar(20) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `friend_id` varchar(20) NOT NULL,
  `friend_wait` int NOT NULL,
  `friend` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`,`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friend`
--

LOCK TABLES `friend` WRITE;
/*!40000 ALTER TABLE `friend` DISABLE KEYS */;
INSERT INTO `friend` VALUES ('user_6a335ef888f54','adfklasladsldsalk','ccc@gmail.com',0,1);
/*!40000 ALTER TABLE `friend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friend_chat`
--

DROP TABLE IF EXISTS `friend_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friend_chat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL,
  `receiver_id` varchar(20) NOT NULL,
  `message_type` enum('text','image','video') NOT NULL DEFAULT 'text',
  `message_id` varchar(50) DEFAULT NULL,
  `chat_history` varchar(100) NOT NULL DEFAULT '',
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friend_chat`
--

LOCK TABLES `friend_chat` WRITE;
/*!40000 ALTER TABLE `friend_chat` DISABLE KEYS */;
INSERT INTO `friend_chat` VALUES (1,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'asdf',NULL,NULL,'2026-06-18 11:30:58',1),(2,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'asdf',NULL,NULL,'2026-06-18 11:30:58',1),(3,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'zxvc',NULL,NULL,'2026-06-18 11:31:05',1),(4,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'zxvc',NULL,NULL,'2026-06-18 11:31:05',1),(5,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'tasukete',NULL,NULL,'2026-06-18 11:32:33',1),(6,'user_6a1906899955d','user_6a20e44e86d82','text',NULL,'tasukete',NULL,NULL,'2026-06-18 11:32:33',1),(7,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34aaea44c1c6.88462506','あｓｄｆ',NULL,NULL,'2026-06-19 11:35:22',1),(8,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34aaea4ba978.71626396','あｓｄｆ',NULL,NULL,'2026-06-19 11:35:22',1),(9,'user_6a1906899955d','user_6a20e44e86d82','image','msg_6a34aaf5ec75a4.28441505','','public/uploads/friend_chat/e64892015a95067a09656a15406f1934.png','スクリーンショット 2025-11-07 140340.png','2026-06-19 11:35:33',1),(10,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34ab740fc220.60254380','asdf',NULL,NULL,'2026-06-19 11:37:40',0),(11,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34ab741142b9.95427587','asdf',NULL,NULL,'2026-06-19 11:37:40',0),(12,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34aba8894a83.81081878','sdfg',NULL,NULL,'2026-06-19 11:38:32',0),(13,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34aba88a8eb8.06038643','sdfg',NULL,NULL,'2026-06-19 11:38:32',0),(14,'user_6a1906899955d','user_6a20e44e86d82','text','msg_6a34aba9f096c0.30209421','asdfa',NULL,NULL,'2026-06-19 11:38:33',0);
/*!40000 ALTER TABLE `friend_chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goal_value`
--

DROP TABLE IF EXISTS `goal_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goal_value` (
  `user_id` varchar(20) NOT NULL,
  `alcohol_limit` int DEFAULT NULL,
  `ciggarette_limit` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `goal_value_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `profile` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goal_value`
--

LOCK TABLES `goal_value` WRITE;
/*!40000 ALTER TABLE `goal_value` DISABLE KEYS */;
INSERT INTO `goal_value` VALUES ('user_6a1906899955d',3,3),('user_6a334436e0167',5,5);
/*!40000 ALTER TABLE `goal_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `user_id` varchar(20) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `password` varchar(30) NOT NULL,
  `mail_address` varchar(40) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES ('user_6a0e77927b826','banana','123','saefd@gmail.com'),('user_6a0e781eaff7e','banana','123','banana@gmail.com'),('user_6a0fced2a1165','user','qwe','aaa@gmail.com'),('user_6a15251201dcf','swerfier','123','bbb@gmail.com'),('user_6a1906899955d','takeya','123','ccc@gmail.com'),('user_6a191df673768','ddd','123','ddd@gmail.com'),('user_6a191f8f778ef','eee','123','eee@gmail.com'),('user_6a19262b8b4a7','fff','123','fff@gmail.com'),('user_6a20d6c787c11','asdfasdf','qwe','sss@gmail.com'),('user_6a20d78901c78','zzz','123','zzz@gmail.com'),('user_6a20e44e86d82','ppp','123','ppp@gmail.com'),('user_6a20ecb3b3581','sdafsdf','123','ooo@gmail.com'),('user_6a27a4098bc0c','admin','123','admin@gmail.com'),('user_6a334436e0167','katou','123','oo@gmail.com'),('user_6a335ef888f54','adfklasladsldsalk','123','anal@gmail.com');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `user_id` varchar(20) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `mail_address` varchar(40) NOT NULL,
  `alcohol_level` int DEFAULT NULL,
  `smoke_level` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES ('user_6a0e77927b826','banana',NULL,'saefd@gmail.com',0,0),('user_6a0e781eaff7e','banana',NULL,'banana@gmail.com',0,0),('user_6a0fced2a1165','user',NULL,'aaa@gmail.com',0,0),('user_6a15251201dcf','swerfier',NULL,'bbb@gmail.com',0,0),('user_6a1906899955d','takeya',NULL,'ccc@gmail.com',0,0),('user_6a191df673768','ddd',NULL,'ddd@gmail.com',0,0),('user_6a191f8f778ef','eee',NULL,'eee@gmail.com',0,0),('user_6a19262b8b4a7','fff',NULL,'fff@gmail.com',0,0),('user_6a20d6c787c11','asdfasdf',NULL,'sss@gmail.com',0,0),('user_6a20d78901c78','zzz',NULL,'zzz@gmail.com',0,0),('user_6a20e44e86d82','ppp',NULL,'ppp@gmail.com',0,0),('user_6a20ecb3b3581','sdafsdf',NULL,'ooo@gmail.com',0,0),('user_6a27a4098bc0c','admin',NULL,'admin@gmail.com',0,0),('user_6a334436e0167','katou',NULL,'oo@gmail.com',0,0),('user_6a335ef888f54','adfklasladsldsalk',NULL,'anal@gmail.com',0,0);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranking`
--

DROP TABLE IF EXISTS `ranking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ranking` (
  `user_name` varchar(20) NOT NULL,
  `alcohol_consumption` int DEFAULT NULL,
  `ciggarette_consumption` int DEFAULT NULL,
  `friend` tinyint(1) NOT NULL,
  `score` int NOT NULL,
  `user_id` varchar(20) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `profile` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranking`
--

LOCK TABLES `ranking` WRITE;
/*!40000 ALTER TABLE `ranking` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-25 10:29:52
