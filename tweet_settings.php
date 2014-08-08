<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

include('inc/include_top.php');
set_time_limit(0); //For large file uploads

//Set return page
$return_url = "tweet_settings.php?id=" . strip_tags($_GET['id']);;
$pass_msg = "";

//Check if logged in
if (mainFuncs::is_logged_in() != true) {
 $page_select = "not_logged_in";
} else {
 $page_select = "tweet_settings";
 $header_info['on_load'] = "ajax_tweet_settings_tab('tab1');";
 $header_info['js_scripts'] =  '<script type="text/javascript" src="inc/scripts/anytime.c.js"></script>' . "\n" . '<link rel="stylesheet" type="text/css" href="inc/scripts/anytime.c.css" />' . "\n";

 //Get data here
 $q1a = $db->get_user_data($_GET['id']);

 if ($_POST['a'] == 'csv_upload') {
  //Bulk CSV upload
  $header_info['on_load'] = "ajax_tweet_settings_tab('tab4');";

  if ($_FILES['csv_file']['name']) {

   //Not ideal, but saves reminding user to chmod a directory
   $handle = @fopen($_FILES['csv_file']['tmp_name'],'r');
   $valid_rows = 0;
   while (($data = @fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) == 2) {
     $valid_rows ++;
     $db->query("INSERT INTO " . DB_PREFIX . "scheduled_tweets (owner_id, tweet_content, time_to_post)
    		  VALUES ('" . $db->prep($q1a['id']) . "','" . $db->prep($data[1]) . "','" . $db->prep($data[0]) . "')");
    }
   }
   @fclose($handle);

   //Check valid rows
   if ($valid_rows == 0) {
    $pass_msg = 19;
   } else {
    $pass_msg = 20;
   }

  }

 }



}

mainFuncs::print_html($page_select);

include('inc/include_bottom.php');
?>
