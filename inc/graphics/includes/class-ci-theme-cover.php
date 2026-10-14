<?php
/**
 * Generative cover: geometric shapes (arc, disc, ring and bar) drawn in SVG so the motion script
 * can animate them stroke by stroke. Six variants cycle through the content; a project, news item
 * or team member with an image shows the image instead. Coordinates live in a viewBox 100 units
 * wide and 100/ratio high.
 *
 * The data-shape, data-depth and data-origin attributes are read by the motion script.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Cover' ) ) {

	/**
	 * Cover markup.
	 */
	class CI_Theme_Cover {

		/**
		 * Light tints of the palette used as backgrounds.
		 *
		 * @var array<string, string>
		 */
		const TINTS = array(
			'gray'  => '#EEF0F4',
			'blue'  => '#E6E8F0',
			'cream' => '#FAF5D2',
			'muted' => '#B9BFD3',
		);

		/**
		 * Variants with a dark background (the caption turns light).
		 *
		 * @var string[]
		 */
		const DARK = array( '0', '3', '5', 'h' );

		/**
		 * Palette color by slug, with the design value as fallback.
		 *
		 * @param string $slug Palette slug.
		 * @return string
		 */
		private static function color( $slug ) {
			$fallback = array(
				'navy'       => '#1C2545',
				'navy-2'     => '#2E3A66',
				'navy-3'     => '#121831',
				'yellow'     => '#E6D10F',
				'red-bright' => '#E22412',
				'muted'      => '#5A6178',
			);
			$hex      = CI_Theme_Section::hex( $slug );
			return '' !== $hex ? strtoupper( $hex ) : $fallback[ $slug ];
		}

		/**
		 * Background of each variant (0–5 cycle, h = hero, p = person).
		 *
		 * @param string $variant Variant key.
		 * @return string
		 */
		private static function background( $variant ) {
			$backgrounds = array(
				'0' => self::color( 'navy' ),
				'1' => self::TINTS['gray'],
				'2' => self::TINTS['blue'],
				'3' => self::color( 'navy-2' ),
				'4' => self::TINTS['cream'],
				'5' => self::color( 'navy-3' ),
				'h' => self::color( 'navy' ),
				'p' => self::TINTS['blue'],
			);
			return $backgrounds[ $variant ];
		}

		/**
		 * Render a cover.
		 *
		 * @param array $args {
		 *     @type string|int $seed     Variant 0–5 (cyclic) or "hero".
		 *     @type string     $ratio    CSS aspect ratio, e.g. "4 / 3".
		 *     @type string     $initials Shows the "person" variant.
		 *     @type string     $caption  Small caption.
		 *     @type int        $image    Attachment ID: replaces the illustration.
		 *     @type bool       $fill     Fills the container (section background) instead of a card.
		 *     @type bool       $priority Main image of the page (LCP): eager load, CSS entrance.
		 *     @type string     $sizes    sizes attribute for the image srcset.
		 *     @type string     $class    Extra class.
		 * }
		 * @return string
		 */
		public static function render( $args = array() ) {
			$args = wp_parse_args(
				$args,
				array(
					'seed'     => '0',
					'ratio'    => '4 / 3',
					'initials' => '',
					'caption'  => '',
					'image'    => 0,
					'fill'     => false,
					'priority' => false,
					'sizes'    => '(max-width: 767px) 100vw, (max-width: 1179px) 50vw, 400px',
					'class'    => '',
				)
			);

			$variant = self::variant( $args['seed'], $args['initials'] );
			$ratio   = self::ratio( $args['ratio'] );
			$style   = sprintf(
				'--ci-cover-ratio:%1$s;--ci-cover-bg:%2$s;--ci-cover-cap:%3$s;',
				esc_attr( $ratio['css'] ),
				esc_attr( self::background( $variant ) ),
				esc_attr( in_array( $variant, self::DARK, true ) ? self::TINTS['muted'] : self::color( 'muted' ) )
			);
			$class   = trim( 'ci-cover ' . ( $args['fill'] ? 'ci-cover--fill ' : '' ) . $args['class'] );

			$image_id = absint( $args['image'] );
			if ( $image_id && wp_attachment_is_image( $image_id ) ) {
				$img = wp_get_attachment_image(
					$image_id,
					'large',
					false,
					array(
						'class'         => 'ci-cover-image',
						'sizes'         => $args['sizes'],
						'loading'       => $args['priority'] ? 'eager' : 'lazy',
						'fetchpriority' => $args['priority'] ? 'high' : 'auto',
						'decoding'      => 'async',
					)
				);
				return sprintf(
					'<div data-cover="image"%1$s class="%2$s" style="%3$s">%4$s</div>',
					$args['priority'] ? ' data-anim-css="cover"' : '',
					esc_attr( $class ),
					$style,
					$img
				);
			}

			$is_hero = 'h' === $variant;
			$h       = $is_hero ? 125 : self::r2( 100 / $ratio['value'] );
			$shapes  = $is_hero ? self::hero_art() : self::shapes( $variant, $h, (string) $args['initials'] );

			return sprintf(
				'<div data-cover="%1$s"%2$s class="%3$s" style="%4$s"><svg viewBox="0 0 100 %5$s" preserveAspectRatio="%6$s" class="ci-cover-svg" aria-hidden="true" focusable="false">%7$s</svg>%8$s</div>',
				$is_hero ? 'hero' : 'art',
				$args['fill'] ? ' data-cover-fill="true"' : '',
				esc_attr( $class ),
				$style,
				esc_attr( (string) $h ),
				( $args['fill'] || $is_hero ) ? 'xMidYMid slice' : 'xMidYMid meet',
				$shapes,
				'' !== $args['caption'] ? '<span class="ci-cover-caption">' . esc_html( $args['caption'] ) . '</span>' : ''
			);
		}

		/**
		 * Variant key.
		 *
		 * @param mixed  $seed     Seed.
		 * @param string $initials Initials.
		 * @return string
		 */
		private static function variant( $seed, $initials ) {
			if ( '' !== (string) $initials ) {
				return 'p';
			}
			if ( 'hero' === $seed ) {
				return 'h';
			}
			return (string) ( abs( (int) $seed ) % 6 );
		}

		/**
		 * Parse "w / h".
		 *
		 * @param string $ratio Ratio.
		 * @return array{css: string, value: float}
		 */
		private static function ratio( $ratio ) {
			$parts = array_map( 'floatval', explode( '/', (string) $ratio ) );
			if ( 2 === count( $parts ) && $parts[0] > 0 && $parts[1] > 0 ) {
				return array(
					'css'   => $parts[0] . ' / ' . $parts[1],
					'value' => $parts[0] / $parts[1],
				);
			}
			return array(
				'css'   => '4 / 3',
				'value' => 4 / 3,
			);
		}

		/**
		 * Round to 2 decimals.
		 *
		 * @param float $n Number.
		 * @return float
		 */
		private static function r2( $n ) {
			return round( $n, 2 );
		}

		/**
		 * Arc: a circle open on its right quarter, rotated $rot degrees.
		 *
		 * @param float $cx  Center x.
		 * @param float $cy  Center y.
		 * @param float $r   Radius.
		 * @param float $rot Rotation in degrees.
		 * @return string SVG path data.
		 */
		private static function arc_path( $cx, $cy, $r, $rot = 0 ) {
			$p = function ( $deg ) use ( $cx, $cy, $r, $rot ) {
				$a = deg2rad( $deg + $rot );
				return self::r2( $cx + $r * cos( $a ) ) . ' ' . self::r2( $cy + $r * sin( $a ) );
			};
			return 'M ' . $p( 45 ) . ' A ' . $r . ' ' . $r . ' 0 1 1 ' . $p( 315 );
		}

		/**
		 * Arc shape.
		 *
		 * @param float  $cx    Center x.
		 * @param float  $cy    Center y.
		 * @param float  $r     Radius.
		 * @param float  $w     Stroke width.
		 * @param string $color Color.
		 * @param float  $rot   Rotation in degrees.
		 * @param float  $depth Parallax depth.
		 * @return string
		 */
		private static function arc( $cx, $cy, $r, $w, $color, $rot = 0, $depth = 0.5 ) {
			return sprintf(
				'<g class="ci-cover-hover-arc"><path data-shape="arc" data-depth="%1$s" d="%2$s" fill="none" stroke="%3$s" stroke-width="%4$s"/></g>',
				$depth,
				self::arc_path( $cx, $cy, $r, $rot ),
				$color,
				$w
			);
		}

		/**
		 * Ring shape (thin outline).
		 *
		 * @param float  $cx    Center x.
		 * @param float  $cy    Center y.
		 * @param float  $r     Radius.
		 * @param string $color Color.
		 * @param float  $depth Parallax depth.
		 * @return string
		 */
		private static function ring( $cx, $cy, $r, $color, $depth = 0.3 ) {
			return sprintf(
				'<g class="ci-cover-hover-ring"><circle data-shape="ring" data-depth="%1$s" cx="%2$s" cy="%3$s" r="%4$s" fill="none" stroke="%5$s" stroke-width="1" vector-effect="non-scaling-stroke"/></g>',
				$depth,
				$cx,
				$cy,
				$r,
				$color
			);
		}

		/**
		 * Disc shape.
		 *
		 * @param float  $cx    Center x.
		 * @param float  $cy    Center y.
		 * @param float  $r     Radius.
		 * @param string $color Color.
		 * @param float  $depth Parallax depth.
		 * @return string
		 */
		private static function disc( $cx, $cy, $r, $color, $depth = 1 ) {
			return sprintf(
				'<g class="ci-cover-hover-disc"><circle data-shape="disc" data-depth="%1$s" cx="%2$s" cy="%3$s" r="%4$s" fill="%5$s"/></g>',
				$depth,
				$cx,
				$cy,
				$r,
				$color
			);
		}

		/**
		 * Bar shape.
		 *
		 * @param float  $x      Left.
		 * @param float  $y      Top.
		 * @param float  $w      Width.
		 * @param float  $h      Height.
		 * @param string $color  Color.
		 * @param string $origin Edge the bar grows from (top | bottom).
		 * @param float  $depth  Parallax depth.
		 * @return string
		 */
		private static function bar( $x, $y, $w, $h, $color, $origin = 'bottom', $depth = 0.8 ) {
			return sprintf(
				'<g class="ci-cover-hover-bar"><rect data-shape="bar" data-origin="%1$s" data-depth="%2$s" x="%3$s" y="%4$s" width="%5$s" height="%6$s" fill="%7$s"/></g>',
				$origin,
				$depth,
				self::r2( $x ),
				self::r2( $y ),
				$w,
				$h,
				$color
			);
		}

		/**
		 * Shapes of each variant.
		 *
		 * @param string $k        Variant.
		 * @param float  $h        viewBox height.
		 * @param string $initials Initials (person variant).
		 * @return string
		 */
		private static function shapes( $k, $h, $initials ) {
			switch ( $k ) {
				case '0':
					return self::arc( 78, $h - 2, 54.5, 11, self::color( 'navy-2' ), -24 ) . self::disc( 21.5, 23.5, 7.5, self::color( 'yellow' ) );
				case '1':
					return self::disc( 20, 8, 40, self::color( 'yellow' ), 0.4 ) . self::bar( 68, $h - 62, 4, 62, self::color( 'red-bright' ) ) . self::disc( 88, 15, 3, self::color( 'navy' ), 1.4 );
				case '2':
					return self::ring( 100, $h, 74.9, self::color( 'navy' ), 0.2 ) . self::ring( 100, $h, 54.9, self::color( 'navy' ), 0.35 ) . self::disc( 100, $h, 35, self::color( 'navy' ), 0.5 ) . self::disc( 17.5, 19.5, 5.5, self::color( 'yellow' ), 1.3 );
				case '3':
					return self::disc( 42, $h + 12, 50, self::color( 'navy' ), 0.3 ) . self::bar( 68.2, 14, 1.8, 24, self::color( 'red-bright' ), 'top' ) . self::disc( 81.5, 22.5, 4.5, self::color( 'yellow' ), 1.3 );
				case '4':
					return self::arc( 40, $h / 2, 27, 10, self::color( 'navy' ) ) . self::disc( 40, $h / 2, 14, self::color( 'yellow' ) ) . self::bar( 78, $h / 2 - 17, 4.5, 34, self::color( 'red-bright' ), 'top' );
				case '5':
					return self::disc( 85, 13, 31, self::color( 'yellow' ), 0.5 ) . self::ring( 18, $h + 4, 51.9, 'rgba(255,255,255,0.35)', 0.2 ) . self::ring( 18, $h + 4, 31.9, 'rgba(255,255,255,0.2)', 0.35 );
				case 'p':
					return self::disc( 90, 10, 28, '#FFFFFF', 0.4 ) . self::disc( 84, 16, 4, self::color( 'yellow' ), 1.3 )
						. sprintf( '<text data-shape="text" data-depth="0.6" x="10" y="%1$s" class="ci-cover-initials">%2$s</text>', self::r2( $h - 9.5 ), esc_html( $initials ) );
				default:
					return '';
			}
		}

		/**
		 * Hero illustration: a large arc with a disc inside and a vertical bar.
		 *
		 * @return string
		 */
		private static function hero_art() {
			return '<g class="ci-cover-hover-arc"><path data-shape="arc" data-depth="0.45" d="M 79.36 32.64 A 50 50 0 1 0 79.36 103.36" fill="none" stroke="' . self::color( 'navy-2' ) . '" stroke-width="18.75"/></g>'
				. '<g class="ci-cover-hover-disc"><circle data-shape="disc" data-depth="0.9" cx="44" cy="68" r="25" fill="' . self::color( 'yellow' ) . '"/></g>'
				. '<g class="ci-cover-hover-bar"><rect data-shape="bar" data-origin="top" data-depth="0.7" x="89" y="39" width="10" height="58" fill="' . self::color( 'red-bright' ) . '"/></g>';
		}
	}
}
