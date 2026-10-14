/**
 * Color picked from the theme.json palette. The slug is stored (not the hex), so a palette
 * change reaches every section and no color falls outside the design.
 */

import { useSettings } from '@wordpress/block-editor';
import { BaseControl, ColorPalette } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';

export function PaletteColorControl( { label, help, value, onChange } ) {
	const [ palette = [] ] = useSettings( 'color.palette' );
	const id = useInstanceId( PaletteColorControl, 'ci-palette-color' );
	const current = palette.find( ( c ) => c.slug === value );

	return (
		<BaseControl
			id={ id }
			label={ label }
			help={ help }
			__nextHasNoMarginBottom
		>
			<ColorPalette
				id={ id }
				colors={ palette }
				value={ current?.color }
				disableCustomColors
				clearable
				onChange={ ( hex ) =>
					onChange(
						palette.find( ( c ) => c.color === hex )?.slug || ''
					)
				}
			/>
		</BaseControl>
	);
}
