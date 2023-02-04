<?php
/**
 * Translation helper functions
 *
 * @link       https://www.cookieyes.com/
 * @since      3.0.0
 * @package    CookieYes\Lite\Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'cky_default_language' ) ) {

	/**
	 * Check if a request is a rest request
	 *
	 * @return string
	 */
	function cky_default_language() {
		$settings = get_option( 'cky_settings' );
		return isset( $settings['languages']['default'] ) ? cky_sanitize_text( $settings['languages']['default'] ) : 'en';
	}
}
if ( ! function_exists( 'cky_selected_languages' ) ) {

	/**
	 * Check if a request is a rest request
	 *
	 * @param string $language Language to add temporarily to the existing list.
	 * @return array
	 */
	function cky_selected_languages( $language = '' ) {
		$settings  = get_option( 'cky_settings' );
		$languages = isset( $settings['languages']['selected'] ) ? cky_sanitize_text( $settings['languages']['selected'] ) : array();
		if ( ! in_array( cky_default_language(), $languages, true ) ) {
			array_push( $languages, cky_default_language() );
		}
		if ( '' !== $language && ! in_array( $language, $languages, true ) ) {
			array_push( $languages, $language );
		}
		return $languages;
	}
}

if ( ! function_exists( 'cky_i18n_is_multilingual' ) ) {

	/**
	 * Return true if multilingual plugin is active
	 *
	 * @return boolean
	 */
	function cky_i18n_is_multilingual() {
		$status = false;

		if ( defined( 'ICL_LANGUAGE_CODE' ) || defined( 'POLYLANG_FILE' ) ) {
			$status = true;
		}
		return $status;
	}
}

if ( ! function_exists( 'cky_current_language' ) ) {
	/**
	 * Returns the current language code of the site
	 *
	 * @return string
	 */
	function cky_current_language() {
		$current_language = null;

		if ( cky_i18n_is_multilingual() ) {
			// If the plugin used is Polylang.
			if ( function_exists( 'pll_current_language' ) ) {

				$current_language = pll_current_language();
				// If current_language is still empty, we have to get the default language.
				if ( empty( $current_language ) ) {
					$current_language = pll_default_language();
				}
			} else {
				// If the plugin used is WPML.
				$null             = null;
				$current_language = apply_filters( 'wpml_current_language', $null );
			}

			// Fallback if neither WPML nor Polylang is used.
			if ( 'all' === $current_language ) {
				$current_language = cky_default_language();
			}
		} else {
			$current_language = cky_default_language();
		}
		$map              = cky_get_lang_map();
		$current_language = isset( $map[ $current_language ] ) ? $map[ $current_language ] : $current_language;
		if ( in_array( $current_language, cky_selected_languages(), true ) === false ) {
			$current_language = cky_default_language();
		}
		return apply_filters( 'cky_current_language', $current_language );
	}
}

if ( ! function_exists( 'cky_get_lang_map' ) ) {
	/**
	 * Returns the current language code of the site
	 *
	 * @return string
	 */
	function cky_get_lang_map() {
		$map = array(
			'pt-pt' => 'pt',
		);

		return apply_filters( 'cky_language_map', $map );
	}
}
