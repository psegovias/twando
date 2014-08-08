<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('../include_top.php');

if (mainFuncs::is_logged_in() != true) {
 include('../content/' . TWANDO_LANG . '/ajax.not_logged_in.php');
} else {

 //Get twitter credentials if not set
 if (!isset($ap_creds)) {$ap_creds = $db->get_ap_creds();}

 if ( ($_REQUEST['update_type'] == 'refresh') and ($_REQUEST['id']) ) {
  //Refresh
  $q1a = $db->get_user_data($_REQUEST['id']);
  $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
  $content = $connection->get('account/verify_credentials');

  if ($connection->http_code == 200) {
   //Update DB
   $tw_user = array('id' => $q1a['id'],
                  'profile_image_url' => $content->profile_image_url,
                  'screen_name' => $content->screen_name,
                  'followers_count' => $content->followers_count,
                  'friends_count' => $content->friends_count,
                  'last_updated' => date("Y-m-d H:i:s")
 		 );

   $db->store_authed_user($tw_user);
   $response_msg = mainFuncs::push_response(9);
  } else {
   $response_msg = mainFuncs::push_response(12);
  }
 }

 elseif ( ($_REQUEST['update_type'] == 'delete') and ($_REQUEST['id']) ) {
  //Delete
  $db->query("DELETE FROM " . DB_PREFIX . "authed_users WHERE id='" . $db->prep($_REQUEST['id']) . "'");
  $db->query("OPTIMIZE TABLE " . DB_PREFIX . "authed_users");
  $db->query("DELETE FROM " . DB_PREFIX . "follow_exclusions WHERE owner_id='" . $db->prep($_REQUEST['id']) . "'");
  $db->query("OPTIMIZE TABLE " . DB_PREFIX . "follow_exclusions");
  $db->query("DELETE FROM " . DB_PREFIX . "cron_logs WHERE owner_id='" . $db->prep($_REQUEST['id']) . "'");
  $db->query("OPTIMIZE TABLE " . DB_PREFIX . "cron_logs");
  $db->query("DELETE FROM " . DB_PREFIX . "scheduled_tweets WHERE owner_id='" . $db->prep($_REQUEST['id']) . "'");
  $db->query("OPTIMIZE TABLE " . DB_PREFIX . "scheduled_tweets");
  $db->query("DROP TABLE `". DB_PREFIX . "fw_" . $_REQUEST['id'] . "`");
  $db->query("DROP TABLE `". DB_PREFIX . "fr_" . $_REQUEST['id'] . "`");
  $response_msg = mainFuncs::push_response(4);
 }



include('../content/' . TWANDO_LANG . '/ajax.index_inc.php');

//End of is logged in
}


include('../include_bottom.php');

?>
