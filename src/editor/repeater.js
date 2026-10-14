/**
 * Repeater: a list of items that can be added, removed, reordered and collapsed.
 * Each item is an object inside an array attribute.
 */

import { Button, Flex, FlexBlock } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { chevronDown, chevronUp, close, plus } from '@wordpress/icons';

/**
 * @param {Object}                                                                    props
 * @param {Array}                                                                     props.items      Attribute value.
 * @param {(items: Object[]) => void}                                                 props.onChange   Receives the new array.
 * @param {Object}                                                                    props.newItem    Item to add.
 * @param {(item: Object, update: (patch: Object) => void, index: number) => Element} props.renderItem Fields of the item.
 * @param {(item: Object, index: number) => string}                                   props.itemLabel  Row title.
 * @param {string}                                                                    props.addLabel   Label of the add button.
 * @param {number}                                                                    [props.min]      Minimum number of items.
 * @param {number}                                                                    [props.max]      Maximum number of items.
 */
export function Repeater( {
	items = [],
	onChange,
	newItem,
	renderItem,
	itemLabel,
	addLabel,
	min = 0,
	max = 99,
} ) {
	const [ open, setOpen ] = useState( null );

	const update = ( index, patch ) =>
		onChange(
			items.map( ( it, i ) => ( i === index ? { ...it, ...patch } : it ) )
		);
	const move = ( from, to ) => {
		const next = items.slice();
		const [ moved ] = next.splice( from, 1 );
		next.splice( to, 0, moved );
		onChange( next );
		setOpen( open === from ? to : open );
	};
	const remove = ( index ) => {
		onChange( items.filter( ( _, i ) => i !== index ) );
		setOpen( null );
	};
	const add = () => {
		onChange( [ ...items, { ...newItem } ] );
		setOpen( items.length );
	};

	return (
		<div className="ci-repeater">
			<ol className="ci-repeater-list">
				{ items.map( ( item, index ) => {
					const title =
						itemLabel( item, index ) ||
						sprintf(
							/* translators: %d: item number in the list. */
							__( 'Item %d', 'ci-theme' ),
							index + 1
						);
					const isOpen = open === index;
					return (
						<li
							key={ index }
							className={ `ci-repeater-row${ isOpen ? ' is-open' : '' }` }
						>
							<Flex className="ci-repeater-head" gap={ 1 }>
								<FlexBlock>
									<Button
										className="ci-repeater-toggle"
										onClick={ () =>
											setOpen( isOpen ? null : index )
										}
										aria-expanded={ isOpen }
										__next40pxDefaultSize
									>
										<span className="ci-repeater-num">
											{ String( index + 1 ).padStart(
												2,
												'0'
											) }
										</span>
										<span className="ci-repeater-title">
											{ title }
										</span>
									</Button>
								</FlexBlock>
								<Button
									icon={ chevronUp }
									label={ __( 'Move up', 'ci-theme' ) }
									size="small"
									disabled={ index === 0 }
									accessibleWhenDisabled
									onClick={ () => move( index, index - 1 ) }
								/>
								<Button
									icon={ chevronDown }
									label={ __( 'Move down', 'ci-theme' ) }
									size="small"
									disabled={ index === items.length - 1 }
									accessibleWhenDisabled
									onClick={ () => move( index, index + 1 ) }
								/>
								<Button
									icon={ close }
									label={ __( 'Remove', 'ci-theme' ) }
									size="small"
									isDestructive
									disabled={ items.length <= min }
									accessibleWhenDisabled
									onClick={ () => remove( index ) }
								/>
							</Flex>
							{ isOpen && (
								<div className="ci-repeater-body ci-editor-stack">
									{ renderItem(
										item,
										( patch ) => update( index, patch ),
										index
									) }
								</div>
							) }
						</li>
					);
				} ) }
			</ol>
			{ items.length < max && (
				<Button
					variant="secondary"
					icon={ plus }
					onClick={ add }
					__next40pxDefaultSize
				>
					{ addLabel || __( 'Add', 'ci-theme' ) }
				</Button>
			) }
		</div>
	);
}
