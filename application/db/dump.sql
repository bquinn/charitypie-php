-- MySQL dump 10.11
--
-- Host: localhost    Database: charitypie
-- ------------------------------------------------------
-- Server version	5.0.67-community-nt

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `categories` (
  `category_id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(45) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Animals',0),(2,'Aged',0),(3,'Blind & Partialy Sighted',0),(4,'Children & Youth',0),(5,'Health',0),(6,'Overseas Aid',0);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories_charities`
--

DROP TABLE IF EXISTS `categories_charities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `categories_charities` (
  `charity_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`charity_id`,`category_id`),
  KEY `FK_categories_charities_categories` (`category_id`),
  CONSTRAINT `FK_categories_charities_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  CONSTRAINT `FK_categories_charities_charities` FOREIGN KEY (`charity_id`) REFERENCES `charities` (`charity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `categories_charities`
--

LOCK TABLES `categories_charities` WRITE;
/*!40000 ALTER TABLE `categories_charities` DISABLE KEYS */;
INSERT INTO `categories_charities` VALUES (6,1),(7,1),(8,1),(5,2),(8,3),(2,4),(3,4),(9,4),(10,4),(4,5),(11,5),(12,5),(1,6);
/*!40000 ALTER TABLE `categories_charities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cause_label`
--

DROP TABLE IF EXISTS `cause_label`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cause_label` (
  `tag_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `cause_id` int(10) unsigned NOT NULL,
  KEY `fk_cause_label_tags` (`tag_id`),
  KEY `fk_cause_label_user` (`user_id`),
  KEY `fk_cause_label_causes` (`cause_id`),
  CONSTRAINT `fk_cause_label_causes` FOREIGN KEY (`cause_id`) REFERENCES `causes` (`cause_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cause_label_tags` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cause_label_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cause_label`
--

LOCK TABLES `cause_label` WRITE;
/*!40000 ALTER TABLE `cause_label` DISABLE KEYS */;
/*!40000 ALTER TABLE `cause_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `causes`
--

DROP TABLE IF EXISTS `causes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `causes` (
  `cause_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(45) character set utf8 default NULL,
  `description` text character set utf8,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`cause_id`),
  KEY `fk_causes_user` (`user_id`),
  CONSTRAINT `fk_causes_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `causes`
--

LOCK TABLES `causes` WRITE;
/*!40000 ALTER TABLE `causes` DISABLE KEYS */;
INSERT INTO `causes` VALUES (1,'F',NULL,2),(2,'Foo','Because foo is important',2),(3,'My Green Pie',NULL,3),(4,'Fish Pie',NULL,3),(5,'Chimps',NULL,7),(6,'Charity Shops',NULL,8);
/*!40000 ALTER TABLE `causes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charities`
--

DROP TABLE IF EXISTS `charities`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `charities` (
  `charity_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) character set utf8 NOT NULL,
  `website` varchar(45) character set utf8 default NULL,
  `description` text character set utf8,
  `phone` varchar(45) character set utf8 default NULL,
  `address` text character set utf8,
  `registration` varchar(45) character set utf8 default NULL,
  `operating_location` varchar(45) character set utf8 default NULL,
  PRIMARY KEY  (`charity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `charities`
--

LOCK TABLES `charities` WRITE;
/*!40000 ALTER TABLE `charities` DISABLE KEYS */;
INSERT INTO `charities` VALUES (1,'Oxfam','http://www.oxfam.org.uk','Oxfam\'s objects are for the prevention and relief of poverty anywhere in the world. Oxfam furthers its objects through three interlinked activities: humanitarian relief, development work and advocacy and campaigning. Oxfam is an affiliate member of Oxfam International.','555-555-555','2700 John Smith Drive, Oxford, OX4 2JY','202918','United Kingdom'),(2,'Save The Children Fund','http://www.savethechildren.org.uk','To relieve the distress and to promote the welfare of children in any country or countries, without differentiation on the ground of race, colour, nationality, creed or sex to educate the public concerning the nature, causes and effects of distress, and want of welfare as aforesaid, and to conduct and procure research concerning the same and to make available the useful results thereof.','555-555-555','1 St. John\'s Lane, London, EC1M 4AR','213890','United Kingdom'),(3,'Barnado\'s','http://www.barnandos.org.uk','Barnando\'s runs a wide variety of projects including: disability services; family centres; education, employment and training; fostering and adoption; youth justice; homelessness; safeguarding and protection and residential and leaving care services. We also campaign, bringing vital issues to the attention of the public and the government. We do this because we believe in children.','555-555-555','Tanners Lane, Barkingside, Ilford, IG6 1QG','216250','United Kingdom'),(4,'Cancer Research UK','http://www.cancerresearchuk.org','To protect and promote the health of the public in particular by research into the nature, causes diagnosis, prevention, treatment and cure of all forms of cancer, including the development of research into practical applications for the prevention, treatment and cure of cancer and to provide information and raise public understanding of such matters','555-555-555','61 Lincoln\'s Inn Fields London, WC2A 3PX','1089464','United Kingdom'),(5,'Age Concern London','http://www.aclondon.org.uk','Age Concern London works across the capital to improve the quality of life for older people and to enhance their status and influence. A regional body, it promotes issues of importance to older people in London, and works in partnership with Age Concern organisations across London. Age Concern London is an independent organisation within the nationwide Age Concern federation. Donations, covenants and legacies are vital to its work.','555-555-555','21 St. Georges Road (CC), SE1 6ES','1092198','United Kingdom'),(6,'WWF-UK','http://www.wwf.org.uk','WWF is one of the world\'s leading conservation organisations, working to create a lasting harmony between humans and the natural world. As part of the international WWF network, WWF-UK addresses global threats to people and nature such as climate change, the peril to endangered species and habitats, and the unsustainable consumption of the world\'s natural resources. We do this by influencing how governments, businesses and people think, learn and act in relation to the world around us, and by working with local communities to improve their livelihoods and the environment upon which we all depend. WWF uses its practical experience, knowledge and credibility to create long-term solutions for the planet\'s environment. The funding we receive from legacies is a vital element of our work, giving us the mechanism to channel conservation work where it is most needed, and achieve these long-term goals.','555-555-555','Panda House Weyside Park, Godalming , Surrey, GU7 1XR','1081247','United Kingdom'),(7,'WSPA - World Society for the Protection of An','http://www.wspa.org.uk','The World Society for the Protection of Animals is working internationally to end the exploitation and suffering of animals. Through humane education, campaigning against cruelty and animal rescue, WSPA\'s aim is to ensure that the principles of animal welfare are universally understood, respected and protected by effectively enforced legislation. WSPA works with over 400 member organisations in more than 100 countries in order to achieve this, making us the most widespread animal welfare charity in the world.','555-555-555','89 Albert Embankment, London, SE1 7TP','1081849','United Kingdom'),(8,'The Guide Dogs for the Blind Association','http://www.guidedogs.org.uk','Since 1934 Guide Dogs has transformed the lives of thousands of blind and partially sighted people through the provision of guide dogs, mobility and other rehabilitation services. Life takes on a whole new meaning when a visually impaired person trains with a guide dog. They can get out and about when and where they want, without having to rely on friends and family. Guide Dogs main work is, and always will be, the training and provision of guide dogs. But we also provide a huge range of other services for blind and partially sighted people. These include long cane training, and help with daily living skills. Guide Dogs are also one of the UK\'s largest contributors to research into ophthalmic disease.','555-555-555','Burghfield Common, Reading, RG7 3YG','209617','United Kingdom'),(9,'Action for Children','http://www.actionforchildren.org.uk','We help nearly 170,000 children, young people and their families through nearly 450 projects across the UK. We also promote social justice by lobbying and campaigning for change','555-555-555','85 Highbury Park, London, N5 1UD','1097940','United Kingdom'),(10,'CHASE Hospice Care for Children','http://www.chasecare.org.uk','CHASE provides a network of care for life-limited young people and their families living in South West London, Surrey and Sussex through our children\'s hospice, Christopher\'s, and a home- and community-based care service','555-555-555','Loseley Park,Guildford, Surrey, GU3 1HS','1042495','United Kingdom'),(11,'British Heart Foundation','http://www.bhf.org.uk','The British Heart Foundation plays a vital role in funding pioneering heart research in the UK, it provides support and care for heart patients and information to help people reduce their own risk of dying prematurely from a heart or circulatory related illness','555-555-555','Greater London House, 180 Hampstead Road, London, NW1 7AW','225971','United Kingdom'),(12,'National Osteoporosis Society ','http://www.nos.org.uk','The society\'s main activities are as follows:charitable activities, services to members, support groups and the general public. The Society is also has active in research, policy and education and awareness.','555-555-555','Camerton, Bat, BA2 0PJ','1102712','United Kingdom');
/*!40000 ALTER TABLE `charities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charity_label`
--

DROP TABLE IF EXISTS `charity_label`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `charity_label` (
  `charity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`tag_id`,`charity_id`),
  KEY `fk_charity_label_charities` (`charity_id`),
  KEY `fk_charity_label_tags` (`tag_id`),
  CONSTRAINT `fk_charity_label_charities` FOREIGN KEY (`charity_id`) REFERENCES `charities` (`charity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_charity_label_tags` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `charity_label`
--

LOCK TABLES `charity_label` WRITE;
/*!40000 ALTER TABLE `charity_label` DISABLE KEYS */;
INSERT INTO `charity_label` VALUES (1,1),(1,2),(2,2),(2,3);
/*!40000 ALTER TABLE `charity_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `contacts` (
  `contact_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) character set utf8 default NULL,
  `title` varchar(45) character set utf8 default NULL,
  `email` varchar(45) character set utf8 default NULL,
  `phone` varchar(45) character set utf8 default NULL,
  `address` text character set utf8,
  `charity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`contact_id`),
  KEY `fk_contacts_charities` (`contact_id`),
  CONSTRAINT `fk_contacts_charities` FOREIGN KEY (`contact_id`) REFERENCES `charities` (`charity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `donations` (
  `donation_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `amount` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`donation_id`),
  KEY `fk_donations_user` (`user_id`),
  CONSTRAINT `fk_donations_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `donations`
--

LOCK TABLES `donations` WRITE;
/*!40000 ALTER TABLE `donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `history` (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(10) unsigned NOT NULL,
  `year` int(11) NOT NULL,
  `revenue` int(11) default NULL,
  `expense` int(11) default NULL,
  PRIMARY KEY  (`history_id`),
  KEY `fk_history_charities` (`charity_id`),
  CONSTRAINT `fk_history_charities` FOREIGN KEY (`charity_id`) REFERENCES `charities` (`charity_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pie_slices`
--

DROP TABLE IF EXISTS `pie_slices`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pie_slices` (
  `slice_id` int(10) unsigned NOT NULL auto_increment,
  `pie_id` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL default '0',
  `recipient_id` int(10) unsigned NOT NULL,
  `recipient_type` varchar(45) NOT NULL,
  PRIMARY KEY  (`slice_id`),
  UNIQUE KEY `unique_slice` (`pie_id`,`recipient_id`,`recipient_type`),
  KEY `fk_pie_slices_pies` (`pie_id`),
  CONSTRAINT `fk_pie_slices_pies` FOREIGN KEY (`pie_id`) REFERENCES `pies` (`pie_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pie_slices`
--

LOCK TABLES `pie_slices` WRITE;
/*!40000 ALTER TABLE `pie_slices` DISABLE KEYS */;
INSERT INTO `pie_slices` VALUES (7,1,0,4,'CHARITY'),(8,1,0,1,'CHARITY'),(20,5,0,2,'CAUSE'),(23,6,0,2,'CHARITY'),(34,8,0,3,'CAUSE'),(35,8,0,2,'CHARITY'),(38,9,0,2,'CHARITY'),(45,10,0,1,'CHARITY'),(46,10,0,3,'CAUSE'),(47,10,0,5,'CAUSE'),(48,11,0,2,'CHARITY'),(49,12,0,2,'CHARITY'),(54,4,20,1,'CHARITY'),(55,2,100,2,'CAUSE'),(56,2,0,5,'CAUSE');
/*!40000 ALTER TABLE `pie_slices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pies`
--

DROP TABLE IF EXISTS `pies`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pies` (
  `pie_id` int(10) unsigned NOT NULL auto_increment,
  `owner_id` int(10) unsigned NOT NULL,
  `owner_type` varchar(45) NOT NULL,
  PRIMARY KEY  (`pie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pies`
--

LOCK TABLES `pies` WRITE;
/*!40000 ALTER TABLE `pies` DISABLE KEYS */;
INSERT INTO `pies` VALUES (1,1,'USER'),(2,2,'USER'),(3,3,'USER'),(4,2,'CAUSE'),(5,3,'CAUSE'),(6,4,'CAUSE'),(7,4,'USER'),(8,5,'USER'),(9,6,'USER'),(10,7,'USER'),(11,5,'CAUSE'),(12,8,'USER'),(13,6,'CAUSE');
/*!40000 ALTER TABLE `pies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tags` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(45) character set utf8 NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'Aged'),(2,'Animals'),(3,'Blind & Partially Sighted');
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) character set utf8 NOT NULL,
  `full_name` text character set utf8,
  `dob` date default NULL,
  `location` text character set utf8,
  `registered_date` varchar(45) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'baris-tosun','baris-tosun','Baris Abdurrahman Tosun','1980-12-17','London, England',''),(2,'nathan@yura.net','nathan','Tony Wu','1980-12-17','London',''),(3,'n@han.org.uk','nathan','Nathan Sudell','1980-12-17','London\r\n',''),(4,'a@b.co','password',NULL,NULL,NULL,'2009-02-26 23:51:18'),(5,'a@b.co','password',NULL,NULL,NULL,'2009-02-26 23:52:11'),(6,'q@w.er','password',NULL,NULL,NULL,'2009-02-26 23:52:50'),(7,'a@b.cd','password',NULL,NULL,NULL,'2009-02-27 00:38:14'),(8,'dtink79@yahoo.co.uk','password',NULL,NULL,NULL,'2009-02-27 08:39:56');
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

-- Dump completed on 2009-03-22 12:07:45
