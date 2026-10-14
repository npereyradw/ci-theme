/**
 * Editor: ci/page-hero.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SectionPreview, SectionStyle, TextField } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	const dark = attributes.variant === 'dark';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Text', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __( 'Use the page title', 'ci-theme' ) }
							help={ __(
								'For templates: shows the title of each page.',
								'ci-theme'
							) }
							checked={ attributes.useTitle }
							onChange={ set( 'useTitle' ) }
							__nextHasNoMarginBottom
						/>
						{ ! attributes.useTitle && (
							<TextField
								label={ __( 'Main title (H1)', 'ci-theme' ) }
								help={ __(
									'It is the only H1 of the page.',
									'ci-theme'
								) }
								value={ attributes.title }
								onChange={ set( 'title' ) }
								multiline
								rows={ 2 }
							/>
						) }
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.lead }
							onChange={ set( 'lead' ) }
							multiline
							rows={ 4 }
						/>
						<ToggleControl
							label={ __(
								'Show the last updated date',
								'ci-theme'
							) }
							help={ __(
								'The date the page was last modified.',
								'ci-theme'
							) }
							checked={ attributes.showUpdated }
							onChange={ set( 'showUpdated' ) }
							__nextHasNoMarginBottom
						/>
						{ attributes.showUpdated && (
							<TextField
								label={ __(
									'Text before the date',
									'ci-theme'
								) }
								value={ attributes.updatedLabel }
								onChange={ set( 'updatedLabel' ) }
							/>
						) }
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Layout', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Background', 'ci-theme' ) }
							value={ attributes.variant }
							options={ [
								{
									value: 'light',
									label: __( 'Light', 'ci-theme' ),
								},
								{
									value: 'dark',
									label: __(
										'Dark, with an arc and a dot',
										'ci-theme'
									),
								},
								{
									value: 'text',
									label: __(
										'Light and compact (legal texts)',
										'ci-theme'
									),
								},
							] }
							onChange={ set( 'variant' ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{ dark ? (
							<ToggleControl
								label={ __( 'Arc and accent dot', 'ci-theme' ) }
								checked={ attributes.decor }
								onChange={ set( 'decor' ) }
								__nextHasNoMarginBottom
							/>
						) : (
							<>
								<ToggleControl
									label={ __( 'Line below', 'ci-theme' ) }
									checked={ attributes.divider }
									onChange={ set( 'divider' ) }
									__nextHasNoMarginBottom
								/>
								<ToggleControl
									label={ __(
										'Area filter (services page)',
										'ci-theme'
									) }
									help={ __(
										'Chips with the areas; they filter the Services block in its Listing layout.',
										'ci-theme'
									) }
									checked={ attributes.areaFilter }
									onChange={ set( 'areaFilter' ) }
									__nextHasNoMarginBottom
								/>
								{ attributes.areaFilter && (
									<TextField
										label={ __(
											'Text of the "all" chip',
											'ci-theme'
										) }
										value={ attributes.allLabel }
										onChange={ set( 'allLabel' ) }
									/>
								) }
							</>
						) }
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Breadcrumbs', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __(
								'Show the breadcrumbs (Home / page)',
								'ci-theme'
							) }
							checked={ attributes.breadcrumbs }
							onChange={ set( 'breadcrumbs' ) }
							__nextHasNoMarginBottom
						/>
						<TextField
							label={ __(
								'Name of this page in the breadcrumbs',
								'ci-theme'
							) }
							help={ __( 'Empty = the page title.', 'ci-theme' ) }
							value={ attributes.breadcrumbLabel }
							onChange={ set( 'breadcrumbLabel' ) }
						/>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
				schemes={ [] }
			/>
			<SectionPreview
				name={ metadata.name }
				attributes={ attributes }
				withPostContext
			/>
		</>
	);
}
