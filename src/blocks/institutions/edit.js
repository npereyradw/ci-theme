/**
 * Editor: ci/institutions.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { ExternalLink, PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { SectionPreview, SectionStyle, TextField } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Content', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Layout', 'ci-theme' ) }
							value={ attributes.variant }
							options={ [
								{
									value: 'statement',
									label: __(
										'Statement and logos, with a line above',
										'ci-theme'
									),
								},
								{
									value: 'label',
									label: __( 'Label and logos', 'ci-theme' ),
								},
							] }
							onChange={ set( 'variant' ) }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{ attributes.variant === 'label' ? (
							<TextField
								label={ __( 'Label', 'ci-theme' ) }
								value={ attributes.label }
								onChange={ set( 'label' ) }
							/>
						) : (
							<TextField
								label={ __( 'Statement', 'ci-theme' ) }
								value={ attributes.text }
								onChange={ set( 'text' ) }
								multiline
								rows={ 3 }
							/>
						) }
						<p className="ci-editor-help">
							{ __(
								'Logos, names and links come from the Institutions section.',
								'ci-theme'
							) }
						</p>
						<ExternalLink href="edit.php?post_type=ci_institution">
							{ __( 'View the institutions', 'ci-theme' ) }
						</ExternalLink>
					</div>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
