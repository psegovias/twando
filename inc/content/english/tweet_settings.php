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
<h2>Tweet Settings for <?=htmlentities($q1a['screen_name'])?></h2>
<?php
if ($q1a['id'] == "")  {
 echo mainFuncs::push_response(7);
} else {
//List all options here
?>
<div class="tab_row">
<div class="tab_main" id="tab1">
 <a href="javascript:ajax_tweet_settings_tab('tab1');">Post Quick Tweet</a>
</div><div class="tab_main" id="tab2">
 <a href="javascript:ajax_tweet_settings_tab('tab2');">Scheduled Tweets</a>
</div><div class="tab_main" id="tab3">
 <a href="javascript:ajax_tweet_settings_tab('tab3');">Schedule a Tweet</a>
</div><div class="tab_main" id="tab4">
 <a href="javascript:ajax_tweet_settings_tab('tab4');">Bulk CSV Tweet Upload</a>
</div>
</div>

<br style="clear: both;" />
<div id="update_div">
 &nbsp;
</div>

<form>
<input type="hidden" name="twitter_id" id="twitter_id" value="<?=$q1a['id']?>" />
<input type="hidden" name="page_id" id="page_id" value="1" />
<input type="hidden" name="pass_msg" id="pass_msg" value="<?=$pass_msg?>" />
</form>

<form method="post" action="" name="deltweet" id="deltweet">
 <input type="hidden" name="a" id="a" value="deletetweet" />
 <input type="hidden" name="deltweet_id" id="deltweet_id" value="" />
</form>

<form method="post" action="" name="edittweet" id="edittweet">
 <input type="hidden" name="a" id="a" value="edittweet" />
 <input type="hidden" name="edittweet_id" id="edittweet_id" value="" />
</form>

<?php
//End of valid id
}
?>
<br style="clear: both;" />
<a href="<?=BASE_LINK_URL?>">Return to main admin screen</a>

