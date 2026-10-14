/**
 * "Style" panels of a section (Styles tab of the sidebar) and the animations option under
 * "Advanced". Design values only: scheme, palette colors and four spacing steps.
 */

import {
	InspectorAdvancedControls,
	InspectorControls,
} from '@wordpress/block-editor';
// ToggleGroupControl is still marked as experimental, but it is the control the editor itself
// uses for short options (alignment, units); the alias is kept across versions.
/* eslint-disable @wordpress/no-unsafe-wp-apis */
import {
	PanelBody,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
/* eslint-enable @wordpress/no-unsafe-wp-apis */
import { __ } from '@wordpress/i18n';
import { PaletteColorControl } from './palette-color';

const SCHEMES = [
	{ value: 'light', label: __( 'Light', 'ci-theme' ) },
	{ value: 'muted', label: __( 'Gray', 'ci-theme' ) },
	{ value: 'dark', label: __( 'Dark', 'ci-theme' ) },
];

const SPACES = [
	{ value: 'none', label: __( 'None', 'ci-theme' ) },
	{ value: 'sm', label: __( 'Small', 'ci-theme' ) },
	{ value: 'md', label: __( 'Normal', 'ci-theme' ) },
	{ value: 'lg', label: __( 'Large', 'ci-theme' ) },
];

function Choice( { label, help, value, options, onChange } ) {
	return (
		<ToggleGroupControl
			label={ label }
			help={ help }
			value={ value }
			onChange={ onChange }
			isBlock
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		>
			{ options.map( ( o ) => (
				<ToggleGroupControlOption
					key={ o.value }
					value={ o.value }
					label={ o.label }
				/>
			) ) }
		</ToggleGroupControl>
	);
}

/**
 * @param {Object}                       props
 * @param {Object}                       props.attributes
 * @param {(attributes: Object) => void} props.setAttributes
 * @param {string[]}                     [props.schemes]     Schemes allowed by the section (all three by default; [] hides it).
 * @param {string[]}                     [props.colors]      Editable colors: title, text, accent.
 * @param {boolean}                      [props.spacing]     Show the spacing.
 */
export function SectionStyle( {
	attributes,
	setAttributes,
	schemes = [ 'light', 'muted', 'dark' ],
	colors = [ 'title', 'text', 'accent' ],
	spacing = true,
} ) {
	const colorFields = {
		title: [ 'titleColor', __( 'Titles', 'ci-theme' ) ],
		text: [ 'textColor', __( 'Texts', 'ci-theme' ) ],
		accent: [ 'accentColor', __( 'Accent (eyebrow bar)', 'ci-theme' ) ],
	};

	return (
		<>
			<InspectorControls group="styles">
				{ schemes.length > 1 && (
					<PanelBody title={ __( 'Scheme', 'ci-theme' ) }>
						<Choice
							label={ __( 'Section background', 'ci-theme' ) }
							value={ attributes.scheme }
							options={ SCHEMES.filter( ( s ) =>
								schemes.includes( s.value )
							) }
							onChange={ ( scheme ) =>
								setAttributes( { scheme } )
							}
						/>
					</PanelBody>
				) }
				{ colors.length > 0 && (
					<PanelBody
						title={ __( 'Colors', 'ci-theme' ) }
						initialOpen={ false }
					>
						<div className="ci-editor-stack">
							<p className="ci-editor-help">
								{ __(
									'Empty = the scheme color. Only colors from the site palette can be chosen.',
									'ci-theme'
								) }
							</p>
							{ colors.map( ( key ) => (
								<PaletteColorControl
									key={ key }
									label={ colorFields[ key ][ 1 ] }
									value={
										attributes[ colorFields[ key ][ 0 ] ]
									}
									onChange={ ( slug ) =>
										setAttributes( {
											[ colorFields[ key ][ 0 ] ]: slug,
										} )
									}
								/>
							) ) }
						</div>
					</PanelBody>
				) }
				{ spacing && (
					<PanelBody
						title={ __( 'Spacing', 'ci-theme' ) }
						initialOpen={ false }
					>
						<div className="ci-editor-stack">
							<Choice
								label={ __( 'Top', 'ci-theme' ) }
								value={ attributes.spaceTop }
								options={ SPACES }
								onChange={ ( spaceTop ) =>
									setAttributes( { spaceTop } )
								}
							/>
							<Choice
								label={ __( 'Bottom', 'ci-theme' ) }
								value={ attributes.spaceBottom }
								options={ SPACES }
								onChange={ ( spaceBottom ) =>
									setAttributes( { spaceBottom } )
								}
							/>
						</div>
					</PanelBody>
				) }
			</InspectorControls>
			<InspectorAdvancedControls>
				<ToggleControl
					label={ __( 'Animations', 'ci-theme' ) }
					help={ __(
						'When off, the section appears without motion.',
						'ci-theme'
					) }
					checked={ attributes.animate !== false }
					onChange={ ( animate ) => setAttributes( { animate } ) }
					__nextHasNoMarginBottom
				/>
			</InspectorAdvancedControls>
		</>
	);
}
