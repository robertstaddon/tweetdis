<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package Tweetdis
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

remove_shortcode( 'tweet_dis' );
remove_shortcode( 'tweet_box' );
remove_shortcode( 'tweet_dis_img' );

delete_option( 'tweetdis_hint' );
delete_option( 'tweetdis_box' );
delete_option( 'tweetdis_image' );
delete_option( 'tweetdis_tweet_author' );
delete_option( 'tweetdis_tweet_settings' );
delete_option( 'tweetdis_rinfo' );

global $wpdb;
$table_list_images = $wpdb->prefix . 'tweetdis_list_img';
$wpdb->query( "DROP TABLE IF EXISTS `{$table_list_images}`" );
