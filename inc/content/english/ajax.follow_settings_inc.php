<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

 //Show content here
 if ($_REQUEST['tab_id']) {
  //Show content based on tab ID
  switch ($_REQUEST['tab_id']) {
    case 'tab1':
?>
<h2>Auto Follow Settings</h2>
<?=$response_msg?>
<?php
if ($q1a['profile_image_url']) {
?>
<img src="<?=$q1a['profile_image_url']?>" style="float: right;" alt="<?=htmlentities($q1a['screen_name'])?>" title="<?=htmlentities($q1a['screen_name'])?>" />
<?php
}
?>
<form method="post" action="" name="settings_form" id="settings_form" onsubmit="ajax_follow_settings_update('tab1','settings_form'); return false;">
<input <?php if ($q1a['auto_follow']) { echo 'checked="checked"';} ?> type="checkbox" name="auto_follow" id="auto_follow" value="1" /> Auto follow back <select name="auto_follow_type" id="auto_follow_type" class="input_box_style" style="width: 120px;"><option value="1" <?php if ((int)$q1a['auto_follow'] < 2) { echo 'selected="selected"';} ?>>all followers</option><option value="2" <?php if ((int)$q1a['auto_follow'] == 2) { echo 'selected="selected"';} ?>>new followers</option></select> of this account?<br />
<input <?php if ($q1a['auto_unfollow']) { echo 'checked="checked"'; } ?> type="checkbox" name="auto_unfollow" id="auto_unfollow" value="1" /> Auto unfollow users who are not following you?<br />
<input <?php if ($q1a['auto_dm']) { echo 'checked="checked"'; } ?> type="checkbox" name="auto_dm" id="auto_dm" value="1" /> Auto DM users when you follow them back?<br />
<input type="hidden" name="a" value="saveop" />
</form>
<input type="submit" value="Save Options" class="submit_button_style" onclick="ajax_follow_settings_update('tab1','settings_form');" />
<br /><br />
When the auto follow cron job is first run, your follower and following lists will be populated. On the next run, following / unfollowing will commence based on the options set above.
<?php
      break;
    case 'tab2':
    case 'tab3':
    $tab_vars = array();
    if ($_REQUEST['tab_id'] == 'tab2') {
     $tab_vars['h2'] = 'Unfollow';
     $tab_vars['checkbox_type'] = 'Follow';
     $tab_vars['type'] = 1;
     $tab_vars['tab_id'] = 'tab2';
     $tab_vars['text1'] = 'Never unfollow these users, even if they don\'t follow this account';
     $tab_vars['text2'] = 'Add screen names to always follow list (one per line; max ' . TWITTER_API_USER_LOOKUP . ' at a time)';
    } elseif ($_REQUEST['tab_id'] == 'tab3') {
     $tab_vars['h2'] = 'Follow';
     $tab_vars['checkbox_type'] = 'Unfollow';
     $tab_vars['type'] = 2;
     $tab_vars['tab_id'] = 'tab3';
     $tab_vars['text1'] = 'Never follow back these users, even if they follow this account';
     $tab_vars['text2'] = 'Add screen names to never follow list (one per line; max ' . TWITTER_API_USER_LOOKUP . ' at a time)';
    }
?>
<h2><?=$tab_vars['h2']?> Exclusions</h2>
<?=$response_msg?>
<div class="follow_ex_block">
<form method="post" action="" name="user_list_form" id="user_list_form"  onsubmit="ajax_follow_settings_update('<?=$tab_vars['tab_id']?>','user_list_form'); return false;">
<?=$tab_vars['text1']?>:
<br />
<?php
$q2 = $db->query("SELECT * FROM " . DB_PREFIX . "follow_exclusions WHERE type='" . (int)$tab_vars['type'] . "' AND owner_id='" . $db->prep($q1a['id']) . "' ORDER BY screen_name ASC");
$row_count = $db->num_rows($q2);
if ($row_count > 4) {
?>
<div class="follow_scroll_block">
<?php
}
?>
<table class="data_table" style="width: 425px;">
<tr>
 <td class="heading">ID</td>
 <td class="heading">Screen Name</td>
 <td class="heading">Profile Image</td>
 <td class="heading"><a href="javascript:ajax_check_all();">Delete</a></td>
</tr>
<?php
$q2 = $db->query("SELECT * FROM " . DB_PREFIX . "follow_exclusions WHERE type='" . (int)$tab_vars['type'] . "' AND owner_id='" . $db->prep($q1a['id']) . "' ORDER BY screen_name ASC");
while ($q2a = $db->fetch_array($q2)) {
?>
<tr>
 <td><?=$q2a['twitter_id']?></td>
 <td><a href="https://twitter.com/<?=$q2a['screen_name']?>" target="_blank"><?=htmlentities($q2a['screen_name'])?></a></td>
 <td><?php if ($q2a['profile_image_url']) { ?><img src="<?=$q2a['profile_image_url']?>" /><?php } ?></td>
 <td><input type="checkbox" name="delete_list[]" class="dcheck" value="<?=$q2a['twitter_id']?>" /></td>
</tr>
<?php
}
if ($row_count == 0) {
?>
<tr>
 <td colspan="4" style="text-align: center;">No Twitter users stored</td>
</tr>
<?php
}
?>
</table>
<?php
if ($row_count > 4) {
?>
</div>
<?php
}
?>
<?php
if ($row_count > 0) {
?>
<input type="hidden" name="a" id="a" value="deleteuserids" />
<input type="hidden" name="follow_type" id="follow_type" value="<?=(int)$tab_vars['type']?>" />
<?php
}
//Fixed in 0.4 - Not showing this closing form tag stopped the next form tags being displayed in Firefox, Chrome etc
?>
</form>
<?php
if ($row_count > 0) {
?>
<input type="submit" value="Delete Selected" class="submit_button_style" onclick="ajax_follow_settings_update('<?=$tab_vars['tab_id']?>','user_list_form');" />
<?php
}
?>
</div>
<div class="follow_ex_block" style="float: right;">

 <form method="post" action="" name="new_user_form" id="new_user_form" onsubmit="ajax_follow_settings_update('<?=$tab_vars['tab_id']?>','new_user_form'); return false;">
<?=$tab_vars['text2']?>:
<br />
<textarea name="twitter_ids_list" id="twitter_ids_list" class="input_box_style" style="height: 249px; margin: 6px 0px 0px 0px;"></textarea><br />
<input type="checkbox" name="follow_now" id="follow_now" value="1" onclick="$('#just_follow').toggle();" /> <?=$tab_vars['checkbox_type']?> these users on Twitter now?<br />
<div id="just_follow" style="display: none;"><input type="checkbox" name="just_follow_now" id="just_follow_now" value="1" /> Only <?=$tab_vars['checkbox_type']?> these users on Twitter (don't add to exclusion list)?<br /></div>
<input type="hidden" name="a" id="a" value="addfollowids" />
<input type="hidden" name="follow_type" id="follow_type" value="<?=(int)$tab_vars['type']?>" />

</form>

<input type="submit" value="Add Twitter IDs" class="submit_button_style" onclick="ajax_follow_settings_update('<?=$tab_vars['tab_id']?>','new_user_form');" />
</div>
<br style="clear: both;" />
<?php
   break;
   case 'tab4':
?>
 <h2>Auto DM Message</h2>
 <?=$response_msg?>
 If you have enabled 'Auto DM' in the 'Auto Follow Settings' tab, the following will be sent as a DM when you auto follow a user who has followed you:
 <br />
 <form method="post" action="" name="autodm_form" id="autodm_form" onsubmit="ajax_follow_settings_update('tab4','autodm_form'); return false;">
<textarea name="dm_content" id="dm_content" class="input_box_style" style="height: 70px; width: 400px;" onkeyup="$('#count_box').val($('#dm_content').val().length);"><?=$q1a['auto_dm_msg']?></textarea><br />
Characters: <input type="text" name="count_box" id="count_box" size="3" value="<?=strlen($q1a['auto_dm_msg'])?>" class="input_box_style" style="width: 30px;"  />
</div>
<br style="clear: both;" />
<input type="hidden" name="a" value="autodmupdate" />
</form>
<input type="submit" value="Save Auto DM" class="submit_button_style" onclick="ajax_follow_settings_update('tab4','autodm_form');" />


<?php
   break;
   case 'tab5':
?>
 <h2>Search to Follow</h2>
 <?=$response_msg?>
 Twando can help you search for new users to follow below. Note that searching is rate-limited by Twitter. If you've set up the cron follow script (you really should have), the system will
 exclude users you were already following at the time of the last update. Please note that mass-following is quite a slow API process and the page may take several minutes to load after you select users to follow.
 <br /><br />
<form method="post" action="" name="stf1_form" id="stf1_form" onsubmit="ajax_follow_settings_update('tab5','stf1_form'); return false;">
<div class="cron_row">
 <div class="cron_left">Search type:</div>
 <div class="cron_right"><select name="search_type" id="search_type" class="input_box_style" style="width: 120px;">
 <option value="1" <?php if ((int)$_REQUEST['search_type'] <= 1) { echo 'selected="selected"'; } ?>>Tweet based</option>
 <option value="2" <?php if ((int)$_REQUEST['search_type'] == 2) { echo 'selected="selected"'; } ?>>User based</option>
</select></div>
</div>
<div class="cron_row">
  <div class="cron_left">Search term:</div>
  <div class="cron_right"><input type="text" name="search_term" id="search_term" class="input_box_style" value="<?=strip_tags($_REQUEST['search_term'])?>" /></div>
</div>
<br style="clear: both;" />
<input type="hidden" name="a" value="stf1update" />
</form>
<input type="submit" value="Search Now" class="submit_button_style" onclick="ajax_follow_settings_update('tab5','stf1_form');" />
<?php
if ( ($_REQUEST['a'] == 'stf1update') and ($_REQUEST['search_term']) ) {
?>
<h2>Search Results</h2>
<?php
 if (sizeof($returned_users) > 0) {
?>
(<a href="javascript:ajax_check_all();">Select all users</a>)
<br /><br />
<form method="post" action="" name="stf2_form" id="stf2_form" onsubmit="ajax_follow_settings_update('tab5','stf2_form'); return false;">
<?php
  foreach ($returned_users as $this_user_id => $this_user_data) {
?>
 <div style="float: left; width: 48px; border: 1px solid grey; text-align: center; margin: 0px 7px 3px 0px;">
  <a href="https://twitter.com/account/redirect_by_id?id=<?=$this_user_id?>" target="_blank">
  <img src="<?=$this_user_data['profile_image_url']?>" width="48" height="48" title="Screen Name: <?=$this_user_data['screen_name'] . " \n"?>Full Name: <?=htmlentities($this_user_data['full_name'])  . " \n"?><?php if (is_numeric($this_user_data['friends_count'])) {?>Following: <?=number_format($this_user_data['friends_count']) . " \n"?><?php } ?><?php if (is_numeric($this_user_data['followers_count'])) {?>Followers: <?=number_format($this_user_data['followers_count'])  . " \n"?><?php } ?><?php if ($this_user_data['tweet']) {?>Last Tweet: <?=htmlentities($this_user_data['tweet'])  . " \n"?><?php } ?>" />
  </a><br />
  <input type="checkbox" class="dcheck" name="follow_ids[]" value="<?=$this_user_id?>" />
 </div>
<?php
  }
?>
<br style="clear: both;" />
<input type="hidden" name="search_type" id="search_type" value="<?=(int)$_REQUEST['search_type']?>" />
<input type="hidden" name="search_term" id="search_term" value="<?=strip_tags($_REQUEST['search_term'])?>" />
<input type="hidden" name="a" value="stf2update" />
</form>
<input type="submit" value="Follow Selected Users" class="submit_button_style" onclick="ajax_follow_settings_update('tab5','stf2_form');" />
<?php
 } else {
  echo mainFuncs::push_response(25);
 }
}
   break;
  //End of switch
  }
 //End of if $_REQUEST['tab_id']
 }
 
?>

