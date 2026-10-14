/**
 * Editor: ci/site-header.
 * Menu links (repeater), logo for dark backgrounds, quote button and the texts of the submenu
 * and the full-screen menu. Services come from the service post type and contact details from
 * the site settings.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	ImageField,
	LinkField,
	Repeater,
	SectionPreview,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Menu', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'The same links are used in the bar and in the full-screen menu.',
								'ci-theme'
							) }
						</p>
						<Repeater
							items={ attributes.items }
							onChange={ set( 'items' ) }
							max={ 7 }
							addLabel={ __( 'Add link', 'ci-theme' ) }
							newItem={ { label: '', url: '', services: false } }
							itemLabel={ ( item ) => item.label }
							renderItem={ ( item, update ) => (
								<>
									<TextField
										label={ __( 'Text', 'ci-theme' ) }
										value={ item.label }
										onChange={ ( label ) =>
											update( { label } )
										}
									/>
									<LinkField
										label={ __( 'Link', 'ci-theme' ) }
										url={ item.url }
										showNewTab={ false }
										onChange={ update }
									/>
									<ToggleControl
										label={ __(
											'Show the services submenu',
											'ci-theme'
										) }
										help={ __(
											'Only one link can have it.',
											'ci-theme'
										) }
										checked={ !! item.services }
										onChange={ ( services ) =>
											update( { services } )
										}
										__nextHasNoMarginBottom
									/>
								</>
							) }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Logo', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<p className="ci-editor-help">
							{ __(
								'The logo is the one set in Site Identity. Add a light version for the dark backgrounds (top of dark pages and the full-screen menu).',
								'ci-theme'
							) }
						</p>
						<ImageField
							label={ __(
								'Logo for dark backgrounds',
								'ci-theme'
							) }
							value={ attributes.logoDark }
							onChange={ ( logoDark ) =>
								setAttributes( { logoDark: logoDark || 0 } )
							}
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Quote button', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.ctaLabel }
							onChange={ set( 'ctaLabel' ) }
						/>
						<TextField
							label={ __( 'Short text (phones)', 'ci-theme' ) }
							value={ attributes.ctaShort }
							onChange={ set( 'ctaShort' ) }
						/>
						<LinkField
							label={ __( 'Link', 'ci-theme' ) }
							url={ attributes.ctaUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { ctaUrl: url } )
							}
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Services submenu', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Link to all services', 'ci-theme' ) }
							value={ attributes.subAllLabel }
							onChange={ set( 'subAllLabel' ) }
						/>
						<LinkField
							label={ __( 'Services page', 'ci-theme' ) }
							url={ attributes.subAllUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { subAllUrl: url } )
							}
						/>
						<TextField
							label={ __( 'Box: title', 'ci-theme' ) }
							value={ attributes.asideTitle }
							onChange={ set( 'asideTitle' ) }
						/>
						<TextField
							label={ __( 'Box: text', 'ci-theme' ) }
							value={ attributes.asideText }
							onChange={ set( 'asideText' ) }
							multiline
							rows={ 2 }
						/>
						<TextField
							label={ __( 'Box: link', 'ci-theme' ) }
							value={ attributes.asideLabel }
							onChange={ set( 'asideLabel' ) }
						/>
						<LinkField
							label={ __( 'Box: destination', 'ci-theme' ) }
							url={ attributes.asideUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { asideUrl: url } )
							}
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Full-screen menu', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __(
								'Title of the services column',
								'ci-theme'
							) }
							value={ attributes.menuServicesTitle }
							onChange={ set( 'menuServicesTitle' ) }
						/>
						<p className="ci-editor-help">
							{ __(
								'Phone, email, hours and WhatsApp come from the site settings; the services, from the Services section.',
								'ci-theme'
							) }
						</p>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
