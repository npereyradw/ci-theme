/**
 * Editor: ci/testimonials.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { ExternalLink } from '@wordpress/components';
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
				>
					<p className="ci-editor-help">
						{ __(
							'Testimonials are managed in the Testimonials section. This section stays hidden until at least one is published.',
							'ci-theme'
						) }
					</p>
					<ExternalLink href="edit.php?post_type=ci_testimonial">
						{ __( 'View the testimonials', 'ci-theme' ) }
					</ExternalLink>
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
