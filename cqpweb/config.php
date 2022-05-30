<?php

/*
 * =================================
 * NECESSARY CONFIGURATION VARIABLES 
 * =================================
 */



/* ----------------------------------- *
 * adminstrators' usernames, separated *
 * by | with no stray whitespace.      *
 * ----------------------------------- */

$superuser_username = 'cqpwebadmin';


/* -------------------------- *
 * database connection config *
 * -------------------------- */

$sql_user     = 'cqpweb';
$sql_password = 'letmein';
$sql_schema   = 'cqpweb_db';
$sql_host     = 'db';



/* ---------------------- *
 * server directory paths *
 * ---------------------- */

$cqpweb_tempdir   = '/data/corpora/cqpweb/tmp';
$cqpweb_uploaddir = '/data/corpora/cqpweb/upload';
$cwb_datadir      = '/data/corpora/cqpweb/corpora';
$cwb_registry     = '/data/corpora/cqpweb/registry';

##################################################
### In the commented-out settings below, the   ###
### values presented are the defaults that     ###
### apply if the config file does not specify  ###
### a value for that setting. (See also Admin  ###
### manual chapter on the configuration file.) ###
##################################################




###########################################
### Locations of programs on the system ###
###########################################

## Path of the directory containing the CWB executables.
# $path_to_cwb = "";

## Path of the directory containing the GNU (or equivalent Unix-y) utility programs.
# $path_to_gnu = ""

## Path of the directory containing the Perl executable.
# $path_to_perl = "";

## Path of the directory containing the Python executable.
# $path_to_python = "";

## Path of the directory containing the R executable.
# $path_to_r = "";

## Extra directories to add to the INCLUDE path when running Perl (for the CEQL parser). 
# $perl_extra_directories = "";


#########################################
### Web daemon features (Apache etc.) ###
#########################################

## Insert the username that the web daemon runs under (e.g. "www-data" on Ubuntu Linux) here to enable some additional file management tools.
# $web_daemon_user = "";

## Insert the user group that the web daemon runs under (e.g. "www-data" on Ubuntu Linux) here to enable some additional file management tools.
# $web_daemon_group = "";


###########################################
### Database features (MySQL / MariaDB) ###
###########################################

## Limit on how many big SQL processes of a single type will be allowed to run at once.
# $sql_big_process_limit = 5;

## Controls how characters are transmitted between the SQL server and CQPweb.
# $sql_utf8_set_required = true;

## This variable declares to CQPweb whether or not the SQL daemon has access to the filesystem.
# $sql_has_file_access = false;

## Set this to true if your SQL daemon disallows LOAD DATA LOCAL, and you can't change this.
# $sql_local_infile_disabled = false;


##############################################################
### Memory, disk cache, and other hardware resource limits ###
##############################################################

## RAM usage limit (in MB) applied to CWB utilities (when used in the Web GUI).
# $cwb_max_ram_usage = 50;

## RAM usage limit (in MB) applied to CWB utilities (when used from the commandline).
# $cwb_max_ram_usage_cli = 1000;

## Max size in bytes of the query cache.
# $query_cache_size_limit = 6442450944;

## Max size in bytes of the user-database cache.
# $db_cache_size_limit = 6442450944;

## Max size in bytes of the restriction cache.
# $restriction_cache_size_limit = 6442450944;

## Max size in bytes of the frequency table cache.
# $freqtable_cache_size_limit = 6442450944;


######################################
### Configuring the user interface ###
######################################

## The number of results to show per page by default.
# $default_per_page = 50;

## The number of items to show per-page in history-type displays.
# $default_history_per_page = 100;

## The number of items to show per-page in the collocation display.
# $default_collocations_per_page = 50;

## Address of image file for the bar chart mode of Distribution.
# $dist_graph_img_path = "../css/img/blue.bmp";

## The number of texts (or other items) to display in Distribution - Frequency Extremes 
# $dist_num_files_to_list = 100;

## How many characters per form to use for grouping forms (words/annotations) in collocation.
# $colloc_max_comparison_length = 40;

## How many characters per form to use for comparing forms (words/annotations) for concordance sorting.
# $sort_max_comparison_length = 40;

## Amount of an uploaded file to display in-browser (in bytes)
# $uploaded_file_bytes_to_show = 102400;

## If set to true, new features deemed experimental will be hidden in the interface.
# $hide_experimental_features = false;


##################################
### Tweaking the look-and-feel ###
##################################

## Colour scheme to use for the main menu page (e.g. "~blue", "~red", etc.). 
# $colour_scheme_for_homepage = "~blue";

## Colour scheme to use for the admin control panel page. 
# $colour_scheme_for_adminpage = "~red";

## Colour scheme to use for the user-login homepage. 
# $colour_scheme_for_userpage = "~green";

## If true, corpora on the main homepage will be sorted by their saved category.
# $homepage_use_corpus_categories = false

## A little bit of text that will appear in the header box of the main menu page.
# $homepage_welcome_message = "Welcome to CQPweb!";

## URL, and optionally link target, for the left-side logo on the main menu page.
# $homepage_logo_left = "";

## URL, and optionally link target, for the right-side logo on the main menu page.
# $homepage_logo_right = "";

## A little bit of text that is suffixed to the corpus name in the main search page header.
# $searchpage_corpus_name_suffix = ": <em>powered by CQPweb</em>";


