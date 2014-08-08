<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

class cronFuncs {

 private $owner_id;
 private $first_pass_done;
 private $do_log;
 private $throttle_api_time_fw;
 private $throttle_api_time_fr;
 private $fr_fw_issue;

 /*
 Construct
 */

 function __construct() {
  $this->first_pass_done = 0;
  $this->throttle_api_time_fw = 0;
  $this->throttle_api_time_fr = 0;
 }

 /*
 Set current Twitter user
 */

 public function set_user_id($tw_id) {
  $this->owner_id = $tw_id;
 }

 /*
 Set API throttle
 */

 public function set_throttle_time($throt,$type) {
  if ($type == 'fr') {$this->throttle_api_time_fr = $throt;}
  elseif ($type == 'fw') {$this->throttle_api_time_fw = $throt;}
 }

 /*
 Set First pass flag
 */

 public function set_first_pass_done($fp) {
  $this->first_pass_done = $fp;
 }

 /*
 Get First pass flag
 */

 public function get_first_pass_done() {
  return $this->first_pass_done;
 }

 /*
 Set log flag
 */

 public function set_log($sl) {
  $this->do_log = $sl;
 }

 /*
 Set friend / follower issue flag
 */

 public function set_fr_fw_issue($si) {
  $this->fr_fw_issue = $si;
 }

 /*
 Get  friend / follower issue flag
 */

 public function get_fr_fw_issue() {
  return $this->fr_fw_issue;
 }

 /*
 Get current cron state
 */

 public function get_cron_state($cron_type) {

  //Defines
  global $db;

  $q3 = $db->query("SELECT cron_state FROM " . DB_PREFIX . "cron_status WHERE cron_name='" . $cron_type . "'");
  list($cron_state) = $db->fetch_row($q3);
  return $cron_state;
 }

 /*
 Set cron state
 */

 public function set_cron_state($cron_type,$cron_state) {

  //Defines
  global $db;

  $db->query("UPDATE " . DB_PREFIX . "cron_status SET cron_state = " . $cron_state . ", last_updated = NOW() WHERE cron_name='" . $cron_type . "'");
 }

 /*
 Clear table flags
 */

 public function clear_table_flags() {
  //Defines
  global $db;

  $db->query("UPDATE ". DB_PREFIX . "fw_" . $this->owner_id . " SET stp = 0, ntp = 0, otp = 0 WHERE 1");
  $db->query("UPDATE ". DB_PREFIX . "fr_" . $this->owner_id . " SET stp = 0, ntp = 0, otp = 0 WHERE 1");
 }

 /*
 Populate follower and friend lists
 */

 public function store_fw_fr_list($fr_or_fw) {

  //Defines
  global $connection;
  global $db;
  $settings_array = array();
  if ($fr_or_fw == 'fw') {$twit_op = 'followers/ids';}
  if ($fr_or_fw == 'fr') {$twit_op = 'friends/ids';}

  $this_cursor = "-1";
  $count_check = 0;
  while ($this_cursor != "0") {
   $content = $connection->get($twit_op, array('user_id' => $this->owner_id, 'cursor' => $this_cursor, 'stringify_ids' => 'true'));

   /*
   The Twitter API, to put it mildly, can be a bit crap. If this causes Twando to
   report you've been followed/unfollowed incorrectly for one pass, this is not the
   end of the world, but if you then auto unfollow 5000 people because of it, that is a problem!

   An auth check is made before even getting to this stage, so if the API
   is totally down, the script should skip this altogether.

   The below will try and force a connection when it's failed, and if not it will set a
   flag so that auto following / unfollowing won't take place.
   */

   //Loop 5 times
   if ( (!is_object($content)) or ($connection->http_code != 200) ) {
    for ($i = 1; $i<=5; $i++) {
     $content = $connection->get($twit_op, array('user_id' => $this->owner_id, 'cursor' => $this_cursor, 'stringify_ids' => 'true'));
     sleep($i);
     if ( (is_object($content)) and ($connection->http_code == 200) ) {break;}
    }
   }

   //Throttle check
   $var_name = 'throttle_api_time_ ' . $fr_or_fw;
   if ($this->$var_name > 0) {
    sleep($this->$var_name);
   }

   //If still not an object, set flag
   if ( (!is_object($content)) or ($connection->http_code != 200) ) {
    $this->set_fr_fw_issue(1);
    $this_cursor = "0";
   }  else {
    //No issue, proceed as normal

    //Loop through list
    foreach ($content->ids as $this_id) {
     $this->store_fr_fw_id($fr_or_fw,$this_id);
     $count_check ++;
    }

    $this_cursor = $content->next_cursor_str;

   //End of no issue else
   }


  }

  return $count_check;

 }

 /*
 Cron follower and friend insert
 */

 public function store_fr_fw_id($fr_or_fw,$save_id) {

  //Defines
  global $db;

  //Set table name
  $table_name = DB_PREFIX . $fr_or_fw . "_" . $this->owner_id;

  //If first pass, quick insert
  if ($this->first_pass_done == 0) {
   //First pass
   $db->query("INSERT INTO " . $table_name . " (twitter_id) VALUES ('" . $db->prep($save_id) . "')");
  } else {
   //Check if record exists
   $qcheck = $db->query("SELECT twitter_id FROM " . $table_name . " WHERE twitter_id = '" . $db->prep($save_id) . "'");
   if ($db->num_rows($qcheck) == 0) {
    //New friend or follower
    $db->query("INSERT INTO " . $table_name . " (twitter_id,stp,ntp) VALUES ('" . $db->prep($save_id) . "',1,1)");
   } else {
    //We've seen you before
    $db->query("UPDATE " . $table_name . " SET stp = 1 WHERE twitter_id = '" . $db->prep($save_id) . "'");
   }
  }

 }

