<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

class mainFuncs {

 /*
 Check if logged in
 */

 public static function is_logged_in(){

  $logged_in = "";

  if (strlen($_SESSION['twando_username']) > 3) {
   $logged_in = true;
  } else {
   $logged_in = false;
  }

  return $logged_in;
 }

 /*
 Basic HTML page printing - no need to pass page titles, meta data
 and so on for this script
 */

 public static function print_html($content_id) {

  include('inc/common/header.php');
  include('inc/content/' . TWANDO_LANG . '/' . $content_id . '.php');
  include('inc/common/footer.php');
 }

 /*
 Error or success message output
 */

 public static function push_response($msg_id) {

  global $response_msgs;

 if ($response_msgs[$msg_id]['text']) {
    return '<span class="' . $response_msgs[$msg_id]['type'] . '">' . htmlentities($response_msgs[$msg_id]['text']) . '</span><br />' . "\n";
  }
 }


}
?>
