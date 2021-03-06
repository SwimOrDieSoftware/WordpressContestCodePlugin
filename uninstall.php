<?php

/**
 * Fired when the plugin is uninstalled.
 *
  * @since      1.0.0
 *
 * @package    Contest_Code_Checker
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete contest codes & contestants
$ccc_post_types = array( 'ccc_codes', 'ccc_contestants' );
$posts = get_posts( array( 'post_type' => $ccc_post_types, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );

if ( $posts ) {
	foreach ( $posts as $p ) {
		wp_delete_post( $p, true);
	}
}

// Unregister options
delete_option( 'ccc_start_date' );
delete_option( 'ccc_end_date' );
delete_option( 'ccc_text_winning' );
delete_option( 'ccc_text_losing' );
delete_option( 'ccc_contest_not_running' );
delete_option( 'ccc_hide_email' );
delete_option( 'ccc_hide_first_name' );
delete_option( 'ccc_hide_last_name' );
delete_option( 'ccc_enable_captcha' );
delete_option( 'ccc_recaptcha_secret_api' );
delete_option( 'ccc_recaptcha_site_key' );