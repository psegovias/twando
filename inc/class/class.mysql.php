<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

/*
Database functions abstracted in 0.3 ready for switch to
mysqli, once this is more widespread
*/

class mySqlCon {

 private $db_host;
 private $db_user;
 private $db_password;
 private $db_table;
 private $db_link;
 public  $output_error;

 /*
 Set DB options from config.php
 */

 function __construct() {
  $this->db_host = DB_HOST;
  $this->db_user = DB_USER;
  $this->db_password = DB_PASSWORD;
  $this->db_table = DB_NAME;
  $this->output_error = 0;
  $this->db_connect();
 }

 /*
 Open DB connection
 */

 private function db_connect() {
  $this->db_link = mysql_connect($this->db_host,$this->db_user,$this->db_password);
  $db_test2 = mysql_select_db($this->db_table,$this->db_link);

  if ( (!$this->db_link) or (!$db_test2) ) {
   mainFuncs::print_html('mysql_error');
   exit;
  }

 }

 /*
 Query
 */

 public function query($res,$pass_no = 1) {
  $q1 = mysql_query($res,$this->db_link);
  if (!$q1) {
   $this->sql_error();
   if ($pass_no == 1) {
    if ($this->output_error == 1) {
     echo 'Attempting to reconnect to MySQL...' . "\n";
    }
    $this->db_close(); 
    $this->db_connect();
    $this->query($res,2);
   } else {
    if ($this->output_error == 1) {
     echo 'Reconnection failed; please check your MySQL server settings!' . "\n";
    }
   }
  } else {
   return $q1;
  }
 }

 /*
 Fetch Array
 */

 public function fetch_array($res) {
  return mysql_fetch_array($res);
 }

 /*
 Fetch Row
 */

 public function fetch_row($res) {
  return mysql_fetch_row($res);
 }

 /*
 Num Rows
 */

 public function num_rows($res) {
  return mysql_num_rows($res);
 }

 /*
 SQL Result
 */

 public function sql_result($res,$par) {
  return mysql_result($res,$par);
 }

 /*
 SQL Error
 */

 public function sql_error() {
  if ($this->output_error == 1) {
   echo "MySQL Error " . mysql_errno($this->db_link) . ": " . mysql_error($this->db_link) . "\n";
  }
 }

 /*
 Get Twitter AP reg details
 */

 public function get_ap_creds() {
  $qcheck = $this->query("SELECT consumer_key, consumer_secret FROM " . DB_PREFIX . "ap_settings WHERE id='twando'");
  //Supress error here in case install_tables.php hasn't been run yet
  return ($this->fetch_array($qcheck));
 }

 /*
 Store authed twitter user
 */

 public function store_authed_user($tw_user) {

  //Defines
  $cols = "";
  $values = "";
  $query = "";

  $q1  = $this->query("SELECT * FROM " . DB_PREFIX . "authed_users WHERE id='" . $this->prep($tw_user['id']) . "'");
  if ($this->num_rows($q1) == 0) {
   //Insert
   foreach ($tw_user as $col => $value) {
    $cols .= $col . ",";
    $values .= "'" . $this->prep($value) . "',";
   }
   $cols = substr($cols,0,-1);
   $values = substr($values,0,-1);

   $query = "INSERT INTO " . DB_PREFIX . "authed_users (" . $cols  . ") VALUES (" . $values . ")";
   $this->query($query);
  } else {
   //Update
   foreach ($tw_user as $col => $value) {
    $values .= $col . "='" . $this->prep($value) . "',";
   }
   $values = substr($values,0,-1);

   $query = "UPDATE " . DB_PREFIX . "authed_users SET " . $values . " WHERE  id='" . $this->prep($tw_user['id']) . "'";
   $this->query($query);
  }
 }

  /*
 Get User fields
 */

 public function get_user_data($user_id) {
  $q1  = $this->query("SELECT * FROM " . DB_PREFIX . "authed_users WHERE id = '" . $this->prep($user_id) . "'");
  return ($this->fetch_array($q1));
 }


 /*
 Store excluded twitter user
 */

 public function store_excluded_user($tw_user) {

  //Defines
  $cols = "";
  $values = "";
  $query = "";

  $q1  = $this->query("SELECT * FROM " . DB_PREFIX . "follow_exclusions WHERE type='" . (int)$tw_user['type'] . "' AND owner_id='" . $this->prep($tw_user['owner_id'])  . "' AND twitter_id='" . $this->prep($tw_user['twitter_id']) . "'");
  if ($this->num_rows($q1) == 0) {
   //Insert
   foreach ($tw_user as $col => $value) {
    $cols .= $col . ",";
    $values .= "'" . $this->prep($value) . "',";
   }
   $cols = substr($cols,0,-1);
   $values = substr($values,0,-1);

   $query = "INSERT INTO " . DB_PREFIX . "follow_exclusions (" . $cols  . ") VALUES (" . $values . ")";
   $this->query($query);
  } else {
   //Update
   foreach ($tw_user as $col => $value) {
    $values .= $col . "='" . $this->prep($value) . "',";
   }
   $values = substr($values,0,-1);

   $query = "UPDATE " . DB_PREFIX . "follow_exclusions SET " . $values . " WHERE type='" . (int)$tw_user['type'] . "' AND owner_id='" . $this->prep($tw_user['owner_id'])  . "' AND twitter_id='" . $this->prep($tw_user['twitter_id']) . "'";
   $this->query($query);
  }
 }

