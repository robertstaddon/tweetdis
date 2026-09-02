<?php

/**
 * Store all plugin settings
 *
 * @package    tweetdis
 * @subpackage tweetdis/includes
 */

/**
 * Keep all plugin settings and update them when user makes customizations
 */
class Tweetdis_Settings {

	/**
	 * The class instance
	 *
	 * @var Tweetdis_Settings|null
	 */
	private static $instance;

	/**
	 * Hint settings
	 *
	 * @var array
	 */
	private $hint_settings;

	/**
	 * Box presets settings
	 *
	 * @var array
	 */
	private $box_settings;

	/**
	 * Box preset names
	 *
	 * @var array
	 */
	private $box_presets;

	/**
	 * Default box preset name
	 *
	 * @var string
	 */
	private $default_box;

	/**
	 * Image template settings
	 *
	 * @var array
	 */
	private $image_settings;

	/**
	 * Image templates names
	 *
	 * @var array
	 */
	private $image_presets;

	/**
	 * Selected image template name
	 *
	 * @var string
	 */
	private $default_image;

	/**
	 * Tweet author
	 *
	 * @var array
	 */
	private $tweet_author;

	/**
	 * Tweet settings
	 *
	 * @var array
	 */
	private $tweet_settings;

	/**
	 * Url of images folder
	 *
	 * @var string
	 */
	private $images_url;

	/**
	 * Demo phrase for admin
	 *
	 * @var array
	 */
	private $demo;

	/**
	 * Initialize only instance of this class
	 */
	private function __construct() {

		$this->hint_settings  = $this->get_option_array( 'tweetdis_hint', $this->default_hint_settings() );
		$this->box_settings   = $this->get_option_array( 'tweetdis_box', array() );
		$this->box_presets    = array();
		$this->default_box    = '';

		$this->image_settings = $this->get_option_array( 'tweetdis_image', array() );
		$this->image_presets  = array();
		$this->default_image  = '';

		$this->tweet_author   = $this->get_option_array( 'tweetdis_tweet_author', $this->default_tweet_author() );
		$this->tweet_settings = $this->get_option_array( 'tweetdis_tweet_settings', $this->default_tweet_settings() );

		$this->images_url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/';

		$this->demo = array(
			'box'   => 'The little vessel continued to beat its way seaward, and the ironclads receded slowly towards the coast',
			'hint'  => 'an example of any article on your blog. So this is kinda the paragraph of usual text in your article and what you see below is the "tweet box" created by TweetDis plugin.',
			'image' => $this->images_url . 'preview_box.png',
		);

	}

	/**
	 * Get instance of this class
	 *
	 * @return Tweetdis_Settings
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new Tweetdis_Settings();
		}

		return self::$instance;

	}

	/**
	 * Get hint settings
	 *
	 * @return array
	 */
	public function get_hint_settings() {
		return $this->hint_settings;
	}

	/**
	 * Get box settings
	 *
	 * @param string $preset Preset name.
	 * @return array
	 */
	public function get_box_settings( $preset ) {

		if ( isset( $this->box_settings[ $preset ] ) && is_array( $this->box_settings[ $preset ] ) ) {
			$settings = $this->box_settings[ $preset ];
			if ( isset( $settings['callforaction'] ) ) {
				$settings['callforaction'] = $this->normalize_call_to_action( $settings['callforaction'] );
			}
			return $settings;
		}

		$default = $this->get_default_box();
		if ( isset( $this->box_settings[ $default ] ) && is_array( $this->box_settings[ $default ] ) ) {
			return $this->box_settings[ $default ];
		}

		return array(
			'callforaction'    => 'Click to share',
			'font_size'        => 'original',
			'colors'           => array(),
			'color_number'     => 0,
			'margin_vertical'  => 'default',
			'default'          => true,
		);
	}

	/**
	 * Get box presets names
	 *
	 * @return array
	 */
	public function get_box_presets() {

		if ( count( $this->box_presets ) === 0 ) {
			$presets           = $this->get_presets_and_default( $this->box_settings );
			$this->box_presets = $presets['presets'];
			$this->default_box = $presets['default'];
		}

		return $this->box_presets;
	}

