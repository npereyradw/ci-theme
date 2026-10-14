/**
 * Editor: ci/audiences.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	PostsPicker,
	Repeater,
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
				/>
				<PanelBody title={ __( 'Audiences', 'ci-theme' ) }>
					<Repeater
						items={ attributes.items }
						onChange={ ( items ) => setAttributes( { items } ) }
						max={ 6 }
						addLabel={ __( 'Add audience', 'ci-theme' ) }
						newItem={ { title: '', text: '', services: [] } }
						itemLabel={ ( item ) => item.title }
						renderItem={ ( item, update ) => (
							<>
								<TextField
									label={ __( 'Title', 'ci-theme' ) }
									value={ item.title }
									onChange={ ( title ) =>
										update( { title } )
									}
								/>
								<TextField
									label={ __( 'Text', 'ci-theme' ) }
									value={ item.text }
									onChange={ ( text ) => update( { text } ) }
									multiline
									rows={ 2 }
								/>
								<PostsPicker
									label={ __(
										'Linked services',
										'ci-theme'
									) }
									help={ __(
										'Type to search; they are shown in this order.',
										'ci-theme'
									) }
									postType="ci_service"
									by="slug"
									value={ item.services || [] }
									onChange={ ( services ) =>
										update( { services } )
									}
								/>
							</>
						) }
					/>
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
