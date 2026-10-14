/**
 * Editor: ci/reach.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { IntroPanel, SectionPreview, SectionStyle } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	return (
		<>
			<InspectorControls>
				<IntroPanel
					attributes={ attributes }
					setAttributes={ setAttributes }
					text
				>
					<ToggleControl
						label={ __( 'List of registrations', 'ci-theme' ) }
						help={ __(
							'It comes from the Institutions section (full name and relation).',
							'ci-theme'
						) }
						checked={ attributes.showList }
						onChange={ ( showList ) =>
							setAttributes( { showList } )
						}
						__nextHasNoMarginBottom
					/>
					<ToggleControl
						label={ __(
							'Large background illustration',
							'ci-theme'
						) }
						checked={ attributes.showArt }
						onChange={ ( showArt ) => setAttributes( { showArt } ) }
						__nextHasNoMarginBottom
					/>
				</IntroPanel>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
				schemes={ [] }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
