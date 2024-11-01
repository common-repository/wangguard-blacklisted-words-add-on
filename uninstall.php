<?php
/**
 * Uninstall WangGuard Blacklisted Words Add-On
 * @version     1.0
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();


//Remove Options used by this Add_on

delete_site_option('wangguard_blacklisted_words_list');
?>