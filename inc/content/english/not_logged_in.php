<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

if (!$content_id) {
exit;
}
global $error_array, $return_url;
?>
<h2>Login</h2>
<?php
if ($error_array['message'] != "") {
 echo $error_array['message'] . '<br />';
} else {
 echo mainFuncs::push_response(6) . '<br />';
}
?>
<form method="post" action="<?=BASE_LINK_URL?>">
Username:<br />
<input type="text" name="username_login" size="20" class="input_box_style" value="<?=$_POST['username_login']?>" />
<br />
Password:<br />
<input type="password" name="password_login" size="20" class="input_box_style" value="<?=$_POST['password_login']?>" />
<br />
<input type="submit" value="Sign In!" name="login" id="login" class="submit_button_style" />
<input type="hidden" name="a" value="login2" />
<input type="hidden" name="return_url" value="<?=$return_url?>" />
</form>

