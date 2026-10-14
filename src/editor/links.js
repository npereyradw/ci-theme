/**
 * Links and buttons.
 * - LinkField: URL with a search of the site pages and "open in a new tab".
 * - ButtonsControl: repeater of buttons { label, kind, url, style, newTab }.
 *   kind "whatsapp" uses the number from the site settings (no URL is typed).
 */

import { URLInput } from '@wordpress/block-editor';
import {
	BaseControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';
import { Repeater } from './repeater';

export function LinkField( {
	label,
	help,
	url,
	newTab,
	onChange,
	showNewTab = true,
} ) {
	const id = useInstanceId( LinkField, 'ci-link-field' );
	return (
		<div className="ci-editor-stack">
			<BaseControl
				id={ id }
				label={ label }
				help={
					help ||
					__(
						'Search for a page of the site or paste an address.',
						'ci-theme'
					)
				}
				__nextHasNoMarginBottom
			>
				<URLInput
					id={ id }
					value={ url || '' }
					onChange={ ( value ) => onChange( { url: value } ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</BaseControl>
			{ showNewTab && (
				<ToggleControl
					label={ __( 'Open in a new tab', 'ci-theme' ) }
					checked={ !! newTab }
					onChange={ ( value ) => onChange( { newTab: value } ) }
					__nextHasNoMarginBottom
				/>
			) }
		</div>
	);
}

const STYLE_OPTIONS = {
	primary: __( 'Red (primary)', 'ci-theme' ),
	dark: __( 'Blue', 'ci-theme' ),
	outline: __( 'Blue outline', 'ci-theme' ),
	'on-dark': __( 'White outline (on dark backgrounds)', 'ci-theme' ),
};

/**
 * @param {Object}                    props
 * @param {Array}                     props.value    Buttons.
 * @param {(value: Object[]) => void} props.onChange
 * @param {string[]}                  [props.styles] Styles allowed by the section.
 * @param {number}                    [props.max]
 */
export function ButtonsControl( {
	value = [],
	onChange,
	styles = [ 'primary', 'outline', 'on-dark', 'dark' ],
	max = 3,
} ) {
	return (
		<Repeater
			items={ value }
			onChange={ onChange }
			max={ max }
			addLabel={ __( 'Add button', 'ci-theme' ) }
			newItem={ {
				label: '',
				kind: 'link',
				url: '',
				style: styles[ 0 ],
				newTab: false,
			} }
			itemLabel={ ( b ) => b.label }
			renderItem={ ( b, update ) => (
				<>
					<TextControl
						label={ __( 'Text', 'ci-theme' ) }
						value={ b.label || '' }
						onChange={ ( label ) => update( { label } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ __( 'Destination', 'ci-theme' ) }
						value={ b.kind || 'link' }
						options={ [
							{
								value: 'link',
								label: __( 'A link', 'ci-theme' ),
							},
							{
								value: 'whatsapp',
								label: __(
									'WhatsApp (from the site settings)',
									'ci-theme'
								),
							},
						] }
						onChange={ ( kind ) => update( { kind } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					{ ( b.kind || 'link' ) === 'link' && (
						<LinkField
							label={ __( 'Link', 'ci-theme' ) }
							url={ b.url }
							newTab={ b.newTab }
							onChange={ update }
						/>
					) }
					<SelectControl
						label={ __( 'Style', 'ci-theme' ) }
						value={ b.style }
						options={ styles.map( ( s ) => ( {
							value: s,
							label: STYLE_OPTIONS[ s ],
						} ) ) }
						onChange={ ( style ) => update( { style } ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</>
			) }
		/>
	);
}
