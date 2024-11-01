<?php
/*
Plugin Name: WangGuard Blacklisted Words Add-On
Plugin URI: http://www.wangguard.com
Description: With this Add-On you can blacklist Words. WangGuard plugin version 1.6 or higher is required, download it for free from <a href="http://wordpress.org/extend/plugins/wangguard/">http://wordpress.org/extend/plugins/wangguard/</a>.
Version: 1.0.1
Author: WangGuard
Author URI: http://www.wangguard.com
License: GPL2
*/

/*  Copyright 2012  WangGuard (email : info@wangguard.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WANGGUARD_BLACKLISTED_WORDS', '1.0.1');

function wangguard_blacklisted_words_init() {

	if (function_exists('load_plugin_textdomain')) {
		$plugin_dir = basename(dirname(__FILE__));
		load_plugin_textdomain('wangguard-blacklisted-words', false, $plugin_dir . "/languages/" );
	}
}
add_action('init', 'wangguard_blacklisted_words_init');

function wangguard_blacklisted_words_activate() {

	add_site_option('wangguard_blacklisted_words_list','');
}
register_activation_hook( 'wangguard-blacklisted-words/wangguard-blacklisted-words.php', 'wangguard_blacklisted_words_activate' );


function wangguard_blacklisted_words_notices() {
	if ( !defined('WANGGUARD_VERSION') ) {
		echo "
		<div  class='error fade'><p><strong>".__('WangGuard Blacklisted Words Add-On is almost ready.', 'wangguard-blacklisted-words')."</strong> ". __('You must install and activate <a href="http://wordpress.org/extend/plugins/wangguard/">WangGuard</a> 1.6-RC1 or higher to use this plugin.', 'wangguard-blacklisted-words')."</p></div>
		";
	}
	else {
		if ( defined('WANGGUARD_VERSION') ) {$version = WANGGUARD_VERSION;}
		if ($version)
			if (version_compare($version , '1.6-RC1') == -1)
				echo "
			<div  class='error fade'><p><strong>".__('WangGuard Blacklisted Words Add-On is almost ready.', 'wangguard-blacklisted-words')."</strong> ". __('You need to upgrade <a href="http://wordpress.org/extend/plugins/wangguard/">WangGuard</a> to version 1.6-RC1 or higher to use this plugin.', 'wangguard-blacklisted-words')."</p></div>
			";
	}
}
add_action('admin_notices', 'wangguard_blacklisted_words_notices');


// Save the new settings
function wangguard_save_blacklisted_words_fileds(){

	//Save banned domains

	$wangguardnewblacklistedwords = $_POST['wangguard_blacklisted_words_list'];
	$wanglisttoarrayblacklisted = explode("\n", maybe_serialize(strtolower($wangguardnewblacklistedwords)));
	update_site_option('wangguard_blacklisted_words_list', $wanglisttoarrayblacklisted);
}
add_action('wangguard_save_setting_option', 'wangguard_save_blacklisted_words_fileds');


//Add setting to WangGuard Setting page
function wangguard_blacklisted_words_fileds() { ?>
					<h3>Blacklisted Words</h3>
					<p>
						<label for="wangguard_blacklisted_words_list"><?php _e( 'Blacklisted Words. One per line', 'wangguard-blacklisted-words' ) ?></label><br />

						<?php $wangguard_blacklisted_words_list = get_site_option( 'wangguard_blacklisted_words_list' );
	$wangguard_blacklisted_words_list = str_replace( ' ', "\n", $wangguard_blacklisted_words_list ); ?>
						<textarea name="wangguard_blacklisted_words_list" id="wangguard_blacklisted_words_list" cols="45" rows="5"><?php echo esc_textarea( $wangguard_blacklisted_words_list == '' ? '' : implode( "\n", (array) $wangguard_blacklisted_words_list ) ); ?></textarea>
					</p>
<?php
}
add_action('wangguard_setting','wangguard_blacklisted_words_fileds' );

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS REGISTRATION FORM BEGINS **/
/********************************************************************/

function wangguard_blacklisted_words_add_on_check_user_name($user_name, $user_email, $errors){

	$blocked = wangguard_look_for_bl_word($user_name);

	if ($blocked) {
		$errors->add('user_name',   __('<strong>ERROR</strong>: Your user name has words not Allowed in this site.', 'wangguard-blacklisted-words'));
		return;
	}
}

function wangguard_blacklisted_words_add_on_check_email($user_name, $user_email, $errors){

	$blocked = wangguard_look_for_bl_word($user_email);

	if ($blocked) {
		$errors->add('user_email',   __('<strong>ERROR</strong>: Your email has words not Allowed in this site.', 'wangguard-blacklisted-words'));
		return;
	}
}
add_action('register_post', 'wangguard_blacklisted_words_add_on_check_user_name',10,3);
add_action('register_post', 'wangguard_blacklisted_words_add_on_check_email',10,3);

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS REGISTRATION FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** ADD MESSAGE IN THE WORDPRESS MULTISITE REGISTRATION FORM BEGINS **/
/********************************************************************/

if (is_multisite()) {
	require( dirname( __FILE__ ) . '/wangguard-blacklisted-words-wpmu.php' );
}


/********************************************************************/

/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS BUDDYPRESS REGISTRATION FORM BEGINS **/
/********************************************************************/

function wangguard_bp_blacklisted_words_code() {
	require( dirname( __FILE__ ) . '/wangguard-blacklisted-words-bp.php' );
}
add_action( 'bp_include', 'wangguard_bp_blacklisted_words_code' );



/********************************************************************/
/*** CHECK DOMAINS IN THE WORDPRESS BUDDYPRESS REGISTRATION FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** CHECK DOMAINS IN THE WOOCOMMERCE MY ACCOUNT FORM BEGINS **/
/********************************************************************/
function wangguard_blacklisted_words_woocommerce_add_on($user_name, $email, $errors){

	$user_email = $_POST['email'];

	$blocked = wangguard_check_bl_word_email($user_email);

	if ($blocked) {
		$errors->add('user_email',   __('<strong>ERROR</strong>: Your email has words not Allowed in this site.', 'wangguard-blacklisted-words'));
		return;
	}
}
if (get_option('woocommerce_enable_myaccount_registration')=='yes') add_action('woocommerce_before_customer_login_form', 'wangguard_blacklisted_words_woocommerce_add_on');
/********************************************************************/
/*** CHECK DOMAINS IN THE WOOCOMMERCE MY ACCOUNT FORM ENDS **/
/********************************************************************/

/********************************************************************/
/*** LOOK FOR WORDS BEGINS **/
/********************************************************************/

function wangguard_look_for_bl_word($words){
	$blacklistedwords = array_filter(array_map('trim', get_site_option('wangguard_blacklisted_words_list')));
	$low_words = strtolower($words);
	if ( !empty( $blacklistedwords ) ) {
		foreach ($blacklistedwords as $key => $blacklistedword) {
			$searchword = "/".$blacklistedword."/i";
			if (preg_match($searchword, $low_words)) {
				return true;
			}
		} return false;
	} else {
		return false;
	}
}
/********************************************************************/
/*** LOOK FOR WORDS ENDS **/
/********************************************************************/
?>