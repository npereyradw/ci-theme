/**
 * Editor: ci/post-body (single post template).
 * The text is written in each post; here, the share buttons.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SectionPreview, TextField } from '@ci-theme/editor';
import metadata from './block.json';

const NETWORKS = [
	[ 'linkedin', 'LinkedIn' ],
	[ 'facebook', 'Facebook' ],
	[ 'whatsapp', 'WhatsApp' ],
	[ 'x', 'X' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const networks = attributes.networks || [];
	const toggle = ( key, on ) =>
		setAttributes( {
			// Keep the order of the list of networks.
			networks: NETWORKS.map( ( [ k ] ) => k ).filter( ( k ) =>
				k === key ? on : networks.includes( k )
			),
		} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Share', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Label', 'ci-theme' ) }
							value={ attributes.shareLabel }
							onChange={ ( shareLabel ) =>
								setAttributes( { shareLabel } )
							}
						/>
						{ NETWORKS.map( ( [ key, label ] ) => (
							<CheckboxControl
								key={ key }
								label={ label }
								checked={ networks.includes( key ) }
								onChange={ ( on ) => toggle( key, on ) }
								__nextHasNoMarginBottom
							/>
						) ) }
						<p className="ci-editor-help">
							{ __(
								'Without any network checked, the share row is hidden.',
								'ci-theme'
							) }
						</p>
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
