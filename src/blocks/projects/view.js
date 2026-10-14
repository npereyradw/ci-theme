/**
 * Project listing: filter by area (chips) and service (select) without reloading. On the URL
 * filtered by service, the list arrives filtered from the server.
 */

function initListing( root ) {
	const chips = root.querySelectorAll( '.ci-chip[data-area]' );
	const select = root.querySelector( '[data-ci-service]' );
	const cards = root.querySelectorAll( '.ci-project-card[data-area]' );
	const grid = root.querySelector( '[data-ci-cards]' );
	const empty = root.querySelector( '[data-ci-empty]' );
	const count = root.querySelector( '[data-ci-count]' );
	let area = '';

	function apply() {
		const service = select ? select.value : '';
		let shown = 0;
		cards.forEach( ( card ) => {
			const visible =
				( ! area || card.dataset.area === area ) &&
				( ! service || card.dataset.service === service );
			card.hidden = ! visible;
			shown += visible ? 1 : 0;
		} );
		chips.forEach( ( chip ) =>
			chip.setAttribute(
				'aria-pressed',
				String( chip.dataset.area === area )
			)
		);
		if ( grid ) {
			grid.hidden = shown === 0;
		}
		if ( empty ) {
			empty.hidden = shown > 0;
		}
		if ( count ) {
			count.textContent = `${ shown } ${ shown === 1 ? count.dataset.one : count.dataset.many }`;
		}
	}

	chips.forEach( ( chip ) =>
		chip.addEventListener( 'click', () => {
			area = chip.dataset.area;
			apply();
		} )
	);
	select?.addEventListener( 'change', apply );
	root.querySelector( '[data-ci-reset]' )?.addEventListener( 'click', () => {
		area = '';
		if ( select ) {
			select.value = '';
		}
		apply();
		chips[ 0 ]?.focus();
	} );
}

document
	.querySelectorAll( '[data-ci-projects-listing]' )
	.forEach( initListing );
