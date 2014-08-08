<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

//English response list
$response_msgs = array();
$response_msgs[1] = array('text'=>'Account authorized sucessfully','type'=>'success');
$response_msgs[2] = array('text'=>'There was an error while trying to authorize that account','type'=>'error');
$response_msgs[3] = array('text'=>'Couldn\'t connect to Twitter. Please check your consumer key and consumer secret values','type'=>'error');
$response_msgs[4] = array('text'=>'Account removed','type'=>'success');
$response_msgs[5] = array('text'=>'Your username/password combination is incorrect. Please try again','type'=>'error');
$response_msgs[6] = array('text'=>'You must be signed in to access this page!','type'=>'error');
$response_msgs[7] = array('text'=>'Invalid Twitter ID specified. Unable to set options','type'=>'error');
$response_msgs[8] = array('text'=>'Settings saved','type'=>'success');
$response_msgs[9] = array('text'=>'User details (screen name, profile image, follower counts etc) refreshed','type'=>'success');
$response_msgs[10] = array('text'=>'Selected Twitter IDs deleted','type'=>'success');
$response_msgs[11] = array('text'=>'Selected Twitter users added to list','type'=>'success');
$response_msgs[12] = array('text'=>'Unable to connect to Twitter to refresh details','type'=>'error');
$response_msgs[13] = array('text'=>'Tweet posted sucessfully','type'=>'success');
$response_msgs[14] = array('text'=>'Unable to post tweet','type'=>'error');
$response_msgs[15] = array('text'=>'Could not connect to MySQL database. Please check your connection settings.','type'=>'error');
$response_msgs[16] = array('text'=>'Scheduled tweet deleted','type'=>'success');
$response_msgs[17] = array('text'=>'Scheduled tweet edited','type'=>'success');
$response_msgs[18] = array('text'=>'Tweet scheduled','type'=>'success');
$response_msgs[19] = array('text'=>'No CSV rows extracted successfully for scheduled tweets','type'=>'error');
$response_msgs[20] = array('text'=>'CSV upload successful. Tweets added to database','type'=>'success');
$response_msgs[21] = array('text'=>'Auto DM message updated','type'=>'success');
$response_msgs[22] = array('text'=>'Cron logs purged','type'=>'success');
$response_msgs[23] = array('text'=>'Invalid cron key','type'=>'error');
$response_msgs[24] = array('text'=>'Cron already running','type'=>'error');
$response_msgs[25] = array('text'=>'No users found for that search. You could already be following all results, or you might have exceeded your API limits.','type'=>'error');
$response_msgs[26] = array('text'=>'All selected users followed successfully','type'=>'success');
$response_msgs[27] = array('text'=>'Some selected users followed successfully, but the API rejected some follow requests','type'=>'success');
$response_msgs[28] = array('text'=>'All follow requests rejected by the API','type'=>'error');
$response_msgs[29] = array('text'=>'At least 2 authed Twitter accounts are required to perform multi account functions','type'=>'error');
$response_msgs[30] = array('text'=>'Follow/unfollow requests sent to API','type'=>'success');
$response_msgs[31] = array('text'=>'Tweet posts sent to API','type'=>'success');
$response_msgs[32] = array('text'=>'Cron run complete','type'=>'success');
$response_msgs[33] = array('text'=>'Twando was unable to look up those users. This could be a temporary issue with the Twitter API, or the users might not exist.','type'=>'error');

//English cron text
$cron_txts = array();
$cron_txts[1] = 'API error. All follow operations disabled for this pass.';
$cron_txts[2] = 'follower';
$cron_txts[3] = 'followers';
$cron_txts[4] = 'friend';
$cron_txts[5] = 'friends';
$cron_txts[6] = ' found on first pass.';
$cron_txts[7] = ' new users followed this account since last update.';
$cron_txts[8] = ' users unfollowed this account since last update.';
$cron_txts[9] = ' new users followed by this account since last update.';
$cron_txts[10] = ' users unfollowed by this account since last update.';
$cron_txts[11] = 'users';
$cron_txts[12] = 'user';
$cron_txts[13] = ' unfollowed automatically.';
$cron_txts[14] = ' followed back automatically.';
$cron_txts[15] = 'DM';
$cron_txts[16] = 'DMs';
$cron_txts[17] = ' automatically sent to new followers.';
$cron_txts[18] = 'Tweet "';
$cron_txts[19] = '" posted succesfully.';
$cron_txts[20] = '" failed to post.';

?>
