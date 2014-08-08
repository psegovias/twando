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
<h2>Send a Quick Tweet</h2>
<?=$response_msg?>
<?php
if ($q1a['profile_image_url']) {
?>
<img src="<?=$q1a['profile_image_url']?>" style="float: right;" alt="<?=htmlentities($q1a['screen_name'])?>" title="<?=htmlentities($q1a['screen_name'])?>" />
<?php
}
?>
To send a quick tweet from this account, use the box below:
<br />
<form method="post" action="" name="quicktweet_form" id="quicktweet_form" onsubmit="ajax_tweet_settings_update('tab1','quicktweet_form'); return false;">
<textarea name="tweet_content" id="tweet_content" class="input_box_style" style="height: 70px; width: 400px;" onkeyup="$('#count_box').val($('#tweet_content').val().length);"></textarea><br />
Characters: <input type="text" name="count_box" id="count_box" size="3" value="0" class="input_box_style" style="width: 30px;"  />
</div>
<br style="clear: both;" />
<input type="hidden" name="a" value="quicktweet" />
</form>
<input type="submit" value="Post Tweet" class="submit_button_style" onclick="ajax_tweet_settings_update('tab1','quicktweet_form');" />
<?php
      break;
    case 'tab2':
?>
<h2>Scheduled Tweets</h2>
<?=$response_msg?>
Your currently scheduled tweets are shown below:
<table class="data_table">
 <tr>
  <td class="heading" width="190">Time to Post</td>
  <td class="heading">Tweet Content</td>
  <td class="heading" width="50">Edit</td>
  <td class="heading" width="20"><img src="inc/images/delete_icon.gif" width="14" height="15" /></td>
 </tr>
<?php
$q2_base = "SELECT SQL_CALC_FOUND_ROWS id, tweet_content, time_to_post FROM " . DB_PREFIX . "scheduled_tweets WHERE owner_id='" . $db->prep($q1a['id']) . "' ORDER BY time_to_post ASC ";

//Pagination include
$js_page_func = 'ajax_tweet_settings_set_page';
$tab_id = 'tab2';
include('../ajax/ajax.pagination_inc.php');

