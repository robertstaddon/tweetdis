<?php

/**
 * The plugin bootstrap file
 *
 * Plugin Name:       Tweet Dis
 * Plugin URI:        https://github.com/robertstaddon/tweetdis
 * Description:       Creates click-to-tweet quotes, hints, and images from existing TweetDis shortcodes.
 * Version:           4.0.2
 * Requires at least: 5.8
 * Requires PHP:      8.0
 * Author:            Abundant Designs
 * Author URI:        https://www.abundantdesigns.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tweetdis
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );

/**
 * The code that runs during plugin activation.
 */
function activate_tweetdis() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/tweetdis-activator.php';
	Tweetdis_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_tweetdis' );

/**
 * Begins execution of the plugin.
 */
function run_tweetdis() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/tweetdis.php';
	$plugin = new Tweetdis( plugin_basename( __FILE__ ), '4.0.2' );
	$plugin->run();

}
run_tweetdis();
