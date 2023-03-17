-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: quaiantique
-- ------------------------------------------------------
-- Server version	8.0.32-0ubuntu0.20.04.2

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
-- Table structure for table `booker`
--

DROP TABLE IF EXISTS `booker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booker` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booker`
--

LOCK TABLES `booker` WRITE;
/*!40000 ALTER TABLE `booker` DISABLE KEYS */;
/*!40000 ALTER TABLE `booker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booker_ingredient`
--

DROP TABLE IF EXISTS `booker_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booker_ingredient` (
  `booker_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  PRIMARY KEY (`booker_id`,`ingredient_id`),
  KEY `IDX_B400A1B18B7E4006` (`booker_id`),
  KEY `IDX_B400A1B1933FE08C` (`ingredient_id`),
  CONSTRAINT `FK_B400A1B18B7E4006` FOREIGN KEY (`booker_id`) REFERENCES `booker` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B400A1B1933FE08C` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booker_ingredient`
--

LOCK TABLES `booker_ingredient` WRITE;
/*!40000 ALTER TABLE `booker_ingredient` DISABLE KEYS */;
/*!40000 ALTER TABLE `booker_ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booker_id` int NOT NULL,
  `cutlerys` smallint NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C74404558B7E4006` (`booker_id`),
  UNIQUE KEY `UNIQ_C7440455A76ED395` (`user_id`),
  CONSTRAINT `FK_C74404558B7E4006` FOREIGN KEY (`booker_id`) REFERENCES `booker` (`id`),
  CONSTRAINT `FK_C7440455A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dish`
--

DROP TABLE IF EXISTS `dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dish` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `archived` tinyint(1) NOT NULL,
  `type` smallint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dish`
--

LOCK TABLES `dish` WRITE;
/*!40000 ALTER TABLE `dish` DISABLE KEYS */;
INSERT INTO `dish` VALUES (4,'Kréol','Entrés au choix:\r\n-Trio de samoussa jambon fromage\r\n-Bouchons piment mayo\r\n-Croquette de poulet\r\n\r\nPlats au choix :\r\n-Boucané pomme de terre\r\n-Rougails saucisses',2680,0,3),(5,'De la mer','Entrés au choix:\r\n-Douzaine d’huîtres des alpes\r\n-Sushis d\'algues végan\r\n\r\nPlats au choix:\r\n-Moule frite (sauce au choix)\r\n-Filet de merlu à la crème',2890,0,3),(20,'Roulé de merlu aux épinards','Assiette de merlu fourré au épinard, sur son lit de petit pois',1290,0,0),(21,'Bouché pur porc','Roulé à la saucisse et jambon mozzarella',1190,0,0),(22,'Gambas grillés','Gambas grillé de la pêche matinal',1490,0,0),(23,'Tartine verte','Tartine de pain a la mousse d\'avocats, tomates et oeufs durs',890,1,0),(24,'La Douzaine d\'huitres','Une douzaine d\'huitres de notre producteurs local',1650,0,0),(25,'Raviolis croustillants','Raviolis fourré au fromage et aux boeufs, accompagné avec une salade verte et carotte',1689,1,1),(26,'Soupe au porc','Soupe de porc avec bouillon en cube et nouilles de riz',1590,0,1),(27,'Boeuf bolognaise','Un morceau de boeuf haché cuit sur le grill, avec sa sauce bolognaise',2280,0,1),(28,'Poulet braisé','Brochette de poulet sur son lit de legumes de saisons',1989,0,1),(29,'Crabe croustillant','Crabe tout droit venue du fond du pacifique, certains disent qu\'il aime l\'argent',100999899,0,1),(30,'Salade Vitaminé','Une salade sucré composé d\'aliments comme la figue, les noix de cajous, des raisins secs...',890,0,2),(31,'Gelée à la figue','Une gelée a la figue accompagné avec les fruits de saison',940,0,2),(32,'6 macarons','6 Macarons au choix selon les saveurs du moment',1190,0,2),(33,'Cake pistache et smoothie pêche','Tout est dans le titre',890,0,2),(34,'Complète','Entrée + Plats + dessert\r\n\r\nAu choix sur la carte (Hors crabe croustillant)',3280,0,3),(35,'Demie','Entrée + Plats ou Plats + dessert\r\n\r\nAu choix sur la carte (hors crabe croustillant)',2670,0,3),(36,'Du chef','Notre formules exclusive, vous découvrirez les saveurs exclusive que le chef aura voulue mettre en avant avec les produit locaux et gastronomique !\r\n\r\nNotre chef étoilé vous proposera une suite de 23 plats, dans une expérience gustative incomparable',7999,0,3),(37,'Luxe','Entrées au choix:\r\n-Toast de caviar\r\n-Foie gras mi-cuit du sud à a figue\r\n-Sashimi de méduse \r\n\r\nPlats au choix:\r\n-Boeuf de Kobe grillé\r\n-Entrecôte de requin\r\n-Oeuf  de dinosaures',11960,1,3);
/*!40000 ALTER TABLE `dish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dish_ingredient`
--

DROP TABLE IF EXISTS `dish_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dish_ingredient` (
  `dish_id` int NOT NULL,
  `ingredient_id` int NOT NULL,
  PRIMARY KEY (`dish_id`,`ingredient_id`),
  KEY `IDX_77196056148EB0CB` (`dish_id`),
  KEY `IDX_77196056933FE08C` (`ingredient_id`),
  CONSTRAINT `FK_77196056148EB0CB` FOREIGN KEY (`dish_id`) REFERENCES `dish` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_77196056933FE08C` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredient` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dish_ingredient`
--

LOCK TABLES `dish_ingredient` WRITE;
/*!40000 ALTER TABLE `dish_ingredient` DISABLE KEYS */;
INSERT INTO `dish_ingredient` VALUES (20,1),(20,4),(20,8),(20,21),(20,25),(20,29),(20,30),(20,33),(20,36),(20,40),(21,8),(21,22),(21,38),(21,40),(22,2),(22,26),(23,5),(23,17),(23,21),(23,51),(23,52),(24,16),(24,27),(25,2),(25,3),(25,6),(25,23),(25,29),(25,30),(25,33),(25,34),(25,37),(25,38),(25,40),(26,3),(26,9),(26,16),(26,21),(26,22),(26,29),(26,30),(26,33),(26,34),(26,35),(26,38),(26,42),(27,2),(27,7),(27,10),(27,17),(27,23),(27,29),(27,30),(27,33),(27,36),(27,40),(28,2),(28,3),(28,6),(28,9),(28,10),(28,24),(28,29),(28,31),(28,34),(29,9),(29,28),(29,29),(29,30),(29,34),(29,36),(29,41),(30,5),(30,11),(30,12),(30,13),(30,14),(30,15),(30,16),(30,18),(30,19),(30,20),(30,34),(30,43),(31,12),(31,13),(31,15),(31,20),(31,43),(32,5),(32,10),(32,11),(32,12),(32,14),(32,16),(32,17),(32,18),(32,20),(32,43),(32,44),(33,18),(33,20),(33,43),(33,44),(33,53);
/*!40000 ALTER TABLE `dish_ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20230304123401','2023-03-04 12:34:11',2395),('DoctrineMigrations\\Version20230307090523','2023-03-07 09:05:32',262);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galery_image`
--

DROP TABLE IF EXISTS `galery_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galery_image` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galery_image`
--

LOCK TABLES `galery_image` WRITE;
/*!40000 ALTER TABLE `galery_image` DISABLE KEYS */;
INSERT INTO `galery_image` VALUES (19,'Roulé de merlu aux épinards','1',2),(20,'Bouché pur porc','2',2),(22,'Gambas grillés','4',2),(23,'Raviolis croustillants','5',2),(24,'Salade Vitaminé','6',2),(25,'Tartine verte','7',2),(26,'Gelée à la figue','8',2),(27,'La Douzaine d\'huitres','9',2),(28,'Soupe au porc','10',2),(29,'Boeuf bolognaise','11',2),(30,'Poulet braisé','12',2),(31,'6 macarons','13',2),(32,'Cake pistache et smoothie pêche','14',2),(33,'Crabe Croustillant','15',2);
/*!40000 ALTER TABLE `galery_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredient`
--

DROP TABLE IF EXISTS `ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredient`
--

LOCK TABLES `ingredient` WRITE;
/*!40000 ALTER TABLE `ingredient` DISABLE KEYS */;
INSERT INTO `ingredient` VALUES (1,'Épinard',0),(2,'Salade verte',0),(3,'Carotte',0),(4,'Petit pois',0),(5,'Avocat',0),(6,'Choux rouge',0),(7,'Champignons',0),(8,'Oignons',0),(9,'Courgette',0),(10,'Poivrons',0),(11,'Pomme',1),(12,'Figue',1),(13,'Grenade',1),(14,'Banane',1),(15,'Framboise',1),(16,'Citron',1),(17,'Tomates',1),(18,'Pêche',1),(19,'Noix de cajous',2),(20,'Pistache',2),(21,'Trace de fruit à coque',2),(22,'Porc',3),(23,'Boeuf',3),(24,'Poulet',3),(25,'Merlu',4),(26,'Gambas',4),(27,'Huitre',4),(28,'Crabe',4),(29,'Poivre',5),(30,'Sel',5),(31,'Moutarde',5),(32,'Miel',5),(33,'Huile',5),(34,'Sésame',5),(35,'Cube Knorr',5),(36,'Ciboulette',5),(37,'Mozzarella',6),(38,'Emmental',6),(40,'Pâte',7),(41,'Riz',7),(42,'Nouilles de Riz',7),(43,'Sucre',7),(44,'Chocolat',7),(51,'Oeuf',7),(52,'Pain',7),(53,'Menthe',5);
/*!40000 ALTER TABLE `ingredient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,'Midi','Uniquement disponible le midi du lundi au vendredi',0),(2,'Soir','Disponible tout les soirs de la semaine',0),(3,'Experience','Sur réservation uniquement',0);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_dish`
--

DROP TABLE IF EXISTS `menu_dish`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_dish` (
  `menu_id` int NOT NULL,
  `dish_id` int NOT NULL,
  PRIMARY KEY (`menu_id`,`dish_id`),
  KEY `IDX_5D327CF6CCD7E912` (`menu_id`),
  KEY `IDX_5D327CF6148EB0CB` (`dish_id`),
  CONSTRAINT `FK_5D327CF6148EB0CB` FOREIGN KEY (`dish_id`) REFERENCES `dish` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5D327CF6CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_dish`
--

LOCK TABLES `menu_dish` WRITE;
/*!40000 ALTER TABLE `menu_dish` DISABLE KEYS */;
INSERT INTO `menu_dish` VALUES (1,4),(1,5),(2,34),(2,35),(3,36),(3,37);
/*!40000 ALTER TABLE `menu_dish` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `service_id` int NOT NULL,
  `booker_id` int NOT NULL,
  `cutlerys` smallint NOT NULL,
  `allergy` tinyint(1) NOT NULL,
  `stage_hour` smallint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42C84955ED5CA9E6` (`service_id`),
  KEY `IDX_42C849558B7E4006` (`booker_id`),
  CONSTRAINT `FK_42C849558B7E4006` FOREIGN KEY (`booker_id`) REFERENCES `booker` (`id`),
  CONSTRAINT `FK_42C84955ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation`
--

LOCK TABLES `reservation` WRITE;
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` smallint NOT NULL,
  `date` date NOT NULL,
  `max_cutlerys` smallint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-17 16:38:08
