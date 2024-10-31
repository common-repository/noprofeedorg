<?php
/*
Plugin Name: noprofeed.org
Plugin URI: http://noprofeed.org/
Description: Help non-profit organizations to spread the word about their activities on the largest possible number of WordPress blogs/sites.
Version: 1.2.1
Author: Ugo Grandolini aka "camaleo"
Author URI: http://grandolini.com
*/
/*
	Copyright (C) 2010,2013 Ugo Grandolini  (email : info@myeasywp.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

// TODO +++++++++++++++++++++++++
define('NOPROFEED_DEBUG', false);
define('isSANDBOX', false);
// TODO +++++++++++++++++++++++++


define('NOP_VERSION', '1.2');
define('NOP_DB_VERSION', '1.0.0');
// TODO automatically upgrade the db structure when needed!

define('NOPROFEED_WID_MAX_WORDS', 20); /* 1.0.1 */


global $wpdb;
define('NOP_TABLE_CACHE', $wpdb->base_prefix . 'noprofeed_cache');


define('NOPROFEED_LOCALE', 'noprofeed');
//define('myEASYcomCaller', 'noprofeed');

//define('NOPROFEED_FOOTER_CREDITS', '<div style="font-size:9px;text-align:center;"><a href="http://myeasywp.com" target="_blank">Improve Your Life, Go The myEASY Way&trade;</a></div>');

//if(strlen(get_option( 'upload_path' ))>0) {
//
//	$tmp = get_option( 'upload_path' );
//	if(stripos($tmp,ABSPATH,0)===false) {
//
//		$tmp = ABSPATH . $tmp;
//	}
//	define('NOPROFEED_UPLOAD_PATH', $tmp );
//}
//else {
//
//	define('NOPROFEED_UPLOAD_PATH', ABSPATH . 'wp-content/uploads');
//}
//define('NOPROFEED_CSS_OVERRIDE_FILE', NOPROFEED_UPLOAD_PATH . '/noprofeed-override.css');

define('NOPROFEED_CSS_OVERRIDE_FILE', dirname(__FILE__) . '/noprofeed-override.css');

//echo ' <code>'. NOPROFEED_CSS_OVERRIDE_FILE .'</code><br>';


//define('SAVE_BTN', __('Update Options', NOPROFEED_LOCALE ));
//define('SAVE_AND_RELOAD_BTN', __('Update Options and Reload The Feeds Cache', NOPROFEED_LOCALE ));
//define('ACTIVATE_BTN', __('Activate', NOPROFEED_LOCALE ));
//define('DEACTIVATE_BTN', __('Deactivate', NOPROFEED_LOCALE ));
//define('DEACTIVATE_FULL_BTN', __('Fully deactivate', NOPROFEED_LOCALE ));


/* 1.0.8: BEG */
//define('NOPROFEED_CDN', 'http://c582341.r41.cf2.rackcdn.com/');
define('NOPROFEED_CDN', plugins_url() . '/noprofeedorg/img/');

//define('MYEASY_CDN', 'http://srht.me/f9'); # 0.1.4
define('MYEASY_CDN', plugins_url() . '/noprofeedorg/');
define('MYEASY_CDN_IMG', MYEASY_CDN . 'img/');
define('MYEASY_CDN_CSS', MYEASY_CDN . 'css/');
define('MYEASY_CDN_JS', MYEASY_CDN . 'js/');

/* 1.0.8: END */

if(defined('NOPROFEED_DEBUG') && NOPROFEED_DEBUG==true) {

	/**
	 * debug only!
	 */
	define('SERVICE_SITE_URL',          'http://camaleo/' );
	define('SERVICE_SITE_NAME',         'http://camaleo' );
}
else {

	define('SERVICE_SITE_URL',          'http://services.myeasywp.com/' );
	define('SERVICE_SITE_NAME',         'http://services.myeasywp.com' );
}