if ($total_items > 0) {

$row_count = $db->num_rows($q2);
while ($q2a = $db->fetch_array($q2)) {
 //Check so pagination doesn't break when deleting
 $page_switch = $page;
 if ($row_count == 1) {
  $page_switch = $page - 1;
 }
 if ($page_switch < 1) {
  $page_switch = 1;
 }
?>

<?php
if ( ($_REQUEST['a'] == 'edittweet') and ($_REQUEST['edittweet_id'] == $q2a['id']) ) {
?>
 <tr>
  <td>
  <form method="post" action="" name="edittweetsave" id="edittweetsave"  onsubmit="javascript:ajax_tweet_settings_update('tab2','edittweetsave'); return false;">
  <input type="hidden" name="a" id="a" value="edittweetsave" />
  <input type="hidden" name="edittweetsave_id" id="edittweetsave_id" value="<?=$q2a['id']?>" />
  <input type="text" name="time_to_post" id="time_to_post" class="input_box_style" style="width: 115px;" value="<?=substr($q2a['time_to_post'],0,(strlen($q2a['time_to_post']) - 3))?>" /> <button id="tweet_post_time_set"><img src="inc/images/calendar.png" alt="Click to set date" title="Click to set date" /></button>
  <div style="display: none;"><input type="hidden" name="tweet_content" id="tweet_content" value="<?=$q2a['tweet_content']?>" /></div>
  </form>
  <script type="text/javascript">
    $('#tweet_post_time_set').click(
      function(e) {
        $('#time_to_post').AnyTime_noPicker().AnyTime_picker({ format: "%Y-%m-%d %H:%i"}).focus();
        e.preventDefault();
      } );
  </script>

  </td>
  <td>
  <textarea name="tweet_content2" id="tweet_content2" class="input_box_style" style="height: 70px; width: 400px;" onkeyup="$('#count_box').val($('#tweet_content2').val().length); $('#tweet_content').val($('#tweet_content2').val());" onmouseout="$('#count_box').val($('#tweet_content2').val().length); $('#tweet_content').val($('#tweet_content2').val());" ><?=$q2a['tweet_content']?></textarea><br />
  Characters: <input type="text" name="count_box" id="count_box" size="3" value="<?=strlen($q2a['tweet_content'])?>" class="input_box_style" style="width: 30px;"  />
  </td>
  <td><a href="javascript:ajax_tweet_settings_update('tab2','edittweetsave');">Save</a></td>
  <td><a href="javascript:ajax_tweet_settings_del_tweet('<?=$q2a['id']?>','<?=$page_switch?>');"><img src="inc/images/delete_icon.gif" width="14" height="15" /></a></td>
 </tr>
 </form>
  <?php
  } else {
  ?>
 <tr>
  <td><?=date(TIMESTAMP_FORMAT,strtotime($q2a['time_to_post']))?></td>
  <td><?=htmlspecialchars($q2a['tweet_content'])?></td>
  <td><a href="javascript:ajax_tweet_settings_edit_tweet_load('<?=$q2a['id']?>');">Edit</a></td>
  <td><a href="javascript:ajax_tweet_settings_del_tweet('<?=$q2a['id']?>','<?=$page_switch?>');"><img src="inc/images/delete_icon.gif" width="14" height="15" /></a></td>
 </tr>
  <?php
  }
  ?>

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
 <td colspan="4" style="text-align: center">No tweets currently scheduled for this user</td>
</tr>
</table>
<?php
//End of 0 total items
}
?>
<br /><br />
*Tweets are posted based on your servers PHP time, which might not be your local time. The current PHP date and time is <?=date(TIMESTAMP_FORMAT)?>.


<?php
  break;
  case 'tab3':
?>
<h2>Schedule a Tweet</h2>
<?=$response_msg?>
<form method="post" action="" name="scheduletweet_form" id="scheduletweet_form"  onsubmit="ajax_tweet_settings_update('tab3','scheduletweet_form'); return false;">
Tweet content:<br />
<textarea name="tweet_content" id="tweet_content" class="input_box_style" style="height: 70px; width: 400px;" onkeyup="$('#count_box').val($('#tweet_content').val().length);"></textarea><br />
Characters: <input type="text" name="count_box" id="count_box" size="3" value="0" class="input_box_style" style="width: 30px;"  />
<br />
Time to post tweet:
<input type="text" name="time_to_post" id="time_to_post" class="input_box_style" style="width: 115px;" value="<?=date('Y-m-d H:i',time() + 3600)?>" /> <button id="tweet_post_time_set"><img src="inc/images/calendar.png" alt="Click to set date" title="Click to set date" /></button>
  <script type="text/javascript">
    $('#tweet_post_time_set').click(
      function(e) {
        $('#time_to_post').AnyTime_noPicker().AnyTime_picker({ format: "%Y-%m-%d %H:%i"}).focus();
        e.preventDefault();
      } );
  </script>
<br />
</div>
<br style="clear: both;" />
<input type="hidden" name="a" value="scheduletweet" />
</form>
<input type="submit" value="Schedule Tweet" class="submit_button_style" onclick="ajax_tweet_settings_update('tab3','scheduletweet_form');" />
<br /><br />
*Tweets are posted based on your servers PHP time, which might not be your local time. The current PHP date and time is <?=date(TIMESTAMP_FORMAT)?>.
<br />
<?php
   break;
   case 'tab4':
?>
 <h2>Bulk CSV Tweet Upload</h2>
<?php
 if ($_REQUEST['pass_msg']) {
  $response_msg = mainFuncs::push_response($_REQUEST['pass_msg'])  . '<br />';
?>
 <script type="text/javascript">$("#pass_msg").val('')</script>
<?php
 }
?>
 <?=$response_msg?>
 <form method="post" action="tweet_settings.php?id=<?=$q1a['id']?>" name="bulkcsvupload" id="bulkcsvupload" enctype="multipart/form-data">
 You can upload a CSV of as many tweets as you like below. The CSV should have 2 columns (no headers are required). Column one should have the post date and time in mySQL format (YYYY-MM-DD HH:MM). Column two
 should contain what you want to tweet (<a href="inc/csv/example.csv" target="_blank">example</a>).
 <br /><br />
 <input type="file" name="csv_file" id="csv_file" class="input_box_style" />
 <br />
 <input type="submit" value="Upload CSV" class="submit_button_style" />
 <input type="hidden" name="a" id="a" value="csv_upload" />
 </form>


<?php
   break;
  //End of switch
  }
 //End of if $_REQUEST['tab_id']
 }
?>

