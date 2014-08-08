twando
======

Monitor your followers, auto follow and unfollow, auto DM new followers, schedule tweets, search &amp; mass follow new users and much, much more.


Requirements
======

PHP 5.2+
MySQL 5+
cURL
OpenSSL
Cron Jobs (Remote cron job services are supported also)


Installation
======

Installing Twando is bascially a 3 minute job, but there are some more advanced configuration options which are explained in detail below. 

3 Minute Install
Download the latest version of Twando.
Unzip the zip file and rename inc/config-sample.php to inc/config.php.
Update the options in config.php with your required settings.
Upload the entire contents of the folder to a directory on your server, e.g. http://www.yoursite.com/twando/.
Visit http://www.yoursite.com/twando/install_tables.php in your browser.
That's pretty much it! You can now set up your Twitter application and auth accounts by following the instructions at http://www.yoursite.com/twando/.
Upgrading From a Previous Version

Upload the entire contents of the download zip to where you previously installed Twando on your server.
Delete the inc/config-sample.php file from your server.
That's it; sorry if you wanted more!
Detailed Configuration Options

In inc/config.php, there are several options you can configure before installing Twando:
DB_NAME
The name of the MySQL database you will use with Twando
DB_USER
The username of the MySQL user you will use with Twando. This user must have full privileges on the database specified above.
DB_PASSWORD
The password for the MySQL username specified above.
DB_HOST
The host address of your MySQL database. This will usually be "localhost".
DB_PREFIX
All created MySQL tables will be prefixed with this value. This gives you the option to run multiple Twando installs from a single database if you wish. You shouldn't set this to anything longer than 10 characters. Changing this after you install will break the script.
LOGIN_USER
The username you will use to log in to your Twando install.
LOGIN_PASSWORD
The password you will use to log in to your Twando install. Try to use a password that's long and hard to guess.
CRON_KEY
This should be a hard to guess string of characters. This is checked when the cron job files are called; since Twando supports remote http calls to your cron jobs, having this key prevents unauthorised running of your cron jobs.
TWANDO_LANG
Twando has been built to support multiple languages in future; currently this should be left as "english".
TIMESTAMP_FORMAT
The format the time and date should be displayed in at various parts of the script, using the parameters from PHP's date() function.
BASE_LINK_URL
The URL of your install, e.g. http://www.yoursite.com/twando/. If you don't set this, the script will try and work out what it is for you but it can't handle things like port numbers in your URL and other fancy stuff like that. If you set this incorrectly, the script won't work.
Random Password Values

You can use the values below in your config.php value. These are randomly generated; refresh the page for new values: 

define('LOGIN_USER','vY1iQ9N9Dp');
define('LOGIN_PASSWORD','Onh2i6qQaSQO99X');

define('CRON_KEY','N04FI9g1fSRRGUsj3S0LzmPHI');



Manual

This manual assumes you have already installed Twando. The below combined with the text help found within your actual Twando installation covers pretty much everything you'll ever need to know. 

Contents
Registering Your Application
Authorizing a Twitter Account
Setting Up Cron Jobs
Follow / Unfollow Settings
Tweet Settings
Log Settings
Multi Account Functions
Registering Your Application

Once installed, the first step you must complete is to register your Twando application with Twitter. The homepage of your install will guide you through this process in full, complete with screenshots of the required steps. It's really very easy to do and once complete you will be able to enter your Consumer Key and Consumer Secret in the text boxes provided. You can edit these values at any time from the homepage of your install if required. Top tip: Only read and write access is required in your Twitter application settings to use Twando (this access level allows the sending of DMs also). 

Authorizing a Twitter Account

