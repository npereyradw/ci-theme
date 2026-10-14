/**
 * Editor: ci/areas.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	return (
		<>
			<InspectorControls>
				<IntroPanel
					attributes={ attributes }
					setAttributes={ setAttributes }
				>
					<TextField
						label={ __( 'Link text of each area', 'ci-theme' ) }
						help={ __(
							'{area} is replaced with the name of the area. Empty = no link.',
							'ci-theme'
						) }
						value={ attributes.linkPattern }
						onChange={ ( linkPattern ) =>
							setAttributes( { linkPattern } )
						}
					/>
					<p className="ci-editor-help">
						{ __(
							'The name, text and icon of each area are edited in Services → Areas.',
							'ci-theme'
						) }
					</p>
				</IntroPanel>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
