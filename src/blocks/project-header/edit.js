/**
 * Editor: ci/project-header (single project template).
 * The data is edited in each project; here, the labels of the fact sheet.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SectionPreview, TextField } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Fact sheet', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'Title, area, image and details come from each project. Empty details are not shown.',
								'ci-theme'
							) }
						</p>
						<TextField
							label={ __( 'Client', 'ci-theme' ) }
							value={ attributes.clientLabel }
							onChange={ set( 'clientLabel' ) }
						/>
						<TextField
							label={ __( 'Location', 'ci-theme' ) }
							value={ attributes.locationLabel }
							onChange={ set( 'locationLabel' ) }
						/>
						<TextField
							label={ __( 'Service', 'ci-theme' ) }
							value={ attributes.serviceLabel }
							onChange={ set( 'serviceLabel' ) }
						/>
						<TextField
							label={ __( 'Year', 'ci-theme' ) }
							value={ attributes.yearLabel }
							onChange={ set( 'yearLabel' ) }
						/>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionPreview
				name={ metadata.name }
				attributes={ attributes }
				withPostContext
			/>
		</>
	);
}
