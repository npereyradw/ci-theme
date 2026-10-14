<?php
/**
 * Site logo: the logo set in Site Identity (or the Site Logo block), with an optional version for
 * dark backgrounds chosen in the header and footer blocks. Without a logo, the site title is
 * shown as text.
 *
 * The image is decorative: the link around it carries the accessible name.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Logo' ) ) {

	/**
	 * Logo markup.
	 */
	class CI_Theme_Logo {

		/**
		 * Attachment ID of the site logo, or 0.
		 *
		 * @return int
		 */
		public static function site_logo_id() {
			$id = (int) get_theme_mod( 'custom_logo' );
			if ( ! $id ) {
				$id = (int) get_option( 'site_logo' );
			}
			return $id && wp_attachment_is_image( $id ) ? $id : 0;
		}

		/**
		 * Accessible name of the home link: "Site name — Home".
		 *
		 * @return string
		 */
		public static function home_label() {
			/* translators: %s: site title. */
			return sprintf( __( '%s — Home', 'ci-theme' ), get_bloginfo( 'name' ) );
		}

		/**
		 * Logo markup.
		 *
		 * @param array $args {
		 *     @type bool $dark    Dark background: use the dark version when there is one.
		 *     @type int  $dark_id Attachment ID of the version for dark backgrounds.
		 *     @type int  $height  Height in px.
		 * }
		 * @return string
		 */
		public static function render( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'dark'    => false,
					'dark_id' => 0,
					'height'  => 40,
				)
			);

			$id = self::site_logo_id();
			if ( $args['dark'] && $args['dark_id'] && wp_attachment_is_image( (int) $args['dark_id'] ) ) {
				$id = (int) $args['dark_id'];
			}
			$style = '--ci-logo-height:' . (int) $args['height'] . 'px';

			if ( ! $id ) {
				return sprintf(
					'<span class="ci-logo ci-logo--text%1$s" style="%2$s">%3$s</span>',
					$args['dark'] ? ' ci-logo--dark' : '',
					esc_attr( $style ),
					esc_html( get_bloginfo( 'name' ) )
				);
			}

			return sprintf(
				'<span class="ci-logo" style="%1$s">%2$s</span>',
				esc_attr( $style ),
				wp_get_attachment_image(
					$id,
					'full',
					false,
					array(
						'class'    => 'ci-logo-image',
						'alt'      => '',
						'loading'  => 'eager',
						'decoding' => 'async',
					)
				)
			);
		}

		/**
		 * Site icon as a small round avatar (e.g. the author of the news), or ''.
		 *
		 * @param int $size Size in px.
		 * @return string
		 */
		public static function icon( $size = 36 ) {
			$url = get_site_icon_url( $size * 2 );
			if ( ! $url ) {
				return '';
			}
			return sprintf(
				'<img src="%1$s" width="%2$d" height="%2$d" alt="" class="ci-logo-icon" loading="lazy" decoding="async">',
				esc_url( $url ),
				(int) $size
			);
		}
	}
}
