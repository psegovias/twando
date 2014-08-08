<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Set return page
$return_url = "multi_account_functions.php";

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "multi_account_functions";
 $header_info['on_load'] = "ajax_multi_account_tab('tab1');";
}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
