/**
 * Fields shared by the sections.
 * - TextField: single-line or multi-line text.
 * - IntroPanel: section heading (eyebrow, title, optional text and link).
 * - ItemsControl: repeater of items made of text fields (features, steps, figures…).
 */

import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { LinkField } from './links';
import { Repeater } from './repeater';

export function TextField( {
	label,
	help,
	value,
	onChange,
	multiline = false,
	rows = 3,
	placeholder,
} ) {
	const Control = multiline ? TextareaControl : TextControl;
	return (
		<Control
			label={ label }
			help={ help }
			value={ value || '' }
			placeholder={ placeholder }
			onChange={ onChange }
			rows={ multiline ? rows : undefined }
			__next40pxDefaultSize={ multiline ? undefined : true }
			__nextHasNoMarginBottom
		/>
	);
}

/**
 * @param {Object}  props
 * @param {Object}  props.attributes
 * @param {Object}  props.setAttributes
 * @param {boolean} [props.text]        Show the text field.
 * @param {string}  [props.textLabel]
 * @param {boolean} [props.link]        Show the section link ("View all →").
 * @param {boolean} [props.showTitle]   Show the title field.
 * @param {string}  [props.title]       Panel title.
 * @param {Element} [props.children]    Extra fields at the end of the panel.
 */
export function IntroPanel( {
	attributes,
	setAttributes,
	text = false,
	textLabel,
	link = false,
	showTitle = true,
	title,
	children,
} ) {
	const set = ( key ) => ( value ) => setAttributes( { [ key ]: value } );
	return (
		<PanelBody title={ title || __( 'Heading', 'ci-theme' ) }>
			<div className="ci-editor-stack">
				<TextField
					label={ __( 'Eyebrow', 'ci-theme' ) }
					help={ __(
						'Small text with the accent bar, above the title.',
						'ci-theme'
					) }
					value={ attributes.eyebrow }
					onChange={ set( 'eyebrow' ) }
				/>
				{ showTitle && (
					<TextField
						label={ __( 'Title', 'ci-theme' ) }
						value={ attributes.title }
						onChange={ set( 'title' ) }
						multiline
						rows={ 2 }
					/>
				) }
				{ text && (
					<TextField
						label={ textLabel || __( 'Text', 'ci-theme' ) }
						value={ attributes.text }
						onChange={ set( 'text' ) }
						multiline
						rows={ 4 }
					/>
				) }
				{ link && (
					<>
						<TextField
							label={ __( 'Link: text', 'ci-theme' ) }
							help={ __( 'Empty = no link.', 'ci-theme' ) }
							value={ attributes.linkLabel }
							onChange={ set( 'linkLabel' ) }
						/>
						<LinkField
							label={ __( 'Link: destination', 'ci-theme' ) }
							url={ attributes.linkUrl }
							showNewTab={ false }
							onChange={ ( { url } ) =>
								setAttributes( { linkUrl: url } )
							}
						/>
					</>
				) }
				{ children }
			</div>
		</PanelBody>
	);
}

/**
 * @param {Object} props
 * @param {Array}  props.items      Items.
 * @param {Object} props.onChange   Receives the new list.
 * @param {Array}  props.fields     [ { key, label, multiline, help } ].
 * @param {string} [props.addLabel]
 * @param {number} [props.max]
 * @param {number} [props.min]
 * @param {string} [props.titleKey] Field used as the row title.
 */
export function ItemsControl( {
	items,
	onChange,
	fields,
	addLabel,
	max = 12,
	min = 0,
	titleKey,
} ) {
	const key = titleKey || fields[ 0 ].key;
	const newItem = Object.fromEntries( fields.map( ( f ) => [ f.key, '' ] ) );
	return (
		<Repeater
			items={ items }
			onChange={ onChange }
			max={ max }
			min={ min }
			addLabel={ addLabel }
			newItem={ newItem }
			itemLabel={ ( item ) => item[ key ] }
			renderItem={ ( item, update ) =>
				fields.map( ( f ) => (
					<TextField
						key={ f.key }
						label={ f.label }
						help={ f.help }
						value={ item[ f.key ] }
						multiline={ f.multiline }
						rows={ f.rows || 3 }
						onChange={ ( value ) => update( { [ f.key ]: value } ) }
					/>
				) )
			}
		/>
	);
}
