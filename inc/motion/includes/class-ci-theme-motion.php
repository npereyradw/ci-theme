<?php
/**
 * Registers the motion script and, only when a block on the page enqueued it, its stylesheet
 * and the tiny head flag that turns animations on before the first paint.
 *
 * In a block theme the template is rendered before wp_head, so by wp_enqueue_scripts we already
 * know whether any block asked for the script.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Motion' ) ) {

	/**
	 * Motion assets.
	 */
	class CI_Theme_Motion {

		const HANDLE = 'ci-theme-motion';
		const FLAG   = 'ci-theme-motion-flag';

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register' ), 5 );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue_head' ), 20 );
		}

		/**
		 * Shared GSAP (ci-theme-gsap) and the section animations, both deferred in the footer,
		 * plus the stylesheet.
		 */
		public static function register() {
			foreach ( array( 'ci-theme-gsap' => 'gsap', self::HANDLE => 'motion' ) as $handle => $dir_name ) {
				$dir   = CI_THEME_DIR . 'build/' . $dir_name . '/';
				$asset = file_exists( $dir . 'index.asset.php' ) ? require $dir . 'index.asset.php' : null;
				if ( ! $asset ) {
					continue;
				}
				wp_register_script(
					$handle,
					CI_THEME_URI . 'build/' . $dir_name . '/index.js',
					$asset['dependencies'],
					$asset['version'],
					array(
						'in_footer' => true,
						'strategy'  => 'defer',
					)
				);
			}
			wp_register_style(
				self::HANDLE,
				CI_THEME_URI . 'inc/motion/assets/css/motion.css',
				array(),
				ci_theme_asset_version( 'inc/motion/assets/css/motion.css' )
			);
		}

		/**
		 * If a block enqueued the script: stylesheet + head flag. The flag adds html.ci-motion
		 * (elements start hidden) unless the visitor asks for reduced motion, and removes it
		 * after 1.2 s if the script never got ready, so content is never left hidden.
		 */
		public static function maybe_enqueue_head() {
			if ( ! wp_script_is( self::HANDLE, 'enqueued' ) ) {
				return;
			}
			wp_enqueue_style( self::HANDLE );

			wp_register_script( self::FLAG, false, array(), CI_THEME_VERSION, false );
			wp_enqueue_script( self::FLAG );
			wp_add_inline_script(
				self::FLAG,
				"(function(d){if(!window.matchMedia||matchMedia('(prefers-reduced-motion: reduce)').matches)return;d.classList.add('ci-motion');setTimeout(function(){if(!d.classList.contains('ci-motion-ready'))d.classList.remove('ci-motion')},1200)})(document.documentElement);"
			);
		}
	}
}
