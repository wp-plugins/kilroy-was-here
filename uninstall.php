<?php
/**
 * Code used when the plugin is removed (not just deactivated but actively deleted through the WordPress Admin).
 *
 * @package Kilroy_was_here
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'kilroywashere-content' );
delete_option( 'kilroywashere-priority' );
delete_option( 'kilroywashere-in_head' );
delete_option( 'kilroywashere-html_comment' );

if ( is_multisite() ) {
	$sites = wp_get_sites();
	if ( $sites ) {
		foreach ( $sites as $site ) {
			delete_blog_option( $site['blog_id'], 'kilroywashere-content' );
			delete_blog_option( $site['blog_id'], 'kilroywashere-priority' );
			delete_blog_option( $site['blog_id'], 'kilroywashere-in_head' );
			delete_blog_option( $site['blog_id'], 'kilroywashere-html_comment' );
		}
	}
}
