<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');
include('inc/class/class.cron.php');

$cron = new cronFuncs();

//Defines
set_time_limit(0);
$run_cron = true;

//Check crpn key and if running
if ( ($argv[1] != CRON_KEY) and ($_GET['cron_key'] != CRON_KEY) ) {
 echo mainFuncs::push_response(23);
 $run_cron = false;
} else {
 if ($cron->get_cron_state('tweet') == 1) {
  echo mainFuncs::push_response(24);
  $run_cron = false;
 }
}

if ($run_cron == true) {

 /*
 New to 0.3 - Some people on super cheap hosting seem to get
 SQL errors - output them if they occur
 */
 $db->output_error = 1;

 //Set cron status
 $cron->set_cron_state('tweet',1);

 //Get credentials
 $ap_creds = $db->get_ap_creds();

 //Loop through all accounts
 $q1 = $db->query("SELECT * FROM " . DB_PREFIX . "authed_users ORDER BY (followers_count + friends_count) ASC");
 while ($q1a = $db->fetch_array($q1)) {

  //Defines
  $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
  $cron->set_user_id($q1a['id']);
  $cron->set_log($q1a['log_data']);

  //Time can vary between PHP and server if server timezone doesn't match PHP timezone.
  //All scripts use PHP time rather than NOW() to avoid issues.
  $current_time = date("Y-m-d H:i:s");

  //Get scheduled tweets for this user older than or equal to current time
  $q2 = $db->query("SELECT * FROM " . DB_PREFIX . "scheduled_tweets WHERE owner_id = '" . $db->prep($q1a['id']) . "' AND time_to_post != '0000-00-00 00:00:00' AND time_to_post <= '" . $db->prep($current_time) . "' ORDER BY time_to_post ASC");
  while ($q2a = $db->fetch_array($q2)) {

   //Post the tweet
   $connection->post('statuses/update', array('status' => $q2a['tweet_content']));

   //Log result - reasons for a non 200 include duplicate tweets, too many tweets
   //posted in a period of time, etc etc.
   if ($connection->http_code == 200) {
    $cron->store_cron_log(2,$cron_txts[18] . $q2a['tweet_content'] . $cron_txts[19],'');
   } else {
    $cron->store_cron_log(2,$cron_txts[18] . $q2a['tweet_content'] . $cron_txts[20],'');
   }

   //Delete the tweet
   $db->query("DELETE FROM " . DB_PREFIX . "scheduled_tweets WHERE owner_id = '" . $db->prep($q1a['id']) . "' AND id = '" . $q2a['id'] . "'");

  }

 //End of db loop
 }

 //Optimize tweet table
 $db->query("OPTIMIZE TABLE " . DB_PREFIX . "scheduled_tweets");

 //Set cron status
 $cron->set_cron_state('tweet',0);

 echo mainFuncs::push_response(32);

//End of run cron
}

include('inc/include_bottom.php');
?>
