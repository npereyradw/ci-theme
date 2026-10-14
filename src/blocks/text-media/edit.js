/**
 * Editor: ci/text-media.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	ImageField,
	IntroPanel,
	SectionPreview,
	SectionStyle,
} from '@ci-theme/editor';
import metadata from './block.json';

const COVERS = [
	[ '0', __( 'Blue with arc', 'ci-theme' ) ],
	[ '1', __( 'Gray with yellow sun', 'ci-theme' ) ],
	[ '2', __( 'Gray with rings', 'ci-theme' ) ],
	[ '3', __( 'Blue with red bar', 'ci-theme' ) ],
	[ '4', __( 'Cream with logo', 'ci-theme' ) ],
	[ '5', __( 'Dark blue', 'ci-theme' ) ],
];

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	return (
		<>
			<InspectorControls>
				<IntroPanel
					attributes={ attributes }
					setAttributes={ setAttributes }
					text
					textLabel={ __(
						'Text (a blank line separates paragraphs)',
						'ci-theme'
					) }
				/>
				<PanelBody title={ __( 'Illustration', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Show', 'ci-theme' ) }
							value={ attributes.media }
							options={ [
								{
									value: 'cover',
									label: __( 'Graphic cover', 'ci-theme' ),
								},
								{
									value: 'image',
									label: __( 'An image', 'ci-theme' ),
								},
							] }
							onChange={ set( 'media' ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{ attributes.media === 'image' ? (
							<ImageField
								label={ __( 'Image', 'ci-theme' ) }
								help={ __(
									'Without an image, the graphic cover is shown.',
									'ci-theme'
								) }
								value={ attributes.image }
								onChange={ set( 'image' ) }
							/>
						) : (
							<SelectControl
								label={ __( 'Cover', 'ci-theme' ) }
								value={ attributes.coverSeed }
								options={ COVERS.map(
									( [ value, label ] ) => ( { value, label } )
								) }
								onChange={ set( 'coverSeed' ) }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						) }
						<SelectControl
							label={ __( 'Aspect ratio', 'ci-theme' ) }
							value={ attributes.ratio }
							options={ [
								'5 / 4',
								'4 / 3',
								'1 / 1',
								'16 / 9',
							].map( ( r ) => ( {
								value: r,
								label: r.replace( ' / ', ':' ),
							} ) ) }
							onChange={ set( 'ratio' ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<SelectControl
							label={ __( 'Side', 'ci-theme' ) }
							value={ attributes.mediaPosition }
							options={ [
								{
									value: 'right',
									label: __( 'Right', 'ci-theme' ),
								},
								{
									value: 'left',
									label: __( 'Left', 'ci-theme' ),
								},
							] }
							onChange={ set( 'mediaPosition' ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
