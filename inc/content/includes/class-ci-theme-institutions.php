<?php
/**
 * Institutions list: logos of professional boards and associations. Used by the footer and
 * the Institutions block. It enqueues its own stylesheet ("ci-theme-institutions").
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Institutions' ) ) {

	/**
	 * Institutions list markup.
	 */
	class CI_Theme_Institutions {

		const LOGO_MAX_W = 120;
		const LOGO_MAX_H = 40;

		/**
		 * Render the list.
		 *
		 * @param array $args { dark: bool, compact: bool (logos in one row on phones) }.
		 * @return string
		 */
		public static function render( $args = array() ) {
			$items = CI_Theme_Content::institutions();
			if ( ! $items ) {
				return '';
			}
			$args = wp_parse_args(
				$args,
				array(
					'dark'    => false,
					'compact' => false,
				)
			);
			wp_enqueue_style( 'ci-theme-institutions' );

			$class = 'ci-institutions' . ( $args['dark'] ? ' ci-institutions--dark' : '' ) . ( $args['compact'] ? ' ci-institutions--compact' : '' );
			$html  = '<ul data-anim-stagger class="' . esc_attr( $class ) . '">';

			foreach ( $items as $item ) {
				$body = self::logo( $item['logo'] )
					. '<span class="ci-institutions-acronym">' . esc_html( $item['acronym'] ) . '</span>'
					. '<span class="ci-institutions-name">' . esc_html( $item['name'] ) . '</span>';

				if ( '' !== $item['url'] ) {
					$body = sprintf(
						'<a href="%1$s" class="ci-institutions-link" target="_blank" rel="noopener noreferrer" title="%2$s">%3$s<span class="visually-hidden"> %4$s</span></a>',
						esc_url( $item['url'] ),
						esc_attr( $item['full_name'] ),
						$body,
						esc_html__( '(opens in a new tab)', 'ci-theme' )
					);
				}
				$html .= '<li class="ci-institutions-item">' . $body . '</li>';
			}

			return $html . '</ul>';
		}

		/**
		 * Logo with its final size set in advance (no layout shift), or a placeholder.
		 *
		 * @param int $id Attachment ID.
		 * @return string
		 */
		private static function logo( $id ) {
			$src = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
			if ( ! $src ) {
				return '<span class="ci-institutions-label" aria-hidden="true">' . esc_html__( 'Logo', 'ci-theme' ) . '</span>';
			}
			list( $url, $w, $h ) = $src;
			$size = '';
			if ( $w >= 16 && $h >= 16 ) {
				$k    = min( 1, self::LOGO_MAX_W / $w, self::LOGO_MAX_H / $h );
				$size = sprintf( ' width="%1$d" height="%2$d" style="width:%1$dpx;height:%2$dpx"', round( $w * $k ), round( $h * $k ) );
			}
			return sprintf( '<img src="%1$s" alt="" loading="lazy" decoding="async"%2$s class="ci-institutions-logo">', esc_url( $url ), $size );
		}
	}
}
