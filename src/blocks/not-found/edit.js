/**
 * Editor: ci/not-found (plantilla 404).
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	ButtonsControl,
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
				<PanelBody title={ __( 'Message', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Label', 'ci-theme' ) }
							value={ attributes.label }
							onChange={ set( 'label' ) }
						/>
						<TextField
							label={ __( 'Title', 'ci-theme' ) }
							value={ attributes.title }
							onChange={ set( 'title' ) }
							multiline
							rows={ 2 }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.text }
							onChange={ set( 'text' ) }
							multiline
							rows={ 3 }
						/>
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Buttons', 'ci-theme' ) }>
					<ButtonsControl
						value={ attributes.buttons }
						styles={ [ 'dark', 'outline', 'primary' ] }
						onChange={ set( 'buttons' ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Links to sections', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __(
								'List name (screen readers)',
								'ci-theme'
							) }
							value={ attributes.navLabel }
							onChange={ set( 'navLabel' ) }
						/>
						<Repeater
							items={ attributes.links }
							onChange={ set( 'links' ) }
							max={ 8 }
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
			</InspectorControls>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
