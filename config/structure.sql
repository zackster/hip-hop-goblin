-- MySQL dump 10.11
--
-- Host: localhost    Database: devsquid_hhg
-- ------------------------------------------------------
-- Server version	5.0.85-community

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
-- Table structure for table `artist_info`
--

DROP TABLE IF EXISTS `artist_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artist_info` (
  `id` int(11) NOT NULL auto_increment,
  `artist` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `artist_submissions`
--

DROP TABLE IF EXISTS `artist_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artist_submissions` (
  `id` int(11) NOT NULL auto_increment,
  `artist` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `album` varchar(255) NOT NULL,
  `time_submitted` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ip_submitted` varchar(16) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `approval_status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blogs` (
  `url` varchar(255) NOT NULL,
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emails` (
  `userid` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scrapefetch`
--

DROP TABLE IF EXISTS `scrapefetch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scrapefetch` (
  `lastupdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  KEY `lastupdate` (`lastupdate`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `songs`
--

DROP TABLE IF EXISTS `songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `songs` (
  `id` int(11) NOT NULL auto_increment,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `artist` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `album` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `listen_count` int(11) NOT NULL default '0',
  `referral` varchar(255) NOT NULL,
  `live` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=22755 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `top50`
--

DROP TABLE IF EXISTS `top50`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `top50` (
  `rank` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`rank`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twitcreep`
--

DROP TABLE IF EXISTS `twitcreep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `twitcreep` (
  `tweetid` varchar(255) NOT NULL,
  `response` int(11) NOT NULL,
  UNIQUE KEY `tweetid` (`tweetid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `remote_addr` varchar(16) NOT NULL,
  `date_added` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19123 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vogoo_ads`
--

DROP TABLE IF EXISTS `vogoo_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vogoo_ads` (
  `ad_id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `mini` int(11) NOT NULL,
  KEY `ad_id` (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vogoo_ads_products`
--

DROP TABLE IF EXISTS `vogoo_ads_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vogoo_ads_products` (
  `ad_id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  KEY `ad_id` (`ad_id`),
  KEY `category` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vogoo_links`
--

DROP TABLE IF EXISTS `vogoo_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vogoo_links` (
  `item_id1` int(11) NOT NULL,
  `item_id2` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `cnt` int(11) default NULL,
  `diff_slope` float default NULL,
  UNIQUE KEY `vogoo_links_ix` (`item_id1`,`item_id2`,`category`),
  KEY `vogoo_links_i1ix` (`item_id1`),
  KEY `vogoo_links_i2ix` (`item_id2`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vogoo_ratings`
--

DROP TABLE IF EXISTS `vogoo_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vogoo_ratings` (
  `member_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `rating` float NOT NULL,
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  UNIQUE KEY `vogoo_ratings_mpix` (`member_id`,`product_id`,`category`),
  KEY `vogoo_ratings_mix` (`member_id`),
  KEY `vogoo_ratings_pix` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-12-20  0:42:58
