/**
 * Editor: ci/post-header (single post template).
 * Title, category, date, excerpt and image come from each post; here, the fixed texts.
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
				<PanelBody title={ __( 'Texts', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'The lead is the excerpt of each post (or the start of the text when it is empty).',
								'ci-theme'
							) }
						</p>
						<TextField
							label={ __( 'After the reading time', 'ci-theme' ) }
							value={ attributes.readingSuffix }
							onChange={ set( 'readingSuffix' ) }
						/>
						<TextField
							label={ __(
								'Author (by default, the site title)',
								'ci-theme'
							) }
							value={ attributes.authorName }
							onChange={ set( 'authorName' ) }
						/>
						<TextField
							label={ __( 'Author description', 'ci-theme' ) }
							value={ attributes.authorRole }
							onChange={ set( 'authorRole' ) }
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