	/**
	 * Get default box preset
	 *
	 * @return string
	 */
	public function get_default_box() {

		if ( $this->default_box === '' ) {
			$presets           = $this->get_presets_and_default( $this->box_settings );
			$this->box_presets = $presets['presets'];
			$this->default_box = $presets['default'];
		}

		return $this->default_box;
	}

	/**
	 * Get tweet author
	 *
	 * @param string $key Key of tweet author settings array. Keys: 'author', 'author_pic'.
	 * @return mixed
	 */
	public function get_tweet_author( $key = '' ) {

		$author = array_merge( $this->default_tweet_author(), is_array( $this->tweet_author ) ? $this->tweet_author : array() );

		if ( $key !== '' ) {
			return $author[ $key ] ?? '';
		}

		return $author;
	}

	/**
	 * Get selected image template settings
	 *
	 * @param string $preset Template name.
	 * @return array
	 */
	public function get_image_settings( $preset = '' ) {

		if ( $preset === '' ) {
			$preset = $this->get_default_image();
		}

		if ( isset( $this->image_settings[ $preset ] ) && is_array( $this->image_settings[ $preset ] ) ) {
			$settings = $this->image_settings[ $preset ];
			if ( isset( $settings['callforaction'] ) ) {
				$settings['callforaction'] = $this->normalize_call_to_action( $settings['callforaction'] );
			}
			return $settings;
		}

		return array(
			'hover_action'  => 'original',
			'image_txt'     => 'blank',
			'position'      => 'center',
			'button_size'   => 'original',
			'callforaction' => 'Share',
			'default'       => true,
		);
	}

	/**
	 * Get image templates names
	 *
	 * @return array
	 */
	public function get_image_presets() {

		if ( count( $this->image_presets ) === 0 ) {
			$templates           = $this->get_presets_and_default( $this->image_settings, 'template_1' );
			$this->image_presets = $templates['presets'];
			$this->default_image = $templates['default'];
		}

		return $this->image_presets;
	}

	/**
	 * Get selected image template
	 *
	 * @return string
	 */
	public function get_default_image() {

		if ( $this->default_image === '' ) {
			$templates           = $this->get_presets_and_default( $this->image_settings, 'template_1' );
			$this->image_presets = $templates['presets'];
			$this->default_image = $templates['default'];
		}

		return $this->default_image;
	}

	/**
	 * Get tweet settings
	 *
	 * @param string $key Key of tweet settings array. Keys: 'twitter', 'follow', 'preposition'.
	 * @return mixed
	 */
	public function get_tweet_settings( $key = '' ) {

		$settings = array_merge( $this->default_tweet_settings(), is_array( $this->tweet_settings ) ? $this->tweet_settings : array() );

		if ( $key !== '' ) {
			return $settings[ $key ] ?? '';
		}

		return $settings;
	}

	/**
	 * Get demo phrase
	 *
	 * @param string $type Type of entity to generate.
	 * @return string
	 */
	public function get_demo( $type ) {
		return $this->demo[ $type ] ?? '';
	}

	/**
	 * Get images url
	 *
	 * @return string
	 */
	public function get_images_url() {
		return $this->images_url;
	}

	/**
	 * Prepare box settings for save
	 *
	 * @return bool
	 */
	public function save_box_settings() {

		if ( ! isset( $_POST['design'] ) ) {
			return false;
		}

		$preset = sanitize_text_field( wp_unslash( $_POST['design'] ) );

		if ( ! isset( $this->box_settings[ $preset ] ) || ! is_array( $this->box_settings[ $preset ] ) ) {
			return false;
		}

		$settings                     = $this->box_settings[ $preset ];
		$settings['callforaction']    = isset( $_POST['callforaction'] ) ? sanitize_text_field( wp_unslash( $_POST['callforaction'] ) ) : $settings['callforaction'];
		$settings['font_size']        = isset( $_POST['font_size'] ) ? sanitize_text_field( wp_unslash( $_POST['font_size'] ) ) : $settings['font_size'];
		$settings['color_number']     = isset( $_POST['color_number'] ) ? intval( $_POST['color_number'] ) : $settings['color_number'];
		$settings['margin_vertical']  = isset( $_POST['margin_vertical'] ) ? sanitize_text_field( wp_unslash( $_POST['margin_vertical'] ) ) : $settings['margin_vertical'];
		$settings['default']          = isset( $_POST['default'] ) ? ( $_POST['default'] === 'true' ) : $settings['default'];

		if ( stripos( $preset, '_at' ) !== false ) {
			$author = array(
				'author'     => isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : $this->get_tweet_author( 'author' ),
				'author_pic' => isset( $_POST['author_pic'] ) ? esc_url_raw( wp_unslash( $_POST['author_pic'] ) ) : $this->get_tweet_author( 'author_pic' ),
			);
			$this->save_author_settings_to_db( $author );
		}

		$this->save_box_settings_to_db( $preset, $settings );

		return true;
	}

