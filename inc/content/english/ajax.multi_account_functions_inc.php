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
<h2>Cross Follow Accounts</h2>
<?=$response_msg?>
<form method="post" action="" name="crossfollow_form" id="crossfollow_form" onsubmit="ajax_multi_account_update('tab1','crossfollow_form'); return false;">
<select name="cross_op" id="cross_op" class="input_box_style" style="width: 95px;">
 <option value="1" selected="selected">Follow</option>
 <option value="2">Unfollow</option>
</select>&nbsp;&nbsp;all your accounts from all your other accounts?<br />
<input type="hidden" name="a" value="cross_go" />
</form>
<input type="submit" value="Cross Follow Accounts" class="submit_button_style" onclick="ajax_multi_account_update('tab1','crossfollow_form');" />
<?php
      break;
    case 'tab2':
?>
<h2>All Follow / Unfollow</h2>
<?=$response_msg?>
<form method="post" action="" name="allfollow_form" id="allfollow_form" onsubmit="ajax_multi_account_update('tab2','allfollow_form'); return false;">
<select name="cross_op" id="cross_op" class="input_box_style" style="width: 95px;">
 <option value="1" selected="selected">Follow</option>
 <option value="2">Unfollow</option>
</select>&nbsp;&nbsp;all the following&nbsp;&nbsp;<select name="cross_type" id="cross_type" class="input_box_style" style="width: 120px;">
 <option value="1" selected="selected">screen names</option>
 <option value="2">Twitter ids</option>
</select>&nbsp;&nbsp;from all your Twitter accounts (enter one screen name / Twitter id per line):<br />
<textarea name="twitter_ids_list" id="twitter_ids_list" class="input_box_style" style="height: 249px; margin: 6px 0px 0px 0px;"></textarea><br />
<input type="hidden" name="a" value="allfollow_go" />
</form>
<input type="submit" value="All Follow / Unfollow" class="submit_button_style" onclick="ajax_multi_account_update('tab2','allfollow_form');" />
<?php
   break;
   case 'tab3':
?>
<h2>Multi Tweet</h2>
<?=$response_msg?>
Post the following tweet from all your Twitter accounts at once:<br />
<form method="post" action="" name="quicktweet_form" id="quicktweet_form" onsubmit="ajax_multi_account_update('tab3','quicktweet_form'); return false;">
<textarea name="tweet_content" id="tweet_content" class="input_box_style" style="height: 70px; width: 400px;" onkeyup="$('#count_box').val($('#tweet_content').val().length);"></textarea><br />
Characters: <input type="text" name="count_box" id="count_box" size="3" value="0" class="input_box_style" style="width: 30px;"  />
</div>
<br style="clear: both;" />
<input type="hidden" name="a" value="quicktweet" />
</form>
<input type="submit" value="Post Tweet" class="submit_button_style" onclick="ajax_multi_account_update('tab3','quicktweet_form');" />
<?php
   break;
  //End of switch
  }
 //End of if $_REQUEST['tab_id']
 }
?>

