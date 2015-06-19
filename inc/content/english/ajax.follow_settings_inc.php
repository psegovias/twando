<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/
if (empty($_REQUEST['search_term']))
{ $search_term = ""; } else { $search_term = $_REQUEST['search_term']; }
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
 <div class="cron_left">Language:</div>
 <div class="cron_right"><select name="search_lang" id="search_lang" class="input_box_style" style="width: 120px;">
<option value="all" <?php if ($_REQUEST['search_lang'] == 'all') { echo 'selected="selected"'; } ?>>All</option>
<option value="en" <?php if ($_REQUEST['search_lang'] == 'en') { echo 'selected="selected"'; } ?>>English</option>
<option value="es" <?php if ($_REQUEST['search_lang'] == 'es') { echo 'selected="selected"'; } ?>>Spanish</option>
<option value="ab" <?php if ($_REQUEST['search_lang'] == 'ab') { echo 'selected="selected"'; } ?>>аҧсуа бызшәа, аҧсшәа</option>
<option value="aa" <?php if ($_REQUEST['search_lang'] == 'aa') { echo 'selected="selected"'; } ?>>Afaraf</option>
<option value="af" <?php if ($_REQUEST['search_lang'] == 'af') { echo 'selected="selected"'; } ?>>Afrikaans</option>
<option value="ak" <?php if ($_REQUEST['search_lang'] == 'ak') { echo 'selected="selected"'; } ?>>Akan</option>
<option value="sq" <?php if ($_REQUEST['search_lang'] == 'sq') { echo 'selected="selected"'; } ?>>Shqip</option>
<option value="am" <?php if ($_REQUEST['search_lang'] == 'am') { echo 'selected="selected"'; } ?>>አማርኛ</option>
<option value="ar" <?php if ($_REQUEST['search_lang'] == 'ar') { echo 'selected="selected"'; } ?>>العربية</option>
<option value="an" <?php if ($_REQUEST['search_lang'] == 'an') { echo 'selected="selected"'; } ?>>aragonés</option>
<option value="hy" <?php if ($_REQUEST['search_lang'] == 'hy') { echo 'selected="selected"'; } ?>>Հայերեն</option>
<option value="as" <?php if ($_REQUEST['search_lang'] == 'as') { echo 'selected="selected"'; } ?>>অসমীয়া</option>
<option value="av" <?php if ($_REQUEST['search_lang'] == 'av') { echo 'selected="selected"'; } ?>>авар мацӀ, магӀарул мацӀ</option>
<option value="ae" <?php if ($_REQUEST['search_lang'] == 'ae') { echo 'selected="selected"'; } ?>>avesta</option>
<option value="ay" <?php if ($_REQUEST['search_lang'] == 'ay') { echo 'selected="selected"'; } ?>>aymar aru</option>
<option value="az" <?php if ($_REQUEST['search_lang'] == 'az') { echo 'selected="selected"'; } ?>>azərbaycan dili</option>
<option value="bm" <?php if ($_REQUEST['search_lang'] == 'bm') { echo 'selected="selected"'; } ?>>bamanankan</option>
<option value="ba" <?php if ($_REQUEST['search_lang'] == 'ba') { echo 'selected="selected"'; } ?>>башҡорт теле</option>
<option value="eu" <?php if ($_REQUEST['search_lang'] == 'eu') { echo 'selected="selected"'; } ?>>euskara, euskera</option>
<option value="be" <?php if ($_REQUEST['search_lang'] == 'be') { echo 'selected="selected"'; } ?>>беларуская мова</option>
<option value="bn" <?php if ($_REQUEST['search_lang'] == 'bn') { echo 'selected="selected"'; } ?>>বাংলা</option>
<option value="bh" <?php if ($_REQUEST['search_lang'] == 'bh') { echo 'selected="selected"'; } ?>>भोजपुरी</option>
<option value="bi" <?php if ($_REQUEST['search_lang'] == 'bi') { echo 'selected="selected"'; } ?>>Bislama</option>
<option value="bs" <?php if ($_REQUEST['search_lang'] == 'bs') { echo 'selected="selected"'; } ?>>bosanski jezik</option>
<option value="br" <?php if ($_REQUEST['search_lang'] == 'br') { echo 'selected="selected"'; } ?>>brezhoneg</option>
<option value="bg" <?php if ($_REQUEST['search_lang'] == 'bg') { echo 'selected="selected"'; } ?>>български език</option>
<option value="my" <?php if ($_REQUEST['search_lang'] == 'my') { echo 'selected="selected"'; } ?>>Burmese</option>
<option value="ca" <?php if ($_REQUEST['search_lang'] == 'ca') { echo 'selected="selected"'; } ?>>català</option>
<option value="ch" <?php if ($_REQUEST['search_lang'] == 'ch') { echo 'selected="selected"'; } ?>>Chamoru</option>
<option value="ce" <?php if ($_REQUEST['search_lang'] == 'ce') { echo 'selected="selected"'; } ?>>нохчийн мотт</option>
<option value="ny" <?php if ($_REQUEST['search_lang'] == 'ny') { echo 'selected="selected"'; } ?>>chiCheŵa, chinyanja</option>
<option value="zh" <?php if ($_REQUEST['search_lang'] == 'zh') { echo 'selected="selected"'; } ?>>中文 (Zhōngwén), 汉语, 漢語</option>
<option value="cv" <?php if ($_REQUEST['search_lang'] == 'cv') { echo 'selected="selected"'; } ?>>чӑваш чӗлхи</option>
<option value="kw" <?php if ($_REQUEST['search_lang'] == 'kw') { echo 'selected="selected"'; } ?>>Kernewek</option>
<option value="co" <?php if ($_REQUEST['search_lang'] == 'co') { echo 'selected="selected"'; } ?>>corsu, lingua corsa</option>
<option value="cr" <?php if ($_REQUEST['search_lang'] == 'cr') { echo 'selected="selected"'; } ?>>ᓀᐦᐃᔭᐍᐏᐣ</option>
<option value="hr" <?php if ($_REQUEST['search_lang'] == 'hr') { echo 'selected="selected"'; } ?>>hrvatski jezik</option>
<option value="cs" <?php if ($_REQUEST['search_lang'] == 'cs') { echo 'selected="selected"'; } ?>>čeština, český jazyk</option>
<option value="da" <?php if ($_REQUEST['search_lang'] == 'da') { echo 'selected="selected"'; } ?>>dansk</option>
<option value="dv" <?php if ($_REQUEST['search_lang'] == 'dv') { echo 'selected="selected"'; } ?>>ދިވެހި</option>
<option value="nl" <?php if ($_REQUEST['search_lang'] == 'nl') { echo 'selected="selected"'; } ?>>Nederlands, Vlaams</option>
<option value="dz" <?php if ($_REQUEST['search_lang'] == 'dz') { echo 'selected="selected"'; } ?>>རྫོང་ཁ</option>
<option value="eo" <?php if ($_REQUEST['search_lang'] == 'eo') { echo 'selected="selected"'; } ?>>Esperanto</option>
<option value="et" <?php if ($_REQUEST['search_lang'] == 'et') { echo 'selected="selected"'; } ?>>eesti, eesti keel</option>
<option value="ee" <?php if ($_REQUEST['search_lang'] == 'ee') { echo 'selected="selected"'; } ?>>Eʋegbe</option>
<option value="fo" <?php if ($_REQUEST['search_lang'] == 'fo') { echo 'selected="selected"'; } ?>>føroyskt</option>
<option value="fj" <?php if ($_REQUEST['search_lang'] == 'fj') { echo 'selected="selected"'; } ?>>vosa Vakaviti</option>
<option value="fi" <?php if ($_REQUEST['search_lang'] == 'fi') { echo 'selected="selected"'; } ?>>suomi, suomen kieli</option>
<option value="fr" <?php if ($_REQUEST['search_lang'] == 'fr') { echo 'selected="selected"'; } ?>>français, langue française</option>
<option value="ff" <?php if ($_REQUEST['search_lang'] == 'ff') { echo 'selected="selected"'; } ?>>Fulfulde, Pulaar, Pular</option>
<option value="gl" <?php if ($_REQUEST['search_lang'] == 'gl') { echo 'selected="selected"'; } ?>>galego</option>
<option value="ka" <?php if ($_REQUEST['search_lang'] == 'ka') { echo 'selected="selected"'; } ?>>ქართული</option>
<option value="de" <?php if ($_REQUEST['search_lang'] == 'de') { echo 'selected="selected"'; } ?>>Deutsch</option>
<option value="el" <?php if ($_REQUEST['search_lang'] == 'el') { echo 'selected="selected"'; } ?>>ελληνικά</option>
<option value="gn" <?php if ($_REQUEST['search_lang'] == 'gn') { echo 'selected="selected"'; } ?>>Avañe'ẽ</option>
<option value="gu" <?php if ($_REQUEST['search_lang'] == 'gu') { echo 'selected="selected"'; } ?>>ગુજરાતી</option>
<option value="ht" <?php if ($_REQUEST['search_lang'] == 'ht') { echo 'selected="selected"'; } ?>>Kreyòl ayisyen</option>
<option value="ha" <?php if ($_REQUEST['search_lang'] == 'ha') { echo 'selected="selected"'; } ?>>(Hausa) هَوُسَ</option>
<option value="he" <?php if ($_REQUEST['search_lang'] == 'he') { echo 'selected="selected"'; } ?>>עברית</option>
<option value="hz" <?php if ($_REQUEST['search_lang'] == 'hz') { echo 'selected="selected"'; } ?>>Otjiherero</option>
<option value="hi" <?php if ($_REQUEST['search_lang'] == 'hi') { echo 'selected="selected"'; } ?>>हिन्दी, हिंदी</option>
<option value="ho" <?php if ($_REQUEST['search_lang'] == 'ho') { echo 'selected="selected"'; } ?>>Hiri Motu</option>
<option value="hu" <?php if ($_REQUEST['search_lang'] == 'hu') { echo 'selected="selected"'; } ?>>magyar</option>
<option value="ia" <?php if ($_REQUEST['search_lang'] == 'ia') { echo 'selected="selected"'; } ?>>Interlingua</option>
<option value="id" <?php if ($_REQUEST['search_lang'] == 'id') { echo 'selected="selected"'; } ?>>Bahasa Indonesia</option>
<option value="ie" <?php if ($_REQUEST['search_lang'] == 'ie') { echo 'selected="selected"'; } ?>>Interlingue</option>
<option value="ga" <?php if ($_REQUEST['search_lang'] == 'ga') { echo 'selected="selected"'; } ?>>Gaeilge</option>
<option value="ig" <?php if ($_REQUEST['search_lang'] == 'ig') { echo 'selected="selected"'; } ?>>Asụsụ Igbo</option>
<option value="ik" <?php if ($_REQUEST['search_lang'] == 'ik') { echo 'selected="selected"'; } ?>>Iñupiaq, Iñupiatun</option>
<option value="io" <?php if ($_REQUEST['search_lang'] == 'io') { echo 'selected="selected"'; } ?>>Ido</option>
<option value="is" <?php if ($_REQUEST['search_lang'] == 'is') { echo 'selected="selected"'; } ?>>Íslenska</option>
<option value="it" <?php if ($_REQUEST['search_lang'] == 'it') { echo 'selected="selected"'; } ?>>italiano</option>
<option value="iu" <?php if ($_REQUEST['search_lang'] == 'iu') { echo 'selected="selected"'; } ?>>ᐃᓄᒃᑎᑐᑦ</option>
<option value="ja" <?php if ($_REQUEST['search_lang'] == 'ja') { echo 'selected="selected"'; } ?>>日本語 (にほんご)</option>
<option value="jv" <?php if ($_REQUEST['search_lang'] == 'jv') { echo 'selected="selected"'; } ?>>basa Jawa</option>
<option value="kl" <?php if ($_REQUEST['search_lang'] == 'kl') { echo 'selected="selected"'; } ?>>kalaallisut, kalaallit oqaasii</option>
<option value="kn" <?php if ($_REQUEST['search_lang'] == 'kn') { echo 'selected="selected"'; } ?>>ಕನ್ನಡ</option>
<option value="kr" <?php if ($_REQUEST['search_lang'] == 'kr') { echo 'selected="selected"'; } ?>>Kanuri</option>
<option value="ks" <?php if ($_REQUEST['search_lang'] == 'ks') { echo 'selected="selected"'; } ?>>कश्मीरी, كشميري‎</option>
<option value="kk" <?php if ($_REQUEST['search_lang'] == 'kk') { echo 'selected="selected"'; } ?>>қазақ тілі</option>
<option value="km" <?php if ($_REQUEST['search_lang'] == 'km') { echo 'selected="selected"'; } ?>>ខ្មែរ, ខេមរភាសា, ភាសាខ្មែរ</option>
<option value="ki" <?php if ($_REQUEST['search_lang'] == 'ki') { echo 'selected="selected"'; } ?>>Gĩkũyũ</option>
<option value="rw" <?php if ($_REQUEST['search_lang'] == 'rw') { echo 'selected="selected"'; } ?>>Ikinyarwanda</option>
<option value="ky" <?php if ($_REQUEST['search_lang'] == 'ky') { echo 'selected="selected"'; } ?>>Кыргызча, Кыргыз тили</option>
<option value="kv" <?php if ($_REQUEST['search_lang'] == 'kv') { echo 'selected="selected"'; } ?>>коми кыв</option>
<option value="kg" <?php if ($_REQUEST['search_lang'] == 'kg') { echo 'selected="selected"'; } ?>>Kikongo</option>
<option value="ko" <?php if ($_REQUEST['search_lang'] == 'ko') { echo 'selected="selected"'; } ?>>한국어, 조선어</option>
<option value="ku" <?php if ($_REQUEST['search_lang'] == 'ku') { echo 'selected="selected"'; } ?>>Kurdî, كوردی‎</option>
<option value="kj" <?php if ($_REQUEST['search_lang'] == 'kj') { echo 'selected="selected"'; } ?>>Kuanyama</option>
<option value="la" <?php if ($_REQUEST['search_lang'] == 'la') { echo 'selected="selected"'; } ?>>latine, lingua latina</option>
<option value="lb" <?php if ($_REQUEST['search_lang'] == 'lb') { echo 'selected="selected"'; } ?>>Lëtzebuergesch</option>
<option value="lg" <?php if ($_REQUEST['search_lang'] == 'lg') { echo 'selected="selected"'; } ?>>Luganda</option>
<option value="li" <?php if ($_REQUEST['search_lang'] == 'li') { echo 'selected="selected"'; } ?>>Limburgs</option>
<option value="ln" <?php if ($_REQUEST['search_lang'] == 'ln') { echo 'selected="selected"'; } ?>>Lingála</option>
<option value="lo" <?php if ($_REQUEST['search_lang'] == 'lo') { echo 'selected="selected"'; } ?>>ພາສາລາວ</option>
<option value="lt" <?php if ($_REQUEST['search_lang'] == 'lt') { echo 'selected="selected"'; } ?>>lietuvių kalba</option>
<option value="lu" <?php if ($_REQUEST['search_lang'] == 'lu') { echo 'selected="selected"'; } ?>>Tshiluba</option>
<option value="lv" <?php if ($_REQUEST['search_lang'] == 'lv') { echo 'selected="selected"'; } ?>>latviešu valoda</option>
<option value="gv" <?php if ($_REQUEST['search_lang'] == 'gv') { echo 'selected="selected"'; } ?>>Gaelg, Gailck</option>
<option value="mk" <?php if ($_REQUEST['search_lang'] == 'mk') { echo 'selected="selected"'; } ?>>македонски јазик</option>
<option value="mg" <?php if ($_REQUEST['search_lang'] == 'mg') { echo 'selected="selected"'; } ?>>fiteny malagasy</option>
<option value="ms" <?php if ($_REQUEST['search_lang'] == 'ms') { echo 'selected="selected"'; } ?>>bahasa Melayu, بهاس ملايو‎</option>
<option value="ml" <?php if ($_REQUEST['search_lang'] == 'ml') { echo 'selected="selected"'; } ?>>മലയാളം</option>
<option value="mt" <?php if ($_REQUEST['search_lang'] == 'mt') { echo 'selected="selected"'; } ?>>Malti</option>
<option value="mi" <?php if ($_REQUEST['search_lang'] == 'mi') { echo 'selected="selected"'; } ?>>te reo Māori</option>
<option value="mr" <?php if ($_REQUEST['search_lang'] == 'mr') { echo 'selected="selected"'; } ?>>मराठी</option>
<option value="mh" <?php if ($_REQUEST['search_lang'] == 'mh') { echo 'selected="selected"'; } ?>>Kajin M̧ajeļ</option>
<option value="mn" <?php if ($_REQUEST['search_lang'] == 'mn') { echo 'selected="selected"'; } ?>>монгол</option>
<option value="na" <?php if ($_REQUEST['search_lang'] == 'na') { echo 'selected="selected"'; } ?>>Ekakairũ Naoero</option>
<option value="nv" <?php if ($_REQUEST['search_lang'] == 'nv') { echo 'selected="selected"'; } ?>>Diné bizaad</option>
<option value="nd" <?php if ($_REQUEST['search_lang'] == 'nd') { echo 'selected="selected"'; } ?>>isiNdebele</option>
<option value="ne" <?php if ($_REQUEST['search_lang'] == 'ne') { echo 'selected="selected"'; } ?>>नेपाली</option>
<option value="ng" <?php if ($_REQUEST['search_lang'] == 'ng') { echo 'selected="selected"'; } ?>>Owambo</option>
<option value="nb" <?php if ($_REQUEST['search_lang'] == 'nb') { echo 'selected="selected"'; } ?>>Norsk bokmål</option>
<option value="nn" <?php if ($_REQUEST['search_lang'] == 'nn') { echo 'selected="selected"'; } ?>>Norsk nynorsk</option>
<option value="no" <?php if ($_REQUEST['search_lang'] == 'no') { echo 'selected="selected"'; } ?>>Norsk</option>
<option value="ii" <?php if ($_REQUEST['search_lang'] == 'ii') { echo 'selected="selected"'; } ?>>ꆈꌠ꒿ Nuosuhxop</option>
<option value="nr" <?php if ($_REQUEST['search_lang'] == 'nr') { echo 'selected="selected"'; } ?>>isiNdebele</option>
<option value="oc" <?php if ($_REQUEST['search_lang'] == 'oc') { echo 'selected="selected"'; } ?>>occitan, lenga d'òc</option>
<option value="oj" <?php if ($_REQUEST['search_lang'] == 'oj') { echo 'selected="selected"'; } ?>>ᐊᓂᔑᓈᐯᒧᐎᓐ</option>
<option value="cu" <?php if ($_REQUEST['search_lang'] == 'cu') { echo 'selected="selected"'; } ?>>ѩзыкъ словѣньскъ</option>
<option value="om" <?php if ($_REQUEST['search_lang'] == 'om') { echo 'selected="selected"'; } ?>>Afaan Oromoo</option>
<option value="or" <?php if ($_REQUEST['search_lang'] == 'or') { echo 'selected="selected"'; } ?>>ଓଡ଼ିଆ</option>
<option value="os" <?php if ($_REQUEST['search_lang'] == 'os') { echo 'selected="selected"'; } ?>>ирон æвзаг</option>
<option value="pa" <?php if ($_REQUEST['search_lang'] == 'pa') { echo 'selected="selected"'; } ?>>ਪੰਜਾਬੀ, پنجابی‎</option>
<option value="pi" <?php if ($_REQUEST['search_lang'] == 'pi') { echo 'selected="selected"'; } ?>>पाऴि</option>
<option value="fa" <?php if ($_REQUEST['search_lang'] == 'fa') { echo 'selected="selected"'; } ?>>فارسی</option>
<option value="pl" <?php if ($_REQUEST['search_lang'] == 'pl') { echo 'selected="selected"'; } ?>>język polski, polszczyzna</option>
<option value="ps" <?php if ($_REQUEST['search_lang'] == 'ps') { echo 'selected="selected"'; } ?>>پښتو</option>
<option value="pt" <?php if ($_REQUEST['search_lang'] == 'pt') { echo 'selected="selected"'; } ?>>português</option>
<option value="qu" <?php if ($_REQUEST['search_lang'] == 'qu') { echo 'selected="selected"'; } ?>>Runa Simi, Kichwa</option>
<option value="rm" <?php if ($_REQUEST['search_lang'] == 'rm') { echo 'selected="selected"'; } ?>>rumantsch grischun</option>
<option value="rn" <?php if ($_REQUEST['search_lang'] == 'rn') { echo 'selected="selected"'; } ?>>Ikirundi</option>
<option value="ro" <?php if ($_REQUEST['search_lang'] == 'ro') { echo 'selected="selected"'; } ?>>limba română</option>
<option value="ru" <?php if ($_REQUEST['search_lang'] == 'ru') { echo 'selected="selected"'; } ?>>Русский</option>
<option value="sa" <?php if ($_REQUEST['search_lang'] == 'sa') { echo 'selected="selected"'; } ?>>संस्कृतम्</option>
<option value="sc" <?php if ($_REQUEST['search_lang'] == 'sc') { echo 'selected="selected"'; } ?>>sardu</option>
<option value="sd" <?php if ($_REQUEST['search_lang'] == 'sd') { echo 'selected="selected"'; } ?>>सिन्धी, سنڌي، سندھی‎</option>
<option value="se" <?php if ($_REQUEST['search_lang'] == 'se') { echo 'selected="selected"'; } ?>>Davvisámegiella</option>
<option value="sm" <?php if ($_REQUEST['search_lang'] == 'sm') { echo 'selected="selected"'; } ?>>gagana fa'a Samoa</option>
<option value="sg" <?php if ($_REQUEST['search_lang'] == 'sg') { echo 'selected="selected"'; } ?>>yângâ tî sängö</option>
<option value="sr" <?php if ($_REQUEST['search_lang'] == 'sr') { echo 'selected="selected"'; } ?>>српски језик</option>
<option value="gd" <?php if ($_REQUEST['search_lang'] == 'gd') { echo 'selected="selected"'; } ?>>Gàidhlig</option>
<option value="sn" <?php if ($_REQUEST['search_lang'] == 'sn') { echo 'selected="selected"'; } ?>>chiShona</option>
<option value="si" <?php if ($_REQUEST['search_lang'] == 'si') { echo 'selected="selected"'; } ?>>සිංහල</option>
<option value="sk" <?php if ($_REQUEST['search_lang'] == 'sk') { echo 'selected="selected"'; } ?>>slovenčina, slovenský jazyk</option>
<option value="sl" <?php if ($_REQUEST['search_lang'] == 'sl') { echo 'selected="selected"'; } ?>>slovenski jezik, slovenščina</option>
<option value="so" <?php if ($_REQUEST['search_lang'] == 'so') { echo 'selected="selected"'; } ?>>Soomaaliga, af Soomaali</option>
<option value="st" <?php if ($_REQUEST['search_lang'] == 'st') { echo 'selected="selected"'; } ?>>Sesotho</option>
<option value="es" <?php if ($_REQUEST['search_lang'] == 'es') { echo 'selected="selected"'; } ?>>español</option>
<option value="su" <?php if ($_REQUEST['search_lang'] == 'su') { echo 'selected="selected"'; } ?>>Basa Sunda</option>
<option value="sw" <?php if ($_REQUEST['search_lang'] == 'sw') { echo 'selected="selected"'; } ?>>Kiswahili</option>
<option value="ss" <?php if ($_REQUEST['search_lang'] == 'ss') { echo 'selected="selected"'; } ?>>SiSwati</option>
<option value="sv" <?php if ($_REQUEST['search_lang'] == 'sv') { echo 'selected="selected"'; } ?>>svenska</option>
<option value="ta" <?php if ($_REQUEST['search_lang'] == 'ta') { echo 'selected="selected"'; } ?>>தமிழ்</option>
<option value="te" <?php if ($_REQUEST['search_lang'] == 'te') { echo 'selected="selected"'; } ?>>తెలుగు</option>
<option value="tg" <?php if ($_REQUEST['search_lang'] == 'tg') { echo 'selected="selected"'; } ?>>тоҷикӣ, toçikī, تاجیکی‎</option>
<option value="th" <?php if ($_REQUEST['search_lang'] == 'th') { echo 'selected="selected"'; } ?>>ไทย</option>
<option value="ti" <?php if ($_REQUEST['search_lang'] == 'ti') { echo 'selected="selected"'; } ?>>ትግርኛ</option>
<option value="bo" <?php if ($_REQUEST['search_lang'] == 'bo') { echo 'selected="selected"'; } ?>>བོད་ཡིག</option>
<option value="tk" <?php if ($_REQUEST['search_lang'] == 'tk') { echo 'selected="selected"'; } ?>>Türkmen, Түркмен</option>
<option value="tl" <?php if ($_REQUEST['search_lang'] == 'tl') { echo 'selected="selected"'; } ?>>Wikang Tagalog</option>
<option value="tn" <?php if ($_REQUEST['search_lang'] == 'tn') { echo 'selected="selected"'; } ?>>Setswana</option>
<option value="to" <?php if ($_REQUEST['search_lang'] == 'to') { echo 'selected="selected"'; } ?>>faka Tonga</option>
<option value="tr" <?php if ($_REQUEST['search_lang'] == 'tr') { echo 'selected="selected"'; } ?>>Türkçe</option>
<option value="ts" <?php if ($_REQUEST['search_lang'] == 'ts') { echo 'selected="selected"'; } ?>>Xitsonga</option>
<option value="tt" <?php if ($_REQUEST['search_lang'] == 'tt') { echo 'selected="selected"'; } ?>>татар теле, tatar tele</option>
<option value="tw" <?php if ($_REQUEST['search_lang'] == 'tw') { echo 'selected="selected"'; } ?>>Twi</option>
<option value="ty" <?php if ($_REQUEST['search_lang'] == 'ty') { echo 'selected="selected"'; } ?>>Reo Tahiti</option>
<option value="ug" <?php if ($_REQUEST['search_lang'] == 'ug') { echo 'selected="selected"'; } ?>>ئۇيغۇرچە‎, Uyghurche</option>
<option value="uk" <?php if ($_REQUEST['search_lang'] == 'uk') { echo 'selected="selected"'; } ?>>українська мова</option>
<option value="ur" <?php if ($_REQUEST['search_lang'] == 'ur') { echo 'selected="selected"'; } ?>>اردو</option>
<option value="uz" <?php if ($_REQUEST['search_lang'] == 'uz') { echo 'selected="selected"'; } ?>>Oʻzbek, Ўзбек, أۇزبېك‎</option>
<option value="ve" <?php if ($_REQUEST['search_lang'] == 've') { echo 'selected="selected"'; } ?>>Tshivenḓa</option>
<option value="vi" <?php if ($_REQUEST['search_lang'] == 'vi') { echo 'selected="selected"'; } ?>>Việt Nam</option>
<option value="vo" <?php if ($_REQUEST['search_lang'] == 'vo') { echo 'selected="selected"'; } ?>>Volapük</option>
<option value="wa" <?php if ($_REQUEST['search_lang'] == 'wa') { echo 'selected="selected"'; } ?>>walon</option>
<option value="cy" <?php if ($_REQUEST['search_lang'] == 'cy') { echo 'selected="selected"'; } ?>>Cymraeg</option>
<option value="wo" <?php if ($_REQUEST['search_lang'] == 'wo') { echo 'selected="selected"'; } ?>>Wollof</option>
<option value="fy" <?php if ($_REQUEST['search_lang'] == 'fy') { echo 'selected="selected"'; } ?>>Frysk</option>
<option value="xh" <?php if ($_REQUEST['search_lang'] == 'xh') { echo 'selected="selected"'; } ?>>isiXhosa</option>
<option value="yi" <?php if ($_REQUEST['search_lang'] == 'yi') { echo 'selected="selected"'; } ?>>ייִדיש</option>
<option value="yo" <?php if ($_REQUEST['search_lang'] == 'yo') { echo 'selected="selected"'; } ?>>Yorùbá</option>
<option value="za" <?php if ($_REQUEST['search_lang'] == 'za') { echo 'selected="selected"'; } ?>>Saɯ cueŋƅ, Saw cuengh</option>
<option value="zu" <?php if ($_REQUEST['search_lang'] == 'zu') { echo 'selected="selected"'; } ?>>isiZulu</option>
</select></div>
</div>
<div class="cron_row">
  <div class="cron_left">Search term:</div>
  <div class="cron_right"><input type="text" name="search_term" id="search_term" class="input_box_style" value="<?=strip_tags($search_term)?>" /></div>
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
  <a href="https://twitter.com/intent/user?user_id=<?=$this_user_id?>" target="_blank">
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
<input type="hidden" name="search_lang" id="search_lang" value="<?=strip_tags($_REQUEST['search_lang'])?>" />
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