	/**
	 * Prepare hint settings for save
	 *
	 * @return bool
	 */
	public function save_hint_settings() {

		if ( ! isset( $_POST['style'] ) ) {
			return false;
		}

		$settings          = $this->hint_settings;
		$settings['style'] = sanitize_text_field( wp_unslash( $_POST['style'] ) );
		$settings['color'] = isset( $_POST['color'] ) ? intval( $_POST['color'] ) : ( $settings['color'] ?? 1 );

		$this->save_hint_settings_to_db( $settings );

		return true;
	}

	/**
	 * Prepare image settings for save
	 *
	 * @return bool
	 */
	public function save_image_settings() {

		if ( ! isset( $_POST['design'] ) ) {
			return false;
		}

		$template = sanitize_text_field( wp_unslash( $_POST['design'] ) );

		if ( ! isset( $this->image_settings[ $template ] ) || ! is_array( $this->image_settings[ $template ] ) ) {
			return false;
		}

		$settings                 = $this->image_settings[ $template ];
		$settings['callforaction'] = isset( $_POST['callforaction'] ) ? sanitize_text_field( wp_unslash( $_POST['callforaction'] ) ) : $settings['callforaction'];
		$settings['hover_action']  = isset( $_POST['hover_action'] ) ? sanitize_text_field( wp_unslash( $_POST['hover_action'] ) ) : $settings['hover_action'];
		$settings['image_txt']     = isset( $_POST['image_txt'] ) ? sanitize_text_field( wp_unslash( $_POST['image_txt'] ) ) : $settings['image_txt'];
		$settings['position']      = isset( $_POST['position'] ) ? sanitize_text_field( wp_unslash( $_POST['position'] ) ) : $settings['position'];
		$settings['button_size']   = isset( $_POST['button_size'] ) ? sanitize_text_field( wp_unslash( $_POST['button_size'] ) ) : $settings['button_size'];

		$this->save_image_settings_to_db( $template, $settings );

		return true;
	}

	/**
	 * Prepare tweet settings for save
	 *
	 * @return bool
	 */
	public function save_tweet_settings() {

		$settings = $this->get_tweet_settings();
		$settings['twitter']     = isset( $_POST['twitter'] ) ? sanitize_text_field( wp_unslash( $_POST['twitter'] ) ) : $settings['twitter'];
		$settings['follow']      = isset( $_POST['follow'] ) ? sanitize_text_field( wp_unslash( $_POST['follow'] ) ) : $settings['follow'];
		$settings['preposition'] = isset( $_POST['preposition'] ) ? sanitize_text_field( wp_unslash( $_POST['preposition'] ) ) : $settings['preposition'];

		unset( $settings['shortener'], $settings['bitly_account'], $settings['bitly_token'] );

		$this->save_tweet_settings_to_db( $settings );

		return true;
	}

	/**
	 * Get presets names and default preset
	 *
	 * @param array  $settings Settings array.
	 * @param string $default  Default preset key.
	 * @return array
	 */
	private function get_presets_and_default( $settings, $default = 'box_01' ) {

		$presets = array(
			'presets' => array(),
			'default' => $default,
		);

		if ( ! is_array( $settings ) ) {
			return $presets;
		}

		foreach ( $settings as $key => $value ) {

			$presets['presets'][] = $key;

			if ( is_array( $value ) && ( $value['default'] ?? false ) === true ) {
				$presets['default'] = $key;
			}

		}

		return $presets;

	}

