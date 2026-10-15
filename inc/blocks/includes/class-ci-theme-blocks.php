<?php
/**
 * Registers every block of the theme from build/blocks/<slug>/block.json, the "CI · …" inserter
 * and pattern categories, the blocks allowed per post type and the shared editor library.
 *
 * Section blocks declare "supports": { "ciSection": true } and receive the common style
 * attributes (scheme, colors, spacing, animation) from section-attributes.json. The default
 * texts of every block are written in English in block.json and translated here, so the
 * editor and the front end show them in the site language.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Blocks' ) ) {

	/**
	 * Block registry.
	 */
	class CI_Theme_Blocks {

		const NAMESPACE_PREFIX = 'ci/';
		const EDITOR_HANDLE    = 'ci-theme-editor';

		/**
		 * Blocks whose view script is only needed by one of their variants (the listings).
		 */
		const OPTIONAL_VIEWS = array( 'ci/services', 'ci/projects' );

		/**
		 * Blocks that rendered the variant that needs their view script.
		 *
		 * @var array<string, bool>
		 */
		private static $needed_views = array();

		/**
		 * Register hooks.
		 */
		public static function init() {
			add_filter( 'block_categories_all', array( __CLASS__, 'add_categories' ) );
			add_filter( 'block_type_metadata', array( __CLASS__, 'add_section_attributes' ) );
			add_filter( 'block_type_metadata', array( __CLASS__, 'translate_defaults' ) );
			add_action( 'init', array( __CLASS__, 'register_editor_library' ), 5 );
			add_action( 'init', array( __CLASS__, 'register_blocks' ) );
			add_action( 'init', array( __CLASS__, 'register_pattern_categories' ) );
			add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_editor_library' ) );
			// The editor canvas is an iframe: its styles (previews, width) are enqueued here.
			add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_canvas_styles' ) );
			add_filter( 'allowed_block_types_all', array( __CLASS__, 'allowed_blocks' ), 10, 2 );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'drop_unused_views' ), 30 );
			// Only the theme patterns: none from the WordPress.org directory.
			add_filter( 'should_load_remote_block_patterns', '__return_false' );
		}

		/**
		 * Inserter categories, in order.
		 *
		 * @return array<string, string> slug => title
		 */
		public static function categories() {
			return array(
				'ci-sections' => __( 'CI · Sections', 'ci-theme' ),
				'ci-dynamic'  => __( 'CI · Dynamic content', 'ci-theme' ),
				'ci-template' => __( 'CI · Templates', 'ci-theme' ),
			);
		}

		/**
		 * Core blocks allowed inside long text (privacy page, project, service and news content).
		 *
		 * @return string[]
		 */
		public static function text_blocks() {
			return array(
				'core/paragraph',
				'core/heading',
				'core/list',
				'core/list-item',
				'core/quote',
				'core/image',
				'core/gallery',
				'core/buttons',
				'core/button',
				'core/file',
				'core/table',
				'core/separator',
				'core/embed',
				'core/video',
				'core/block',
			);
		}

		/**
		 * "CI · …" categories first in the inserter.
		 *
		 * @param array $categories Registered categories.
		 * @return array
		 */
		public static function add_categories( $categories ) {
			$ours = array();
			foreach ( self::categories() as $slug => $title ) {
				$ours[] = array(
					'slug'  => $slug,
					'title' => $title,
					'icon'  => null,
				);
			}
			return array_merge( $ours, $categories );
		}

		/**
		 * Pattern categories: one section at a time, or a whole page.
		 */
		public static function register_pattern_categories() {
			register_block_pattern_category(
				'ci-sections',
				array(
					'label'       => __( 'CI · Sections', 'ci-theme' ),
					'description' => __( 'Ready-made sections of the site.', 'ci-theme' ),
				)
			);
			register_block_pattern_category(
				'ci-pages',
				array(
					'label'       => __( 'CI · Pages', 'ci-theme' ),
					'description' => __( 'Complete pages to start a new page from.', 'ci-theme' ),
				)
			);
		}

		/**
		 * Whether the metadata belongs to a block of this theme.
		 *
		 * @param array $metadata block.json contents.
		 * @return bool
		 */
		private static function is_ours( $metadata ) {
			return ! empty( $metadata['name'] ) && 0 === strpos( $metadata['name'], self::NAMESPACE_PREFIX );
		}

		/**
		 * Common section attributes (section-attributes.json); the block's own definitions win,
		 * so a block can change a default (e.g. a dark hero).
		 *
		 * @param array $metadata block.json contents.
		 * @return array
		 */
		public static function add_section_attributes( $metadata ) {
			if ( ! self::is_ours( $metadata ) || empty( $metadata['supports']['ciSection'] ) ) {
				return $metadata;
			}
			$common                 = self::section_attributes();
			$own                    = isset( $metadata['attributes'] ) ? $metadata['attributes'] : array();
			$metadata['attributes'] = array_merge( $common, $own );
			foreach ( $own as $key => $definition ) {
				if ( isset( $common[ $key ] ) ) {
					$metadata['attributes'][ $key ] = array_merge( $common[ $key ], $definition );
				}
			}
			return $metadata;
		}

		/**
		 * Translates the default texts of the theme blocks (strings and the string fields of
		 * array defaults). Attributes with an "enum" hold option values, and lowercase slugs
		 * ("primary", "on-dark") are option values inside arrays, so neither is translated.
		 *
		 * @param array $metadata block.json contents.
		 * @return array
		 */
		public static function translate_defaults( $metadata ) {
			if ( ! self::is_ours( $metadata ) || empty( $metadata['attributes'] ) ) {
				return $metadata;
			}
			foreach ( $metadata['attributes'] as $key => $definition ) {
				if ( isset( $definition['default'] ) && ! isset( $definition['enum'] ) ) {
					$metadata['attributes'][ $key ]['default'] = self::translate_value( $definition['default'] );
				}
			}
			return $metadata;
		}

		/**
		 * Translates a default value recursively.
		 *
		 * @param mixed $value Default value.
		 * @return mixed
		 */
		private static function translate_value( $value ) {
			if ( is_string( $value ) ) {
				if ( '' === trim( $value ) || preg_match( '/^[a-z0-9-]+$/', $value ) ) {
					return $value;
				}
				// phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText -- block.json defaults, listed in the POT file.
				return translate( $value, 'ci-theme' );
			}
			if ( is_array( $value ) ) {
				return array_map( array( __CLASS__, 'translate_value' ), $value );
			}
			return $value;
		}

		/**
		 * Section attribute definitions.
		 *
		 * @return array
		 */
		public static function section_attributes() {
			static $attributes = null;
			if ( null === $attributes ) {
				$json       = file_get_contents( __DIR__ . '/../section-attributes.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
				$attributes = json_decode( (string) $json, true );
				$attributes = is_array( $attributes ) ? $attributes : array();
			}
			return $attributes;
		}

		/**
		 * Shared editor library (window.ciThemeEditor). Registered before the blocks, which
		 * list it as a dependency of their editor scripts.
		 */
		public static function register_editor_library() {
			$dir   = CI_THEME_DIR . 'build/editor/';
			$asset = file_exists( $dir . 'index.asset.php' ) ? require $dir . 'index.asset.php' : null;
			if ( ! $asset ) {
				return;
			}
			wp_register_script(
				self::EDITOR_HANDLE,
				CI_THEME_URI . 'build/editor/index.js',
				$asset['dependencies'],
				$asset['version'],
				true
			);
			wp_set_script_translations( self::EDITOR_HANDLE, 'ci-theme', CI_THEME_DIR . 'languages' );
			if ( file_exists( $dir . 'index.css' ) ) {
				wp_register_style( self::EDITOR_HANDLE, CI_THEME_URI . 'build/editor/index.css', array(), $asset['version'] );
			}
		}

		/**
		 * Editor: the library's own styles (repeater rows, panels).
		 */
		public static function enqueue_editor_library() {
			wp_enqueue_style( self::EDITOR_HANDLE );
		}

		/**
		 * Editor canvas (iframe): the library's styles for the previews. Only in the admin.
		 */
		public static function enqueue_canvas_styles() {
			if ( is_admin() ) {
				wp_enqueue_style( self::EDITOR_HANDLE );
			}
		}

		/**
		 * Every build/blocks/<slug>/block.json.
		 */
		public static function register_blocks() {
			foreach ( glob( CI_THEME_DIR . 'build/blocks/*/block.json' ) as $file ) {
				$type = register_block_type( dirname( $file ) );
				if ( $type && ! empty( $type->editor_script_handles ) ) {
					foreach ( $type->editor_script_handles as $handle ) {
						wp_set_script_translations( $handle, 'ci-theme', CI_THEME_DIR . 'languages' );
					}
				}
			}
		}

		/**
		 * Blocks offered per post type. Pages are built with sections; long text (news, projects,
		 * services, privacy) only gets text blocks; template blocks live in the theme templates.
		 *
		 * @param bool|string[]           $allowed Allowed blocks.
		 * @param WP_Block_Editor_Context $context Editor context.
		 * @return bool|string[]
		 */
		public static function allowed_blocks( $allowed, $context ) {
			if ( empty( $context->post ) ) {
				return $allowed; // Site Editor: templates and template parts use every block.
			}

			$sections = array();
			foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
				if ( 0 === strpos( $name, self::NAMESPACE_PREFIX ) && in_array( $type->category, array( 'ci-sections', 'ci-dynamic' ), true ) ) {
					$sections[] = $name;
				}
			}

			switch ( $context->post->post_type ) {
				case 'page':
				case 'wp_block':
					return array_merge( $sections, self::text_blocks() );
				case 'post':
				case CI_Theme_Content::SERVICE:
				case CI_Theme_Content::PROJECT:
					return self::text_blocks();
				default:
					return $allowed;
			}
		}

		/**
		 * Called from render.php by the variant that uses the view script.
		 *
		 * @param string $block Block name.
		 */
		public static function needs_view( $block ) {
			self::$needed_views[ $block ] = true;
		}

		/**
		 * The template is rendered before wp_head: drop the view scripts no block needed
		 * (e.g. the filters of the services and projects listings on the home page).
		 */
		public static function drop_unused_views() {
			foreach ( self::OPTIONAL_VIEWS as $name ) {
				$type = WP_Block_Type_Registry::get_instance()->get_registered( $name );
				if ( ! $type || isset( self::$needed_views[ $name ] ) ) {
					continue;
				}
				foreach ( $type->view_script_handles as $handle ) {
					// Only the block's own view.js (e.g. ci-theme-projects-view-script).
					if ( false !== strpos( $handle, '-view-script' ) ) {
						wp_dequeue_script( $handle );
					}
				}
			}
		}
	}
}
