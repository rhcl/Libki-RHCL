SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `libki`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientplugins`
--

CREATE TABLE IF NOT EXISTS `clientplugins` (
  `name` varchar(255) NOT NULL,
  `description` text,
  `trigger` varchar(100) default NULL,
  `url` text,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `machine_name` varchar(255) NOT NULL,
  `category` varchar(100) default NULL,
  `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `require_reservation` tinyint(1) NOT NULL default '0',
  `command` varchar(10) default NULL,
  PRIMARY KEY  (`machine_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE IF NOT EXISTS `logins` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) NOT NULL,
  `password` varchar(32) default NULL,
  `units` bigint(20) default NULL,
  `status` varchar(50) default NULL,
  `message` text,
  `notes` text,
  `last_accessed` datetime default NULL,
  `machine` varchar(128) NOT NULL default '',
  `admin` tinyint(1) NOT NULL default '0',
  `troublemaker` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL auto_increment,
  `machine_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `starting_time` datetime NOT NULL,
  `ending_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `serverplugins`
--

CREATE TABLE IF NOT EXISTS `serverplugins` (
  `name` varchar(255) NOT NULL,
  `description` text,
  `trigger` varchar(100) NOT NULL,
  `command` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `value` text,
  `description` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

CREATE TABLE IF NOT EXISTS `statistics` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(100) default NULL,
  `machine` varchar(100) default NULL,
  `status` varchar(255) default NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

