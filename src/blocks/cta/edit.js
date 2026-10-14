/**
 * Editor: ci/cta.
 * Text (from the site settings or its own) and buttons. Style: light or gray scheme, colors,
 * spacing.
 */

import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { ButtonsControl, SectionPreview, SectionStyle } from '@ci-theme/editor';
import metadata from './block.json';

export default function Edit( { attributes, setAttributes } ) {
	const { useGlobal, eyebrow, title, text, buttons } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Text', 'ci-theme' ) }>
					<div className="ci-editor-stack">
						<ToggleControl
							label={ __(
								'Use the text of the site settings',
								'ci-theme'
							) }
							help={ __(
								'Change it once for every page. Turn it off to write a text for this page only (empty fields keep using the site settings).',
								'ci-theme'
							) }
							checked={ useGlobal }
							onChange={ ( value ) =>
								setAttributes( { useGlobal: value } )
							}
							__nextHasNoMarginBottom
						/>
						{ ! useGlobal && (
							<>
								<TextControl
									label={ __( 'Eyebrow', 'ci-theme' ) }
									value={ eyebrow }
									placeholder={ __(
										'Free quote',
										'ci-theme'
									) }
									onChange={ ( value ) =>
										setAttributes( { eyebrow: value } )
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
								<TextareaControl
									label={ __( 'Title', 'ci-theme' ) }
									value={ title }
									rows={ 2 }
									onChange={ ( value ) =>
										setAttributes( { title: value } )
									}
									__nextHasNoMarginBottom
								/>
								<TextareaControl
									label={ __( 'Text', 'ci-theme' ) }
									value={ text }
									rows={ 3 }
									onChange={ ( value ) =>
										setAttributes( { text: value } )
									}
									__nextHasNoMarginBottom
								/>
							</>
						) }
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Buttons', 'ci-theme' ) }>
					<ButtonsControl
						value={ buttons }
						styles={ [ 'primary', 'on-dark' ] }
						onChange={ ( value ) =>
							setAttributes( { buttons: value } )
						}
					/>
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
