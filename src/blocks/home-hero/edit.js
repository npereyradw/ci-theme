/**
 * Editor: ci/home-hero.
 */

import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	ButtonsControl,
	SectionPreview,
	SectionStyle,
	TextField,
} from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Text', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Eyebrow', 'ci-theme' ) }
							value={ attributes.eyebrow }
							onChange={ set( 'eyebrow' ) }
						/>
						<TextField
							label={ __( 'Main title (H1)', 'ci-theme' ) }
							help={ __(
								'It is the only H1 of the page.',
								'ci-theme'
							) }
							value={ attributes.title }
							onChange={ set( 'title' ) }
							multiline
							rows={ 2 }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							value={ attributes.lead }
							onChange={ set( 'lead' ) }
							multiline
							rows={ 4 }
						/>
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Buttons', 'ci-theme' ) }>
					<ButtonsControl
						value={ attributes.buttons }
						styles={ [ 'primary', 'on-dark' ] }
						onChange={ set( 'buttons' ) }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Counter card', 'ci-theme' ) }
					initialOpen={ false }
				>
					<div className="ci-editor-stack">
						<TextField
							label={ __( 'Number', 'ci-theme' ) }
							help={ __(
								'A number (e.g. +30) counts up from 0 when it appears. Empty = no card.',
								'ci-theme'
							) }
							value={ attributes.badgeNumber }
							onChange={ set( 'badgeNumber' ) }
						/>
						<TextField
							label={ __( 'Text', 'ci-theme' ) }
							help={ __(
								'Each line goes on its own row.',
								'ci-theme'
							) }
							value={ attributes.badgeText }
							onChange={ set( 'badgeText' ) }
							multiline
							rows={ 2 }
						/>
					</div>
				</PanelBody>
				<PanelBody
					title={ __( 'Illustration', 'ci-theme' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __(
							'Show the large background illustration',
							'ci-theme'
						) }
						checked={ attributes.showArt }
						onChange={ set( 'showArt' ) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>
			<SectionStyle
				attributes={ attributes }
				setAttributes={ setAttributes }
				schemes={ [] }
				spacing={ false }
			/>
			<SectionPreview name={ metadata.name } attributes={ attributes } />
		</>
	);
}
