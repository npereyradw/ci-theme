/**
 * Editor: ci/site-footer.
 * Logo for dark backgrounds, text under the logo, site links (repeater), column titles,
 * institutions and legal notice. Services, contact details, social profiles and institutions
 * are loaded automatically.
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
				<PanelBody title={ __( 'Brand', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<ImageField
							label={ __(
								'Logo for dark backgrounds',
								'ci-theme'
							) }
							help={ __(
								'Without it, the logo of Site Identity is used.',
								'ci-theme'
							) }
							value={ attributes.logoDark }
							onChange={ ( logoDark ) =>
								setAttributes( { logoDark: logoDark || 0 } )
							}
						/>
						<TextField
							label={ __( 'Text under the logo', 'ci-theme' ) }
							value={ attributes.about }
							onChange={ set( 'about' ) }
							multiline
							rows={ 3 }
						/>
						<p className="ci-editor-help">
							{ __(
								'Social profiles come from the site settings.',
								'ci-theme'
							) }
						</p>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Site links', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Column title', 'ci-theme' ) }
							value={ attributes.siteTitle }
							onChange={ set( 'siteTitle' ) }
						/>
						<Repeater
							items={ attributes.siteLinks }
							onChange={ set( 'siteLinks' ) }
							max={ 10 }
							addLabel={ __( 'Add link', 'ci-theme' ) }
							newItem={ { label: '', url: '' } }
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
								</>
							) }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Services and contact', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __(
								'Show the services column',
								'ci-theme'
							) }
							checked={ attributes.showServices }
							onChange={ set( 'showServices' ) }
							__nextHasNoMarginBottom
						/>
						<TextField
							label={ __( 'Services title', 'ci-theme' ) }
							value={ attributes.servicesTitle }
							onChange={ set( 'servicesTitle' ) }
						/>
						<TextField
							label={ __( 'Contact title', 'ci-theme' ) }
							value={ attributes.contactTitle }
							onChange={ set( 'contactTitle' ) }
						/>
						<p className="ci-editor-help">
							{ __(
								'Phone, email, hours and location come from the site settings.',
								'ci-theme'
							) }
						</p>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Institutions and legal notice', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __(
								'Show the institution logos',
								'ci-theme'
							) }
							checked={ attributes.showInstitutions }
							onChange={ set( 'showInstitutions' ) }
							__nextHasNoMarginBottom
						/>
						<TextField
							label={ __( 'Logos label', 'ci-theme' ) }
							value={ attributes.institutionsLabel }
							onChange={ set( 'institutionsLabel' ) }
						/>
						<TextField
							label={ __( 'Legal notice', 'ci-theme' ) }
							help={ __(
								'{year} is replaced with the current year and {site} with the site title.',
								'ci-theme'
							) }
							value={ attributes.legal }
							onChange={ set( 'legal' ) }
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
			</InspectorControls>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
