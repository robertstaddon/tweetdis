<?php

/**
 * Tweetdis Image
 *
 * @package    tweetdis
 * @subpackage tweetdis/includes/entities
 */

/**
 * Image properties and functions
 */
class Tweetdis_Image extends Tweetdis_Entity {

	/**
	 * Image URL
	 *
	 * @var string
	 */
	private $image_url;

	/**
	 * Image align class
	 *
	 * @var string
	 */
	private $image_class;

	/**
	 * Initialize the class and set image parameters
	 *
	 * @param string $phrase    Image markup.
	 * @param array  $atts      Shortcode attributes.
	 * @param string $shortcode Shortcode name.
	 */
	public function __construct( $phrase, $atts, $shortcode ) {

		parent::__construct( $phrase, false );

		$params       = shortcode_atts(
			array(
				'url'    => '',
				'inject' => '',
			),
			$atts,
			$shortcode
		);
		$this->params = $this->prepare_image_params( $params );

	}

	/**
	 * Prepare image html for page and feed
	 *
	 * @return string
	 */
	public function display() {

		$image_url = $this->parse_image_link();
		if ( ! $image_url ) {
			return '';
		}

		$this->get_link_sections( $image_url );
		$tweet_link = $this->make_tweet_link();

		$comment = "<!--'Made with TweetDis plugin for Wordpress'-->";

		if ( is_feed() ) {
			$layout = $comment . '<img src="' . esc_url( $this->image_url ) . '"/>
                        <br><a href="' . esc_url( $tweet_link ) . '" target="_blank">[tweet this image]</a><br>';
		} else {
			$image_template_settings = $this->settings->get_image_settings();
			$image_template_name     = $this->settings->get_default_image();
			$image_url               = $this->image_url;
			$image_class             = $this->image_class;

			ob_start();
			echo $comment;
			include 'view/image-view.php';
			$layout = ob_get_clean();
		}

		return $this->remove_eol_and_spaces( $layout );

	}

	/**
	 * Prepare image html for template selected in settings
	 *
	 * @param string $image_template_name Image template to show.
	 * @return string
	 */
	public function display_custom( $image_template_name ) {

		$image_url   = $this->phrase;
		$image_class = 'aligncenter';

		$image_template_settings = $this->settings->get_image_settings( $image_template_name );
		$settings_page           = true;

		ob_start();
		include 'view/image-view.php';
		$layout = ob_get_clean();
		return $this->remove_eol_and_spaces( $layout );

	}

	/**
	 * Get tweet link sections: 'phrase', 'hidden'
	 *
	 * @param string $image_share_url Image url to include in the tweet.
	 */
	private function get_link_sections( $image_share_url ) {

		$space_after = false;

		$tweet_settings = $this->settings->get_tweet_settings();
		if ( ( $tweet_settings['twitter'] ?? '' ) !== '' ) {

			$tweet_reference = $this->make_tweet_reference( $tweet_settings );
			$space_after     = ( $tweet_reference['link_section'] === 'reference_after' ) ? $space_after : true;
			if ( ! $this->add_to_link( $tweet_reference['reference'], $tweet_reference['link_section'], $space_after ) ) {
				return;
			}
			$space_after = ! $space_after;
		}

		$this->add_to_link( $this->tweet_url(), 'url', $space_after );

		$settings = $this->settings->get_image_settings();
		if ( ( $settings['image_txt'] ?? 'blank' ) !== 'blank' ) {

			if ( $settings['image_txt'] === 'image_alt' ) {
				$image_txt = $this->parse_image_alt();
			} else {
				$image_txt = get_the_title();
			}

			$image_txt = $this->shorten_text( is_string( $image_txt ) ? $image_txt : '' );
			if ( ! $this->add_to_link( $image_txt, 'image_txt' ) ) {
				return;
			}
		}

		if ( ( $this->params['hidden'] ?? '' ) !== '' ) {
			$tweet_hidden = $this->shorten_text( $this->params['hidden'] );
			if ( ! $this->add_to_link( $tweet_hidden, 'hidden' ) ) {
				return;
			}
		}

		if ( $this->tweet_length > 10 ) {
			$phrase = html_entity_decode( $image_share_url );
			$this->add_phrase( $phrase );
		}

	}

	/**
	 * Parse image tag and return the site image URL
	 *
	 * @return string|false
	 */
	private function parse_image_link() {

		if ( ! preg_match( '/<img[^>]+src\s*=\s*["\']([^"\']+)["\']/i', $this->phrase, $img_tag ) ) {
			return false;
		}

		$this->image_url   = $img_tag[1];
		$this->image_class = 'aligncenter';

		if ( preg_match( '/<img[^>]+class\s*=\s*["\']([^"\']+)["\']/i', $this->phrase, $img_class ) ) {
			$class = $img_class[1];
			foreach ( array( 'alignnone', 'alignleft', 'aligncenter', 'alignright' ) as $align ) {
				if ( strpos( $class, $align ) !== false ) {
					$this->image_class = $align;
					break;
				}
			}
		}

		return $this->validate_url( $this->image_url ) ? $this->image_url : false;

	}

	/**
	 * Parse image alt attribute
	 *
	 * @return string
	 */
	private function parse_image_alt() {

		if ( ! preg_match( '/<img[^>]+alt\s*=\s*["\']([^"\']*)["\']/i', $this->phrase, $alt ) ) {
			return '';
		}

		return $alt[1];

	}

	/**
	 * Prepare image parameters
	 *
	 * @param array $params Shortcode parameters.
	 * @return array
	 */
	private function prepare_image_params( $params ) {

		$params['hidden'] = $params['inject'];
		unset( $params['inject'] );

		return $params;
	}

}