#############################
### User account creation ###
#############################

## If true, users can sign themselves up for an account on your server.
# $allow_account_self_registration = true;

## If true, users can change the email address associated with their account.
# $allow_account_email_change = false;

## If true, a user who changes their email address is not removed from groups they may have been added to automatically.
# $allow_account_email_change_group_persist = false;

## If self-registration is switched off, you can set this to the contact info of the manager of accounts.
# $account_create_contact = "";

## If true, the account-creation form is protected by a CAPTCHA challenge.
# $account_create_captcha = true;

## If true, any given email address can only be used to create ONE account. 
# $account_create_one_per_email = false;

## If you are obliged to specify a privacy/data-protection policy, insert its URL here, and it will be rendered in appropriate places.
# $account_privacy_policy_url = '';

## This number controls how hard it is to crack encrypted CQPweb passwords (higher = harder).
# $blowfish_cost = 11;

## Minimum length of valid passwords on your server (in characters).
# $password_minimum_length = 7;

## The name of the function to use to generate random password suggestions (in the admin UI).
# $create_password_function = "password_insert_internal";


##########################
### User corpus system ###
##########################

## If true, enables users to upload and index their own corpora.
# $user_corpora_enabled = false;

## If true, the colleaguate network/data-sharing system is switched on.
# $colleaguate_system_enabled = false;

## Maximum number of user corpora that the system will install simultaneously.
# $max_installer_processes = 1;

## This number controls how long jobs in the installer queue will wait between checks on their queue position.
# $installer_process_wait_secs = 3;


########################
### RSS feed control ###
########################

## If true, an RSS 2.0 feed of system-administration messages is available.
# $rss_feed_available = false;

## Specifies the URL for the RSS feed.
# $rss_link = "..";

## Specifies the title of the feed.
# $rss_feed_title = "CQPweb System Messages";

## The basic description that will pop up in subscribers' feed readers. 
# $rss_description = "Messages from the CQPweb server's administrator";


#####################################
### Debugging and error reporting ###
#####################################

## If true, prints debug and progress messages when an administrator is logged in. 
# $debug_on = false; 

## If true, debug and progress messages will be shown to all users, not just the admin, when $debug_on is true.
# $debug_for_all = false;

## If true, messages to and from the SQL server will be emitted when $debug_on is true.
# $debug_sql = false;

## If true, messages to and from CQP will be emitted when $debug_on is true.
# $debug_cqp = false;

## If true, debug and progress messages will have HTML formatting (except in the CLI or server log).
# $debug_html = true;

## If true, debug and progress messages will be sent to the browser or CLI when $debug_on is true.
# $debug_to_screen = true;

## If true, debug and progress messages will be sent to the server log when $debug_on is true
## (the server log also receives messages sent to a disconnected browser).
# $debug_to_log = false;

## If true, prints callstack on fatal error when an administrator is logged in. 
# $backtrace_on = false;

## If true, the callstack backtrace will be shown to all users, not just the admin, when $backtrace_on is true.
# $backtrace_for_all = false;

## If true, the callstack is shown in one-call-per-line format, rather than PHP's normal array-style layout.
# $backtrace_compact = true;

## If true, the callstack backtrace will be sent to the browser or CLI when $backtrace_on is true.
# $backtrace_to_screen = true;

## If true, the callstack backtrace will be sent to the server log when $backtrace_on is true
## (the server log also receives callstacks sent to a disconnected browser).
# $backtrace_to_log = false;


###########################################
### Miscellaneous configuration options ###
###########################################

## If true, CQPweb appears 'switched off' to the web. 
# $cqpweb_switched_off = false;

## A short HTML message included in the apology page presented when CQPweb is switched off.
# $cqpweb_switched_off_extra_message = "";

## An optional absolute URL for the root directory of the CQPweb code .
# $cqpweb_root_url = "";

## If true, CQPweb assumes it is not connected to the internet and is only being accessed locally.
# $cqpweb_no_internet = false;

## Email address used for "From:" and "Reply-To:" when CQPweb sends out emails.
# $cqpweb_email_from_address = "";

## Email address of the server administrator (shown to users as a point of contact).
# $server_admin_email_address = "";

## Label by which login cookies will identify themselves to users' browsers.
# $cqpweb_cookie_name = "CQPwebLogonToken";

## The maximum length of time, in seconds, that a user's login will persist between visits.
# $cqpweb_cookie_max_persist = 5184000;

## If true, CQPweb assumes it is running on Windows; if false, it assumes Unix. 
## If no value is specified, CQPweb guesses the OS, or falls back to false.
# $cqpweb_running_on_windows = false;

## If true, CQPweb will use Unix CL tools like awk, head etc.; otherwise, it won't.
## (This is independent of the "running on Windows" setting to avoid making guesses.)
# $use_unix_tools = false;

## If true, CQPweb uses CWB-Perl to parse simple queries, instead of the internal parser.
# $use_external_ceql_parser = false;

## If true, switches on TAB-optimisation (translation of "easy" CEQL patterns to TAB instead of Standard queries in CQP). 
# $use_ceql_tab_optimisation = false;


## 
# 

## 
# 

## 
# 

## 
# 

## 
# 

## 
# 

## 
# 

## 
# 

## 
# 













