<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

if (!$content_id) {
exit;
}
global $q1a, $pass_msg;
?>
<h2>Log Settings for <?=htmlentities($q1a['screen_name'])?></h2>
<?php
if ($q1a['id'] == "")  {
 echo mainFuncs::push_response(7);
} else {
//List all options here
?>
<div class="tab_row">
<div class="tab_main" id="tab1">
 <a href="javascript:ajax_log_settings_tab('tab1');">Log Settings</a>
</div><div class="tab_main" id="tab2">
 <a href="javascript:ajax_log_settings_tab('tab2');">View Follow Logs</a>
</div><div class="tab_main" id="tab3">
 <a href="javascript:ajax_log_settings_tab('tab3');">View Tweet Logs</a>
</div><div class="tab_main" id="tab4">
 <a href="javascript:ajax_log_settings_tab('tab4');">Purge Log History</a>
</div>
</div>

<br style="clear: both;" />
<div id="update_div">
 &nbsp;
</div>

<form>
<input type="hidden" name="twitter_id" id="twitter_id" value="<?=$q1a['id']?>" />
<input type="hidden" name="page_id" id="page_id" value="1" />
</form>

<?php
//End of valid id
}
?>
<br style="clear: both;" />
<a href="<?=BASE_LINK_URL?>">Return to main admin screen</a>

