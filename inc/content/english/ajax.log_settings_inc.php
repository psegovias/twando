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
<h2>Log Settings</h2>
<?=$response_msg?>
<?php
if ($q1a['profile_image_url']) {
?>
<img src="<?=$q1a['profile_image_url']?>" style="float: right;" alt="<?=htmlentities($q1a['screen_name'])?>" title="<?=htmlentities($q1a['screen_name'])?>" />
<?php
}
?>
<form method="post" action="" name="settings_form" id="settings_form" onsubmit="ajax_log_settings_update('tab1','settings_form'); return false;">
<input <?php if ($q1a['log_data']) { echo 'checked="checked"'; } ?> type="checkbox" name="log_data" id="log_data" value="1" /> Log actions from cron jobs?<br />
<input type="hidden" name="a" value="saveop" />
</form>
<input type="submit" value="Save Options" class="submit_button_style" onclick="ajax_log_settings_update('tab1','settings_form');" />
<?php
      break;
    case 'tab2':
    case 'tab3':

    if ($_REQUEST['tab_id'] == 'tab2') {
     $tab_vars['h2'] = 'Follow';
     $tab_vars['type'] = 1;
     $tab_vars['tab_id'] = 'tab2';
   } elseif ($_REQUEST['tab_id'] == 'tab3') {
     $tab_vars['h2'] = 'Tweet';
     $tab_vars['type'] = 2;
     $tab_vars['tab_id'] = 'tab3';
    }

?>
<h2>View <?=$tab_vars['h2']?> Logs</h2>
<table class="data_table">
 <tr>
  <td class="heading">Log Content</td>
  <?php if ($tab_vars['type'] == 1) { ?>
  <td class="heading" width="445">Affected Users</td>
  <?php } ?>
  <td class="heading" width="130">Log Time</td>
 </tr>
<?php
$q2_base = "SELECT SQL_CALC_FOUND_ROWS id, log_text, affected_users, last_updated FROM " . DB_PREFIX . "cron_logs WHERE owner_id='" . $db->prep($q1a['id']) . "' AND type='" . (int)$tab_vars['type'] . "' ORDER BY last_updated DESC ";

//Pagination include
$js_page_func = 'ajax_log_settings_set_page';
$tab_id =  $tab_vars['tab_id'];
include('../ajax/ajax.pagination_inc.php');

if ($total_items > 0) {

$row_count = $db->num_rows($q2);
while ($q2a = $db->fetch_array($q2)) {
?>
 <tr>
  <td><?=htmlspecialchars($q2a['log_text'])?></td>
  <?php if ($tab_vars['type'] == 1) { ?>
  <td>
<?php
 $aff_users = unserialize($q2a['affected_users']);
 $count = 0;
 if (is_array($aff_users)) {
  foreach ($aff_users as $this_id) {
   if ($count <= 169) {
    //Limit to showing first 169 due to potential page size
    $db->print_au($this_id,array("Screen Name","Name","Following","Followers","Data Cached"));
   }
   $count ++;
  }
 }
?>
  </td>
  <?php } ?>
  <td><?=date(TIMESTAMP_FORMAT,strtotime($q2a['last_updated']))?></td>
 </tr>
<?php
}
?>
</table>
<div class="page_nav">
 <div class="left_side">
 <?=$total_items?> result<?php if ($total_items != 1) {echo 's';}?> found; showing page <?=$page?> of <?=$total_pages?>.
 </div>
 <div class="right_side">
<?php
if ($total_pages > 1) {
?>
  <div class="box"><?=$back_string?></div>
  <?=$page_list_string?>
  <div class="box"><?=$next_string?></div>
<?php
} else {
?>
 <div class="boxw">&nbsp;</div>
<?php
}
?>
 </div>
</div>
<?php
//If total items > 0
} else {
?>
<tr>
 <td colspan="3" style="text-align: center">No cron logs found</td>
</tr>
</table>
<?php
//End of 0 total items
}
?>
<?php
   break;
   case 'tab4':
?>
<h2>Purge Log History</h2>
<?=$response_msg?>
<form method="post" action="" name="log_purge_form" id="log_purge_form" onsubmit="ajax_log_settings_update('tab4','log_purge_form'); return false;">
Delete&nbsp;&nbsp;<select name="log_type" id="log_type" class="input_box_style" style="width: 90px;">
 <option value="0" selected="selected">all logs</option>
 <option value="1">follow logs</option>
 <option value="2">tweet logs</option>
</select>&nbsp;&nbsp;for this account&nbsp;&nbsp;<select name="log_time" id="log_time" class="input_box_style" style="width: 150px;">
 <option value="1" selected="selected">from the beginning</option>
 <option value="2">older than 30 days</option>
 <option value="3">older than 90 days</option>
</select>
<br />
<input type="checkbox" name="empty_cache" id="empty_cache" value="1" /> Empty user cache?<br />
<input type="hidden" name="a" value="deletelogs" />
</form>
<input type="submit" value="Purge Logs" class="submit_button_style" onclick="ajax_log_settings_update('tab4','log_purge_form');" />
<?php
   break;
  //End of switch
  }
 //End of if $_REQUEST['tab_id']
 }
?>

