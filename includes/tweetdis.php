<?php

/**
 * The file that defines the core plugin class
 *
 * @package    tweetdis
 * @subpackage tweetdis/includes
 */

/**
 * The core plugin class.
 */
class Tweetdis {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var Tweetdis_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 *
	 * @var string
	 */
	protected $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @param string $plugin_slug Plugin basename.
	 * @param string $version     Plugin version.
	 */
	public function __construct( $plugin_slug, $version ) {

		$this->plugin_name = 'tweetdis';
		$this->version     = $version;
		$this->plugin_slug = $plugin_slug;

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tweetdis-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tweetdis-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tweetdis-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tweetdis-entity.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/entities/tweetdis-hint.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/entities/tweetdis-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/entities/tweetdis-image.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/tweetdis-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/tweetdis-public.php';

		$this->loader = new Tweetdis_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {

		$plugin_i18n = new Tweetdis_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Tweetdis_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'add_tweetdis_mce_button' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'register_styles_and_scripts' );
		$this->loader->add_action( 'admin_print_styles', $plugin_admin, 'enqueue_tinymce_style' );
		$this->loader->add_action( 'wp_ajax_tweetdis_get_preview', $plugin_admin, 'get_preview' );
		$this->loader->add_action( 'wp_ajax_tweetdis_save_settings', $plugin_admin, 'save_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Tweetdis_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_style' );

		$this->loader->add_shortcode( 'tweet_dis', $plugin_public, 'do_shortcodes' );
		$this->loader->add_shortcode( 'tweet_box', $plugin_public, 'do_shortcodes' );
		$this->loader->add_shortcode( 'tweet_dis_img', $plugin_public, 'do_shortcodes' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

}
