<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Set return page
$return_url = "follow_settings.php?id=" . strip_tags($_GET['id']);
$response_msg = "";

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "follow_settings";
 $header_info['on_load'] = "ajax_follow_settings_tab('tab1');";
 //Get data here
 $q1a = $db->get_user_data($_GET['id']);
}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
