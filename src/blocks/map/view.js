/**
 * Map behind a "facade": the map image is already on the page; MapLibre (≈ 800 KB) is downloaded
 * only when the visitor taps "Explore map" (dynamic import: it lives in a separate chunk).
 */

function initMap( root ) {
	const canvas = root.querySelector( '[data-ci-map-canvas]' );
	const explore = root.querySelector( '[data-ci-map-explore]' );
	const center = root.querySelector( '[data-ci-map-center]' );
	const template = root.querySelector( 'template[data-ci-map-marker]' );
	const info = root.querySelector( '[data-ci-map-info]' );
	const credits =
		info &&
		root.querySelector( `#${ info.getAttribute( 'aria-controls' ) }` );

	info?.addEventListener( 'click', () => {
		const open = info.getAttribute( 'aria-expanded' ) !== 'true';
		info.setAttribute( 'aria-expanded', String( open ) );
		credits.hidden = ! open;
	} );

	explore?.addEventListener( 'click', async () => {
		if ( root.dataset.state !== 'image' ) {
			return;
		}
		root.dataset.state = 'loading';
		explore.disabled = true;
		explore.textContent = root.dataset.loading;
		try {
			const { createMap } = await import( './interactive-map' );
			await createMap( canvas, {
				lng: Number( root.dataset.lng ),
				lat: Number( root.dataset.lat ),
				zoom: Number( root.dataset.zoom ),
				worker: root.dataset.worker,
				marker: template.content.firstElementChild.cloneNode( true ),
				labels: JSON.parse( root.dataset.labels || '{}' ),
			} );
			root.dataset.state = 'interactive';
			explore.remove();
			center?.remove();
		} catch {
			root.dataset.state = 'error';
			explore.textContent = root.dataset.error;
		}
	} );
}

document.querySelectorAll( '[data-ci-map]' ).forEach( initMap );
