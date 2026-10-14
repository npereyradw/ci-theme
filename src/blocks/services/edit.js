/**
 * Editor: ci/services.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	LinkField,
	QueryControls,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	const listing = attributes.layout === 'listing';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Variant', 'ci-theme' ) }>
					<SelectControl
						label={ __( 'Layout', 'ci-theme' ) }
						help={
							listing
								? __(
										'For the services page: it follows the area filter of the page header and has no title of its own.',
										'ci-theme'
									)
								: undefined
						}
						value={ attributes.layout }
						options={ [
							{
								value: 'grid',
								label: __( 'With a title', 'ci-theme' ),
							},
							{
								value: 'listing',
								label: __(
									'Listing (services page)',
									'ci-theme'
								),
							},
						] }
						onChange={ set( 'layout' ) }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</PanelBody>
				{ ! listing && (
					<IntroPanel
						attributes={ attributes }
						setAttributes={ setAttributes }
						link
					/>
				) }
				<PanelBody title={ __( 'Services to show', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<QueryControls
							attributes={ attributes }
							setAttributes={ setAttributes }
							postType="ci_service"
							allLabel={ __(
								'All of them, in the order of the Services section',
								'ci-theme'
							) }
						/>
						<ToggleControl
							label={ __(
								'Hide those "coming soon"',
								'ci-theme'
							) }
							checked={ attributes.hideSoon }
							onChange={ set( 'hideSoon' ) }
							__nextHasNoMarginBottom
						/>
						{ ! listing && (
							<ToggleControl
								label={ __(
									'Line above the section',
									'ci-theme'
								) }
								help={ __(
									'Separates the section from the previous one when both are white. In the service template, the service being viewed is not repeated.',
									'ci-theme'
								) }
								checked={ attributes.divider }
								onChange={ set( 'divider' ) }
								__nextHasNoMarginBottom
							/>
						) }
					</div>
				</PanelBody>
				{ listing && (
					<PanelBody
						title={ __( 'Inquiry card', 'ci-theme' ) }
						initialOpen={ false }
					>
						<div className="ci-editor-stack">
							<ToggleControl
								label={ __(
									'Show at the end of the listing',
									'ci-theme'
								) }
								checked={ attributes.showInquiry }
								onChange={ set( 'showInquiry' ) }
								__nextHasNoMarginBottom
							/>
							<TextField
								label={ __( 'Title', 'ci-theme' ) }
								value={ attributes.inquiryTitle }
								onChange={ set( 'inquiryTitle' ) }
							/>
							<TextField
								label={ __( 'Text', 'ci-theme' ) }
								value={ attributes.inquiryText }
								onChange={ set( 'inquiryText' ) }
								multiline
								rows={ 2 }
							/>
							<TextField
								label={ __( 'Link: text', 'ci-theme' ) }
								value={ attributes.inquiryLabel }
								onChange={ set( 'inquiryLabel' ) }
							/>
							<LinkField
								label={ __( 'Link: destination', 'ci-theme' ) }
								url={ attributes.inquiryUrl }
								showNewTab={ false }
								onChange={ ( { url } ) =>
									setAttributes( { inquiryUrl: url } )
								}
							/>
						</div>
					</PanelBody>
				) }
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview
				name={ metadata.name }
				attributes={ attributes }
				withPostContext
			/>
		</>
	);
}
