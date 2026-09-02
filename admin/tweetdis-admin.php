<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    tweetdis
 * @subpackage tweetdis/admin
 */

/**
 * Defines the plugin name, version, and hooks for
 * enqueueing the admin-specific stylesheet and JavaScript.
 */
class Tweetdis_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin settings
	 *
	 * @var Tweetdis_Settings
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = Tweetdis_Settings::get_instance();

	}

	/**
	 * Register stylesheets and scripts for the admin area
	 */
	public function register_styles_and_scripts() {

		wp_register_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'tweetdis-admin.min.css', array(), $this->version );
		wp_register_style( $this->plugin_name . '-modal', plugin_dir_url( __FILE__ ) . 'tweetdis-mce.min.css', array(), $this->version );
		wp_register_style( $this->plugin_name . '-brand', plugin_dir_url( dirname( __FILE__ ) ) . 'public/tweetdis-brand.css', array( $this->plugin_name . '-admin' ), $this->version );
		wp_register_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'tweetdis-admin.min.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * Enqueue the stylesheet for the admin area
	 */
	public function enqueue_style() {

		wp_enqueue_style( $this->plugin_name . '-admin' );
		wp_enqueue_style( $this->plugin_name . '-brand' );

	}

	/**
	 * Enqueue the stylesheet for modal in tiny mce editor
	 */
	public function enqueue_tinymce_style() {

		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( $screen->id === 'page' || $screen->base === 'post' ) {
			wp_enqueue_style( $this->plugin_name . '-modal' );
		}
	}

	/**
	 * Enqueue JavaScript for the admin area
	 */
	public function enqueue_scripts() {

		wp_enqueue_media();
		wp_localize_script(
			$this->plugin_name . '-admin',
			'Td_Ajax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tweetdis_admin' ),
			)
		);
		wp_enqueue_script( $this->plugin_name . '-admin' );

	}

	/**
	 * Add plugin menu
	 */
	public function add_menu() {

		add_menu_page( 'Tweet Dis', 'Tweet Dis', 'manage_options', $this->plugin_name . '-menu', array( $this, 'show_settings' ), $this->settings->get_images_url() . 'icon.png' );
		add_submenu_page( $this->plugin_name . '-menu', 'Tweet Dis Settings', 'Settings', 'manage_options', $this->plugin_name . '-menu' );

	}

	/**
	 * Add tweetdis button with its functions to tiny mce editor
	 */
	public function add_tweetdis_mce_button() {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( true === get_user_option( 'rich_editing' ) ) {

			add_filter( 'mce_external_plugins', array( $this, 'add_tweetdis_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_tweetdis_mce_button' ) );

		}

	}

	/**
	 * Add plugin to tiny mce plugins
	 *
	 * @param array $plugin_array TinyMCE plugins.
	 * @return array
	 */
	public function add_tweetdis_tinymce_plugin( $plugin_array ) {

		$plugin_array['tweetdis'] = plugin_dir_url( __FILE__ ) . 'tweetdis-mce.min.js';
		return $plugin_array;

	}

	/**
	 * Add button to tiny mce buttons
	 *
	 * @param array $buttons TinyMCE buttons.
	 * @return array
	 */
	public function register_tweetdis_mce_button( $buttons ) {

		$buttons[] = 'tweetdis';
		return $buttons;

	}

	/**
	 * Show settings page
	 */
	public function show_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'tweetdis' ) );
		}

		$this->enqueue_style();
		$this->enqueue_scripts();
		include_once 'view/page-settings.php';

	}

	/**
	 * Get preview for settings tabs
	 */
	public function get_preview() {

		$this->verify_ajax_request();

		if ( ! isset( $_POST['tabs'] ) ) {
			wp_die();
		}

		$tab = sanitize_text_field( wp_unslash( $_POST['tabs'] ) );

		if ( $tab === 'hint' ) {

			$settings = $this->settings->get_hint_settings();
			include_once 'view/hint-tab.php';

		} elseif ( $tab === 'image' ) {

			$settings = $this->get_image_preview_settings();
			include_once 'view/image-tab.php';

		} elseif ( $tab === 'tweet' ) {

			$settings = $this->settings->get_tweet_settings();
			include_once 'view/tweet-tab.php';

		} else {

			$settings = $this->get_box_preview_settings();
			$params   = $this->get_box_preview_params();
			include_once 'view/box-tab.php';

		}

		wp_die();
	}

	/**
	 * Save settings
	 */
	public function save_settings() {

		$this->verify_ajax_request();

		if ( ! isset( $_POST['tabs'] ) ) {
			echo 'Error, please try again';
			wp_die();
		}

		$result = false;
		$tab    = sanitize_text_field( wp_unslash( $_POST['tabs'] ) );

		if ( $tab === 'box' ) {
			$result = $this->settings->save_box_settings();
		} elseif ( $tab === 'hint' ) {
			$result = $this->settings->save_hint_settings();
		} elseif ( $tab === 'image' ) {
			$result = $this->settings->save_image_settings();
		} elseif ( $tab === 'tweet' ) {
			$result = $this->settings->save_tweet_settings();
		}

		echo $result ? 'Saved' : 'Error, please try again';

		wp_die();
	}

	/**
	 * Get box preset settings for preview
	 *
	 * @return array
	 */
	private function get_box_preview_settings() {

		$design = isset( $_POST['design'] ) ? sanitize_text_field( wp_unslash( $_POST['design'] ) ) : $this->settings->get_default_box();
		return $this->settings->get_box_settings( $design );

	}

	/**
	 * Get box preview parameters 'design', 'author', 'author_pic'
	 *
	 * @return array
	 */
	private function get_box_preview_params() {

		return array(
			'design'     => isset( $_POST['design'] ) ? sanitize_text_field( wp_unslash( $_POST['design'] ) ) : $this->settings->get_default_box(),
			'author'     => isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : $this->settings->get_tweet_author( 'author' ),
			'author_pic' => isset( $_POST['author_pic'] ) ? esc_url_raw( wp_unslash( $_POST['author_pic'] ) ) : $this->settings->get_tweet_author( 'author_pic' ),
		);

	}

	/**
	 * Get image template settings for preview
	 *
	 * @return array
	 */
	private function get_image_preview_settings() {

		$design             = isset( $_POST['design'] ) ? sanitize_text_field( wp_unslash( $_POST['design'] ) ) : $this->settings->get_default_image();
		$settings           = $this->settings->get_image_settings( $design );
		$settings['design'] = $design;

		return $settings;

	}

	/**
	 * Verify AJAX capability and nonce
	 */
	private function verify_ajax_request() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Forbidden', '', array( 'response' => 403 ) );
		}

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'tweetdis_admin' ) ) {
			wp_die( 'Forbidden', '', array( 'response' => 403 ) );
		}

	}

}
