/**
 * Editor: ci/contact.
 * Contact Form 7 form, request types, field labels, messages, success text and the side column
 * (WhatsApp, contact details and social profiles from the site settings).
 */

import apiFetch from '@wordpress/api-fetch';
import { InspectorControls } from '@wordpress/block-editor';
import {
	ExternalLink,
	PanelBody,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	ItemsControl,
	LinkField,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

const FIELDS = [
	[ 'name', __( 'Name (required)', 'ci-theme' ) ],
	[ 'company', __( 'Company', 'ci-theme' ) ],
	[ 'email', __( 'Email (required)', 'ci-theme' ) ],
	[ 'phone', __( 'Phone', 'ci-theme' ) ],
	[ 'service', __( 'Service', 'ci-theme' ) ],
	[ 'location', __( 'Location', 'ci-theme' ) ],
	[ 'message', __( 'Message (required)', 'ci-theme' ) ],
];

/** Contact Form 7 forms (listed only for users who can edit them). */
function useForms() {
	const [ forms, setForms ] = useState( [] );
	useEffect( () => {
		apiFetch( { path: '/contact-form-7/v1/contact-forms?per_page=50' } )
			.then( ( list ) => setForms( Array.isArray( list ) ? list : [] ) )
			.catch( () => setForms( [] ) );
	}, [] );
	return forms;
}

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	const forms = useForms();
	const fields = attributes.fields || {};
	const setField = ( name, key, value ) =>
		setAttributes( {
			fields: {
				...fields,
				[ name ]: { ...fields[ name ], [ key ]: value },
			},
		} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Form', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Contact Form 7 form', 'ci-theme' ) }
							value={ String( attributes.formId || 0 ) }
							options={ [
								{
									value: '0',
									label: __(
										'The one in the site settings',
										'ci-theme'
									),
								},
								...forms.map( ( f ) => ( {
									value: String( f.id ),
									label: f.title,
								} ) ),
							] }
							onChange={ ( v ) =>
								setAttributes( {
									formId: parseInt( v, 10 ) || 0,
								} )
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<p className="ci-editor-help">
							{ __(
								'Recipient, subject and mail texts are set in Contact → Contact Forms.',
								'ci-theme'
							) }
						</p>
						<ExternalLink href="admin.php?page=wpcf7">
							{ __( 'Open Contact Form 7', 'ci-theme' ) }
						</ExternalLink>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Request type', 'ci-theme' ) }
					initialOpen={ false }
				>
					<ItemsControl
						items={ attributes.types }
						onChange={ set( 'types' ) }
						max={ 3 }
						min={ 1 }
						addLabel={ __( 'Add type', 'ci-theme' ) }
						titleKey="label"
						fields={ [
							{
								key: 'value',
								label: __(
									'Value (sent in the mail subject)',
									'ci-theme'
								),
							},
							{
								key: 'label',
								label: __( 'Text', 'ci-theme' ),
							},
							{
								key: 'short',
								label: __( 'Short text (phones)', 'ci-theme' ),
							},
							{
								key: 'cta',
								label: __( 'Send button text', 'ci-theme' ),
							},
						] }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Fields', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						{ FIELDS.map( ( [ name, title ] ) => (
							<div
								key={ name }
								className="ci-editor-stack ci-editor-group"
							>
								<strong>{ title }</strong>
								<TextField
									label={ __( 'Label', 'ci-theme' ) }
									value={ fields[ name ]?.label }
									onChange={ ( v ) =>
										setField( name, 'label', v )
									}
								/>
								<TextField
									label={
										name === 'service'
											? __( 'First option', 'ci-theme' )
											: __( 'Placeholder', 'ci-theme' )
									}
									value={ fields[ name ]?.placeholder }
									onChange={ ( v ) =>
										setField( name, 'placeholder', v )
									}
								/>
							</div>
						) ) }
						<TextField
							label={ __( 'Last service option', 'ci-theme' ) }
							help={ __( 'Empty = no such option.', 'ci-theme' ) }
							value={ attributes.unsureOption }
							onChange={ set( 'unsureOption' ) }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Messages and note', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Required field', 'ci-theme' ) }
							value={ attributes.msgRequired }
							onChange={ set( 'msgRequired' ) }
						/>
						<TextField
							label={ __( 'Invalid email', 'ci-theme' ) }
							value={ attributes.msgEmail }
							onChange={ set( 'msgEmail' ) }
						/>
						<TextField
							label={ __( 'Sending error', 'ci-theme' ) }
							value={ attributes.msgError }
							onChange={ set( 'msgError' ) }
							multiline
							rows={ 2 }
						/>
						<TextField
							label={ __( 'Button while sending', 'ci-theme' ) }
							value={ attributes.sendingLabel }
							onChange={ set( 'sendingLabel' ) }
						/>
						<TextField
							label={ __( 'Required fields note', 'ci-theme' ) }
							value={ attributes.requiredNote }
							onChange={ set( 'requiredNote' ) }
						/>
						<TextField
							label={ __(
								'Text before the privacy link',
								'ci-theme'
							) }
							value={ attributes.privacyText }
							onChange={ set( 'privacyText' ) }
						/>
						<TextField
							label={ __( 'Privacy link: text', 'ci-theme' ) }
							value={ attributes.privacyLabel }
							onChange={ set( 'privacyLabel' ) }
						/>
						<LinkField
							label={ __(
								'Privacy link: destination (empty = the privacy page)',
								'ci-theme'
							) }
							url={ attributes.privacyUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { privacyUrl: url } )
							}
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Success message', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Title', 'ci-theme' ) }
							value={ attributes.successTitle }
							onChange={ set( 'successTitle' ) }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.successText }
							onChange={ set( 'successText' ) }
							multiline
							rows={ 2 }
						/>
						<TextField
							label={ __( 'Link to WhatsApp', 'ci-theme' ) }
							value={ attributes.successWhatsapp }
							onChange={ set( 'successWhatsapp' ) }
						/>
						<TextField
							label={ __(
								'Button to send another one',
								'ci-theme'
							) }
							value={ attributes.againLabel }
							onChange={ set( 'againLabel' ) }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Side column', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __( 'WhatsApp card', 'ci-theme' ) }
							checked={ attributes.showWhatsapp }
							onChange={ set( 'showWhatsapp' ) }
							__nextHasNoMarginBottom
						/>
						<TextField
							label={ __( 'WhatsApp: title', 'ci-theme' ) }
							value={ attributes.whatsappTitle }
							onChange={ set( 'whatsappTitle' ) }
						/>
						<TextField
							label={ __( 'WhatsApp: text', 'ci-theme' ) }
							value={ attributes.whatsappText }
							onChange={ set( 'whatsappText' ) }
						/>
						<TextField
							label={ __( 'Phone label', 'ci-theme' ) }
							value={ attributes.labelPhone }
							onChange={ set( 'labelPhone' ) }
						/>
						<TextField
							label={ __( 'Email label', 'ci-theme' ) }
							value={ attributes.labelEmail }
							onChange={ set( 'labelEmail' ) }
						/>
						<TextField
							label={ __( 'Hours label', 'ci-theme' ) }
							value={ attributes.labelHours }
							onChange={ set( 'labelHours' ) }
						/>
						<TextField
							label={ __( 'Location label', 'ci-theme' ) }
							value={ attributes.labelLocation }
							onChange={ set( 'labelLocation' ) }
						/>
						<ToggleControl
							label={ __( 'Social profiles', 'ci-theme' ) }
							checked={ attributes.showSocials }
							onChange={ set( 'showSocials' ) }
							__nextHasNoMarginBottom
						/>
						<p className="ci-editor-help">
							{ __(
								'Contact details and social profiles come from the site settings.',
								'ci-theme'
							) }
						</p>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
				schemes={ [ 'light', 'muted' ] }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
