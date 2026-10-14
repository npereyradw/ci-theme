<?php
/**
 * Breadcrumbs: Home → listing page → ancestors → current page, with the BreadcrumbList
 * structured data search engines read. Items can be changed with the "ci_theme_breadcrumbs"
 * filter.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Breadcrumbs' ) ) {

	/**
	 * Breadcrumbs.
	 */
	class CI_Theme_Breadcrumbs {

		/**
		 * Items for the current request: [ [ label, url ], … ], the last one without URL.
		 *
		 * @param string       $current_label Replaces the label of the current page.
		 * @param WP_Post|null $post          Post of a template block preview (no main query).
		 * @return array<int, array{0:string, 1:string}>
		 */
		public static function items( $current_label = '', $post = null ) {
			$items = array( array( __( 'Home', 'ci-theme' ), home_url( '/' ) ) );
			$post  = $post ? $post : get_post();
			$news  = (int) get_option( 'page_for_posts' );

			if ( is_home() && ! is_front_page() ) {
				$items[] = array( get_the_title( $news ), '' );
			} elseif ( is_post_type_archive() ) {
				$items[] = array( post_type_archive_title( '', false ), '' );
			} elseif ( is_category() ) {
				// News categories hang from the posts page.
				if ( $news ) {
					$items[] = array( get_the_title( $news ), get_permalink( $news ) );
				}
				$items[] = array( single_term_title( '', false ), '' );
			} elseif ( is_tax() ) {
				$items[] = array( single_term_title( '', false ), '' );
			} elseif ( $post ) {
				// Services, projects and news: the page that lists them goes first.
				$parent = self::listing( $post->post_type );
				if ( $parent ) {
					$items[] = $parent;
				}
				foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
					$items[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
				}
				$items[] = array( get_the_title( $post ), '' );
			}

			if ( '' !== $current_label && count( $items ) > 1 ) {
				$items[ count( $items ) - 1 ][0] = $current_label;
			}

			/**
			 * Filters the breadcrumb items.
			 *
			 * @param array $items [ [ label, url ], … ].
			 */
			return apply_filters( 'ci_theme_breadcrumbs', $items );
		}

		/**
		 * Page that lists the posts of a type: [ label, url ], or null.
		 *
		 * @param string $type Post type.
		 * @return array{0:string, 1:string}|null
		 */
		private static function listing( $type ) {
			if ( CI_Theme_Content::SERVICE === $type ) {
				$page = CI_Theme_Routes::services_page();
				return $page ? array( get_the_title( $page ), get_permalink( $page ) ) : null;
			}
			if ( 'post' === $type ) {
				$news = (int) get_option( 'page_for_posts' );
				return $news ? array( get_the_title( $news ), get_permalink( $news ) ) : null;
			}
			$object = get_post_type_object( $type );
			$url    = get_post_type_archive_link( $type );
			return $object && $url ? array( $object->labels->name, $url ) : null;
		}

		/**
		 * Markup and structured data.
		 *
		 * @param array $args {
		 *     label: string current label, css: bool entrance by CSS (first screen),
		 *     post: WP_Post|null post of a template block preview.
		 * }
		 * @return string
		 */
		public static function render( $args = array() ) {
			$args  = wp_parse_args(
				$args,
				array(
					'label' => '',
					'css'   => true,
					'post'  => null,
				)
			);
			$items = self::items( $args['label'], $args['post'] );
			if ( count( $items ) < 2 ) {
				return '';
			}

			$last = count( $items ) - 1;
			$list = '';
			$ld   = array();
			foreach ( $items as $i => $item ) {
				$list .= $i === $last || '' === $item[1]
					? '<li><span' . ( $i === $last ? ' aria-current="page"' : '' ) . '>' . esc_html( $item[0] ) . '</span></li>'
					: '<li><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a></li>';
				$entry = array(
					'@type'    => 'ListItem',
					'position' => $i + 1,
					'name'     => wp_strip_all_tags( $item[0] ),
				);
				if ( '' !== $item[1] ) {
					$entry['item'] = $item[1];
				}
				$ld[] = $entry;
			}

			$json = wp_json_encode(
				array(
					'@context'        => 'https://schema.org',
					'@type'           => 'BreadcrumbList',
					'itemListElement' => $ld,
				),
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
			);

			return sprintf(
				'<nav aria-label="%1$s" class="ci-breadcrumbs"%2$s><ol>%3$s</ol></nav>%4$s',
				esc_attr__( 'Breadcrumbs', 'ci-theme' ),
				$args['css'] ? ' data-anim-css="rise"' : '',
				$list,
				wp_get_inline_script_tag( $json, array( 'type' => 'application/ld+json' ) )
			);
		}
	}
}
