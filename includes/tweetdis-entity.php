<?php

/**
 * Tweetdis Entity
 *
 * @package    tweetdis
 * @subpackage tweetdis/includes
 */

/**
 * Base class for plugin entities
 */
abstract class Tweetdis_Entity {

	/**
	 * Tweet length
	 *
	 * @var int
	 */
	protected $tweet_length;

	/**
	 * Tweet link segments
	 *
	 * @var array
	 */
	protected $link;

	/**
	 * Shortcode attributes
	 *
	 * @var array
	 */
	protected $params;

	/**
	 * Tweet intent url
	 *
	 * @var string
	 */
	protected $tweet_intent;

	/**
	 * Plugin settings
	 *
	 * @var Tweetdis_Settings
	 */
	protected $settings;

	/**
	 * Phrase to tweet
	 *
	 * @var string
	 */
	protected $phrase;

	/**
	 * Is multibyte module enabled
	 *
	 * @var bool
	 */
	protected $mb_enabled;

	/**
	 * Initialize the class
	 *
	 * @param string $phrase Phrase to tweet.
	 * @param bool   $clean  If phrase should be cleaned.
	 */
	protected function __construct( $phrase, $clean = true ) {

		$this->tweet_length = 280 - 24;
		$this->tweet_intent = 'https://twitter.com/intent/tweet?text=';
		$this->settings     = Tweetdis_Settings::get_instance();

		$phrase = is_string( $phrase ) ? $phrase : '';

		if ( $clean ) {
			$this->phrase = strip_tags( html_entity_decode( $phrase, ENT_QUOTES, 'UTF-8' ) );
		} else {
			$this->phrase = $phrase;
		}

		$this->link = array(
			'reference_before' => '',
			'image_txt'        => '',
			'phrase'           => '',
			'author'           => '',
			'hidden'           => '',
			'url'              => '',
			'reference_after'  => '',
			'custom'           => '',
		);
		$this->mb_enabled = function_exists( 'mb_internal_encoding' );

	}

	/**
	 * Display Tweetdis_Entity
	 */
	abstract public function display();

	/**
	 * Compose tweet link
	 *
	 * @return string
	 */
	protected function make_tweet_link() {

		$tweet_link = $this->link['reference_before'] . $this->link['image_txt'] . $this->link['phrase'] .
			$this->link['author'] . $this->link['hidden'] . $this->link['custom'] .
			$this->link['url'] . $this->link['reference_after'];

		$recommend_to_follow = $this->settings->get_tweet_settings( 'follow' );
		if ( is_string( $recommend_to_follow ) && $recommend_to_follow !== '' ) {
			$tweet_link .= '&related=' . $recommend_to_follow;
		}

		return $this->tweet_intent . $tweet_link;

	}

	/**
	 * Get tweet link sections: 'reference_before'/ 'reference_after', 'custom'
	 *
	 * @return bool True if enough space left for next sections.
	 */
	protected function get_first_link_sections() {

		if ( isset( $this->params['custom'] ) && $this->params['custom'] !== '' ) {
			$tweet_custom = $this->shorten_text( $this->params['custom'] );
			$this->add_to_link( $tweet_custom, 'custom' );
			$this->add_to_link( $this->tweet_url(), 'url', false );
			return false;
		}

		$space_after = false;

		$tweet_settings = $this->settings->get_tweet_settings();
		if ( ( $tweet_settings['twitter'] ?? '' ) !== '' ) {
			$tweet_reference = $this->make_tweet_reference( $tweet_settings );
			$space_after     = ( $tweet_reference['link_section'] === 'reference_after' ) ? $space_after : true;
			$this->add_to_link( $tweet_reference['reference'], $tweet_reference['link_section'], $space_after );
			$space_after = ! $space_after;
		}

		$this->add_to_link( $this->tweet_url(), 'url', $space_after );

		return true;
	}

	/**
	 * Adds phrase to link
	 *
	 * @param string $phrase Phrase to add.
	 */
	protected function add_phrase( $phrase ) {

		$phrase = is_string( $phrase ) ? $phrase : '';
		$phrase = ( $this->mb_enabled ) ? mb_convert_encoding( $phrase, 'UTF-8', 'UTF-8' ) : $phrase;
		$phrase = $this->shorten_text( $phrase );
		$this->add_to_link( $phrase, 'phrase' );

	}