require_once('inc/myEASYcom.php');
require_once('class.noprofeed.php');
//require_once('class.noprofeed-widget.php');


if(is_admin()) {

//	global $_wp_contextual_help;    //todo: avoid to show other stuff in the contextual help?
//var_dump($_wp_contextual_help);


	$NOPROFEED_backend = new noprofeed_BACKEND();
	$NOPROFEED_backend->main_plugin = __FILE__;
	$NOPROFEED_backend->locale = NOPROFEED_LOCALE;
	$NOPROFEED_backend->css = 'npf-wid';
	$NOPROFEED_backend->js = 'npf-wid';
	$NOPROFEED_backend->register_plugin_settings(__FILE__);   // adding a link to the settings page in the plugin list page


	/**
	 * help contents
	 */
	//	$_backend->help = '*** noprofeed HELP ***'; //todo?

	/*TODO
	 * dashboard plugin own item
	 */
//	$NOPROFEED_backend->dash_title = '<span>' . __( 'noprofeed info', NOPROFEED_LOCALE ) . '</span>';
//	$NOPROFEED_backend->dash_contents = ''
//							. '<p>' . __( 'bla bla bla bla...', NOPROFEED_LOCALE ) . '</p>'
//	;

} else {

	$NOPROFEED_frontend = new noprofeed_FRONTEND();
	$NOPROFEED_frontend->main_plugin = __FILE__;
	$NOPROFEED_frontend->locale = NOPROFEED_LOCALE;
	$NOPROFEED_frontend->css = 'npf-wid';
	$NOPROFEED_frontend->js = 'npf-wid';
}

/**
 * Load the widget.
 */
add_action('widgets_init', 'load_noprofeed_widget');

//add_action('wp_head', 'noprofeed_head');
//function noprofeed_head() {
//
//	wp_register_style('npf-wid-style', plugins_url('', __FILE__) . '/css/npf-wid.css');
//	wp_register_script('npf-wid-script', plugins_url('', __FILE__) . '/js/npf-wid.js');
//echo plugins_url('', __FILE__) . '/js/npf-wid.js';
//}


/**
 * housekeeping
 */
function noprofeed_activate() {

	/**
	 * everything you need to do when activating the plugin
	 */

//echo 'activate';

	global $wpdb;

	if($wpdb->get_var('SHOW TABLES LIKE \''.NOP_TABLE_CACHE.'\' ') != NOP_TABLE_CACHE) {

		$sql = 'CREATE TABLE `' . NOP_TABLE_CACHE . '` ('

				.'`id` int(12) unsigned NOT NULL AUTO_INCREMENT,'
				.'`lastDisplay` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\','
				.'`feedID` int(12) unsigned NOT NULL,'
				.'`feedURL` varchar(255) NOT NULL,'
				.'`siteTITLE` varchar(255) NOT NULL,'
				.'`favicon` varchar(255) NOT NULL,'
				.'`siteURL` varchar(255) NOT NULL,'
				.'`description` varchar(255) NOT NULL,'
				.'`title` varchar(255) NOT NULL,'
				.'`language` varchar(16) DEFAULT NULL,'
				.'`permalink` varchar(255) NOT NULL,'
				.'`feedDATE` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\','
				.'`content` longtext NOT NULL,'

				.'`orgName` varchar(255) NOT NULL,'
				.'`orgURL` varchar(255) NOT NULL,'
				.'`orgTown` varchar(255) NOT NULL,'
				.'`orgCountry` varchar(255) NOT NULL,'

				.'PRIMARY KEY (`id`)'

		.')  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1';

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option('nop_wid_db_version', NOP_DB_VERSION);
	}

	if(strlen(get_option('nop_wid_feedReadMore'))==0) { update_option('nop_wid_feedReadMore', '1'); }
	if(strlen(get_option('nop_wid_feeds2show'))==0) { update_option('nop_wid_feeds2show', '1'); }
	if(strlen(get_option('nop_wid_feedShowOrg'))==0) { update_option('nop_wid_feedShowOrg', '1'); }
	if(strlen(get_option('nop_wid_feedShowOrgDonate'))==0) { update_option('nop_wid_feedShowOrgDonate', '1'); }
	if(strlen(get_option('nop_wid_feedStyle_3D'))==0) { update_option('nop_wid_feedStyle_3D', '1'); }

	if(strlen(get_option('nop_wid_feedStyle_bgcolor'))==0) { update_option('nop_wid_feedStyle_bgcolor', 'B5DBB4'); }
	if(strlen(get_option('nop_wid_feedStyle_bordcolor'))==0) { update_option('nop_wid_feedStyle_bordcolor', 'FFFFFF'); }
	if(strlen(get_option('nop_wid_feedStyle_linkscolor'))==0) { update_option('nop_wid_feedStyle_linkscolor', '2585A8'); }
	if(strlen(get_option('nop_wid_feedStyle_textcolor'))==0) { update_option('nop_wid_feedStyle_textcolor', '7B7051'); }

	if(strlen(get_option('nop_wid_feedStyle_paper'))==0) { update_option('nop_wid_feedStyle_paper', '1'); }

	if(strlen(get_option('nop_wid_height'))==0) { update_option('nop_wid_height', '0'); }
	if(strlen(get_option('nop_wid_width'))==0) { update_option('nop_wid_width', '122'); }
	if(strlen(get_option('nop_wid_widthSizeDisplay_value'))==0) { update_option('nop_wid_widthSizeDisplay_value', '265'); }

	if(strlen(get_option('nop_wid_last_cache_update'))==0) { update_option('nop_wid_last_cache_update', '0'); }
}

