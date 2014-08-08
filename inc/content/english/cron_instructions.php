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
<h2>Cron Job Instructions</h2>
In order to fully use the Twando system, there are two cron jobs you must set up. Usually you can set up these cron jobs very easily via your hosting control panel (or directly in SSH if you have access). However if for
any reason you don't have local cron job access, you can also call these files remotely.
<h2>Follow Cron Job</h2>
This cron is used to execute the auto follow/unfollow/DM commands. You should run it at a time when you are not using API requests (e.g. during the night). We recommend running the cron once per day, but you can run it more
frequently if you wish. Even if you don't have auto follow or unfollow enabled, you should still run this cron job as it will record who followed and unfollowed each account.
<br /><br />
<div class="cron_row">
 <div class="cron_left">Local Command:</div>
 <div class="cron_right"><input type="text" name="code1" id="code1" class="input_box_style" style="width: 800px;" value="php <?=getcwd()?>/cron_follow.php <?=CRON_KEY?>" /></div>
</div>
<div class="cron_row">
 <div class="cron_left">Remote URL:</div>
 <div class="cron_right"><input type="text" name="code2" id="code2" class="input_box_style" style="width: 800px;" value="<?=BASE_LINK_URL?>cron_follow.php?cron_key=<?=CRON_KEY?>" /></div>
</div>
<br style="clear: both;" />
<h2>Tweet Cron Job</h2>
This cron is used to post your scheduled tweets. When the cron file is executed, tweets scheduled for that time or before will be posted to Twitter, so how often you run this cron really depends how often you need to post scheduled tweets. Calling the cron
every five minutes is probably reasonable. If you never schedule any tweets you don't need to set up this cron.
<br /><br />
<div class="cron_row">
 <div class="cron_left">Local Command:</div>
 <div class="cron_right"><input type="text" name="code3" id="code3" class="input_box_style" style="width: 800px;" value="php <?=getcwd()?>/cron_tweet.php <?=CRON_KEY?>" /></div>
</div>
<div class="cron_row">
 <div class="cron_left">Remote URL:</div>
 <div class="cron_right"><input type="text" name="code4" id="code4" class="input_box_style" style="width: 800px;" value="<?=BASE_LINK_URL?>cron_tweet.php?cron_key=<?=CRON_KEY?>" /></div>
</div>
<br style="clear: both;" />
<h2>Cron Status</h2>
It's important you don't stop the cron jobs while they are running as logs will then be lost for that pass and other unexpected issues can occur. If you do for some unavoidable reason stop a cron job while it is processing, the system will think the cron is still running
and you will not be able to run the cron again (you will receive a "cron already running" error). You can reset the cron running status below if this happens.
<br /><br />
<?php
$cron_types = array('follow','tweet');
foreach ($cron_types as $this_cron_type) {
 //Get data
 $q2  = $db->query("SELECT cron_state,last_updated FROM "  . DB_PREFIX . "cron_status WHERE cron_name = '" . $this_cron_type . "'");
 $q2a = $db->fetch_array($q2);

 //Set Date
 $output_date = "";
 if ($q2a['last_updated'] == '0000-00-00 00:00:00') {
  $output_date = "Cron has never been run";
 } else {
  $ouput_date = 'Cron state logged on ' . date(TIMESTAMP_FORMAT,strtotime($q2a['last_updated']));
 }

 //Output status
 echo '<div class="cron_row">' . "\n";
 echo '<div class="cron_left" style="margin: 0px;">' . ucwords($this_cron_type) . ' cron job state:</div>';
 echo '<div class="cron_right">';
 if ($q2a['cron_state'] == 0) {echo 'Not running';}
 elseif ($q2a['cron_state'] == 1) {echo '<a href="cron_instructions.php?cron_reset=yes&cron_type=' . $this_cron_type . '" onclick="javascript:if(confirm(\'Are you sure you want to update the status of this cron job? This can cause multiple issues if the cron is still running.\')) return (true); else return (false)">Running</a>';}
 echo '&nbsp;&nbsp;&nbsp;<i>(' .  $ouput_date .  ')</i>';
 echo '</div>' . "\n";
 echo '</div>' . "\n";

}
?>
<br style="clear: both;" />
<br />
<a href="<?=BASE_LINK_URL?>">Return to main admin screen</a>