	/**
	 * Shorten the text to match tweet max length
	 *
	 * @param string $text Text to be shortened.
	 * @return string
	 */
	protected function shorten_text( $text ) {

		$text = is_string( $text ) ? $text : '';

		if ( $this->mb_enabled && ( mb_strlen( $text, 'UTF-8' ) > $this->tweet_length - 1 ) ) {

			$text = mb_substr( $text, 0, $this->tweet_length - 3, 'UTF-8' );

			$last_space = mb_strrpos( $text, ' ', 0, 'UTF-8' );
			if ( false !== $last_space ) {
				$text = mb_substr( $text, 0, $last_space, 'UTF-8' );
			}

			$text .= ( mb_substr( $text, -1, 1, 'UTF-8' ) === '.' ) ? '..' : '...';

		} elseif ( ! $this->mb_enabled && ( strlen( $text ) > $this->tweet_length - 1 ) ) {

			$text = substr( $text, 0, $this->tweet_length - 3 );

			$last_space = strrpos( $text, ' ' );
			if ( false !== $last_space ) {
				$text = substr( $text, 0, $last_space );
			}

			$text .= ( substr( $text, -1, 1 ) === '.' ) ? '..' : '...';
		}

		return $text;

	}

	/**
	 * Add value to link section and recalculate length
	 *
	 * @param string $add         Text to add.
	 * @param string $part        Name of the link section to add.
	 * @param bool   $space_after Add space after section.
	 * @return bool True if it is possible to add more text.
	 */
	protected function add_to_link( $add, $part, $space_after = true ) {

		$add = is_string( $add ) ? $add : '';

		if ( $space_after ) {
			$add .= ' ';
		}

		if ( $part !== 'url' ) {
			$this->tweet_length -= ( $this->mb_enabled ) ? mb_strlen( $add, 'UTF-8' ) : strlen( $add );
		}

		$this->link[ $part ] = rawurlencode( html_entity_decode( $add ) );

		if ( $this->tweet_length <= 3 ) {
			return false;
		}

		return true;

	}

	/**
	 * Add twitter account reference
	 *
	 * @param array $tweet_settings Tweet settings array.
	 * @return array
	 */
	protected function make_tweet_reference( $tweet_settings ) {

		$tweet_reference = array(
			'link_section' => 'reference_after',
			'reference'    => '',
		);

		$preposition = $tweet_settings['preposition'] ?? 'none';

		switch ( $preposition ) {
			case 'none':
				break;
			case 'RT':
				$tweet_reference['link_section'] = 'reference_before';
				// Fall through.
			default:
				$tweet_reference['reference'] = $preposition . ' ';
				break;
		}

		$tweet_reference['reference'] .= '@' . ( $tweet_settings['twitter'] ?? '' );

		return $tweet_reference;

	}

	/**
	 * Get tweet url
	 *
	 * @return string
	 */
	protected function tweet_url() {

		$url = $this->validate_url( $this->params['url'] ?? '' );

		if ( ! $url ) {
			$permalink = get_permalink();
			return is_string( $permalink ) ? $permalink : '';
		}

		return $url;

	}

	/**
	 * Validate url
	 *
	 * @param mixed $url URL to validate.
	 * @return string|false Valid URL or false.
	 */
	protected function validate_url( $url ) {

		if ( ! is_string( $url ) || $url === '' ) {
			return false;
		}

		$parts = wp_parse_url( $url );
		if ( is_array( $parts ) && ! isset( $parts['scheme'] ) ) {
			$url = 'https://' . $url;
		}

		return filter_var( $url, FILTER_VALIDATE_URL );

	}

	/**
	 * Remove line breaks and extra whitespaces
	 *
	 * @param string $html Tweetdis output html.
	 * @return string
	 */
	protected function remove_eol_and_spaces( $html ) {

		$html = is_string( $html ) ? $html : '';
		$html = str_replace( "\n", '', $html );
		$html = str_replace( "\r", '', $html );
		return preg_replace( '/\s{2,}/', ' ', $html );

	}

}
