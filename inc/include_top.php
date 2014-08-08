<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

/*
Config
*/

ob_start();
include('config.php');
ob_end_clean();

/*
Includes
*/

include('class/class.mysql.php');
include('class/class.mainfuncs.php');
include('class/twitteroauth.php');
include('content/' . TWANDO_LANG . '/lang.php');

/*
URL of intall. You can override this if you wish
with a static define in config.php
*/

if (!defined(BASE_LINK_URL)) {
 if ($_SERVER['HTTPS']) {$url_check = 'https://';} else {$url_check = 'http://';}
 $url_check .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
 $filename = array_pop(explode("/",$url_check));
 $url_check = str_replace($filename,"",$url_check);
 define('BASE_LINK_URL',$url_check);
}

/*
Internal defines - you shouldn't need to change these
*/

define('TWANDO_VERSION','0.6');
define('TWITTER_API_LIMIT',15);
define('TWITTER_API_LIST_FW',5000);
define('TWITTER_API_USER_LOOKUP',100);
define('TABLE_ROWS_PER_PAGE',10);
define('TWITTER_TWEET_SEARCH_PP',100);
define('TWITTER_USER_SEARCH_PP',20);

/*
Start
*/

$db = new mySqlCon();
session_start();

?>