 /*
 Store user cache details
 */

 public function store_cached_user($tw_user) {

  //Defines
  $cols = "";
  $values = "";
  $query = "";

  if ($tw_user['twitter_id'] != "") {

   $q1  = $this->query("SELECT * FROM " . DB_PREFIX . "user_cache WHERE twitter_id='" . $this->prep($tw_user['twitter_id']) . "'");
   if ($this->num_rows($q1) == 0) {
    //Insert
    foreach ($tw_user as $col => $value) {
     $cols .= $col . ",";
     $values .= "'" . $this->prep($value) . "',";
    }
    $cols = substr($cols,0,-1);
    $values = substr($values,0,-1);

    $query = "INSERT INTO " . DB_PREFIX . "user_cache (" . $cols  . ") VALUES (" . $values . ")";
    $this->query($query);
   } else {
    //Update
    foreach ($tw_user as $col => $value) {
     $values .= $col . "='" . $this->prep($value) . "',";
    }
    $values = substr($values,0,-1);

    $query = "UPDATE " . DB_PREFIX . "user_cache SET " . $values . " WHERE twitter_id='" . $this->prep($tw_user['twitter_id']) . "'";
    $this->query($query);
  }

  }
 }

 /*
 Create friend and follower tables on account auth
 */

 public function create_cron_tables($tw_user_id) {

  $this->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fw_" . $tw_user_id . "` (
  `twitter_id` varchar(48) NOT NULL,
  `stp` tinyint(1) NOT NULL default '0',
  `ntp` tinyint(1) NOT NULL default '0',
  `otp` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`twitter_id`),
  KEY `stp` (`stp`),
  KEY `ntp` (`ntp`),
  KEY `otp` (`otp`)
  );");

  $this->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fr_" . $tw_user_id . "` (
  `twitter_id` varchar(48) NOT NULL,
  `stp` tinyint(1) NOT NULL default '0',
  `ntp` tinyint(1) NOT NULL default '0',
  `otp` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`twitter_id`),
  KEY `stp` (`stp`),
  KEY `ntp` (`ntp`),
  KEY `otp` (`otp`)
  );");

 }

 /*
 Echo affected user data
 */

 public function print_au($user_id,$lang_ops) {

  //If this was a public facing app then it would be worth caching these
  //responses once per page load, but it's not, so it's not :)

  //Get Data
  $qcheck  = $this->query("SELECT * FROM " . DB_PREFIX . "user_cache WHERE twitter_id = '" . $this->prep($user_id) . "'");
  $qchecka = $this->fetch_array($qcheck);

  //Echo image and rollover
  if ($qchecka['screen_name'] == "") {
   echo '<a href="https://twitter.com/account/redirect_by_id?id=' . $user_id . '" target="_blank"><img class="affuser" src="inc/images/def.gif" /></a>';
  } else {
   //Nearly used a jQuery tooltip, but bloaty and not really needed
   echo '<a href="https://twitter.com/account/redirect_by_id?id=' . $qchecka['twitter_id'] . '" target="_blank">';
   echo  '<img title="' . $lang_ops[0] . ': ' . $qchecka['screen_name'] . " \n";
   echo  $lang_ops[1] . ': ' . $qchecka['actual_name'] . " \n"; 
   echo  $lang_ops[2] . ': ' . number_format($qchecka['friends_count']) . " \n";
   echo  $lang_ops[3] . ': ' . number_format($qchecka['followers_count']) . " \n";
   echo  $lang_ops[4] . ': ' . date(TIMESTAMP_FORMAT,strtotime($qchecka['last_updated'])) . " \n";
   echo '" class="affuser" src="' . $qchecka['profile_image_url'] . '" /></a>';
  }

 }

 /*
 Check friends list
 */

 public function is_on_fr_list($twitter_id,$check_id) {

  //Check if user is on
  $qcheck = $this->query("SELECT * FROM " . DB_PREFIX . "fr_" . $twitter_id . " WHERE twitter_id ='" . $this->prep($check_id) . "'");
  if ($this->num_rows($qcheck) == 0) {
   return false;
  } else {
   return true;
  }
 }

 /*
 Get all authed user IDs
 */

 public function get_all_user_data() {

  //Defines
  $return_array = array();

  //Pull data
  $qcheck = $this->query("SELECT * FROM " . DB_PREFIX . "authed_users WHERE id!=''");
  while ($qchecka = $this->fetch_array($qcheck)) {
   $return_array[$qchecka['id']] = $qchecka;
  }

  return $return_array;

 }

 /*
 Check table exists
 */

 public function check_table_exists($table_name) {

   $res = $this->query("
        SELECT COUNT(*) AS count
        FROM information_schema.tables
        WHERE table_schema = '" . $this->db_table . "'
        AND table_name = '" . $table_name . "'
    ");

    return $this->sql_result($res, 0) == 1;
 }

 /*
 Close DB connection
 */

 private function db_close() {
  @mysql_close();
 }

 /*
 Sanatize DB input
 */

 public function prep($mysql_string) {

  if (get_magic_quotes_gpc()) {
   $mysql_string = stripslashes($mysql_string);
  }

  $mysql_string = mysql_real_escape_string($mysql_string);

  return $mysql_string;
 }

 /*
 Close DB connection when class is unset
 */

 function __destruct() {
  $this->db_close();
 }

}
?>
