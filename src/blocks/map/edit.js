/**
 * Editor: ci/map.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	ImageField,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

function NumberField( { label, help, value, onChange, step = 'any' } ) {
	return (
		<TextControl
			type="number"
			step={ step }
			label={ label }
			help={ help }
			value={ value ?? '' }
			onChange={ ( v ) => onChange( v === '' ? 0 : parseFloat( v ) ) }
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Location card', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Place', 'ci-theme' ) }
							help={ __(
								'Empty = the location of the site settings.',
								'ci-theme'
							) }
							value={ attributes.place }
							onChange={ set( 'place' ) }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.subtitle }
							onChange={ set( 'subtitle' ) }
						/>
						<TextField
							label={ __( 'Directions link', 'ci-theme' ) }
							help={ __( 'Empty = no link.', 'ci-theme' ) }
							value={ attributes.directionsLabel }
							onChange={ set( 'directionsLabel' ) }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Interactive map', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Button', 'ci-theme' ) }
							value={ attributes.exploreLabel }
							onChange={ set( 'exploreLabel' ) }
						/>
						<TextField
							label={ __( 'While loading', 'ci-theme' ) }
							value={ attributes.loadingLabel }
							onChange={ set( 'loadingLabel' ) }
						/>
						<TextField
							label={ __( 'If it fails', 'ci-theme' ) }
							value={ attributes.errorLabel }
							onChange={ set( 'errorLabel' ) }
						/>
						<NumberField
							label={ __( 'Latitude', 'ci-theme' ) }
							value={ attributes.lat }
							onChange={ set( 'lat' ) }
						/>
						<NumberField
							label={ __( 'Longitude', 'ci-theme' ) }
							value={ attributes.lng }
							onChange={ set( 'lng' ) }
						/>
						<NumberField
							label={ __( 'Initial zoom', 'ci-theme' ) }
							help={ __(
								'4 = country, 15 = street.',
								'ci-theme'
							) }
							value={ attributes.zoom }
							onChange={ set( 'zoom' ) }
							step="0.1"
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Preview image', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'Shown before the map loads, centered on the location. Without coordinates there is no interactive map; without images the card sits on a plain background.',
								'ci-theme'
							) }
						</p>
						<ImageField
							label={ __( 'Desktop', 'ci-theme' ) }
							value={ attributes.imageDesktop }
							onChange={ set( 'imageDesktop' ) }
						/>
						<ImageField
							label={ __( 'Phone', 'ci-theme' ) }
							value={ attributes.imageMobile }
							onChange={ set( 'imageMobile' ) }
						/>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
				schemes={ [ 'light', 'muted' ] }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