You can authorize as many Twitter accounts as you like with your application; simply click the large "Sign in with Twitter" button. Top tip: If you are signed into a Twitter account that has already been authorized, Twitter will simply re-auth your application for that account (it won't give you the option to sign out of Twitter first). Therefore, when authorizing a new account, you should always ensure you are either signed out of all Twitter accounts on the Twitter website, or signed into the account you wish to authorize. 

Setting Up Cron Jobs

After you have authorized your Twitter accounts, you should set up the cron jobs as soon as possible so this is taken care of. There are two cron jobs that need to be set up; full instructions on how to set these up can be found by clicking the "Cron job instructions" link towards the bottom of the homepage of your install. The cron_follow.php script logs who has followed and unfollowed your accounts, as well as taking care of the auto follow/unfollow and auto DM functionality. The script is designed to throttle requests for accounts with a large number of friends and followers. With the Twitter API 1.0, processing around 1.7 million friends and followers an hour was no problem. Twitter API 1.1 has reduced the limits significantly; if you have less than about 250,000 friends or followers, you should be comfortably able to run this cron job once per hour, otherwise it's recommended to run it once per day. 

The cron_tweet.php script powers the scheduled tweets functionality. Any tweets posted for a time equal to or less than the current time will be posted when the cron job is run, so how often you run this cron job depends on how many tweets you have to post and how close to the time you specify you need to post them. Normally running this cron job every five minutes would be reasonable. It's always preferable to run cron jobs locally from your own hosting account, but if for some reason your host doesn't support this, you can also call the cron jobs using a remote cron job service (just search on Google if you need one; there are loads of them about). 

It is not recommended to run the cron scripts directly via a browser as they can take several minutes to complete; for large accounts they could take in excess of one hour so make sure you set up cron jobs to run these scripts. 

Follow / Unfollow Settings

To access the follow / unfollow settings for an authorized Twitter account, simply click the "Follows" link in the account table on the homepage of your install. There are then several tabbed options available which are detailed below. 

Auto Follow Settings

There are several checkbox options under this tab:
Auto follow back all followers / new followers of this account
When ticked, Twando will automatically follow back your followers for this account when the follow cron job is run. Selecting "all followers" from the drop down means that all followers of this account will be followed back. Selecting "new followers" means that only new followers will be followed back. This can be very useful if you have a positive difference between your followers and who you are following and want to maintain this by only following back new followers.
Auto unfollow users who are not following you
If ticked, Twando will check who you are following against who is following you and automatically unfollow anyone who's not following you when the follow cron job is run.
Auto DM users when you follow them back
If you want to send an Auto DM to your followers (when you automatically follow them back), tick this box. The DM will only be sent if you have set one in the "Auto DM Message" tab (detailed below). Of course, many people find this super annoying, but the option is there if you want it.
Unfollow Exclusions

Twando allows you to specify unfollow exclusions; these come into play if you are using the "Auto unfollow users who are not following you" option detailed above. Any Twitter accounts you specify here will not be unfollowed automatically; this is really useful for example if you run Twando on your main personal Twitter account, but there are certain people you follow that you don't want to automatically unfollow. You can add as many exclusions as you require and then delete them later if you wish. When adding an exclusion, there is a checkbox titled "Follow these users on Twitter now?" which as the name suggests will follow the users you submit on Twitter. You can therefore use this screen to mass follow a list of Twitter users; there is even an additional checkbox which appears so you can mass follow on Twitter without needing to add the users to your exclusion list. 

Follow Exclusions

This works in exactly the same way as "Unfollow Exclusions" except users specifed here will not be followed back when they follow you and you have enabled "Auto follow back". 

Auto DM Message

If you have enabled "Auto DM users when you follow them back" then you can specify the message you will automatically DM to new followers here. To limit the annoyingness of this feature for your followers, the DM is sent when you follow back (not when they follow you), so at least the follower then has the option to reply to you if they wish. 

Search to Follow

Twando allows you to search for new users to follow and mass follow these users with two clicks. Top tip: Mass following too quickly is a very easy way to get your Twitter account banned.
Tweet Based Search
This method searches for users who have tweeted about a particular topic; for example searching for "Eden Hazard" would display users who posted about this.
User Based Search
This method is slightly different in that it searches user data (screen name, full name, description etc); searching here for "Eden Hazard" would bring up his official account as well as the various parody accounts using his name and so on.
Once the search is complete, tick the checkbox underneath the users you want to follow (there is a "Select all users" link above the results to select them all) and click the "Follow Selected Users" button to follow the selected users. Top tip: When following new users, you should untick the "Auto unfollow users who are not following you" from the "Auto Follow Settings" tab; a possible approach might be to disable auto unfollowing, follow some users and then re-enable auto unfollow after a week or so. 

Tweet Settings

To access the tweet settings for an authorized Twitter account, simply click the "Tweets" link in the account table on the homepage of your install. There are then several tabbed options available which are detailed below. 

Post Quick Tweet

Although Twando is certainly not intended as a replacement for your Twitter client of choice, or even the Twitter web interface, the ability to simply post a quick tweet from your account is included. This is also useful for checking your account is working as expected. 

Scheduled Tweets

This screen lists all the tweets currently scheduled for the selected account. You can edit any scheduled tweet by clicking the "Edit" link; click the bin (trashcan) icon to delete a scheduled tweet. 

Schedule a Tweet

Here you can schedule a tweet to be posted from the selected Twitter account. The time and date must be specified in MySQL format (YYYY-MM-DD HH:MM) but this is easily set by clicking the calendar icon which will bring up a time and date selector window. Tweets are posted based on the PHP time set in your hosting account; this may not be the same as your local time. The scheduled tweet cron posts scheduled tweets from before or exactly the time the cron is run, so the post time may not be exactly the same as the time you specify depending on how often you run the tweet cron job. 

Bulk CSV Tweet Upload

This system allows you to upload a CSV of scheduled tweets. The CSV should contain just two columns; column 1 should contain the tweet time in MySQL format (YYYY-MM-DD HH:MM), column 2 the tweet text. There is an example csv included with your install. The maximum size of the CSV you can upload depends on your host; typically shared hosts limit file uploads to 2mb but you may be able to upload larger files. Top tip: Spreadsheet programs such as Microsoft Excel will sometimes convert the date into an incorrect format; it's therefore recommended to create your csv in a text editor such as Notepad if you are doing this manually. 

Log Settings

To access the log settings for an authorized Twitter account, simply click the "Logs" link in the account table on the homepage of your install. There are then several tabbed options available which are detailed below. 

Log Settings

This checkbox simple sets if you would like to log the actions for this account when either the follow or tweet cron job is run. By default this is enabled when you authorize a new account. We would recommend enabling logging for all accounts as the logs provide useful information. 

View Follow Logs

This table displays the logs from the follow cron job. The information here shows who has unfollowed your account, who you have followed and consequently which users Twando auto followed or unfollowed for you. This screen also logs when automatic DMs are sent. In the "Affected Users" column you will see the Twitter profile images of the relevant users affected by a particular log entry. Hovering over the image will show additional useful information about their account (name, screen name, follower counts etc); clicking the image will open a new window link to their profile on Twitter.com. 

View Tweet Logs

This table shows your scheduled tweet history and shows both the time the tweet was posted and also whether or not the tweet posting was sucessful. 

Purge Log History

As the name suggests, this tab allows you to delete log history for a particular account if you wish. There is also an option to "Empty user cache"; the user cache is shared between all your Twitter accounts and is used to display more information about Twitter users in the "View Follow Logs" section. If one of your accounts is being followed by 1000 new users a day, the cache could get quite large quite quickly so you may want to empty it periodically. If you are proficient with phpMyAdmin, you can truncate the (tw_)user_cache table yourself at any time. This will not affect the log records; it just means that the additional information about a user (name, screen name, follower counts etc) will not be displayed in the "Affected Users" column in the "View Follow Logs" section. 

Multi Account Functions

Once you have authorized two or more Twitter accounts, you can then make use of the multi account functions Twando provides. These can be accessed by clicking the "Multi account functions" link towards the bottom of the homepage of your install. There are then several tabbed options available which are detailed below. 

Cross Follow Accounts

This option is extremely useful if you have a lot of Twitter accounts authorized with your Twando install and want to increase the number of followers to all of them. With one click, Twando will follow all your Twitter accounts from all your other Twitter accounts. You can also do the reverse and cross unfollow to remove the connection between accounts. 

All Follow / Unfollow

This section allows you to follow or unfollow a list of screen names or Twitter id's from all your accounts. For example, if you had 100 Twitter accounts authorized and wanted them all to follow a particular user, you could perform this operation very easily here. 

Multi Tweet

As the name suggests, you can enter a Tweet here which will be posted by all your Twitter accounts simultaneously.