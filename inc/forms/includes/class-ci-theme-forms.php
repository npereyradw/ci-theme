<?php
/**
 * Forms with the theme's own markup (contact and "notify me") sent to Contact Form 7 through its
 * REST endpoint. Contact Form 7 keeps sending the mail and storing the messages; its own scripts
 * and styles are not loaded. Spam is filtered with a honeypot field.
 *
 * The client script reads its configuration from data-* attributes of the form (no inline JS).
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Forms' ) ) {

	/**
	 * Form helpers.
	 */
	class CI_Theme_Forms {

		const HANDLE = 'ci-theme-forms';

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_filter( 'wpcf7_load_js', '__return_false' );
			add_filter( 'wpcf7_load_css', '__return_false' );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'remove_cf7_recaptcha' ), 1 );
			add_action( 'init', array( __CLASS__, 'register' ), 5 );
		}

		/**
		 * The forms do not use reCAPTCHA: Google's script never loads, even if keys are added in
		 * Contact Form 7 → Integration.
		 */
		public static function remove_cf7_recaptcha() {
			remove_action( 'wp_enqueue_scripts', 'wpcf7_recaptcha_enqueue_scripts', 20 );
		}

		/**
		 * Client script (deferred). Blocks list it as a view script.
		 */
		public static function register() {
			$dir   = CI_THEME_DIR . 'build/forms/';
			$asset = file_exists( $dir . 'index.asset.php' ) ? require $dir . 'index.asset.php' : null;
			if ( ! $asset ) {
				return;
			}
			wp_register_script(
				self::HANDLE,
				CI_THEME_URI . 'build/forms/index.js',
				$asset['dependencies'],
				$asset['version'],
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}

		/**
		 * Contact Form 7 form ID for a purpose: the one chosen in the block, or the site setting.
		 *
		 * @param string $purpose contact | notify.
		 * @param int    $own     ID chosen in the block (0 = use the site setting).
		 * @return int
		 */
		public static function form_id( $purpose, $own = 0 ) {
			$id = absint( $own );
			if ( ! $id ) {
				$site = CI_Theme_Section::site();
				$key  = 'notify' === $purpose ? 'notify_form_id' : 'contact_form_id';
				$id   = isset( $site[ $key ] ) ? (int) $site[ $key ] : 0;
			}
			return ( $id && 'wpcf7_contact_form' === get_post_type( $id ) ) ? $id : 0;
		}

		/**
		 * data-* attributes for a form element, escaped.
		 *
		 * @param int    $form_id CF7 form ID.
		 * @param string $action  Form name for analytics (letters, numbers, _).
		 * @return string
		 */
		public static function data_attributes( $form_id, $action ) {
			wp_enqueue_script( self::HANDLE );
			$attrs = array(
				'data-ci-form'  => $action,
				'data-endpoint' => rest_url( 'contact-form-7/v1/contact-forms/' . (int) $form_id . '/feedback' ),
				'data-form-id'  => (int) $form_id,
				'data-locale'   => determine_locale(),
			);
			$out = '';
			foreach ( $attrs as $name => $value ) {
				$out .= ' ' . $name . '="' . esc_attr( (string) $value ) . '"';
			}
			return $out;
		}
	}
}
