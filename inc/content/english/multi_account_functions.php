<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

if (!$content_id) {
exit;
}
global $q1a, $db
?>
<h2>Multi Account Functions</h2>
<?php
//Check we have more than 1 account authed, otherwise these functions
//can't really be used
$qcheck = $db->query("SELECT * FROM " . DB_PREFIX .  "authed_users WHERE 1");
if ($db->num_rows($qcheck) < 2)  {
 echo mainFuncs::push_response(29);
} else {
//List all options here
?>
<div class="tab_row">
<div class="tab_main" id="tab1">
 <a href="javascript:ajax_multi_account_tab('tab1');">Cross Follow Accounts</a>
</div><div class="tab_main" id="tab2">
 <a href="javascript:ajax_multi_account_tab('tab2');">All Follow / Unfollow</a>
</div><div class="tab_main" id="tab3">
 <a href="javascript:ajax_multi_account_tab('tab3');">Multi Tweet</a>
</div>
</div>

<br style="clear: both;" />
<div id="update_div">
 &nbsp;
</div>

<?php
//End of valid response
}
?>
<br style="clear: both;" />
<a href="<?=BASE_LINK_URL?>">Return to main admin screen</a>

