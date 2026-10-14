/**
 * Editor: ci/process.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import {
	ImageField,
	IntroPanel,
	ItemsControl,
	SectionPreview,
	SectionStyle,
} from '@ci-theme/editor';
import metadata from './block.json';

const SEEDS = [ '0', '1', '2', '3', '4', '5' ].map( ( value ) => ( {
	value,
	/* translators: %d: illustration number. */
	label: sprintf( __( 'Illustration %d', 'ci-theme' ), Number( value ) + 1 ),
} ) );

export default function Edit( { attributes, setAttributes } ) {
	const cover = attributes.layout === 'cover';
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Layout', 'ci-theme' ) }
							value={ attributes.layout }
							options={ [
								{
									value: 'timeline',
									label: __( 'Timeline', 'ci-theme' ),
								},
								{
									value: 'cover',
									label: __(
										'List with an illustration',
										'ci-theme'
									),
								},
							] }
							onChange={ ( layout ) =>
								setAttributes( { layout } )
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{ cover && (
							<>
								<p className="ci-editor-help">
									{ __(
										'In the service template each service uses its own cover. On a page, choose an image or an illustration.',
										'ci-theme'
									) }
								</p>
								<ImageField
									label={ __(
										'Image (optional)',
										'ci-theme'
									) }
									value={ attributes.image }
									onChange={ ( image ) =>
										setAttributes( { image } )
									}
								/>
								{ ! attributes.image && (
									<SelectControl
										label={ __(
											'Illustration',
											'ci-theme'
										) }
										value={ attributes.coverSeed }
										options={ SEEDS }
										onChange={ ( coverSeed ) =>
											setAttributes( { coverSeed } )
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								) }
							</>
						) }
					</div>
				</PanelBody>
				<IntroPanel
					attributes={ attributes }
					setAttributes={ setAttributes }
					showTitle={ ! cover }
				/>
				<PanelBody title={ __( 'Steps', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'The numbering and the colors (white, yellow, blue) are automatic.',
								'ci-theme'
							) }
						</p>
						<ItemsControl
							items={ attributes.steps }
							onChange={ ( steps ) => setAttributes( { steps } ) }
							max={ 6 }
							addLabel={ __( 'Add step', 'ci-theme' ) }
							fields={ [
								{
									key: 'title',
									label: __( 'Title', 'ci-theme' ),
								},
								{
									key: 'text',
									label: __( 'Text', 'ci-theme' ),
									multiline: true,
								},
							] }
						/>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview
				name={ metadata.name }
				attributes={ attributes }
				withPostContext={ cover }
			/>
		</>
	);
}
