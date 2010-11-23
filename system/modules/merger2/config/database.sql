-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `mergerMode` varchar(14) NOT NULL default '',
  `mergerTemplate` varchar(64) NOT NULL default 'modulemerger_default',
  `mergerContainer` char(1) NOT NULL default '',
  `mergerData` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_article`
-- 

CREATE TABLE `tl_article` (
  `inheritable` char(1) NOT NULL default '1',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
