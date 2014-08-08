/*
Twando.com Free PHP Twitter Application 
http://www.twando.com/
*/

//Basic defines
$.ajaxSetup ({cache: false});
var ajax_load = '<center><img src="inc/images/ajax-loader.gif" alt="Loading..." width="16" height="11" style="margin-top: 10px;" /></center>';

//Index page method for refresh and delete
function ajax_index_update(this_update_type,twitter_id) {
 $("#twitter_user_table").html(ajax_load).load('inc/ajax/ajax.index.php?update_type=' + this_update_type + '&id=' + twitter_id);
};

//Tab select content
function tab_highlight(tab_id) {
 $('.tab_main').css('background', '#fff');
 $('.tab_main').css('border-bottom', '1px solid #33ccff');
 $('#' + tab_id).css('background', 'none');
 $('#' + tab_id).css('border-bottom', '1px solid #9AE4E8');
}

//Content selector follow settings page
function ajax_follow_settings_tab(tab_id) {
 tab_highlight(tab_id);
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.follow_settings.php?update_type=show_tab&tab_id=' + tab_id + '&twitter_id=' + $('#twitter_id').val());
}

//Update data follow settings page
function ajax_follow_settings_update(tab_id,form_id) {
 var form_data = $("#" + form_id).serialize() + '&update_type=update_data&form_id=' + form_id + '&tab_id=' + tab_id + '&twitter_id=' + $('#twitter_id').val();
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.follow_settings.php',form_data);
}

//Content selector follow settings page
function ajax_tweet_settings_tab(tab_id) {
 tab_highlight(tab_id);
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.tweet_settings.php?update_type=show_tab&tab_id=' + tab_id + '&twitter_id=' + $('#twitter_id').val() +  '&pass_msg=' + $('#pass_msg').val());
}

//Update data tweet settings page
function ajax_tweet_settings_update(tab_id,form_id) {
 var form_data = $("#" + form_id).serialize() + '&update_type=update_data&form_id=' + form_id + '&tab_id=' + tab_id + '&page_id=' +  $('#page_id').val() + '&twitter_id=' + $('#twitter_id').val();
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.tweet_settings.php',form_data);
}

//Tweet settings page pagination
function ajax_tweet_settings_set_page(tab_id,set_page) {
 $("#page_id").val(set_page);
 ajax_tweet_settings_update(tab_id,'');
}

//Delete scheduled tweet
function ajax_tweet_settings_del_tweet(tweet_id,page_pass) {
 $("#page_id").val(page_pass);
 $("#deltweet_id").val(tweet_id);
 ajax_tweet_settings_update('tab2','deltweet');
}

//Edit scheduled tweet
function ajax_tweet_settings_edit_tweet_load(tweet_id) {
 $("#edittweet_id").val(tweet_id);
 ajax_tweet_settings_update('tab2','edittweet');
}

//Content selector follow settings page
function ajax_log_settings_tab(tab_id) {
 tab_highlight(tab_id);
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.log_settings.php?update_type=show_tab&tab_id=' + tab_id + '&twitter_id=' + $('#twitter_id').val());
}

//Update data log settings page
function ajax_log_settings_update(tab_id,form_id) {
 var form_data = $("#" + form_id).serialize() + '&update_type=update_data&form_id=' + form_id + '&tab_id=' + tab_id + '&page_id=' +  $('#page_id').val() + '&twitter_id=' + $('#twitter_id').val();
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.log_settings.php',form_data);
}

//Log page pagination
function ajax_log_settings_set_page(tab_id,set_page) {
 $("#page_id").val(set_page);
 ajax_log_settings_update(tab_id,'');
}

//Select All Checkboxes
function ajax_check_all() {
 $('.dcheck').prop('checked','checked');
}

//Multi account functions page
function ajax_multi_account_tab(tab_id) {
 tab_highlight(tab_id);
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.multi_account_functions.php?update_type=show_tab&tab_id=' + tab_id);
}

//Update data multi account functions page
function ajax_multi_account_update(tab_id,form_id) {
 var form_data = $("#" + form_id).serialize() + '&update_type=update_data&form_id=' + form_id + '&tab_id=' + tab_id + '&page_id=' +  $('#page_id').val() + '&twitter_id=' + $('#twitter_id').val();
 $("#update_div").html(ajax_load).load('inc/ajax/ajax.multi_account_functions.php',form_data);
}







