<?php
/**
 * Helpers shared by the render.php of every section.
 *
 * Attributes become classes and CSS custom properties on the wrapper, so each stylesheet stays
 * the single source of the rules. Everything is validated here: colors must be slugs of the
 * theme.json palette, spacing one of four steps, URLs go through esc_url. A raw attribute never
 * reaches the markup.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Section' ) ) {

	/**
	 * Section wrapper, site settings and small pieces of markup.
	 */
	class CI_Theme_Section {

		/**
		 * Theme palette from theme.json: slug => hex value.
		 *
		 * @return array<string, string>
		 */
		public static function palette() {
			static $palette = null;
			if ( null === $palette ) {
				$colors  = wp_get_global_settings( array( 'color', 'palette', 'theme' ) );
				$palette = is_array( $colors ) ? wp_list_pluck( $colors, 'color', 'slug' ) : array();
			}
			return $palette;
		}

		/**
		 * CSS value for a palette slug, or '' if it is not in the palette.
		 *
		 * @param mixed $slug Attribute value.
		 * @return string
		 */
		public static function color( $slug ) {
			$slug = is_string( $slug ) ? $slug : '';
			return isset( self::palette()[ $slug ] ) ? 'var(--wp--preset--color--' . $slug . ')' : '';
		}

		/**
		 * Hex value of a palette slug (for places that cannot use CSS variables), or ''.
		 *
		 * @param string $slug Palette slug.
		 * @return string
		 */
		public static function hex( $slug ) {
			return isset( self::palette()[ $slug ] ) ? (string) self::palette()[ $slug ] : '';
		}

		/**
		 * CSS value for a spacing step; '' for the default ("md").
		 *
		 * @param mixed $step Attribute value.
		 * @return string
		 */
		public static function space( $step ) {
			$map = array(
				'none' => '0px',
				'sm'   => 'var(--wp--preset--spacing--section-sm)',
				'lg'   => 'var(--wp--preset--spacing--section-lg)',
			);
			return is_string( $step ) && isset( $map[ $step ] ) ? $map[ $step ] : '';
		}

		/**
		 * Wrapper attributes of a section, from the common attributes.
		 *
		 * @param array $attributes Block attributes.
		 * @param array $args {
		 *     @type string   $class   Block class (ci-<slug>).
		 *     @type string[] $classes Extra classes.
		 *     @type bool     $section Add .ci-section (vertical padding). Default true.
		 *     @type string   $style   Extra inline custom properties (already sanitised).
		 *     @type array    $attrs   Extra HTML attributes.
		 * }
		 * @return string Attributes string, escaped.
		 */
		public static function wrapper( $attributes, $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'class'   => '',
					'classes' => array(),
					'section' => true,
					'style'   => '',
					'attrs'   => array(),
				)
			);

			$classes = array( $args['class'] );
			if ( $args['section'] ) {
				$classes[] = 'ci-section';
			}
			$scheme = isset( $attributes['scheme'] ) ? $attributes['scheme'] : 'light';
			if ( in_array( $scheme, array( 'muted', 'dark' ), true ) ) {
				$classes[] = 'ci-section--' . $scheme;
			}
			$classes = array_merge( $classes, (array) $args['classes'] );

			$vars  = array(
				'--ci-title'  => self::color( isset( $attributes['titleColor'] ) ? $attributes['titleColor'] : '' ),
				'--ci-body'   => self::color( isset( $attributes['textColor'] ) ? $attributes['textColor'] : '' ),
				'--ci-accent' => self::color( isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '' ),
				'--ci-pt'     => self::space( isset( $attributes['spaceTop'] ) ? $attributes['spaceTop'] : '' ),
				'--ci-pb'     => self::space( isset( $attributes['spaceBottom'] ) ? $attributes['spaceBottom'] : '' ),
			);
			$style = '';
			foreach ( $vars as $name => $value ) {
				if ( '' !== $value ) {
					$style .= $name . ':' . $value . ';';
				}
			}
			$style .= $args['style'];

			$extra = array( 'class' => implode( ' ', array_filter( $classes ) ) );
			if ( '' !== $style ) {
				$extra['style'] = $style;
			}
			if ( isset( $attributes['animate'] ) && false === $attributes['animate'] ) {
				$extra['data-anim-off'] = '';
			}
			foreach ( $args['attrs'] as $name => $value ) {
				$extra[ $name ] = $value;
			}

			return get_block_wrapper_attributes( $extra );
		}

		/**
		 * Site settings (contact details, social profiles, default call to action, forms and
		 * analytics). The theme stores none of them: a companion plugin provides them through the
		 * "ci_theme_site_settings" filter. Missing keys fall back to empty values.
		 *
		 * @return array<string, mixed>
		 */
		public static function site() {
			static $site = null;
			if ( null !== $site ) {
				return $site;
			}

			$defaults = array(
				'phone'            => '',
				'whatsapp'         => '',
				'email'            => '',
				'hours'            => '',
				'hours_short'      => '',
				'location'         => '',
				'reach'            => '',
				'social'           => array(),
				'cta_eyebrow'      => __( 'Free quote', 'ci-theme' ),
				'cta_title'        => __( 'Tell us about your project and get a fixed quote.', 'ci-theme' ),
				'cta_text'         => __( 'We reply on business days.', 'ci-theme' ),
				'contact_form_id'  => 0,
				'notify_form_id'   => 0,
				'analytics_id'     => '',
				'analytics_domain' => '',
				'cookie_text'      => __( 'We use Google Analytics cookies to understand how the site is used. Learn more in our', 'ci-theme' ),
				'cookie_link'      => __( 'Privacy policy', 'ci-theme' ),
				'cookie_button'    => __( 'Got it', 'ci-theme' ),
			);

			/**
			 * Filters the site settings used by the theme.
			 *
			 * @param array $settings Settings, see the defaults above for the keys.
			 */
			$site = wp_parse_args( (array) apply_filters( 'ci_theme_site_settings', array() ), $defaults );

			// Derived values.
			$site['phone_href']   = '' !== $site['phone'] ? 'tel:' . preg_replace( '/[^\d+]/', '', (string) $site['phone'] ) : '';
			$site['whatsapp_url'] = '' !== $site['whatsapp'] ? 'https://wa.me/' . preg_replace( '/\D/', '', (string) $site['whatsapp'] ) : '';
			$site['social']       = array_values(
				array_filter(
					(array) $site['social'],
					static function ( $profile ) {
						return is_array( $profile ) && ! empty( $profile['url'] );
					}
				)
			);
			return $site;
		}

		/**
		 * Internal paths ("/contact/") become site URLs; anything else goes through esc_url.
		 *
		 * @param mixed $url Raw URL.
		 * @return string Escaped URL, or '' if empty.
		 */
		public static function url( $url ) {
			$url = is_string( $url ) ? trim( $url ) : '';
			if ( '' === $url ) {
				return '';
			}
			if ( '/' === $url[0] && ( ! isset( $url[1] ) || '/' !== $url[1] ) ) {
				$url = home_url( $url );
			}
			return esc_url( $url );
		}

		/**
		 * Privacy policy URL: the one set in the block, or the WordPress privacy page.
		 *
		 * @param mixed $url URL from the block ('' = use the privacy page).
		 * @return string Escaped URL, or ''.
		 */
		public static function privacy_url( $url ) {
			$own = self::url( $url );
			return '' !== $own ? $own : esc_url( get_privacy_policy_url() );
		}

		/**
		 * Screen reader note for links that open in a new tab.
		 *
		 * @return string
		 */
		public static function new_tab_note() {
			return '<span class="visually-hidden"> ' . esc_html__( '(opens in a new tab)', 'ci-theme' ) . '</span>';
		}

		/**
		 * Buttons of a repeater: [{ label, url, style, kind, newTab }].
		 * Kind "whatsapp" takes its URL from the site settings and is skipped when there is none.
		 *
		 * @param mixed  $buttons Attribute value.
		 * @param string $class   Extra class for the row.
		 * @param array  $attrs   Extra attributes for the row (e.g. data-anim-css).
		 * @return string Escaped HTML.
		 */
		public static function buttons( $buttons, $class = '', $attrs = array() ) {
			$buttons = is_array( $buttons ) ? $buttons : array();
			$styles  = array( 'primary', 'dark', 'outline', 'on-dark' );
			$site    = self::site();
			$html    = '';

			foreach ( $buttons as $button ) {
				$label = isset( $button['label'] ) ? trim( (string) $button['label'] ) : '';
				$kind  = isset( $button['kind'] ) ? $button['kind'] : 'link';
				$url   = 'whatsapp' === $kind ? esc_url( $site['whatsapp_url'] ) : self::url( isset( $button['url'] ) ? $button['url'] : '' );
				if ( '' === $label || '' === $url ) {
					continue;
				}
				$style   = isset( $button['style'] ) && in_array( $button['style'], $styles, true ) ? $button['style'] : 'primary';
				$new_tab = 'whatsapp' === $kind || ! empty( $button['newTab'] );

				$html .= sprintf(
					'<a href="%1$s" class="ci-btn ci-btn--%2$s"%3$s>%4$s%5$s%6$s</a>',
					$url,
					esc_attr( $style ),
					$new_tab ? ' target="_blank" rel="noopener noreferrer"' : '',
					'whatsapp' === $kind ? '<span class="ci-whatsapp-dot" aria-hidden="true"></span>' : '',
					esc_html( $label ),
					$new_tab ? self::new_tab_note() : ''
				);
			}

			if ( '' === $html ) {
				return '';
			}

			$extra = '';
			foreach ( $attrs as $name => $value ) {
				$extra .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
			}
			return '<div class="ci-btn-row ' . esc_attr( $class ) . '"' . $extra . '>' . $html . '</div>';
		}

		/**
		 * The aria-labelledby attribute pointing to the section title, only when there is one.
		 *
		 * @param array  $attributes Block attributes.
		 * @param string $id         Title id.
		 * @param string $key        Attribute holding the title.
		 * @return string
		 */
		public static function labelled( $attributes, $id, $key = 'title' ) {
			return ! empty( $attributes[ $key ] ) && '' !== trim( (string) $attributes[ $key ] ) ? ' aria-labelledby="' . esc_attr( $id ) . '"' : '';
		}

		/**
		 * Text with its line breaks kept (one per line in the textarea), escaped.
		 *
		 * @param mixed $text Text.
		 * @return string
		 */
		public static function lines( $text ) {
			$text = is_string( $text ) ? trim( $text ) : '';
			return nl2br( esc_html( $text ), false );
		}

		/**
		 * Paragraphs from a textarea: a blank line starts a new paragraph.
		 *
		 * @param mixed  $text  Text.
		 * @param string $class Class of each paragraph.
		 * @return string
		 */
		public static function paragraphs( $text, $class = 'ci-text' ) {
			$text = is_string( $text ) ? trim( $text ) : '';
			if ( '' === $text ) {
				return '';
			}
			$html = '';
			foreach ( preg_split( '/\R\s*\R/u', $text ) as $paragraph ) {
				$html .= '<p class="' . esc_attr( $class ) . '">' . self::lines( $paragraph ) . '</p>';
			}
			return $html;
		}

		/**
		 * Section link ("View all →") from the linkLabel / linkUrl attributes.
		 *
		 * @param array  $attributes Block attributes.
		 * @param string $class      Class.
		 * @return string
		 */
		public static function link( $attributes, $class = 'ci-link' ) {
			$label = isset( $attributes['linkLabel'] ) ? trim( (string) $attributes['linkLabel'] ) : '';
			$url   = self::url( isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '' );
			if ( '' === $label || '' === $url ) {
				return '';
			}
			return sprintf( '<a href="%1$s" class="%2$s">%3$s</a>', $url, esc_attr( $class ), esc_html( $label ) );
		}

		/**
		 * Standard section intro: eyebrow, h2 (with an id for aria-labelledby) and optional text,
		 * with the section link on the right when there is one.
		 *
		 * @param array $attributes Block attributes (eyebrow, title, text, linkLabel, linkUrl).
		 * @param array $args { id: string, text: bool show the text, link: bool show the link }.
		 * @return string
		 */
		public static function intro( $attributes, $args = array() ) {
			$args  = wp_parse_args(
				$args,
				array(
					'id'   => '',
					'text' => false,
					'link' => true,
				)
			);
			$title = isset( $attributes['title'] ) ? trim( (string) $attributes['title'] ) : '';
			$intro = self::eyebrow( isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '' );
			if ( '' !== $title ) {
				$intro .= sprintf( '<h2 id="%1$s" class="ci-h2">%2$s</h2>', esc_attr( $args['id'] ), esc_html( $title ) );
			}
			if ( $args['text'] && ! empty( $attributes['text'] ) ) {
				$intro .= '<p class="ci-text">' . self::lines( $attributes['text'] ) . '</p>';
			}
			if ( '' === $intro ) {
				return '';
			}
			$intro = '<div class="ci-section-intro">' . $intro . '</div>';
			$link  = $args['link'] ? self::link( $attributes ) : '';
			return $link ? '<div class="ci-section-head">' . $intro . $link . '</div>' : $intro;
		}

		/**
		 * "External link" arrow in SVG (the ↗ character renders as an emoji on iOS).
		 *
		 * @return string
		 */
		public static function external_icon() {
			return '<svg viewBox="0 0 12 12" width="0.75em" height="0.75em" class="ci-external-icon" aria-hidden="true" focusable="false"><path d="M3 9 L9 3 M4 3 H9 V8" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		}

		/**
		 * Eyebrow: small label above a title, with the accent bar.
		 *
		 * @param mixed  $text  Text.
		 * @param string $class Extra class.
		 * @param string $tag   Tag.
		 * @return string
		 */
		public static function eyebrow( $text, $class = '', $tag = 'p' ) {
			$text = is_string( $text ) ? trim( $text ) : '';
			if ( '' === $text ) {
				return '';
			}
			$tag = in_array( $tag, array( 'p', 'span', 'h2' ), true ) ? $tag : 'p';
			return sprintf( '<%1$s class="ci-eyebrow %2$s">%3$s</%1$s>', $tag, esc_attr( $class ), esc_html( $text ) );
		}
	}
}
