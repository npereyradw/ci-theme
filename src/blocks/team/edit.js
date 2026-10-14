/**
 * Editor: ci/team.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { ExternalLink } from '@wordpress/components';
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
					text
				>
					<TextField
						label={ __(
							'Caption when there is no photo',
							'ci-theme'
						) }
						help={ __( 'Empty = no caption.', 'ci-theme' ) }
						value={ attributes.placeholderCaption }
						onChange={ ( placeholderCaption ) =>
							setAttributes( { placeholderCaption } )
						}
					/>
					<p className="ci-editor-help">
						{ __(
							'People, their photos (featured image), roles and profiles are managed in the Team section.',
							'ci-theme'
						) }
					</p>
					<ExternalLink href="edit.php?post_type=ci_team_member">
						{ __( 'View the team', 'ci-theme' ) }
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
