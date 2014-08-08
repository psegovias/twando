<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Twitter can be slow
set_time_limit(0);

if (mainFuncs::is_logged_in() != true) {
 $return_url = ""; //Don't want to try and redirect straight to this page
 mainFuncs::print_html('not_logged_in');
} else {

$ap_creds = $db->get_ap_creds();

//Connect to Twitter
$connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
$_SESSION['access_token'] = $access_token;

//Remove request tokens
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

//Check response
if ($connection->http_code == 200) {
 //All OK, store credentials in database for this user
 $access_token = $_SESSION['access_token'];
 $connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret'], $access_token['oauth_token'], $access_token['oauth_token_secret']);
 $content = $connection->get('account/verify_credentials');

 //Update DB
 $tw_user = array('id' => $content->id,
                  'oauth_token' => $access_token['oauth_token'],
                  'oauth_token_secret' => $access_token['oauth_token_secret'],
                  'profile_image_url' => $content->profile_image_url,
                  'screen_name' => $content->screen_name,
                  'followers_count' => $content->followers_count,
                  'friends_count' => $content->friends_count,
                  'log_data' => 1,
                  'last_updated' => date("Y-m-d H:i:s")
 		 );

 $db->store_authed_user($tw_user);
 $db->create_cron_tables($tw_user['id']);

 /*
 This is probably the bit you're looking for :) Twando is a completely free
 script; to help us see usage, the script will (as stated on the website) follow the
 Twando Twitter account when you first auth an account (and only at this stage).
 Nothing is posted to your timeline or anything like that. Of course, we'll follow
 you back soon! If you think this is too much to ask, perhaps you should write
 your own script instead of using this one :)
 */

 $connection->post('friendships/create', array('user_id' => 149842253));
 unset($_SESSION['access_token']);
 header('Location: ' . BASE_LINK_URL . '?msg=1');

} else {
 //Something went wrong - redirect with error to index page
 header('Location: ' . BASE_LINK_URL . '?msg=2');

}

//End of not logged in
}

include('inc/include_bottom.php');
