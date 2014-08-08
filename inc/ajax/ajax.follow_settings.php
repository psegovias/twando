<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('../include_top.php');

//Can be a long script
set_time_limit(0);

if (mainFuncs::is_logged_in() != true) {
 include('../content/' . TWANDO_LANG . '/ajax.not_logged_in.php');
} else {

 //Define response
 $response_msg = "";

 if ($_REQUEST['update_type'] == 'update_data') {

  //Updates to be done here
  switch ($_REQUEST['tab_id']) {
    case 'tab1':

    $auto_follow = 0;
    if ((int)$_REQUEST['auto_follow'] == 1) {
     $auto_follow = (int)$_REQUEST['auto_follow_type'];
    }

    $tw_user = array('id' => $_REQUEST['twitter_id'],
                  'auto_follow' => $auto_follow,
                  'auto_unfollow' => (int)$_REQUEST['auto_unfollow'],
                  'auto_dm' => (int)$_REQUEST['auto_dm'],
                  'last_updated' => date("Y-m-d H:i:s")
 		 );

    $db->store_authed_user($tw_user);
    $response_msg = mainFuncs::push_response(8);
    break;

   case 'tab2':
   case 'tab3':

    if ( ($_REQUEST['a'] == 'deleteuserids') and ((sizeof($_REQUEST['delete_list'])) > 0) ) {
     foreach ($_REQUEST['delete_list'] as $this_id) {
      $db->query("DELETE FROM " . DB_PREFIX . "follow_exclusions WHERE type='" . (int)$_REQUEST['follow_type'] . "' AND twitter_id='" .  $db->prep($this_id) . "' AND owner_id='" . $db->prep($_REQUEST['twitter_id']) . "'");
     }
     $response_msg = mainFuncs::push_response(10);
    }
    if ( ($_REQUEST['a'] == 'addfollowids') and ($_REQUEST['twitter_ids_list']) ) {



     $screen_names = array();
     $screen_name_list = "";
     $screen_names = explode("\n",str_replace("\r",'',$_REQUEST['twitter_ids_list']));

     if (sizeof($screen_names) > 0) {
      //Lookup Screen names
      if (sizeof($screen_names) > TWITTER_API_USER_LOOKUP) {$screen_names = array_slice($screen_names,0,TWITTER_API_USER_LOOKUP);}
      $ap_creds = $db->get_ap_creds();
      $q1a = $db->get_user_data($_REQUEST['twitter_id']);
      $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
      foreach ($screen_names as $this_name) {$screen_name_list .= preg_replace('/[^a-z0-9_]/i','',$this_name) . ',';}
      $screen_name_list = substr($screen_name_list,0,-1);

      $content = $connection->post('users/lookup', array('screen_name' => $screen_name_list));

      //New to 0.2 - users/lookup can be flaky; show error if invalid response
      if ($connection->http_code == 200) {

       foreach ($content as $user_row) {

        $tw_user = array(
                  'twitter_id' => $user_row->id,
                  'owner_id' => $_REQUEST['twitter_id'],
                  'profile_image_url' => $user_row->profile_image_url,
                  'screen_name' => $user_row->screen_name,
                  'type' => (int)$_REQUEST['follow_type'],
                  'last_updated' => date("Y-m-d H:i:s")
 		  );

        //We're making the API request anyway, might as well save caching time later
        $tw_user_cache = array(
                  'twitter_id' => $user_row->id,
                  'profile_image_url' => $user_row->profile_image_url,
                  'screen_name' => $user_row->screen_name,
                  'actual_name' => $user_row->name,
                  'followers_count' => $user_row->followers_count,
                  'friends_count' => $user_row->friends_count,
                  'last_updated' => date("Y-m-d H:i:s")
 		  );

        if (($tw_user['twitter_id']) and ($tw_user['screen_name']) ) {
         if ((int)$_REQUEST['just_follow_now'] == 0) {
          $db->store_excluded_user($tw_user);
         }
         $db->store_cached_user($tw_user_cache);
         $response_msg = mainFuncs::push_response(11);

         if ((int)$_REQUEST['follow_now'] == 1) {
          if ((int)$_REQUEST['follow_type'] == 1) {
           $connection->post('friendships/create',array('user_id' => $user_row->id));
          } elseif ( ((int)$_REQUEST['follow_type'] == 2) and ($user_row->id != 149842253) ) {
           $connection->post('friendships/destroy',array('user_id' => $user_row->id));
          }
         }

        //End of valid Twitter ID and screen name
        }

       //End of user row loop
       }

      } else {
       //Not a valid response
       $response_msg = mainFuncs::push_response(33);
      }
    }
   }
   break;

   case 'tab4':

    if ($_REQUEST['a'] == 'autodmupdate') {
     $tw_user = array('id' => $_REQUEST['twitter_id'],
                    'auto_dm_msg' => $_REQUEST['dm_content'],
                    'last_updated' => date("Y-m-d H:i:s")
 		 );

     $db->store_authed_user($tw_user);
     $response_msg = mainFuncs::push_response(21);
    }

   break;
   case 'tab5':

    if ( ($_REQUEST['a'] == 'stf1update') and ($_REQUEST['search_term']) ) {

     //Get twitter details and make connection
     $ap_creds = $db->get_ap_creds();
     $q1a = $db->get_user_data($_REQUEST['twitter_id']);
     $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
     $returned_users = array();

     //Search type
     if ($_REQUEST['search_type'] == 1) {

     /*
     Fixed in version 0.5 for Twitter API 1.1
     */

       //Get Results
       $content = $connection->get('search/tweets',array('q' => $_REQUEST['search_term'],'count' => TWITTER_TWEET_SEARCH_PP));


       if ($content->statuses) {
        foreach ($content->statuses as $user_row) {
         if (!$db->is_on_fr_list($_REQUEST['twitter_id'],$user_row->user->id_str)) {
          $returned_users[$user_row->user->id_str] = array("screen_name" => $user_row->user->screen_name,
       						               "profile_image_url" => $user_row->user->profile_image_url,
                                                               "tweet" => $user_row->text,
                                                               "full_name" => $user_row->user->name
                                                               );

         }
        }
       }

     } elseif ($_REQUEST['search_type'] == 2) {

      //Loop through results
      for ($i = 1; $i<=5; $i++) {
       $content = $connection->get('users/search',array('q' => $_REQUEST['search_term'],'count' => TWITTER_USER_SEARCH_PP,'page'=>$i));

       if ($content) {
        foreach ($content as $user_row) {
         if (!$db->is_on_fr_list($_REQUEST['twitter_id'],$user_row->id_str)) {
          $returned_users[$user_row->id_str] = array("screen_name" => $user_row->screen_name,
       					             "profile_image_url" => $user_row->profile_image_url,
                                                     "full_name" => $user_row->name,
                                                     "followers_count" => $user_row->followers_count,
                                                     "friends_count" => $user_row->friends_count,
                                                     "tweet" => $user_row->status->text
                                                     );
         }
        }
       }
      }


     }

     //Next post check
    } elseif ( ($_REQUEST['a'] == 'stf2update') and ($_REQUEST['follow_ids']) ) {

      //Loop through ids
      $ap_creds = $db->get_ap_creds();
      $q1a = $db->get_user_data($_REQUEST['twitter_id']);
      $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
      $good_flag = false;
      $bad_flag = false;

      foreach ($_REQUEST['follow_ids'] as $this_id) {
       $connection->post('friendships/create',array('user_id' => $this_id));
       if ($connection->http_code == 200) {
        $good_flag = true;
       } else {
        $bad_flag = true;
       }
      }

      //Show appropriate error message
      if (($good_flag) and (!$bad_flag)) {
       $response_msg = mainFuncs::push_response(26);
      } elseif (($good_flag) and ($bad_flag)) {
       $response_msg = mainFuncs::push_response(27);
      } elseif ((!$good_flag) and ($bad_flag)) {
       $response_msg = mainFuncs::push_response(28);
      }

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

 include('../content/' . TWANDO_LANG . '/ajax.follow_settings_inc.php');


//End of is logged in
}


include('../include_bottom.php');
?>
