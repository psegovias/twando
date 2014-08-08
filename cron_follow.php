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
 if ($cron->get_cron_state('follow') == 1) {
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
 $cron->set_cron_state('follow',1);

 //Get credentials
 $ap_creds = $db->get_ap_creds();

 //Loop through all accounts
 $q1 = $db->query("SELECT * FROM " . DB_PREFIX . "authed_users ORDER BY (followers_count + friends_count) ASC");
 while ($q1a = $db->fetch_array($q1)) {

  //Defines
  $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $q1a['oauth_token'], $q1a['oauth_token_secret']);
  $cron->set_user_id($q1a['id']);
  $cron->set_first_pass_done($q1a['fr_fw_fp']);
  $cron->set_log($q1a['log_data']);
  $cron->set_throttle_time(0,'fr');
  $cron->set_throttle_time(0,'fw');
  $cron->set_fr_fw_issue(0);

  //Refresh details
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

   /*
   API Version 1.1 has really killed the limits here; from 350 per hour to 15 per
   15 minutes. Also Follower and Friend lists are now separately rate limited; code
   adjusted below to set individual throttle rates. Thanks Twitter, real helpful (not).

   On the plus side, these limits are now not used up elsewhere in the application
   so you can tweet away while this cron is running.

   Rate limit checking on every request is still very slow and most sensible
   users would never need it, hence just setting a throttle value once per account.
   */

   //Work out if to throttle
   $ops_array = array('fw' => 'followers_count','fr' => 'friends_count');
   $rate_con = $cron->get_remaining_hits();
   $this_limit = 0;

   foreach ($ops_array as $this_op => $this_op_var) {

    if ((int)$rate_con[$this_op . '_reset'] == 0) {
     //Either bang on the reset or we couldn't get a connection
     $this_limit = TWITTER_API_LIMIT;
    } elseif ($rate_con[$this_op . '_remaining'] >= $rate_con[$this_op . '_limit']) {
     //This should never happen
     $this_limit = $rate_con[$this_op . '_limit'];
    } elseif ((int)$rate_con[$this_op . '_remaining'] < 3) {
     //Really don't want to attempt with so few requests remaining. Sleep until reset
     if ((int)$rate_con[$this_op . '_reset'] > 0) {
      sleep($rate_con[$this_op . '_reset']);
     }
     $this_limit = $rate_con[$this_op . '_limit'];
    } else {
     $this_limit = $rate_con[$this_op . '_remaining'];
    }

    //Work out throttle
    if ($content->$this_op_var > ($this_limit * TWITTER_API_LIST_FW)) {
     //Work out worst case throttle time
     if ($this_limit != 0) {
      $throt_time = ((int)(900 / $this_limit) + 1);
     } else {
      $throt_time = 1;
     }
     $cron->set_throttle_time($throt_time,$this_op);
    }

   //End of foreach op type
   }

   //Set table flags
   $cron->clear_table_flags();
   $c1 = $cron->store_fw_fr_list('fw');
   $c2 = $cron->store_fw_fr_list('fr');

   if ($cron->get_fr_fw_issue() == 1) {
    $cron->store_cron_log(1,$cron_txts[1],'');
    $cron->set_first_pass_done(666);
   }

  } else {
   //Can't connect to this account so set flag to skip stages below
    $cron->set_first_pass_done(666);
  }

  //Log number of followers on first pass
  if ($cron->get_first_pass_done() == 0) {

    //Correct text - don't just add s to help multi language support later
    if ($c1 == 1) {$f_txt1 = $cron_txts[2];} else {$f_txt1 = $cron_txts[3];}
    if ($c2 == 1) {$f_txt2 = $cron_txts[4];} else {$f_txt2 = $cron_txts[5];}

    $cron->store_cron_log(1,$c1 . ' ' . $f_txt1 . $cron_txts[6],'');
    $cron->store_cron_log(1,$c2 . ' ' . $f_txt2 . $cron_txts[6],'');

    //Update user to indicate first pass done
    $cron->set_first_pass_done_db();

  } elseif ( ($cron->get_first_pass_done() == 1)  and ($cron->get_fr_fw_issue() == 0) ) {

   /*
   Decided in the end that if there is an API error, not doing anything at all is probably
   safest. Disabling auto-follow alone is fine, but for a large account an API connection
   failure for the friends and followers list could mean a lot of entries being added to
   the user cache.
   */

   //Get array of new users
   $fw_fr_types = array(
    		       'fw_new' => $cron_txts[7],
   		       'fw_gone' =>  $cron_txts[8],
                       'fr_new' =>  $cron_txts[9],
                       'fr_gone' =>  $cron_txts[10]
                       );
   $data_array = array();
   $data_array = $cron->get_id_changes();

   //Loop through and log data
   foreach ($fw_fr_types as $key => $text) {
    if ((sizeof($data_array[$key])) > 0) {
     //Log row
     if ((sizeof($data_array[$key])) == 1) {$text = str_replace($cron_txts[11],$cron_txts[12],$text);}
     $cron->store_cron_log(1,sizeof($data_array[$key]) .  $text,$data_array[$key],1);

     //Store users for lookup later
     foreach ($data_array[$key] as $this_twitter_id) {
      $tw_user_cache = array('twitter_id' => $this_twitter_id);
      $db->store_cached_user($tw_user_cache);
     }
    }
   }

   //Update flags to indicate cross matches
   $db->query("UPDATE " . DB_PREFIX . "fw_" . $q1a['id'] . " fw,  " . DB_PREFIX . "fr_" . $q1a['id'] . " fr SET fw.otp = 1, fr.otp = 1 WHERE fw.twitter_id = fr.twitter_id AND fw.stp = 1 AND fr.stp = 1");

   //Unfollow people who aren't following
   if ( ((int)$q1a['auto_unfollow'] == 1) and ($cron->get_fr_fw_issue() == 0) ) {

    //Get array of ids not to unfollow
    $never_unfollow = array();
    $never_unfollow = $cron->get_id_exclusions(1);

    //Loop through IDs we're following, but that aren't in followers table
    $unfollow_now = array();
    $qheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "fr_" . $q1a['id'] . " WHERE otp = 0 AND stp = 1");
    while ($qchecka = $db->fetch_array($qheck)) {
     if (!in_array($qchecka['twitter_id'],$never_unfollow)) {
      $unfollow_now[] = (string)$qchecka['twitter_id'];
     }
    }

    //If we have people to unfollow, do so now
    if ((sizeof($unfollow_now)) > 0) {
     foreach ($unfollow_now as $this_id) {

      //Execute unfollow - not rate limited
      $content = $connection->post('friendships/destroy', array('user_id' => $this_id));


      if ($connection->http_code == 200) {

       //Make use of the fact friendship create/destroy returns said user
       $tw_user_cache = array(
                  'twitter_id' => $content->id,
                  'profile_image_url' => $content->profile_image_url,
                  'screen_name' => $content->screen_name,
                  'actual_name' => $content->name,
                  'followers_count' => $content->followers_count,
                  'friends_count' => $content->friends_count,
                  'last_updated' => date("Y-m-d H:i:s")
 	);
       $db->store_cached_user($tw_user_cache);

       //Delete from table; no point in logging x people unfollowed on next pass
       //if logging it here anyway
       $db->query("DELETE FROM " . DB_PREFIX . "fr_" . $q1a['id'] . " WHERE otp = 0 AND twitter_id = '" . $db->prep($this_id) . "'");

      } else {
       $key = "";
       $key = array_search($this_id,$unfollow_now);
       unset($unfollow_now[$key]);
      }

     }

     //Log now
     $db->query("OPTIMIZE TABLE " . DB_PREFIX . "fr_" . $q1a['id']);

     if ((sizeof($unfollow_now)) > 0) {
      if ((sizeof($unfollow_now)) == 1) {$u_txt = $cron_txts[12];} else {$u_txt = $cron_txts[11];}
      $cron->store_cron_log(1,sizeof($unfollow_now) . ' ' . $u_txt . $cron_txts[13],$unfollow_now);
     }

    }


   //End of auto unfollow
   }

   //Follow back users
   if ( ((int)$q1a['auto_follow'] > 0) and ($cron->get_fr_fw_issue() == 0) ) {

     //Get array of ids not to follow
    $never_follow = array();
    $never_follow = $cron->get_id_exclusions(2);

    //Loop through IDs who are following us, but that aren't in friends table
    $follow_now = array();
    if ((int)$q1a['auto_follow'] == 1 ){
     //Any followers we're not following yet
     $qheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "fw_" . $q1a['id'] . " WHERE otp = 0 AND stp = 1");
    } elseif ((int)$q1a['auto_follow'] == 2) {
     //Just new followers
     $qheck = $db->query("SELECT twitter_id FROM " . DB_PREFIX . "fw_" . $q1a['id'] . " WHERE otp = 0 AND stp = 1 AND ntp = 1");
    }

    while ($qchecka = $db->fetch_array($qheck)) {
     if (!in_array($qchecka['twitter_id'],$never_follow)) {
      $follow_now[] = (string)$qchecka['twitter_id'];
     }
    }

    //If we have people to follow, do so now
    if ((sizeof($follow_now)) > 0) {
     $dm_count = 0;
     foreach ($follow_now as $this_id) {

      //Execute follow - not rate limited
      $content = $connection->post('friendships/create', array('user_id' => $this_id));

      /*
      OK, if it's a protected account, don't bother doing anything apart from sending the initial
      follow request. $content->protected sometimes returns 1 not true as listed in the API guide.
      If already followed, should return 403 but check error message just in case 200 is returned
      */

      $protected_acc = 0;
      if ( ($content->protected) or (preg_match("/already requested to follow/i",$content->error)) ) {
       $protected_acc = 1;
      }

      if ( ($connection->http_code == 200) and ($protected_acc == 0) ) {

       //Make use of the fact friendship create/destroy returns said user
       $tw_user_cache = array(
                  'twitter_id' => $content->id,
                  'profile_image_url' => $content->profile_image_url,
                  'screen_name' => $content->screen_name,
                  'actual_name' => $content->name,
                  'followers_count' => $content->followers_count,
                  'friends_count' => $content->friends_count,
                  'last_updated' => date("Y-m-d H:i:s")
 	);
       $db->store_cached_user($tw_user_cache);

       //If Auto DM, send it here
       if ( ((int)$q1a['auto_dm'] == 1) and ($q1a['auto_dm_msg'] != "") ) {
        //Send DM here - not rate limited
        $connection->post('direct_messages/new', array('user_id' => $this_id, 'text' => $q1a['auto_dm_msg']));
        if ($connection->http_code == 200) {
         $dm_count ++;
        }
       }

       //Insert into friends now that we're following, rather than appearing in
       //logs on next pass.
       $db->query("INSERT INTO " . DB_PREFIX . "fr_" . $q1a['id'] . " (twitter_id,stp,otp) VALUES ('" . $db->prep($this_id) . "',1,1)");

      } else {
       $key = "";
       $key = array_search($this_id,$follow_now);
       unset($follow_now[$key]);
      }

     }

     //Log now
     $db->query("OPTIMIZE TABLE " . DB_PREFIX . "fw_" . $q1a['id']);

     if ((sizeof($follow_now)) > 0) {
      if ((sizeof($follow_now)) == 1) {$u_txt = $cron_txts[12];} else {$u_txt = $cron_txts[11];}
      $cron->store_cron_log(1,sizeof($follow_now) . ' ' . $u_txt . $cron_txts[14],$follow_now);
     }

     if ($dm_count > 0) {
      if ($dm_count == 1) {$d_txt = $cron_txts[15];} else {$d_txt = $cron_txts[16];}
       $cron->store_cron_log(1,$dm_count . ' ' . $d_txt . $cron_txts[17],'');
      }
     }

    //End of auto follow back
    }

    //Now logging is complete, delete any users not seen to prevent them being
    //logged on next run
    $cron->delete_unseen_ids();

    //Try and lookup cached user ids we've stored

    //Check remaining API requests
    $rate_con = $cron->get_remaining_hits();
    $req_total = $rate_con['ul_remaining'];

    for ($i=1; $i<=2; $i++) {

     //Get users
     $uncached_users = array();
     $uncached_users = $cron->get_uncached_users($i);
     $user_total = sizeof($uncached_users);

     //Loop and build
     if ( ($req_total > 0) and ($user_total > 0) ) {
      $uncached_users_split = array();
      $uncached_users_split = array_chunk($uncached_users,TWITTER_API_USER_LOOKUP,true);

      foreach ($uncached_users_split as $this_user_array) {
       $pass_ids = "";
       foreach ($uncached_users as $this_id) {
        $pass_ids .= $this_id . ',';
       }
       $pass_ids = substr($pass_ids,0,-1);

       //Run lookup now
       $content = "";
       $content = $connection->post('users/lookup', array('user_id' => $pass_ids));
       if ($content) {
        foreach ($content as $user_row) {

         //Cache user
         $tw_user_cache = array(
                  'twitter_id' => $user_row->id,
                  'profile_image_url' => $user_row->profile_image_url,
                  'screen_name' => $user_row->screen_name,
                  'actual_name' => $user_row->name,
                  'followers_count' => $user_row->followers_count,
                  'friends_count' => $user_row->friends_count,
                  'last_updated' => date("Y-m-d H:i:s")
 		 );

         //Save user
         $db->store_cached_user($tw_user_cache);
        }
       }

       $req_total --;
       if ($req_total <= 0) {break;}
      }
     }

   //End of $i 1/2 loop
   }

   //Next, check again in case any Twitter IDs no longer exist and are still in
   //the DB cache
   $rate_con = $cron->get_remaining_hits();
   $req_total = $rate_con['us_remaining'];

   //Get users
   $uncached_users = array();
   $uncached_users = $cron->get_uncached_users(1);
   $user_total = sizeof($uncached_users);

   //Loop and build
   if ( ($req_total > 0) and ($user_total > 0) ) {

     foreach ($uncached_users as $this_id) {
      //Get user
      $content = $connection->get('users/show', array('user_id' => $this_id));

      //If not a 200 code (i.e. 404) delete row from table
      if ($connection->http_code != 200) {
       $db->query("DELETE FROM " . DB_PREFIX . "user_cache WHERE twitter_id='" . $db->prep($this_id) . "'");
      }

      $req_total --;
      if ($req_total <= 0) {break;}
     }

    //Optimise table
    $db->query("OPTIMIZE TABLE " . DB_PREFIX . "user_cache");

   }


  //End of first pass check
  }

 //End of db loop
 }

 //Set cron status
 $cron->set_cron_state('follow',0);

 echo mainFuncs::push_response(32);

//End of run cron
}

include('inc/include_bottom.php');
?>
