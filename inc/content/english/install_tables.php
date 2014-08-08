<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

if (!$content_id) {
exit;
}
global $db;
?>
<h2>Install MySQL Tables</h2>
Checking tables....<br />
<?php
$tables_array = array("ap_settings","authed_users","cron_logs","cron_status","follow_exclusions","scheduled_tweets","user_cache");
$table_error = false;
foreach ($tables_array as $this_table) {
 //Check if table is there
 if ($db->check_table_exists(DB_PREFIX . $this_table)) {
  echo '<span class="success">Table ' . DB_PREFIX . $this_table .  ' created.</span><br />' . "\n";
 } else {
  echo '<span class="error">Table ' . DB_PREFIX . $this_table .  ' could not be created.</span><br />' . "\n";
  $table_error = true;
 }
}
if ($table_error == true) {
 echo '<br /><span class="error">One or more tables could not be created. Please check your MySQL settings and try again.</span><br />' . "\n";
}
?>
<h2>Config Check</h2>
Checking config file for basic issues....<br />
<?php
$config_error = false;
//Check default values
if ( (LOGIN_USER == 'admin') and (LOGIN_PASSWORD == 'password') ) {
 echo '<span class="error">Warning: You have not changed the default username and password in config.php.</span><br />' . "\n";
 $config_error = true;
}
if (CRON_KEY == 'abc123') {
 echo '<span class="error">Warning: You have not changed the default cron key value in config.php.</span><br />' . "\n";
 $config_error = true;
}
if (strlen(DB_PREFIX) > 10) {
 echo '<span class="error">Warning: Your database prefix setting in config.php is unusually long. This could cause issues.</span><br />' . "\n";
 $config_error = true;
}
if ($config_error == false) {
 echo '<span class="success">No basic issues found in your config.php file.</span><br />' . "\n";
}
?>
<br style="clear: both;" />
<a href="<?=BASE_LINK_URL?>">Return to main admin screen</a>
