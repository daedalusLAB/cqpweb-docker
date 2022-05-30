<?php
/*
 * CQPweb: a user-friendly interface to the IMS Corpus Query Processor
 * Copyright (C) 2008-today Andrew Hardie and contributors
 *
 * See http://cwb.sourceforge.net/cqpweb.php
 *
 * This file is part of CQPweb.
 * 
 * CQPweb is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * CQPweb is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @file
 * 
 * This script finalises CQPweb setup once the config file has been created.
 */



require('../lib/environment.php');

/* include function library files */
require('../lib/general-lib.php');
require('../lib/sql-lib.php');
require('../lib/query-lib.php');
require('../lib/sql-definitions.php');
require('../lib/useracct-lib.php');
require('../lib/html-lib.php');
require('../lib/exiterror-lib.php');

require ('../bin/cli-lib.php');




/* BEGIN HERE */


/* refuse to run unless we are in CLI mode */
if (php_sapi_name() != 'cli')
	exit("Critical error: Cannot run CLI scripts over the web!\n");

echo "\nNow finalising setup for this installation of CQPweb....\n";

/* create partial environment */
$sql_schema = $sql_password = $sql_user = $sql_host = $sql_utf8_set_required = null;
$mysql_schema = $mysql_webpass = $mysql_webuser = $mysql_server = $mysql_utf8_set_required = null;

$superuser_username = $default_keyness_calc_stat = $default_colloc_calc_stat = $default_colloc_minfreq = $default_colloc_range = $default_max_dbsize = $blowfish_cost = null;

include ('../lib/defaults.php');
if (file_exists('../lib/config.php'))
	require('../lib/config.php');
else
	require('../lib/config.inc.php');

$Config = new NotAFullConfig();
$Config->debug_on                  = false;
$Config->client_is_disconnected    = false;
$Config->debug_html                = true;
$Config->backtrace_on              = false;
$Config->backtrace_for_all         = false;
$Config->Api                       = false;
$Config->sql_utf8_set_required     = $sql_utf8_set_required ?? ($mysql_utf8_set_required ?? false);
$Config->sql_schema                = $sql_schema   ?? $mysql_schema  ;
$Config->sql_password              = $sql_password ?? $mysql_webpass ;
$Config->sql_user                  = $sql_user     ?? $mysql_webuser ;
$Config->sql_host                  = $sql_host     ?? $mysql_server  ;

/* instead of cqpweb_startup_environment( ... ); ....... */
$Config->sql_link = create_sql_link();

/* extend the partial environment! -- these are the values needed for user account creation; draw in from config/defaults */
$Config->default_keyness_calc_stat = $default_keyness_calc_stat;
$Config->default_colloc_calc_stat  = $default_colloc_calc_stat;
$Config->default_colloc_minfreq    = $default_colloc_minfreq;
$Config->default_colloc_range      = $default_colloc_range;
$Config->default_max_dbsize        = $default_max_dbsize;
$Config->blowfish_cost             = $blowfish_cost;

/* create only the parts of $User needed to survive the security check */
class NotAFullUser { public function is_admin () { return true; } }
$User = new NotAFullUser();


echo "\nInstalling database structure; please wait.\n";

/* before we create any tables, for safety, we need to reset the database default collation to the best available collation */
do_sql_query("alter database `{$Config->sql_schema}` CHARACTER SET utf8mb4 COLLATE " . deduce_best_sql_bin_collation());

/* all right! now we can get building. */
do_sql_total_reset();

echo "\nDatabase setup complete.\n";

echo "\nNow, we must set passwords for each user account specified as a superuser.\n";


foreach(explode('|', $superuser_username) as $super)
{
	#$pw = get_variable_string("a password for user ``$super''");
	$pw = "letmein";
	add_new_user($super, $pw, 'not-specified@nowhere.net', USER_STATUS_ACTIVE);
	echo "\nAccount setup complete for ``$super''\n";
}

echo "--- done.\n";


/* destroy partial environment */

$Config->sql_link->close();
unset($Config);
unset($User);


/* with DB installed, we can now startup the REAL environment.... */

cqpweb_startup_environment(CQPWEB_STARTUP_DONT_CONNECT_CQP|CQPWEB_STARTUP_ENFORCE_CLI, RUN_LOCATION_ADM);


/* When we created the tables (above), we relied on the ability of the system 
 * to deduce the best collations for string fields.
 * 
 * Henceforth, we want to have this info stored and at the ready. So...  */
echo "\nStoring best collations for optimal use of your database's Unicode handling ...\n";

register_all_best_sql_collations();

echo "--- done.\n";


echo "\nCreating built-in mapping tables...\n";

regenerate_builtin_mapping_tables();

echo "--- done.\n";


/*
 * If more setup actions come along, add them here
 * (e.g. annotation templates, xml templates...
 */


echo "\nAutosetup complete; you can now start using CQPweb.\n";

cqpweb_shutdown_environment();

exit(0);


