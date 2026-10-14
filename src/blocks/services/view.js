/**
 * Area filter of the services page, without reloading.
 *
 * The chips of the page header ([data-ci-area-filter] a[data-area]) are links to the URLs
 * filtered by area, so they work without JavaScript and search engines index them. With
 * JavaScript, a click filters the cards at once and the address bar changes to that URL.
 */

function filterLists( area ) {
	document
		.querySelectorAll( '[data-ci-services-list]' )
		.forEach( ( list ) => {
			let shown = 0;
			list.querySelectorAll( '.ci-service-card[data-area]' ).forEach(
				( card ) => {
					const visible = ! area || card.dataset.area === area;
					card.hidden = ! visible;
					shown += visible ? 1 : 0;
				}
			);
			const count = list.querySelector( '[data-ci-count]' );
			if ( count ) {
				count.textContent = `${ shown } ${ shown === 1 ? count.dataset.one : count.dataset.many }`;
			}
		} );
}

document.querySelectorAll( '[data-ci-area-filter]' ).forEach( ( group ) => {
	if ( ! document.querySelector( '[data-ci-services-list]' ) ) {
		return; // No listing on the page: the chips navigate as usual.
	}
	group.addEventListener( 'click', ( event ) => {
		const chip = event.target.closest( 'a[data-area]' );
		if ( ! chip || event.metaKey || event.ctrlKey || event.shiftKey ) {
			return;
		}
		event.preventDefault();
		group
			.querySelectorAll( 'a[data-area]' )
			.forEach( ( a ) => a.removeAttribute( 'aria-current' ) );
		chip.setAttribute( 'aria-current', 'page' );
		filterLists( chip.dataset.area );
		window.history.replaceState( null, '', chip.href );
	} );
} );
