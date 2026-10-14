<?php
/**
 * Animated area icons built from the same shapes as the covers (arc, disc and bar). Each one is
 * a small scene the motion script animates (data-icon / data-loop).
 *
 * - beam:    a beam on two columns, with the disc resting on it as a load.
 * - arch:    an arch drawn over the floor and a sun pulsing inside.
 * - antenna: an antenna with waves that draw and pulse.
 *
 * Each area picks its icon in its "icon" term meta; without one, icons follow the area order.
 *
 * @package CI_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'CI_Theme_Area_Icon' ) ) {

	/**
	 * Area icon markup.
	 */
	class CI_Theme_Area_Icon {

		/**
		 * Available icons.
		 *
		 * @return array<string, string> Key => label.
		 */
		public static function options() {
			return array(
				'beam'    => __( 'Beam', 'ci-theme' ),
				'arch'    => __( 'Arch', 'ci-theme' ),
				'antenna' => __( 'Antenna', 'ci-theme' ),
			);
		}

		/**
		 * Icon key for an area: its own one, or the next one in the list by position.
		 *
		 * @param string $icon  Icon chosen for the area ('' if none).
		 * @param int    $index Position of the area.
		 * @return string
		 */
		public static function resolve( $icon, $index ) {
			$keys = array_keys( self::options() );
			return in_array( $icon, $keys, true ) ? $icon : $keys[ $index % count( $keys ) ];
		}

		/**
		 * SVG of an icon.
		 *
		 * @param string $icon Icon key.
		 * @return string
		 */
		public static function svg( $icon ) {
			$white  = '#FFFFFF';
			$yellow = CI_Theme_Section::hex( 'yellow' );
			$red    = CI_Theme_Section::hex( 'red-bright' );

			switch ( $icon ) {
				case 'arch':
					$inner = '<rect data-icon="grow-x" x="4" y="56" width="56" height="4" fill="' . esc_attr( $red ) . '"/>'
						. '<circle data-icon="pop" data-loop="pulse" cx="32" cy="45.5" r="5.5" fill="' . esc_attr( $yellow ) . '"/>'
						. '<path data-icon="draw" d="M 10 56 A 22 22 0 0 1 54 56" fill="none" stroke="' . $white . '" stroke-width="6"/>';
					break;
				case 'antenna':
					$wave  = '<path data-icon="draw" data-loop="wave" d="%s" fill="none" stroke="' . $white . '" stroke-width="4" stroke-linecap="round"/>';
					$inner = '<rect data-icon="grow-up" x="13" y="46" width="6" height="16" fill="' . esc_attr( $red ) . '"/>'
						. '<circle data-icon="pop" cx="16" cy="36" r="7" fill="' . esc_attr( $yellow ) . '"/>'
						. sprintf( $wave, 'M 28 24 A 16 16 0 0 1 28 48' )
						. sprintf( $wave, 'M 37 15 A 28 28 0 0 1 37 57' )
						. sprintf( $wave, 'M 46 6 A 40 40 0 0 1 46 66' );
					break;
				default:
					$inner = '<rect data-icon="grow-up" x="14" y="30" width="6" height="30" fill="' . esc_attr( $red ) . '"/>'
						. '<rect data-icon="grow-up" x="44" y="30" width="6" height="30" fill="' . esc_attr( $red ) . '"/>'
						. '<rect data-icon="grow-x" x="6" y="24" width="52" height="6" fill="' . $white . '"/>'
						. '<circle data-icon="drop" data-loop="bob" cx="32" cy="15" r="8" fill="' . esc_attr( $yellow ) . '"/>';
			}

			return '<svg data-area-icon viewBox="0 0 64 64" width="64" height="64" class="ci-area-icon" aria-hidden="true" focusable="false">' . $inner . '</svg>';
		}
	}
}
