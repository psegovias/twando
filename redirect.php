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

//Create request connection
$connection = new TwitterOAuth($ap_creds['consumer_key'], $ap_creds['consumer_secret']);
$request_token = $connection->getRequestToken(BASE_LINK_URL . 'callback.php');
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
//Redirect based on response code
switch ($connection->http_code) {
  case 200:
    $url = $connection->getAuthorizeURL($token);
    header('Location: ' . $url);
    break;
  default:
    header('Location: ' . BASE_LINK_URL . '?msg=3');
    break;
}

}

include('inc/include_bottom.php');
