/**
 * Interactive map, loaded on demand by view.js.
 * MapLibre GL with OpenFreeMap vector tiles (free, commercial use allowed, data © OpenStreetMap)
 * styled with the theme colors, and the block marker on the location.
 */

import * as maplibregl from 'maplibre-gl';
import 'maplibre-gl/dist/maplibre-gl.css';

const STYLE_URL = 'https://tiles.openfreemap.org/styles/positron';

/* Theme palette applied to the "positron" base style. */
const FILL = {
	background: '#EEF0F4',
	park: '#E2E7EF',
	landcover_wood: '#E2E7EF',
	landuse_residential: '#E7EAF1',
	water: '#C3CFE6',
	building: '#E0E4EC',
	'aeroway-area': '#E7EAF1',
};
const HIDE = /shield|airport|railway|aeroway|tunnel|pier|path/;

function applyStyle( style ) {
	style.layers = style.layers
		.filter( ( l ) => ! HIDE.test( l.id ) )
		.map( ( l ) => {
			const paint = { ...( l.paint || {} ) };
			if ( l.id in FILL ) {
				paint[
					l.type === 'background' ? 'background-color' : 'fill-color'
				] = FILL[ l.id ];
			} else if ( l.id === 'waterway' ) {
				paint[ 'line-color' ] = '#C3CFE6';
			} else if ( l.type === 'line' && /casing/.test( l.id ) ) {
				paint[ 'line-color' ] = '#D7DBE4';
			} else if ( l.type === 'line' && /highway/.test( l.id ) ) {
				paint[ 'line-color' ] = '#FFFFFF';
			} else if ( l.type === 'line' && /boundary/.test( l.id ) ) {
				paint[ 'line-color' ] = '#9AA1B8';
			} else if ( l.type === 'symbol' ) {
				const city =
					/label_city|label_town|label_state|label_country/.test(
						l.id
					);
				paint[ 'text-color' ] = city ? '#1C2545' : '#5A6178';
				paint[ 'text-halo-color' ] = '#EEF0F4';
				paint[ 'text-halo-width' ] = 1.4;
				// City names go above the marker, not under it.
				if ( /label_city/.test( l.id ) ) {
					return {
						...l,
						paint,
						layout: {
							...( l.layout || {} ),
							'text-offset': [ 0, -2.6 ],
							'text-anchor': 'bottom',
						},
					};
				}
			}
			return { ...l, paint };
		} );
	return style;
}

export async function createMap(
	container,
	{ lng, lat, zoom, worker, marker, labels }
) {
	maplibregl.setWorkerUrl( worker );
	const style = applyStyle(
		await ( await window.fetch( STYLE_URL ) ).json()
	);

	const map = new maplibregl.Map( {
		container,
		style,
		center: [ lng, lat ],
		zoom,
		minZoom: 4,
		maxZoom: 15,
		// Credits are shown with the block's own button (closed at first).
		attributionControl: false,
		cooperativeGestures: true,
		locale: labels,
	} );
	// On phones the location card covers the lower part: the place is centered higher.
	if ( container.clientWidth < 700 ) {
		map.setPadding( { top: 0, bottom: 84, left: 0, right: 0 } );
	}
	map.addControl(
		new maplibregl.NavigationControl( { showCompass: false } ),
		'top-right'
	);
	new maplibregl.Marker( { element: marker, anchor: 'center' } )
		.setLngLat( [ lng, lat ] )
		.addTo( map );

	await new Promise( ( resolve ) => map.once( 'load', resolve ) );
	return map;
}
