<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');

//Set return page
$return_url = "cron_instructions.php";

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "cron_instructions";

 if ($_GET['cron_reset'] == 'yes') {
  //Update the cron status
  $db->query("UPDATE "  . DB_PREFIX . "cron_status SET cron_state = '0', last_updated = '" . $db->prep(date("Y-m-d H:i:s")) . "' WHERE cron_name = '" . $db->prep($_GET['cron_type']) . "'");

  //To prevent the URL being refreshed accidentally by the user, redirect to page without query string
  Header("Location: " . $return_url);
 }

}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
