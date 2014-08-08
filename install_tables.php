<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Set return page
$return_url = "install_tables.php";

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "install_tables";

/*
 Install the tables here. Do so in such a way that if you run this script
 twice by accident, it doesn't break everything.
*/

 $db->query("
  CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "ap_settings` (
  `id` varchar(10) NOT NULL,
  `consumer_key` varchar(255) NOT NULL,
  `consumer_secret` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
  );
 ");


 $db->query("
   CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "authed_users` (
  `id` varchar(48) NOT NULL,
  `oauth_token` varchar(255) NOT NULL,
  `oauth_token_secret` varchar(255) NOT NULL,
  `profile_image_url` text NOT NULL,
  `screen_name` varchar(32) NOT NULL,
  `followers_count` int(11) NOT NULL,
  `friends_count` int(11) NOT NULL,
  `auto_follow` tinyint(1) NOT NULL default '0',
  `auto_unfollow` tinyint(1) NOT NULL default '0',
  `auto_dm` tinyint(1) NOT NULL default '0',
  `auto_dm_msg` text NOT NULL,
  `log_data` tinyint(1) NOT NULL default '0',
  `fr_fw_fp` tinyint(1) NOT NULL default '0',
  `last_updated` datetime NOT NULL,
  PRIMARY KEY  (`id`)
  );
 ");

 $db->query("
  CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cron_logs` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` varchar(48) NOT NULL,
  `type` tinyint(1) NOT NULL default '0',
  `log_text` text NOT NULL,
  `affected_users` longtext NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `type` (`type`)
  );
 ");

 $db->query("
  CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cron_status` (
  `cron_name` varchar(10) NOT NULL default '',
  `cron_state` tinyint(1) NOT NULL default '0',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`cron_name`)
  );
 ");

 $db->query("
  INSERT INTO `" . DB_PREFIX . "cron_status` (`cron_name`, `cron_state`, `last_updated`) VALUES
 ('follow', 0, '0000-00-00 00:00:00'),
 ('tweet', 0, '0000-00-00 00:00:00');
 ");

 $db->query("
 CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "follow_exclusions` (
  `type` tinyint(1) NOT NULL default '0',
  `owner_id` varchar(48) NOT NULL,
  `twitter_id` varchar(48) NOT NULL,
  `screen_name` varchar(32) NOT NULL,
  `profile_image_url` text NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY  (`type`,`owner_id`,`twitter_id`),
  KEY `type` (`type`),
  KEY `owner_id` (`owner_id`)
 );
 ");

 $db->query("
 CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "scheduled_tweets` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` varchar(48) NOT NULL,
  `tweet_content` text NOT NULL,
  `time_to_post` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `owner_id` (`owner_id`),
  KEY `time_to_post` (`time_to_post`)
 );
 ");

 $db->query("
  CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "user_cache` (
  `twitter_id` varchar(48) NOT NULL,
  `screen_name` varchar(32) NOT NULL,
  `actual_name` varchar(64) NOT NULL,
  `profile_image_url` text NOT NULL,
  `followers_count` int(11) NOT NULL default '0',
  `friends_count` int(11) NOT NULL default '0',
  `last_updated` datetime NOT NULL,
  PRIMARY KEY  (`twitter_id`),
  KEY `screen_name` (`screen_name`)
  );
 ");

}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
