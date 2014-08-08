<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

/*
Database credentials
*/

define('DB_NAME','');
define('DB_USER','');
define('DB_PASSWORD','');
define('DB_HOST','localhost');
define('DB_PREFIX','tw_');

/*
Login credentials
*/

define('LOGIN_USER','admin');
define('LOGIN_PASSWORD','password');

/*
Cron job access key - set to something random (no spaces)
*/

define('CRON_KEY','abc123');

/*
Language - only English for now, maybe more options later
*/

define('TWANDO_LANG','english');

/*
Timestamp format to use with PHP's date() function
*/

define('TIMESTAMP_FORMAT','d/m/Y H:i'); //Set to 'm/d/Y H:i' for US format

/*
Define base URL of your install - with trailing slash! You can leave this
commented out and the script will try and work it out for you, but it's better
to specify this if possible
*/

//define('BASE_LINK_URL','http://www.yoursite.com/twando/');


?>
