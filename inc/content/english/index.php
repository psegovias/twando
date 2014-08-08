<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

if (!$content_id) {
exit;
}
global $db, $ap_creds;

if ((int)$_GET['msg']>0) {
 $response_msg = mainFuncs::push_response((int)$_GET['msg']);
}
?>
<?php
if ( (!$ap_creds['consumer_key']) or (!$ap_creds['consumer_secret']) ) {
?>
<h2>Register Your Application</h2>
Before you can start using Twando, you must first register your application with Twitter.
<ol>
 <li>First, <a href="install_tables.php">click here to install the MySQL tables</a> if you have not done so already.</li>
 <li>Next, visit <a href="https://dev.twitter.com/apps/new" target="_blank">https://dev.twitter.com/apps/new</a>;  you will need to sign in to your Twitter account to register an app.</li>
 <li>Enter the values as demonstrated in <a href="inc/images/reg_ap.jpg" target="_blank">this picture</a>; your application URL is <b><?=BASE_LINK_URL?></b>; your callback URL is <b><?=BASE_LINK_URL . 'callback.php'?></b>.</li>
 <li>You will then be given a consumer key and consumer secret (<a href="inc/images/reg_ap2.jpg" target="_blank">example</a>). Enter these in the boxes below to complete the setup of your Twitter app.</li>
 <li>You must also make sure your application is set with <b>read/write access</b> on the settings page; <a href="inc/images/reg_ap3.jpg" target="_blank">click here to view</a>.</li>
</ol>
<form method="post" action="<?=BASE_LINK_URL?>">
Consumer Key:<br />
<input type="text" name="consumer_key" size="20" class="input_box_style" value="<?=$_POST['consumer_key']?>" style="width: 350px;" />
<br />
Consumer Secret:<br />
<input type="text" name="consumer_secret" size="20" class="input_box_style" value="<?=$_POST['consumer_secret']?>" style="width: 350px;"  />
<br />
<input type="submit" value="Save Values!" name="login" id="login" class="submit_button_style" />
<input type="hidden" name="a" value="savekeys2" />
</form>
<?php
} else {
?>
<h2>Application Details</h2>
Your application has been registered with a consumer key and a consumer secret. If you would like to update
these credentials, please <a href="javascript:void(0);" onclick="$('#cred_update_div').toggle();">click here</a>.
<div id="cred_update_div" style="display: none;">
<form method="post" action="<?=BASE_LINK_URL?>">
Consumer Key:<br />
<input type="text" name="consumer_key" size="20" class="input_box_style" value="<?=$ap_creds['consumer_key']?>" style="width: 350px;" />
<br />
Consumer Secret:<br />
<input type="text" name="consumer_secret" size="20" class="input_box_style" value="<?=$ap_creds['consumer_secret']?>" style="width: 350px;"  />
<br />
<input type="submit" value="Save Values!" name="login" id="login" class="submit_button_style" />
<input type="hidden" name="a" value="savekeys2" />
</form>
</div>
<h2>Authorized Twitter Accounts</h2>
<?php
 if ($response_msg) {
  echo '<div id="index_resp_msg">' . $response_msg . '</div>';
 }
?>
<div id="twitter_user_table">
 &nbsp;
</div>
To authorize another account, make sure you are either signed out of all accounts or signed into the account you want to authorize on <a href="https://twitter.com/" target="_blank">Twitter</a> before clicking the button below.
<br />
<a href="redirect.php"><img src="inc/images/twitter_sign_in.jpg" style="margin: 5px 0px 0px 0px" alt="Sign in with Twitter" width="384" height="63" /></a>
<h2>Further Options</h2>
<a href="multi_account_functions.php">Multi account functions</a><br />  
<a href="cron_instructions.php">Cron job instructions</a><br />
<?php
//End of application registered
}
?>
