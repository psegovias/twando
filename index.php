<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Set return page
if (strip_tags($_POST['return_url'] != "")) {
 $return_url = strip_tags($_POST['return_url']);
} else {
 $return_url = "";
}
$response_msg = "";

//Set login
if ($_POST['a'] == 'login2') {

 //Set
 $_POST['username_login'] = strip_tags(trim($_POST['username_login']));
 $_POST['password_login'] = strip_tags(trim($_POST['password_login']));

 //Check Credentials
 if ( ($_POST['username_login'] != LOGIN_USER) or ($_POST['password_login'] != LOGIN_PASSWORD) ) {
  $error_array['message'] = mainFuncs::push_response(5);
 } else {

  //Set cookies
  $_SESSION['twando_username'] = $_POST['username_login'];

  //Redirect
  header("Location: " . BASE_LINK_URL . $return_url);

 //End of do login sequence
 }
}

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "index";

 //Do save keys update
 if ($_POST['a'] == 'savekeys2') {

   //Set
   $_POST['consumer_key'] = strip_tags(trim($_POST['consumer_key']));
   $_POST['consumer_secret'] = strip_tags(trim($_POST['consumer_secret']));

   //Check if in DB
   $q1 = $db->query("SELECT * FROM " . DB_PREFIX . "ap_settings WHERE id='twando'");
   if ($db->num_rows($q1) == 0) {
    //Insert
    $db->query("INSERT INTO " . DB_PREFIX . "ap_settings (id,consumer_key,consumer_secret) VALUES
    		('twando','" . $db->prep($_POST['consumer_key']) . "','" . $db->prep($_POST['consumer_secret']) . "')");

   } else {
    //Update
    $db->query("UPDATE " . DB_PREFIX . "ap_settings SET consumer_key='" . $db->prep($_POST['consumer_key']) . "', consumer_secret='" . $db->prep($_POST['consumer_secret']) . "'
    		WHERE id='twando'");
   }

 //End of keys update
 }


}

//Check if we need to load twitter api box here
$ap_creds = @$db->get_ap_creds();
if ( ($ap_creds['consumer_key']) and ($ap_creds['consumer_secret']) ) {
 $header_info['on_load'] = "ajax_index_update('0','0');";
}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