function noprofeed_deactivate() {

	/**
	 * everything you need to do when deactivating the plugin
	 */
	global $wpdb, $langs, $categories, $regions;

//echo 'deactivate';

	delete_option('nop_wid_db_version');

	delete_option('nop_wid_feedReadMore');
	delete_option('nop_wid_feeds2show');
	delete_option('nop_wid_feedHeight');
	delete_option('nop_wid_feedShowOrg');
	delete_option('nop_wid_feedShowOrgDonate');
	delete_option('nop_wid_feedStyle_3D');

	delete_option('nop_wid_feedStyle_bgcolor');
	delete_option('nop_wid_feedStyle_bordcolor');
	delete_option('nop_wid_feedStyle_linkscolor');
	delete_option('nop_wid_feedStyle_textcolor');

	delete_option('nop_wid_feedStyle_paper');

	delete_option('nop_wid_height');
	delete_option('nop_wid_width');
	delete_option('nop_wid_widthSizeDisplay_value');

	delete_option('nop_wid_last_cache_update');

	require_once('inc/shared-functions.php');

	foreach($langs as $key => $val) {

		delete_option('nop_wid_feedFilter_lang_'.$key);
	}

	foreach($categories as $key => $val) {

		delete_option('nop_wid_feedFilter_category_'.$key);
	}

	foreach($regions as $key => $val) {

		delete_option('nop_wid_feedFilter_region_'.$key);
	}

	/**
	 * sometimes permissions does not allow to remove a table, in such cases we remove its contents
	 */
	$sql = 'EMPTY TABLE ' . NOP_TABLE_CACHE . ' ';
	$wpdb->query($sql);

	/**
	 * then we try to remove the table itself
	 */
	$sql = 'DROP TABLE ' . NOP_TABLE_CACHE . ' ';
	$wpdb->query($sql);
}

register_activation_hook(__FILE__, 'noprofeed_activate');
register_deactivation_hook(__FILE__, 'noprofeed_deactivate');



//var_dump($_SERVER);
//echo $_SERVER['HTTP_REFERER'].'<br>';
//$tmp = $_SERVER['HTTP_REFERER'];
//$tmpa = explode('/', $tmp);
//echo $tmpa[0].'<br>';

//die();

?>