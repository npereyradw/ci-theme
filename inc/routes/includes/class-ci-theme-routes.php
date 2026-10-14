<?php
/**
 * Filtered listings with their own URL, so each filter can be shared and indexed:
 *
 * - {services page}/area/{slug}/       The services page, filtered by area (query var ci_area).
 *                                      The services page is the page whose path matches the
 *                                      rewrite slug of the service post type.
 * - {projects archive}/service/{slug}/ The projects archive, filtered by service (ci_service).
 * - {posts page}/page/{n}/             Pages of the posts page. When the permalink structure
 *                                      starts with the posts page (/news/%postname%/), WordPress
 *                                      would read "page" as the slug of a post.
 *
 * The "area" and "service" URL segments are translatable. An unknown slug is a 404. The listing
 * blocks read the query vars to render the filtered list.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Routes' ) ) {

	/**
	 * Rewrite rules, query vars, URLs and validation.
	 */
	class CI_Theme_Routes {

		const AREA    = 'ci_filter_area';
		const SERVICE = 'ci_filter_service';

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'rewrite_rules' ), 20 );
			add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
			add_action( 'template_redirect', array( __CLASS__, 'validate' ), 1 );
			add_action( 'after_switch_theme', 'flush_rewrite_rules' );
			// The rules depend on these options: rebuild them on the next request.
			add_action( 'update_option_page_for_posts', array( __CLASS__, 'reset_rules' ) );
			add_action( 'update_option_permalink_structure', array( __CLASS__, 'reset_rules' ) );
		}

		/**
		 * Forces the rewrite rules to be rebuilt on the next request.
		 */
		public static function reset_rules() {
			delete_option( 'rewrite_rules' );
		}

		/**
		 * URL segment of the area filter.
		 *
		 * @return string
		 */
		public static function area_segment() {
			return sanitize_title( _x( 'area', 'URL segment of the services filtered by area', 'ci-theme' ) );
		}

		/**
		 * URL segment of the service filter.
		 *
		 * @return string
		 */
		public static function service_segment() {
			return sanitize_title( _x( 'service', 'URL segment of the projects filtered by service', 'ci-theme' ) );
		}

		/**
		 * Page that lists the services: the page whose path is the rewrite slug of the service
		 * post type.
		 *
		 * @return WP_Post|null
		 */
		public static function services_page() {
			$type = get_post_type_object( CI_Theme_Content::SERVICE );
			$slug = $type && ! empty( $type->rewrite['slug'] ) ? $type->rewrite['slug'] : '';
			$page = '' !== $slug ? get_page_by_path( $slug ) : null;

			/**
			 * Filters the page that lists the services.
			 *
			 * @param WP_Post|null $page Page.
			 */
			$page = apply_filters( 'ci_theme_services_page', $page );
			return $page instanceof WP_Post ? $page : null;
		}

		/**
		 * URL of the services filtered by an area ('' without a services page).
		 *
		 * @param string $slug Area slug.
		 * @return string
		 */
		public static function area_url( $slug ) {
			$page = self::services_page();
			return $page ? trailingslashit( get_permalink( $page ) ) . self::area_segment() . '/' . $slug . '/' : '';
		}

		/**
		 * URL of the projects filtered by a service ('' without a projects archive).
		 *
		 * @param string $slug Service slug.
		 * @return string
		 */
		public static function service_projects_url( $slug ) {
			$archive = get_post_type_archive_link( CI_Theme_Content::PROJECT );
			return $archive ? trailingslashit( $archive ) . self::service_segment() . '/' . $slug . '/' : '';
		}

		/**
		 * Rules added before the post type rules ({services}/{slug} would catch "area").
		 */
		public static function rewrite_rules() {
			$services = self::services_page();
			if ( $services ) {
				$uri = get_page_uri( $services );
				add_rewrite_rule( '^' . preg_quote( $uri, '#' ) . '/' . self::area_segment() . '/([^/]+)/?$', 'index.php?pagename=' . $uri . '&' . self::AREA . '=$matches[1]', 'top' );
			}

			$projects = get_post_type_object( CI_Theme_Content::PROJECT );
			if ( $projects && $projects->has_archive ) {
				$archive = true === $projects->has_archive ? $projects->rewrite['slug'] : $projects->has_archive;
				add_rewrite_rule( '^' . preg_quote( $archive, '#' ) . '/' . self::service_segment() . '/([^/]+)/?$', 'index.php?post_type=' . CI_Theme_Content::PROJECT . '&' . self::SERVICE . '=$matches[1]', 'top' );
			}

			$posts_page = (int) get_option( 'page_for_posts' );
			$uri        = $posts_page ? get_page_uri( $posts_page ) : '';
			if ( $uri ) {
				add_rewrite_rule( '^' . preg_quote( $uri, '#' ) . '/page/?([0-9]{1,})/?$', 'index.php?pagename=' . $uri . '&paged=$matches[1]', 'top' );
			}
		}

		/**
		 * Public query vars.
		 *
		 * @param string[] $vars Vars.
		 * @return string[]
		 */
		public static function query_vars( $vars ) {
			$vars[] = self::AREA;
			$vars[] = self::SERVICE;
			return $vars;
		}

		/**
		 * Area slug of the current request ('' if none).
		 *
		 * @return string
		 */
		public static function area() {
			return sanitize_title( (string) get_query_var( self::AREA ) );
		}

		/**
		 * Service slug of the current request ('' if none).
		 *
		 * @return string
		 */
		public static function service() {
			return sanitize_title( (string) get_query_var( self::SERVICE ) );
		}

		/**
		 * Area of the request, if it exists.
		 *
		 * @return WP_Term|null
		 */
		public static function area_term() {
			$slug = self::area();
			if ( '' === $slug || ! taxonomy_exists( CI_Theme_Content::AREA ) ) {
				return null;
			}
			$term = get_term_by( 'slug', $slug, CI_Theme_Content::AREA );
			return $term instanceof WP_Term ? $term : null;
		}

		/**
		 * Service of the request, if it exists (see CI_Theme_Content::services()).
		 *
		 * @return array|null
		 */
		public static function service_item() {
			$slug = self::service();
			return '' !== $slug ? CI_Theme_Content::service_by_slug( $slug ) : null;
		}

		/**
		 * Unknown area, unknown service or a service without projects → 404.
		 */
		public static function validate() {
			$bad_area    = '' !== self::area() && ! self::area_term();
			$bad_service = '' !== self::service() && ( ! self::service_item() || ! CI_Theme_Content::projects(
				array(
					'service' => self::service(),
					'limit'   => 1,
				)
			) );
			if ( $bad_area || $bad_service ) {
				global $wp_query;
				$wp_query->set_404();
				// A filter that does not exist is a 404, not a redirect to a guessed URL.
				remove_action( 'template_redirect', 'redirect_canonical' );
				status_header( 404 );
				nocache_headers();
			}
		}
	}
}
