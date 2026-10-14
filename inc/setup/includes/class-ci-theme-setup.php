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

			// Each block loads its stylesheet only on the pages where it is used.
			add_filter( 'should_load_separate_core_block_assets', '__return_true' );

			// Theme stylesheets are printed inline: no render-blocking requests.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'allow_inline_styles' ), 100 );
			add_filter( 'styles_inline_size_limit', array( __CLASS__, 'inline_size_limit' ) );

			// Modern browsers render emoji natively; the WordPress polyfill is not needed.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'emoji_svg_url', '__return_false' );
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
		 * Marks the theme stylesheets (base and modules) as inlinable, so WordPress prints them
		 * in a <style> tag while the page total stays under the limit. Block styles already are.
		 */
		public static function allow_inline_styles() {
			$styles = wp_styles();
			foreach ( $styles->registered as $handle => $style ) {
				if ( 0 !== strpos( $handle, 'ci-theme-' ) || ! is_string( $style->src ) || 0 !== strpos( $style->src, CI_THEME_URI ) || $styles->get_data( $handle, 'path' ) ) {
					continue;
				}
				$path = CI_THEME_DIR . substr( $style->src, strlen( CI_THEME_URI ) );
				if ( is_readable( $path ) ) {
					wp_style_add_data( $handle, 'path', $path );
				}
			}
		}

		/**
		 * The CSS of a full page (theme + blocks) is about 50 KB before compression.
		 *
		 * @return int Bytes.
		 */
		public static function inline_size_limit() {
			return 80000;
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
			$color = CI_Theme_Section::hex( 'navy' );
			if ( $color ) {
				printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ) );
			}
		}
	}
}
