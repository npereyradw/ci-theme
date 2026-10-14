/**
 * Editor: ci/projects.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	QueryControls,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

const TEMPLATE_SOURCES = [ 'current-service', 'related' ];

export default function Edit( { attributes, setAttributes } ) {
	const listing = attributes.layout === 'listing';
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'ci-theme' ) }>
					<SelectControl
						label={ __( 'Layout', 'ci-theme' ) }
						value={ attributes.layout }
						options={ [
							{
								value: 'cards',
								label: __(
									'Section with some projects',
									'ci-theme'
								),
							},
							{
								value: 'listing',
								label: __(
									'Full listing with filters (projects page)',
									'ci-theme'
								),
							},
						] }
						onChange={ set( 'layout' ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				{ listing ? (
					<PanelBody title={ __( 'Filters', 'ci-theme' ) }>
						<div className="ci-editor-stack">
							<p className="ci-editor-help">
								{ __(
									'Every published project is shown. The areas and services of the filter come from their sections.',
									'ci-theme'
								) }
							</p>
							<TextField
								label={ __(
									'Name of the area group (screen readers)',
									'ci-theme'
								) }
								value={ attributes.filterLabel }
								onChange={ set( 'filterLabel' ) }
							/>
							<TextField
								label={ __( 'All areas button', 'ci-theme' ) }
								value={ attributes.allAreasLabel }
								onChange={ set( 'allAreasLabel' ) }
							/>
							<TextField
								label={ __(
									'Service selector label',
									'ci-theme'
								) }
								value={ attributes.serviceLabel }
								onChange={ set( 'serviceLabel' ) }
							/>
							<TextField
								label={ __(
									'All services option',
									'ci-theme'
								) }
								value={ attributes.allServicesLabel }
								onChange={ set( 'allServicesLabel' ) }
							/>
							<TextField
								label={ __( 'No results: text', 'ci-theme' ) }
								value={ attributes.emptyTitle }
								onChange={ set( 'emptyTitle' ) }
								multiline
								rows={ 2 }
							/>
							<TextField
								label={ __(
									'No results: button (empty = no button)',
									'ci-theme'
								) }
								value={ attributes.emptyButton }
								onChange={ set( 'emptyButton' ) }
							/>
						</div>
					</PanelBody>
				) : (
					<>
						<IntroPanel
							attributes={ attributes }
							setAttributes={ setAttributes }
							link
						>
							{ attributes.source === 'current-service' && (
								<p className="ci-editor-help">
									{ __(
										'The link goes to the projects of the service being viewed.',
										'ci-theme'
									) }
								</p>
							) }
							<ToggleControl
								label={ __(
									'Line above the title',
									'ci-theme'
								) }
								help={ __(
									'Separates the section from the previous one when both are white.',
									'ci-theme'
								) }
								checked={ attributes.divider }
								onChange={ set( 'divider' ) }
								__nextHasNoMarginBottom
							/>
						</IntroPanel>
						<PanelBody
							title={ __( 'Projects to show', 'ci-theme' ) }
						>
							<QueryControls
								attributes={ attributes }
								setAttributes={ setAttributes }
								postType="ci_project"
								sources={ [
									'all',
									'area',
									'service',
									'manual',
									...TEMPLATE_SOURCES,
								] }
							/>
						</PanelBody>
					</>
				) }
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview
				name={ metadata.name }
				attributes={ attributes }
				withPostContext={
					! listing && TEMPLATE_SOURCES.includes( attributes.source )
				}
			/>
		</>
	);
}
