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

    if ( ($_REQUEST['a'] == 'cross_go') and  ((int)$_REQUEST['cross_op'] > 0) ) {
     //Get ap creds
     $ap_creds = $db->get_ap_creds();
     $ua  = $db->get_all_user_data();
     $ua2 = $ua;

     //Loop
     foreach ($ua as $this_id => $this_data) {
      $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $this_data['oauth_token'], $this_data['oauth_token_secret']);
      foreach ($ua2 as $this_id2 => $this_data2) {
       if ($this_id != $this_id2) {
        if ((int)$_REQUEST['cross_op'] == 1) {
         $connection->post('friendships/create',array('user_id' => $this_id2));
        } elseif ((int)$_REQUEST['cross_op'] == 2) {
         $connection->post('friendships/destroy',array('user_id' => $this_id2));
        }
       }
      }
     }


     //Response message - nothing too descriptive really required here
     $response_msg = mainFuncs::push_response(30);

    }

    break;
   case 'tab2':

    if ( ($_REQUEST['a'] == 'allfollow_go') and  ($_REQUEST['twitter_ids_list']) ) {

     //Get data
     $screen_names = explode("\n",str_replace("\r",'',$_REQUEST['twitter_ids_list']));
     $ap_creds = $db->get_ap_creds();
     $ua  = $db->get_all_user_data();

     //Set options
     if ((int)$_REQUEST['cross_op'] == 1) {$twt_op = 'friendships/create';}
     elseif ((int)$_REQUEST['cross_op'] == 2) {$twt_op = 'friendships/destroy';}
     if ((int)$_REQUEST['cross_type'] == 1) {$usr_op = 'screen_name';}
     elseif ((int)$_REQUEST['cross_type'] == 2) {$usr_op = 'user_id';}

     //Loop
     foreach ($ua as $this_id => $this_data) {
      $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $this_data['oauth_token'], $this_data['oauth_token_secret']);
      foreach ($screen_names as $this_name) {
       $this_name = trim($this_name);
       $connection->post($twt_op, array($usr_op => $this_name));
      }
     }

     //Response message - nothing too descriptive really required here
     $response_msg = mainFuncs::push_response(30);

    }

   break;
  case 'tab3':

   if ( ($_REQUEST['a'] == 'quicktweet') and ($_REQUEST['tweet_content']) ) {
    //Get Data
    $ap_creds = $db->get_ap_creds();
    $ua  = $db->get_all_user_data();
    foreach ($ua as $this_id => $this_data) {
     $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $this_data['oauth_token'], $this_data['oauth_token_secret']);
     $connection->post('statuses/update', array('status' => $_REQUEST['tweet_content']));
    }

     //Response message - nothing too descriptive really required here
     $response_msg = mainFuncs::push_response(31);

   }

   break;
  //End of tab switch
  }

 //End of data update POST
 }

 include('../content/' . TWANDO_LANG . '/ajax.multi_account_functions_inc.php');


//End of is logged in
}


include('../include_bottom.php');
?>
