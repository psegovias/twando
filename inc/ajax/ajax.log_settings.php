<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('../include_top.php');

if (mainFuncs::is_logged_in() != true) {
 include('../content/' . TWANDO_LANG . '/ajax.not_logged_in.php');
} else {

 //Define response
 $response_msg = "";

 if ($_REQUEST['update_type'] == 'update_data') {
  //Updates to be done here
  switch ($_REQUEST['tab_id']) {
    case 'tab1':
    $tw_user = array('id' => $_REQUEST['twitter_id'],
                  'log_data' => (int)$_REQUEST['log_data'],
                  'last_updated' => date("Y-m-d H:i:s")
 		 );

    $db->store_authed_user($tw_user);
    $response_msg = mainFuncs::push_response(8);
    break;
   case 'tab4':

    if ($_REQUEST['a'] == 'deletelogs') {
     //Set query
     $base_query = "DELETE FROM " . DB_PREFIX . "cron_logs WHERE owner_id='" . $db->prep($_REQUEST['twitter_id']) . "'";
     if ((int)$_REQUEST['log_time'] == 2) {
      $base_query .= " AND last_updated < '" . date("Y-m-d H:i:s",strtotime('-30 days')) . "'";
     } elseif ((int)$_REQUEST['log_time'] == 3) {
      $base_query .= " AND last_updated < '" . date("Y-m-d H:i:s",strtotime('-90 days')) . "'";
     }
     if ((int)$_REQUEST['log_type'] > 0) {
      $base_query .= " AND type = " .  (int)$_REQUEST['log_type'];
     }

     $db->query($base_query);
     $db->query("OPTIMIZE TABLE " . DB_PREFIX . "cron_logs");

     if ((int)$_REQUEST['empty_cache'] == 1) {
      $db->query("TRUNCATE TABLE " . DB_PREFIX . "user_cache");
      $db->query("OPTIMIZE TABLE " . DB_PREFIX . "user_cache");
     }
     $response_msg = mainFuncs::push_response(22);
    }

   break;
  //End of tab switch
  }

 //End of data update POST
 }

 //Get account details
 if (!$q1a) {
  $q1a = $db->get_user_data($_REQUEST['twitter_id']);
 }


 include('../content/' . TWANDO_LANG . '/ajax.log_settings_inc.php');



//End of is logged in
}


include('../include_bottom.php');
?>
