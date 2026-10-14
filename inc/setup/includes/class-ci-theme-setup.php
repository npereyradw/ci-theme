<?php
/**
 * Theme supports, base stylesheet (front end and editor), font preload and head clean-up.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Setup' ) ) {

	/**
	 * Registers what every page needs; sections load their own assets from their blocks.
	 */
	class CI_Theme_Setup {

		const BASE_HANDLE = 'ci-theme-base';

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_action( 'after_setup_theme', array( __CLASS__, 'supports' ) );
			add_action( 'init', array( __CLASS__, 'register_base_style' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_base_style' ) );
			add_action( 'wp_head', array( __CLASS__, 'preload_font' ), 1 );
		}

		/**
		 * Theme supports, translations and editor stylesheet.
		 */
		public static function supports() {
			load_theme_textdomain( 'ci-theme', CI_THEME_DIR . 'languages' );
			add_theme_support( 'editor-styles' );
			add_theme_support( 'responsive-embeds' );
			add_theme_support( 'html5', array( 'script', 'style', 'navigation-widgets' ) );
			add_theme_support(
				'custom-logo',
				array(
					'flex-width'  => true,
					'flex-height' => true,
				)
			);
			remove_theme_support( 'core-block-patterns' );
			add_editor_style( 'assets/css/base.css' );
		}

		/**
		 * Base stylesheet handle; block styles build on it.
		 */
		public static function register_base_style() {
			wp_register_style(
				self::BASE_HANDLE,
				CI_THEME_URI . 'assets/css/base.css',
				array(),
				ci_theme_asset_version( 'assets/css/base.css' )
			);
		}

		/**
		 * Front end: base stylesheet on every page.
		 */
		public static function enqueue_base_style() {
			wp_enqueue_style( self::BASE_HANDLE );
		}

		/**
		 * Preloads the variable font (headings are the LCP and must not wait for it) and sets the
		 * browser bar color on phones from the theme palette.
		 */
		public static function preload_font() {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( CI_THEME_URI . 'assets/fonts/geist/Geist-Variable.woff2' )
			);
			$color = '';
			foreach ( (array) wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $entry ) {
				if ( isset( $entry['slug'], $entry['color'] ) && 'navy' === $entry['slug'] ) {
					$color = $entry['color'];
				}
			}
			if ( $color ) {
				printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ) );
			}
		}
	}
}
