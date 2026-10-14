/**
 * Editor: ci/stats.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
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
					text
					link
				/>
				<PanelBody title={ __( 'Figures', 'ci-theme' ) }>
					<ItemsControl
						items={ attributes.stats }
						onChange={ ( stats ) => setAttributes( { stats } ) }
						max={ 6 }
						addLabel={ __( 'Add figure', 'ci-theme' ) }
						fields={ [
							{
								key: 'value',
								label: __( 'Value', 'ci-theme' ),
								help: __(
									'Numbers (e.g. +30) count up from 0 when they appear.',
									'ci-theme'
								),
							},
							{
								key: 'label',
								label: __( 'Description', 'ci-theme' ),
								multiline: true,
								rows: 2,
							},
						] }
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
