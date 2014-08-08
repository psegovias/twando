<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/
?>
<?=$response_msg?>
<?php
 if ($response_msg) {
 //If we already have a message from $_GET['msg'] in the main index.php include,
 //then we want to hide that here.
?>
<script type="text/javascript">$('#index_resp_msg').hide();</script>
<?php
 }

?>
<table class="data_table">
 <tr>
  <td class="heading">ID</td>
  <td class="heading">Screen Name</td>
  <td class="heading">Profile Image</td>
  <td class="heading">Following</td>
  <td class="heading">Followers</td>
  <td class="heading" colspan="3" style="text-align: center;">Settings &amp; Options</td>
  <td class="heading"><img src="inc/images/refresh_icon.gif" width="16" height="16" /></td>
  <td class="heading"><img src="inc/images/delete_icon.gif" width="14" height="15" /></td>
 </tr>
<?php
$row_count = 0;
$q1 = $db->query("SELECT * FROM " . DB_PREFIX . "authed_users WHERE 1 ORDER BY screen_name ASC");
while ($q1a = $db->fetch_array($q1)) {
?>
 <tr>
  <td><?=$q1a['id']?></td>
  <td><a href="https://twitter.com/<?=$q1a['screen_name']?>" target="_blank"><?=htmlentities($q1a['screen_name'])?></a></td>
  <td><?php if ($q1a['profile_image_url']) { ?><img src="<?=$q1a['profile_image_url']?>" /><?php } ?></td>
  <td><?=$q1a['friends_count']?></td>
  <td><?=$q1a['followers_count']?></td>
  <td><a href="follow_settings.php?id=<?=$q1a['id']?>">Follows</a></td>
  <td><a href="tweet_settings.php?id=<?=$q1a['id']?>">Tweets</a></td>
  <td><a href="log_settings.php?id=<?=$q1a['id']?>">Logs</a></td>
  <td><a href="javascript:ajax_index_update('refresh','<?=$q1a['id']?>');"><img src="inc/images/refresh_icon.gif" width="16" height="16" alt="Click to refresh user details for this Twitter id" title="Click to refresh user details for this Twitter id" /></a></td>
  <td><a href="javascript:ajax_index_update('delete','<?=$q1a['id']?>');" onclick="javascript:if(confirm('Are you sure you want to remove this account? All your Twando options and settings for this Twitter account will be lost.')) return (true); else return (false)"><img src="inc/images/delete_icon.gif" width="14" height="15" alt="Click to delete this account from your Twando application" title="Click to delete this account from your Twando application" /></a></td>
 </tr>
<?php
 $row_count ++;
}
if ($row_count == 0) {
?>
<tr>
 <td colspan="11" style="text-align: center">You haven't yet authorized any Twitter accounts</td>
</tr>
<?php
}
?>
</table>

