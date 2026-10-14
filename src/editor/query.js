/**
 * Controls of the dynamic sections: which content to show (all, by area, by service or picked
 * by hand; in templates, the current service or related items) and how many.
 * Attributes: source, area, service, ids, limit.
 */

import {
	FormTokenField,
	RangeControl,
	SelectControl,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const FIELDS = '_fields';

function useRecords( kind, name, enabled = true ) {
	return useSelect(
		( select ) =>
			enabled
				? select( coreStore ).getEntityRecords( kind, name, {
						per_page: -1,
						[ FIELDS ]:
							kind === 'taxonomy'
								? 'id,slug,name'
								: 'id,slug,title',
						...( kind === 'postType'
							? { orderby: 'menu_order', order: 'asc' }
							: {} ),
					} )
				: null,
		[ kind, name, enabled ]
	);
}

const titleOf = ( record ) =>
	record?.title?.raw || record?.title?.rendered || record?.name || '';

/**
 * @param {Object}   props
 * @param {Object}   props.attributes
 * @param {Object}   props.setAttributes
 * @param {string}   props.postType      Post type of the list (to pick by hand).
 * @param {string[]} [props.sources]     Allowed sources: all, area, service, manual,
 *                                       current-service, related.
 * @param {boolean}  [props.limit]       Show the amount.
 * @param {number}   [props.maxLimit]
 * @param {string}   [props.allLabel]    Label of the "all" option.
 */
export function QueryControls( {
	attributes,
	setAttributes,
	postType,
	sources = [ 'all', 'area', 'manual' ],
	limit = true,
	maxLimit = 12,
	allLabel,
} ) {
	const source = attributes.source || 'all';
	const areas = useRecords(
		'taxonomy',
		'ci_area',
		sources.includes( 'area' )
	);
	const services = useRecords(
		'postType',
		'ci_service',
		sources.includes( 'service' )
	);
	const posts = useRecords(
		'postType',
		postType,
		sources.includes( 'manual' ) && source === 'manual'
	);

	const labels = {
		all: allLabel || __( 'All, newest first', 'ci-theme' ),
		area: __( 'From one area', 'ci-theme' ),
		service: __( 'From one service', 'ci-theme' ),
		manual: __( 'Picked by hand', 'ci-theme' ),
		'current-service': __(
			'From the service being viewed (service template)',
			'ci-theme'
		),
		related: __( 'Related to the one being viewed (template)', 'ci-theme' ),
	};

	const byTitle = Object.fromEntries(
		( posts || [] ).map( ( p ) => [ titleOf( p ), p.id ] )
	);
	const byId = Object.fromEntries(
		( posts || [] ).map( ( p ) => [ p.id, titleOf( p ) ] )
	);

	return (
		<div className="ci-editor-stack">
			{ sources.length > 1 && (
				<SelectControl
					label={ __( 'Show', 'ci-theme' ) }
					value={ source }
					options={ sources.map( ( s ) => ( {
						value: s,
						label: labels[ s ],
					} ) ) }
					onChange={ ( value ) => setAttributes( { source: value } ) }
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			) }
			{ source === 'area' && (
				<SelectControl
					label={ __( 'Area', 'ci-theme' ) }
					value={ attributes.area || '' }
					options={ [
						{
							value: '',
							label: __( 'Choose an area', 'ci-theme' ),
						},
						...( areas || [] ).map( ( a ) => ( {
							value: a.slug,
							label: a.name,
						} ) ),
					] }
					onChange={ ( value ) => setAttributes( { area: value } ) }
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			) }
			{ source === 'service' && (
				<SelectControl
					label={ __( 'Service', 'ci-theme' ) }
					value={ attributes.service || '' }
					options={ [
						{
							value: '',
							label: __( 'Choose a service', 'ci-theme' ),
						},
						...( services || [] ).map( ( s ) => ( {
							value: s.slug,
							label: titleOf( s ),
						} ) ),
					] }
					onChange={ ( value ) =>
						setAttributes( { service: value } )
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			) }
			{ source === 'manual' && (
				<FormTokenField
					label={ __( 'Choose and sort the items', 'ci-theme' ) }
					value={ ( attributes.ids || [] )
						.map( ( id ) => byId[ id ] )
						.filter( Boolean ) }
					suggestions={ Object.keys( byTitle ) }
					onChange={ ( titles ) =>
						setAttributes( {
							ids: titles
								.map( ( t ) => byTitle[ t ] )
								.filter( Boolean ),
						} )
					}
					__experimentalExpandOnFocus
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			) }
			{ limit && source !== 'manual' && (
				<RangeControl
					label={ __( 'Amount', 'ci-theme' ) }
					help={ __( '0 = all.', 'ci-theme' ) }
					value={ attributes.limit ?? 0 }
					min={ 0 }
					max={ maxLimit }
					onChange={ ( value ) =>
						setAttributes( { limit: value ?? 0 } )
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			) }
		</div>
	);
}

/**
 * Choose and sort entries of a post type. Stores the IDs or, with by="slug", the slugs (more
 * stable if the content is migrated to another site).
 *
 * @param {Object} props
 * @param {string} props.label
 * @param {string} [props.help]
 * @param {string} props.postType
 * @param {Array}  props.value    IDs or slugs.
 * @param {Object} props.onChange Receives the new list.
 * @param {string} [props.by]     "id" (default) or "slug".
 */
export function PostsPicker( {
	label,
	help,
	postType,
	value = [],
	onChange,
	by = 'id',
} ) {
	const posts = useRecords( 'postType', postType );
	const keyOf = ( p ) => ( by === 'slug' ? p.slug : p.id );
	const byTitle = Object.fromEntries(
		( posts || [] ).map( ( p ) => [ titleOf( p ), keyOf( p ) ] )
	);
	const byKey = Object.fromEntries(
		( posts || [] ).map( ( p ) => [ keyOf( p ), titleOf( p ) ] )
	);
	return (
		<FormTokenField
			label={ label }
			help={ help }
			value={ value.map( ( k ) => byKey[ k ] ).filter( Boolean ) }
			suggestions={ Object.keys( byTitle ) }
			onChange={ ( titles ) =>
				onChange(
					titles.map( ( t ) => byTitle[ t ] ).filter( Boolean )
				)
			}
			__experimentalExpandOnFocus
			__next40pxDefaultSize
			__nextHasNoMarginBottom
		/>
	);
}
