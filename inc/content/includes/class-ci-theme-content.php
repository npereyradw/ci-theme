<?php
/**
 * Reads the site content in the shape the sections need.
 *
 * The theme does not register post types: a companion plugin does, with these names and fields.
 *
 * - ci_service      Services. Meta: status ("active" | "coming_soon"), includes (one item per
 *                   line), cover (0–5). Taxonomy: ci_area. Menu order = display order.
 * - ci_project      Projects. Meta: client, location, year, service (ci_service ID), cover.
 *                   Taxonomy: ci_area. Featured image = project photo.
 * - ci_team_member  Team. Meta: role, initials, education, linkedin. Featured image = photo.
 * - ci_testimonial  Testimonials. Title = author. Meta: quote, role.
 * - ci_institution  Institutions. Title = acronym. Meta: detail, full_name, relation
 *                   ("registration" | "member"), website. Featured image = logo.
 * - ci_area         Areas (taxonomy). Description = area text. Term meta: icon, order.
 * - post            News. Meta: cover.
 *
 * Every reader returns an empty list when its post type is not registered, so the theme never
 * breaks without the plugin.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Content' ) ) {

	/**
	 * Content readers. Results are cached per request.
	 */
	class CI_Theme_Content {

		const SERVICE     = 'ci_service';
		const PROJECT     = 'ci_project';
		const TEAM_MEMBER = 'ci_team_member';
		const TESTIMONIAL = 'ci_testimonial';
		const INSTITUTION = 'ci_institution';
		const AREA        = 'ci_area';

		/**
		 * Cache per request.
		 *
		 * @var array<string, mixed>
		 */
		private static $cache = array();

		/**
		 * Two-digit number ("01").
		 *
		 * @param int $n Number.
		 * @return string
		 */
		public static function pad( $n ) {
			return str_pad( (string) (int) $n, 2, '0', STR_PAD_LEFT );
		}

		/**
		 * Post shown by a template block: the one being viewed if it is of $type. In the Site
		 * Editor (a template without a post) the preview uses an example of that type.
		 *
		 * @param string $type Post type.
		 * @return WP_Post|null
		 */
		public static function context_post( $type ) {
			$post = get_post();
			if ( $post && $type === $post->post_type ) {
				return $post;
			}
			if ( self::is_preview() && post_type_exists( $type ) ) {
				$posts = get_posts(
					array(
						'post_type'   => $type,
						'post_status' => array( 'publish', 'draft' ),
						'numberposts' => 1,
						'orderby'     => array(
							'menu_order' => 'ASC',
							'date'       => 'DESC',
						),
					)
				);
				return $posts ? $posts[0] : null;
			}
			return null;
		}

		/**
		 * Whether this is an editor preview (server-side render through the REST API).
		 *
		 * @return bool
		 */
		public static function is_preview() {
			return defined( 'REST_REQUEST' ) && REST_REQUEST;
		}

		/**
		 * Rendered content of a post (its text blocks), for template blocks. Guarded against a
		 * post that would render itself.
		 *
		 * @param WP_Post $post Post.
		 * @return string
		 */
		public static function content_html( $post ) {
			static $rendering = array();
			if ( isset( $rendering[ $post->ID ] ) ) {
				return '';
			}
			$rendering[ $post->ID ] = true;
			$html                   = apply_filters( 'the_content', str_replace( ']]>', ']]&gt;', $post->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
			unset( $rendering[ $post->ID ] );
			return $html;
		}

		/**
		 * Generative cover variant of a post: its "cover" field, or ID % 6.
		 *
		 * @param int $post_id Post ID.
		 * @return int
		 */
		public static function cover_seed( $post_id ) {
			$value = get_post_meta( $post_id, 'cover', true );
			return is_numeric( $value ) ? (int) $value : $post_id % 6;
		}

		/**
		 * First area of a post.
		 *
		 * @param int $post_id Post ID.
		 * @return WP_Term|null
		 */
		public static function area_of( $post_id ) {
			if ( ! taxonomy_exists( self::AREA ) ) {
				return null;
			}
			$terms = get_the_terms( $post_id, self::AREA );
			return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
		}

		/**
		 * One service in the shape the sections use.
		 *
		 * @param WP_Post $post Service.
		 * @return array{id:int, slug:string, n:string, title:string, url:string, soon:bool, area:string, area_slug:string, excerpt:string, items:string[], cover:int}
		 */
		public static function service_item( $post ) {
			$area  = self::area_of( $post->ID );
			$items = preg_split( '/\R/u', (string) get_post_meta( $post->ID, 'includes', true ) );
			return array(
				'id'        => $post->ID,
				'slug'      => $post->post_name,
				'n'         => self::pad( $post->menu_order ),
				'title'     => get_the_title( $post ),
				'url'       => get_permalink( $post ),
				'soon'      => 'coming_soon' === get_post_meta( $post->ID, 'status', true ),
				'area'      => $area ? $area->name : '',
				'area_slug' => $area ? $area->slug : '',
				'excerpt'   => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'items'     => array_values( array_filter( array_map( 'trim', $items ) ) ),
				'cover'     => self::cover_seed( $post->ID ),
			);
		}

		/**
		 * Published services, in menu order.
		 *
		 * @return array<int, array>
		 */
		public static function services() {
			if ( isset( self::$cache['services'] ) ) {
				return self::$cache['services'];
			}
			$out = array();
			if ( post_type_exists( self::SERVICE ) ) {
				foreach ( self::ordered_posts( self::SERVICE ) as $post ) {
					$out[] = self::service_item( $post );
				}
			}
			self::$cache['services'] = $out;
			return $out;
		}

		/**
		 * Service by slug, or null.
		 *
		 * @param string $slug Slug.
		 * @return array|null
		 */
		public static function service_by_slug( $slug ) {
			foreach ( self::services() as $service ) {
				if ( $service['slug'] === $slug ) {
					return $service;
				}
			}
			return null;
		}

		/**
		 * Areas, by their "order" term meta and then by name. The description is the area text.
		 *
		 * @return array<int, array{id:int, slug:string, name:string, text:string, icon:string, url:string}>
		 */
		public static function areas() {
			if ( isset( self::$cache['areas'] ) ) {
				return self::$cache['areas'];
			}
			$out = array();
			if ( taxonomy_exists( self::AREA ) ) {
				$terms = get_terms(
					array(
						'taxonomy'   => self::AREA,
						'hide_empty' => false,
						'orderby'    => 'name',
					)
				);
				if ( ! is_wp_error( $terms ) ) {
					usort(
						$terms,
						static function ( $a, $b ) {
							$order_a = (int) get_term_meta( $a->term_id, 'order', true );
							$order_b = (int) get_term_meta( $b->term_id, 'order', true );
							return $order_a === $order_b ? strcmp( $a->name, $b->name ) : $order_a - $order_b;
						}
					);
					foreach ( $terms as $term ) {
						$out[] = array(
							'id'   => $term->term_id,
							'slug' => $term->slug,
							'name' => $term->name,
							'text' => $term->description,
							'icon' => (string) get_term_meta( $term->term_id, 'icon', true ),
							'url'  => CI_Theme_Routes::area_url( $term->slug ),
						);
					}
				}
			}
			self::$cache['areas'] = $out;
			return $out;
		}

		/**
		 * Projects.
		 *
		 * @param array $args {
		 *     @type int    $limit   Max items (0 = all).
		 *     @type string $service Service slug filter.
		 *     @type string $area    Area slug filter.
		 *     @type int[]  $ids     Hand-picked IDs, in that order (overrides the filters).
		 *     @type int[]  $exclude IDs to skip.
		 * }
		 * @return array<int, array>
		 */
		public static function projects( $args = array() ) {
			if ( ! post_type_exists( self::PROJECT ) ) {
				return array();
			}
			$args  = wp_parse_args(
				$args,
				array(
					'limit'   => 0,
					'service' => '',
					'area'    => '',
					'ids'     => array(),
					'exclude' => array(),
				)
			);
			$query = array(
				'post_type'      => self::PROJECT,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__not_in'   => array_map( 'absint', (array) $args['exclude'] ),
			);
			$ids   = array_filter( array_map( 'absint', (array) $args['ids'] ) );
			if ( $ids ) {
				$query['post__in'] = $ids;
				$query['orderby']  = 'post__in';
			}
			$out = array();
			foreach ( get_posts( $query ) as $post ) {
				$item = self::project_item( $post );
				if ( ! $ids ) {
					if ( $args['service'] && $item['service_slug'] !== $args['service'] ) {
						continue;
					}
					if ( $args['area'] && $item['area_slug'] !== $args['area'] ) {
						continue;
					}
				}
				$out[] = $item;
				if ( $args['limit'] && count( $out ) >= (int) $args['limit'] ) {
					break;
				}
			}
			return $out;
		}

		/**
		 * One project, for cards and the project template.
		 *
		 * @param WP_Post $post Project.
		 * @return array
		 */
		public static function project_item( $post ) {
			$services   = array_column( self::services(), null, 'id' );
			$service_id = (int) get_post_meta( $post->ID, 'service', true );
			$service    = isset( $services[ $service_id ] ) ? $services[ $service_id ] : null;
			$area       = self::area_of( $post->ID );
			return array(
				'id'           => $post->ID,
				'title'        => get_the_title( $post ),
				'url'          => get_permalink( $post ),
				'excerpt'      => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'client'       => (string) get_post_meta( $post->ID, 'client', true ),
				'location'     => (string) get_post_meta( $post->ID, 'location', true ),
				'year'         => (string) get_post_meta( $post->ID, 'year', true ),
				'area'         => $area ? $area->name : '',
				'area_slug'    => $area ? $area->slug : '',
				'service'      => $service ? $service['title'] : '',
				'service_slug' => $service ? $service['slug'] : '',
				'service_url'  => $service ? $service['url'] : '',
				'cover'        => self::cover_seed( $post->ID ),
				'image'        => (int) get_post_thumbnail_id( $post ),
			);
		}

		/**
		 * Projects related to one: same service first, then same area, then the rest.
		 *
		 * @param int $post_id Project ID.
		 * @param int $limit   Max items (0 = all).
		 * @return array<int, array>
		 */
		public static function related_projects( $post_id, $limit = 3 ) {
			$current = self::projects( array( 'ids' => array( $post_id ) ) );
			$current = $current ? $current[0] : array(
				'service_slug' => '',
				'area_slug'    => '',
			);
			$groups  = array( array(), array(), array() );
			foreach ( self::projects( array( 'exclude' => array( $post_id ) ) ) as $project ) {
				if ( '' !== $current['service_slug'] && $project['service_slug'] === $current['service_slug'] ) {
					$groups[0][] = $project;
				} elseif ( $project['area_slug'] === $current['area_slug'] ) {
					$groups[1][] = $project;
				} else {
					$groups[2][] = $project;
				}
			}
			return array_slice( array_merge( $groups[0], $groups[1], $groups[2] ), 0, $limit > 0 ? (int) $limit : null );
		}

		/**
		 * Short date ("08 Oct 2026"), without the dot some locales add to abbreviated months.
		 *
		 * @param WP_Post $post Post.
		 * @return string
		 */
		public static function short_date( $post ) {
			return str_replace( '.', '', get_the_date( 'd M Y', $post ) );
		}

		/**
		 * Reading time ("3 min"), at 200 words per minute.
		 *
		 * @param WP_Post $post Post.
		 * @return string
		 */
		public static function reading_time( $post ) {
			$words = str_word_count( wp_strip_all_tags( $post->post_content ) );
			/* translators: %d: minutes. */
			return sprintf( __( '%d min', 'ci-theme' ), max( 1, (int) round( $words / 200 ) ) );
		}

		/**
		 * Published news (blog posts).
		 *
		 * @param array $args { limit: int, exclude: int[], category: string slug }.
		 * @return array<int, array>
		 */
		public static function news( $args = array() ) {
			$args  = wp_parse_args(
				$args,
				array(
					'limit'    => 3,
					'exclude'  => array(),
					'category' => '',
				)
			);
			$posts = get_posts(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => $args['limit'] ? (int) $args['limit'] : -1,
					'post__not_in'   => array_map( 'absint', (array) $args['exclude'] ),
					'category_name'  => $args['category'],
				)
			);
			return array_map( array( __CLASS__, 'news_item' ), $posts );
		}

		/**
		 * One news item, for cards and the listing.
		 *
		 * @param WP_Post $post Post.
		 * @return array
		 */
		public static function news_item( $post ) {
			$categories = get_the_category( $post->ID );
			return array(
				'id'            => $post->ID,
				'title'         => get_the_title( $post ),
				'url'           => get_permalink( $post ),
				'excerpt'       => wp_strip_all_tags( get_the_excerpt( $post ) ),
				'category'      => $categories ? $categories[0]->name : __( 'News', 'ci-theme' ),
				'category_slug' => $categories ? $categories[0]->slug : '',
				'category_url'  => $categories ? get_category_link( $categories[0] ) : '',
				'date'          => self::short_date( $post ),
				'date_iso'      => get_the_date( 'c', $post ),
				'reading'       => self::reading_time( $post ),
				'cover'         => self::cover_seed( $post->ID ),
				'image'         => (int) get_post_thumbnail_id( $post ),
			);
		}

		/**
		 * Published team members, in menu order.
		 *
		 * @return array<int, array{name:string, initials:string, role:string, education:string, linkedin:string, photo:int}>
		 */
		public static function team() {
			$out = array();
			foreach ( self::ordered_posts( self::TEAM_MEMBER ) as $post ) {
				$out[] = array(
					'name'      => get_the_title( $post ),
					'initials'  => (string) get_post_meta( $post->ID, 'initials', true ),
					'role'      => (string) get_post_meta( $post->ID, 'role', true ),
					'education' => (string) get_post_meta( $post->ID, 'education', true ),
					'linkedin'  => (string) get_post_meta( $post->ID, 'linkedin', true ),
					'photo'     => (int) get_post_thumbnail_id( $post ),
				);
			}
			return $out;
		}

		/**
		 * Published testimonials, in menu order.
		 *
		 * @return array<int, array{name:string, text:string, role:string}>
		 */
		public static function testimonials() {
			$out = array();
			foreach ( self::ordered_posts( self::TESTIMONIAL ) as $post ) {
				$out[] = array(
					'name' => get_the_title( $post ),
					'text' => (string) get_post_meta( $post->ID, 'quote', true ),
					'role' => (string) get_post_meta( $post->ID, 'role', true ),
				);
			}
			return $out;
		}

		/**
		 * Published institutions, in menu order.
		 *
		 * @return array<int, array{id:int, acronym:string, name:string, full_name:string, type:string, url:string, logo:int}>
		 */
		public static function institutions() {
			if ( isset( self::$cache['institutions'] ) ) {
				return self::$cache['institutions'];
			}
			$relations = array(
				'registration' => __( 'Registration', 'ci-theme' ),
				'member'       => __( 'Member', 'ci-theme' ),
			);
			$out       = array();
			foreach ( self::ordered_posts( self::INSTITUTION ) as $post ) {
				$acronym  = get_the_title( $post );
				$detail   = (string) get_post_meta( $post->ID, 'detail', true );
				$full     = (string) get_post_meta( $post->ID, 'full_name', true );
				$relation = (string) get_post_meta( $post->ID, 'relation', true );
				$out[]    = array(
					'id'        => $post->ID,
					'acronym'   => $acronym,
					'name'      => $detail,
					'full_name' => '' !== $full ? $full : trim( $acronym . ' ' . $detail ),
					'type'      => isset( $relations[ $relation ] ) ? $relations[ $relation ] : $relations['registration'],
					'url'       => (string) get_post_meta( $post->ID, 'website', true ),
					'logo'      => (int) get_post_thumbnail_id( $post ),
				);
			}
			self::$cache['institutions'] = $out;
			return $out;
		}

		/**
		 * Published posts of a type, in menu order and then by title.
		 *
		 * @param string $type Post type.
		 * @return WP_Post[]
		 */
		private static function ordered_posts( $type ) {
			if ( ! post_type_exists( $type ) ) {
				return array();
			}
			return get_posts(
				array(
					'post_type'      => $type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => array(
						'menu_order' => 'ASC',
						'title'      => 'ASC',
					),
				)
			);
		}
	}
}
