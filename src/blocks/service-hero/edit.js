/**
 * Editor: ci/service-hero (single service template).
 * The texts of each service are edited in the service; here, the fixed texts of the header.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { LinkField, SectionPreview, TextField } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	const text = ( key, label, multiline = false ) => (
		<TextField
			key={ key }
			label={ label }
			value={ attributes[ key ] }
			onChange={ set( key ) }
			multiline={ multiline }
			rows={ 2 }
		/>
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Active service', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'Title, number, area, description and "What it includes" come from each service. These texts are the same for all of them.',
								'ci-theme'
							) }
						</p>
						{ text(
							'quoteLabel',
							__( 'Quote button', 'ci-theme' )
						) }
						<LinkField
							label={ __(
								'Button destination (the service is added)',
								'ci-theme'
							) }
							url={ attributes.quoteUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { quoteUrl: url } )
							}
						/>
						{ text(
							'whatsappLabel',
							__(
								'WhatsApp button (empty = no button)',
								'ci-theme'
							)
						) }
						{ text(
							'includesTitle',
							__( 'Box title', 'ci-theme' )
						) }
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Service "coming soon"', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						{ text( 'soonBadge', __( 'Badge', 'ci-theme' ) ) }
						{ text(
							'soonIncludesTitle',
							__( 'Box title', 'ci-theme' )
						) }
						{ text(
							'notifyLabel',
							__( 'Form: title', 'ci-theme' )
						) }
						{ text(
							'notifyPlaceholder',
							__( 'Form: email placeholder', 'ci-theme' )
						) }
						{ text(
							'notifyButton',
							__( 'Form: button', 'ci-theme' )
						) }
						{ text(
							'notifySending',
							__( 'Form: button while sending', 'ci-theme' )
						) }
						{ text(
							'notifyPrivacyText',
							__( 'Text before the privacy link', 'ci-theme' )
						) }
						{ text(
							'notifyPrivacyLabel',
							__( 'Privacy link', 'ci-theme' )
						) }
						{ text(
							'notifyLinkLabel',
							__( 'Contact link (empty = no link)', 'ci-theme' )
						) }
						<LinkField
							label={ __(
								'Contact link destination',
								'ci-theme'
							) }
							url={ attributes.notifyLinkUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { notifyLinkUrl: url } )
							}
						/>
						{ text(
							'msgRequired',
							__( 'Message: empty email', 'ci-theme' )
						) }
						{ text(
							'msgEmail',
							__( 'Message: invalid email', 'ci-theme' )
						) }
						{ text(
							'msgError',
							__( 'Message: sending error', 'ci-theme' ),
							true
						) }
						<p className="ci-editor-help">
							{ __(
								'The form is the "notify me" form of the site settings; the confirmation message is edited in Contact Form 7.',
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