	/**
	 * Change default tweet box
	 *
	 * @param string $preset New default box preset.
	 */
	private function change_default_box( $preset ) {

		$current = $this->get_default_box();
		if ( isset( $this->box_settings[ $current ] ) ) {
			$this->box_settings[ $current ]['default'] = false;
		}
		$this->default_box = $preset;

	}

	/**
	 * Change selected image template
	 *
	 * @param string $template New default image template.
	 */
	private function change_default_image( $template ) {

		$current = $this->get_default_image();
		if ( isset( $this->image_settings[ $current ] ) ) {
			$this->image_settings[ $current ]['default'] = false;
		}
		if ( isset( $this->image_settings[ $template ] ) ) {
			$this->image_settings[ $template ]['default'] = true;
		}
		$this->default_image = $template;

	}

	/**
	 * Save box settings
	 *
	 * @param string $preset   Preset to update.
	 * @param array  $settings Settings to save.
	 */
	private function save_box_settings_to_db( $preset, $settings ) {

		$this->box_settings[ $preset ] = $settings;

		if ( ( $settings['default'] ?? false ) === true && $this->get_default_box() !== $preset ) {
			$this->change_default_box( $preset );
		}

		update_option( 'tweetdis_box', $this->box_settings );
	}

	/**
	 * Save author data
	 *
	 * @param array $author Author data to save.
	 */
	private function save_author_settings_to_db( $author ) {

		$this->tweet_author = $author;
		update_option( 'tweetdis_tweet_author', $this->tweet_author );

	}

	/**
	 * Save hint settings
	 *
	 * @param array $settings Settings to save.
	 */
	private function save_hint_settings_to_db( $settings ) {

		$this->hint_settings = $settings;
		update_option( 'tweetdis_hint', $this->hint_settings );
	}

	/**
	 * Save image settings
	 *
	 * @param string $template Template to update.
	 * @param array  $settings Settings to save.
	 */
	private function save_image_settings_to_db( $template, $settings ) {

		$this->image_settings[ $template ] = $settings;

		if ( $this->get_default_image() !== $template ) {
			$this->change_default_image( $template );
		}

		update_option( 'tweetdis_image', $this->image_settings );
	}

	/**
	 * Save tweet settings
	 *
	 * @param array $settings Settings to save.
	 */
	private function save_tweet_settings_to_db( $settings ) {

		$this->tweet_settings = $settings;
		update_option( 'tweetdis_tweet_settings', $this->tweet_settings );

	}

	/**
	 * Replace legacy Twitter CTA copy with share wording.
	 *
	 * @param string $text Call to action text.
	 * @return string
	 */
	private function normalize_call_to_action( $text ) {

		if ( ! is_string( $text ) ) {
			return 'Click to share';
		}

		$normalized = strtolower( trim( $text ) );

		if ( $normalized === 'click to tweet' ) {
			return 'Click to share';
		}

		if ( $normalized === 'tweet' ) {
			return 'Share';
		}

		return $text;
	}

	/**
	 * Get an option that must be an array
	 *
	 * @param string $name    Option name.
	 * @param array  $default Default value.
	 * @return array
	 */
	private function get_option_array( $name, $default ) {

		$value = get_option( $name, $default );
		return is_array( $value ) ? $value : $default;

	}

	/**
	 * Default hint settings
	 *
	 * @return array
	 */
	private function default_hint_settings() {
		return array(
			'style' => 'background',
			'color' => 1,
		);
	}

	/**
	 * Default tweet author settings
	 *
	 * @return array
	 */
	private function default_tweet_author() {
		return array(
			'author'     => '',
			'author_pic' => plugin_dir_url( dirname( __FILE__ ) ) . 'assets/images/timface.jpeg',
		);
	}

	/**
	 * Default tweet settings
	 *
	 * @return array
	 */
	private function default_tweet_settings() {
		return array(
			'twitter'     => '',
			'follow'      => '',
			'preposition' => 'none',
		);
	}

}
