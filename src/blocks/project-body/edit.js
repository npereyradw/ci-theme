/**
 * Editor: ci/project-body (single project template).
 * The summary and the content come from each project; here, the fixed texts.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { LinkField, SectionPreview, TextField } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Texts', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'The summary (excerpt) and the description are written in each project.',
								'ci-theme'
							) }
						</p>
						<TextField
							label={ __( 'Title', 'ci-theme' ) }
							value={ attributes.title }
							onChange={ set( 'title' ) }
						/>
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Box', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Title', 'ci-theme' ) }
							value={ attributes.asideTitle }
							onChange={ set( 'asideTitle' ) }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.asideText }
							onChange={ set( 'asideText' ) }
							multiline
							rows={ 3 }
						/>
						<TextField
							label={ __( 'Button', 'ci-theme' ) }
							value={ attributes.buttonLabel }
							onChange={ set( 'buttonLabel' ) }
						/>
						<LinkField
							label={ __(
								'Button destination (the service is added)',
								'ci-theme'
							) }
							url={ attributes.buttonUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { buttonUrl: url } )
							}
						/>
						<TextField
							label={ __(
								'Link to the service (empty = no link)',
								'ci-theme'
							) }
							help={ __(
								'It is followed by the name of the service.',
								'ci-theme'
							) }
							value={ attributes.serviceLinkLabel }
							onChange={ set( 'serviceLinkLabel' ) }
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