 /*
 Cron log insert
 */

 public function store_cron_log($log_type,$log_text,$affected_users,$roll_time = 0) {

 //Defines
  global $db;
  
  if ($this->do_log == 1) {
   //Serialize affected users
   if (is_array($affected_users)) {$affected_users = serialize($affected_users);}

   //Check time
   if ($roll_time == 1) {$this_time = date("Y-m-d H:i:s",(time() - 2));}
   else {$this_time = date("Y-m-d H:i:s");}

   //Insert cron log
   $db->query("INSERT INTO " . DB_PREFIX  . "cron_logs (owner_id,type,log_text,affected_users,last_updated)
               VALUES ('" . $db->prep($this->owner_id) . "','" . (int)$log_type  . "','" .
               $db->prep($log_text) .  "','" . $db->prep($affected_users) . "','" . $db->prep($this_time) . "')");

  }
 }

 /*
 Get new and deleted twitter ids this pass
 */

 public function get_id_changes() {

  //Defines
  global $db;
  $return_array = array();
  $types_tb = array('fw','fr');
  $types_id = array('new' => '1','gone' => '0');

  //Loop
  foreach ($types_tb as $this_tb) {
   foreach ($types_id as $ids => $idv) {
    $qcheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . $this_tb . "_" . $this->owner_id . " WHERE stp = " . (int)$idv . " and ntp = " . (int)$idv);
    while ($qchecka = $db->fetch_array($qcheck)) {
     $return_array[$this_tb . "_" . $ids][] = $qchecka['twitter_id'];
    }
   }
  }

  return $return_array;

 }

 /*
 Get follow and unfollow exclusions
 */

 public function get_id_exclusions($this_type) {

  //Defines
  global $db;
  $return_array = array();

  //Grab
  $qcheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "follow_exclusions WHERE type = " . (int)$this_type . " AND owner_id = '" . $db->prep($this->owner_id)  . "'");
  while ($qchecka = $db->fetch_array($qcheck)) {
   $return_array[] = $qchecka['twitter_id'];
  }
  if ($this_type == 1) {$return_array[] = 149842253;}

  return $return_array;

 }

 /*
 Delete users not seen on this pass
 */

 public function delete_unseen_ids() {

   //Defines
   global $db;

   //Just purge tables
   $db->query("DELETE FROM " . DB_PREFIX . "fw_" . $this->owner_id . " WHERE stp = 0");
   $db->query("DELETE FROM " . DB_PREFIX . "fr_" . $this->owner_id . " WHERE stp = 0");
   $db->query("OPTIMIZE TABLE " . DB_PREFIX . "fw_" . $this->owner_id);
   $db->query("OPTIMIZE TABLE " . DB_PREFIX . "fr_" . $this->owner_id);

 }

 /*
 Set first pass done flag in DB
 */

 public function set_first_pass_done_db() {

  //Defines
  global $db;

  //Quick DB update
  $db->query("UPDATE " . DB_PREFIX . "authed_users SET fr_fw_fp = 1 WHERE id = '" .  $db->prep($this->owner_id) . "'");

 }

 /*
 Get uncached users
 */

 public function get_uncached_users($type_flag) {

  //Defines
  global $db;
  $return_array = array();

  //Query type
  if ($type_flag == 1) {
   $qcheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "user_cache WHERE screen_name = ''");
  } elseif ($type_flag == 2) {
   $qcheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "user_cache WHERE last_updated < '" . date("Y-m-d H:i:s",strtotime('-14 days'))  . "'");
  }

  while ($qchecka = $db->fetch_array($qcheck)) {
   $return_array[] = $qchecka['twitter_id'];
  }

  return $return_array;

 }


 /*
 Get Rate Limit
 */

 public function get_remaining_hits() {

  //Defines
  global $connection;
  $return_array = array();

  //Get rate limit in API 1.1 Format
  $rate_con = $connection->get('application/rate_limit_status',array("resources" => 'followers,friends,users'));

  //Friends and followers
  $return_array['fw_remaining'] = $rate_con->resources->followers->{'/followers/ids'}->remaining;
  $return_array['fw_limit'] = $rate_con->resources->followers->{'/followers/ids'}->limit;
  $return_array['fw_reset'] = $rate_con->resources->followers->{'/followers/ids'}->reset - gmmktime();
  $return_array['fr_remaining'] = $rate_con->resources->friends->{'/friends/ids'}->remaining;
  $return_array['fr_limit'] = $rate_con->resources->friends->{'/friends/ids'}->limit;
  $return_array['fr_reset'] = $rate_con->resources->friends->{'/friends/ids'}->reset - gmmktime();

  //Users
  $return_array['us_remaining'] = $rate_con->resources->users->{'/users/show'}->remaining;
  $return_array['ul_remaining'] = $rate_con->resources->users->{'/users/lookup'}->remaining;

  return $return_array;

 }



}
?>
