/**
 * Editor: ci/features.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	IntroPanel,
	ItemsControl,
	SectionPreview,
	SectionStyle,
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
				<PanelBody title={ __( 'Items', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<SelectControl
							label={ __( 'Layout', 'ci-theme' ) }
							value={ attributes.variant }
							options={ [
								{
									value: 'dot',
									label: __(
										'With an accent dot',
										'ci-theme'
									),
								},
								{
									value: 'cards',
									label: __( 'Cards', 'ci-theme' ),
								},
							] }
							onChange={ ( variant ) =>
								setAttributes( { variant } )
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<p className="ci-editor-help">
							{ __( 'The numbering is automatic.', 'ci-theme' ) }
						</p>
						<ItemsControl
							items={ attributes.items }
							onChange={ ( items ) => setAttributes( { items } ) }
							max={ 8 }
							addLabel={ __( 'Add item', 'ci-theme' ) }
							fields={ [
								{
									key: 'title',
									label: __( 'Title', 'ci-theme' ),
								},
								{
									key: 'text',
									label: __( 'Text', 'ci-theme' ),
									multiline: true,
								},
							] }
						/>
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
