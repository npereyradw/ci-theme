/**
 * Header: a floating bar (logo centered on desktop) that collapses to the right on scroll,
 * leaving only the "Menu" pill. The full-screen menu opens as a circle (phones) or as an
 * inflating pill (desktop), with masked link reveals and the background illustration drawing in.
 *
 * State lives in attributes of the <header>: data-scrolled, data-open, data-sub (read by the CSS).
 */

import { gsap } from 'gsap';

const reduceMotion = () =>
	window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
const isDesktop = () => window.matchMedia( '(min-width: 1180px)' ).matches;

function setFlag( el, name, on ) {
	if ( on ) {
		el.setAttribute( name, '' );
	} else {
		el.removeAttribute( name );
	}
}

function initHeader( header ) {
	const bar = header.querySelector( '[data-header-bar]' );
	const barBg = header.querySelector( '[data-bar-bg]' );
	const wrap = header.querySelector( '[data-bar-items]' );
	const items = header.querySelectorAll( '[data-bar-item]' );
	const toggle = header.querySelector( '[data-menu-toggle]' );
	const menu = header.querySelector( '[data-menu]' );
	const sub = header.querySelector( '[data-sub]' );
	const subToggle = header.querySelector( '[data-sub-toggle]' );
	const subGroup = header.querySelector( '[data-sub-group]' );
	const labelMenu = toggle.querySelector( '[data-label-menu]' );
	const labelClose = toggle.querySelector( '[data-label-close]' );
	const fillNavy = toggle.querySelector( '[data-fill-navy]' );
	const fillWhite = toggle.querySelector( '[data-fill-white]' );

	const state = { sub: false, open: false, scrolled: false, morphed: false };
	let collapseTl = null;
	let menuTl = null;
	let subTl = null;
	let subCloseTimer;

	/* ── Services submenu (desktop, expanded bar): curtain and staggered links ── */

	function setSub( value ) {
		if ( ! sub || state.sub === value ) {
			return;
		}
		state.sub = value;
		setFlag( header, 'data-sub', value );
		subToggle.setAttribute( 'aria-expanded', String( value ) );
		subToggle.classList.toggle( 'ci-header-nav-toggle-open', value );

		subTl?.revert();
		subTl = null;

		if ( value ) {
			sub.hidden = false;
			if ( reduceMotion() ) {
				return;
			}
			subTl = gsap
				.timeline( { defaults: { ease: 'expo.out' } } )
				.fromTo(
					sub,
					{ clipPath: 'inset(0% 0% 100% 0%)' },
					{ clipPath: 'inset(0% 0% 0% 0%)', duration: 0.6 },
					0
				)
				.from(
					sub.querySelectorAll( '[data-sub-item]' ),
					{ y: 14, autoAlpha: 0, duration: 0.6, stagger: 0.035 },
					0.12
				)
				.from(
					sub.querySelectorAll( '[data-sub-aside]' ),
					{ y: 20, autoAlpha: 0, duration: 0.7 },
					0.2
				);
			return;
		}

		if ( reduceMotion() ) {
			sub.hidden = true;
			return;
		}
		subTl = gsap.timeline().to( sub, {
			clipPath: 'inset(0% 0% 100% 0%)',
			duration: 0.35,
			ease: 'power3.in',
			onComplete: () => {
				sub.hidden = true;
				gsap.set( sub, { clearProps: 'clipPath' } );
				subTl = null;
			},
		} );
	}

	// Crossing another link on the way to the submenu (diagonal movement) does not close it right
	// away: it waits a moment and cancels if the pointer reaches the submenu.
	const keepSub = () => window.clearTimeout( subCloseTimer );
	const openSub = () => {
		keepSub();
		setSub( true );
	};
	const closeSubSoon = () => {
		keepSub();
		subCloseTimer = window.setTimeout( () => setSub( false ), 280 );
	};

	/* ── Bar → pill ───────────────────────────────────────────── */

	// The white background of the bar shrinks to the exact shape and position of the "Menu" pill
	// while it turns yellow; only then does its content appear.
	function morph() {
		if ( state.scrolled ) {
			setSub( false );
		}
		if ( state.morphed === state.scrolled ) {
			return;
		}
		state.morphed = state.scrolled;

		const desktop = isDesktop();
		const br = bar.getBoundingClientRect();
		const tr = toggle.getBoundingClientRect();
		const r = tr.height / 2;
		const full = 'inset(0px 0px 0px 0px round 0px 0px 16px 16px)';
		const pill = `inset(${ tr.top - br.top }px ${ br.right - tr.right }px ${ br.bottom - tr.bottom }px ${
			tr.left - br.left
		}px round ${ r }px ${ r }px ${ r }px ${ r }px)`;

		collapseTl?.kill();
		const tl = gsap.timeline();
		if ( state.scrolled ) {
			tl.to(
				items,
				{
					yPercent: -110,
					autoAlpha: 0,
					duration: 0.45,
					stagger: 0.03,
					ease: 'power3.in',
				},
				0
			)
				.fromTo(
					barBg,
					{ clipPath: full },
					{
						clipPath: pill,
						backgroundColor: '#E6D10F',
						borderColor: 'rgba(220, 223, 230, 0)',
						duration: 0.8,
						ease: 'expo.inOut',
					},
					0.1
				)
				.fromTo(
					wrap,
					{ clipPath: full },
					{ clipPath: pill, duration: 0.8, ease: 'expo.inOut' },
					0.1
				);
			if ( desktop ) {
				tl.fromTo(
					toggle,
					{ autoAlpha: 0 },
					{ autoAlpha: 1, duration: 0.3, ease: 'power2.out' },
					0.72
				);
			}
		} else {
			if ( desktop ) {
				tl.to(
					toggle,
					{ autoAlpha: 0, duration: 0.2, ease: 'power2.in' },
					0
				);
			}
			tl.fromTo(
				barBg,
				{ clipPath: pill },
				{
					clipPath: full,
					backgroundColor: '#FFFFFF',
					borderColor: 'rgba(220, 223, 230, 1)',
					duration: 0.75,
					ease: 'expo.inOut',
				},
				0.05
			)
				.fromTo(
					wrap,
					{ clipPath: pill },
					{ clipPath: full, duration: 0.75, ease: 'expo.inOut' },
					0.05
				)
				.to(
					items,
					{
						yPercent: 0,
						autoAlpha: 1,
						duration: 0.55,
						stagger: 0.03,
						ease: 'expo.out',
					},
					0.4
				)
				// Once expanded, no clipping (so focus outlines and shadows are not cut).
				.set( wrap, { clearProps: 'clipPath' } );
		}
		if ( reduceMotion() ) {
			tl.progress( 1 );
		}
		collapseTl = tl;
	}

	// "Scrolled" state (desktop only: on phones the bar stays the same), with hysteresis so it
	// does not flicker near the threshold.
	function onScroll() {
		const y = window.scrollY;
		const next = isDesktop() && ( state.scrolled ? y > 40 : y > 120 );
		if ( next !== state.scrolled ) {
			state.scrolled = next;
			setFlag( header, 'data-scrolled', next );
			morph();
		}
	}

	// If the window is resized while the bar is collapsed, the pill is repositioned.
	function onResize() {
		onScroll();
		if ( ! state.morphed ) {
			return;
		}
		const br = bar.getBoundingClientRect();
		const tr = toggle.getBoundingClientRect();
		const r = tr.height / 2;
		gsap.set( barBg, {
			clipPath: `inset(${ tr.top - br.top }px ${ br.right - tr.right }px ${ br.bottom - tr.bottom }px ${
				tr.left - br.left
			}px round ${ r }px ${ r }px ${ r }px ${ r }px)`,
		} );
	}

	/* ── Full-screen menu ─────────────────────────────────────── */

	function openMenu() {
		menuTl?.revert();
		menuTl = null;
		menu.hidden = false;
		menu.focus( { preventScroll: true } );
		if ( reduceMotion() ) {
			return;
		}

		const b = toggle.getBoundingClientRect();
		const q = ( sel ) => menu.querySelectorAll( sel );

		// Phones: the bar stays in place and turns blue; the menu opens as a circle from the burger.
		if ( ! isDesktop() ) {
			const ox = b.left + b.width / 2;
			const oy = b.top + b.height / 2;
			const radius = Math.hypot(
				Math.max( ox, window.innerWidth - ox ),
				Math.max( oy, window.innerHeight - oy )
			);
			menuTl = gsap
				.timeline( { defaults: { ease: 'expo.out' } } )
				.to(
					barBg,
					{
						backgroundColor: '#1C2545',
						borderColor: 'rgba(255, 255, 255, 0.12)',
						duration: 0.5,
						ease: 'power2.out',
					},
					0
				)
				.fromTo(
					menu,
					{ clipPath: `circle(0px at ${ ox }px ${ oy }px)` },
					{
						clipPath: `circle(${ radius }px at ${ ox }px ${ oy }px)`,
						duration: 0.9,
						ease: 'expo.inOut',
					},
					0
				)
				.from(
					q( '[data-menu-line]' ),
					{ yPercent: 110, duration: 0.9, stagger: 0.07 },
					0.3
				)
				.from(
					q( '[data-menu-rule]' ),
					{
						scaleX: 0,
						transformOrigin: '0% 50%',
						duration: 0.9,
						stagger: 0.07,
					},
					0.35
				)
				.from(
					q( '[data-menu-sub]' ),
					{ x: -16, autoAlpha: 0, duration: 0.7, stagger: 0.035 },
					0.5
				)
				.from(
					q( '[data-menu-rise]' ),
					{ y: 24, autoAlpha: 0, duration: 0.8, stagger: 0.08 },
					0.65
				)
				.from(
					q( '[data-menu-arc]' ),
					{ drawSVG: '0%', duration: 1.4, ease: 'power3.inOut' },
					0.35
				)
				.from(
					q( '[data-menu-bar]' ),
					{ scaleY: 0, transformOrigin: '50% 0%', duration: 0.9 },
					0.8
				)
				.from(
					q( '[data-menu-dot]' ),
					{
						scale: 0,
						transformOrigin: '50% 50%',
						duration: 0.9,
						ease: 'back.out(2)',
					},
					0.9
				);
			return;
		}

		// The menu is an inflating pill: same proportions and round ends at all times (an inset with a
		// huge radius keeps the ends semicircular), centered on the real pill.
		const vw = window.innerWidth;
		const vh = window.innerHeight;
		const cx = b.left + b.width / 2;
		const cy = b.top + b.height / 2;
		const farthest = Math.max(
			Math.hypot( cx, cy ),
			Math.hypot( vw - cx, cy ),
			Math.hypot( cx, vh - cy ),
			Math.hypot( vw - cx, vh - cy )
		);
		const k = ( 2 * farthest ) / b.height + 0.5; // scale so the pill covers the whole screen
		const stadium = ( f ) => {
			const w = ( b.width * f ) / 2;
			const h = ( b.height * f ) / 2;
			return `inset(${ cy - h }px ${ vw - cx - w }px ${ vh - cy - h }px ${ cx - w }px round 9999px)`;
		};
		// With the expanded bar, wait for the call-to-action button to fade out.
		const d = state.scrolled ? 0 : 0.1;
		// With the expanded bar (no pill) the word "Menu" is not shown: only "Close" appears.
		if ( ! state.scrolled ) {
			gsap.set( labelMenu, { autoAlpha: 0 } );
		}
		menuTl = gsap
			.timeline( { defaults: { ease: 'expo.out' } } )
			// 1) The pill turns blue (a very fast fill from its center) while
			// 2) the blue menu grows from the oval shape of the pill to the full screen.
			.fromTo(
				fillNavy,
				{ clipPath: 'circle(0% at 50% 50%)' },
				{
					clipPath: 'circle(75% at 50% 50%)',
					duration: 0.14,
					ease: 'power2.out',
				},
				d
			)
			.to(
				toggle,
				{ color: '#FFFFFF', duration: 0.1, ease: 'none' },
				d + 0.04
			)
			.set( barBg, { autoAlpha: 0 }, d + 0.14 )
			.fromTo(
				menu,
				{ clipPath: stadium( 1 ) },
				{ clipPath: stadium( k ), duration: 0.8, ease: 'power2.in' },
				d
			)
			// 3) With the menu already opening, the pill fills with white and reads "Close".
			.fromTo(
				fillWhite,
				{ clipPath: 'circle(0% at 50% 50%)' },
				{
					clipPath: 'circle(75% at 50% 50%)',
					duration: 0.35,
					ease: 'power3.inOut',
				},
				0.7
			)
			.to(
				toggle,
				{ color: '#1C2545', duration: 0.12, ease: 'none' },
				0.81
			)
			.to(
				labelMenu,
				{
					autoAlpha: 0,
					yPercent: -40,
					duration: 0.2,
					ease: 'power2.in',
				},
				0.62
			)
			.fromTo(
				labelClose,
				{ autoAlpha: 0, yPercent: 40 },
				{
					autoAlpha: 1,
					yPercent: 0,
					duration: 0.3,
					ease: 'power2.out',
				},
				0.82
			)
			.from(
				q( '[data-menu-line]' ),
				{ yPercent: 110, duration: 0.9, stagger: 0.07 },
				0.45
			)
			.from(
				q( '[data-menu-rule]' ),
				{
					scaleX: 0,
					transformOrigin: '0% 50%',
					duration: 0.9,
					stagger: 0.07,
				},
				0.5
			)
			.from(
				q( '[data-menu-sub]' ),
				{ x: -16, autoAlpha: 0, duration: 0.7, stagger: 0.03 },
				0.6
			)
			.from(
				q( '[data-menu-rise]' ),
				{ y: 24, autoAlpha: 0, duration: 0.8, stagger: 0.08 },
				0.65
			)
			.from(
				q( '[data-menu-arc]' ),
				{ drawSVG: '0%', duration: 1.4, ease: 'power3.inOut' },
				0.5
			)
			.from(
				q( '[data-menu-bar]' ),
				{ scaleY: 0, transformOrigin: '50% 0%', duration: 0.9 },
				0.9
			)
			.from(
				q( '[data-menu-dot]' ),
				{
					scale: 0,
					transformOrigin: '50% 50%',
					duration: 0.9,
					ease: 'back.out(2)',
				},
				1
			);
	}

	function closeMenu() {
		if ( menu.hidden ) {
			return;
		}
		const tl = menuTl;
		if ( tl && ! reduceMotion() ) {
			tl.eventCallback( 'onReverseComplete', () => {
				menu.hidden = true;
				tl.revert();
				if ( menuTl === tl ) {
					menuTl = null;
				}
			} );
			tl.timeScale( isDesktop() ? 2.6 : 1.8 ).reverse();
		} else {
			menu.hidden = true;
		}
		if ( menu.contains( menu.ownerDocument.activeElement ) ) {
			toggle.focus( { preventScroll: true } );
		}
	}

	function setOpen( value ) {
		if ( state.open === value ) {
			return;
		}
		state.open = value;
		setFlag( header, 'data-open', value );
		toggle.setAttribute( 'aria-expanded', String( value ) );
		toggle.setAttribute(
			'aria-label',
			value ? toggle.dataset.labelClose : toggle.dataset.labelOpen
		);
		// Lock the body scroll while the menu is open.
		document.body.style.overflow = value ? 'hidden' : '';
		if ( value ) {
			setSub( false );
			openMenu();
		} else {
			closeMenu();
		}
	}

	/* ── Events ────────────────────────────────────────────────── */

	toggle.addEventListener( 'click', () => setOpen( ! state.open ) );

	if ( subToggle ) {
		subToggle.addEventListener( 'click', () => setSub( ! state.sub ) );
		subGroup.addEventListener( 'mouseenter', openSub );
		sub.addEventListener( 'mouseenter', keepSub );
		header
			.querySelectorAll( '.ci-header-nav > a' )
			.forEach( ( link ) =>
				link.addEventListener( 'mouseenter', closeSubSoon )
			);
	}

	header.addEventListener( 'mouseleave', () => {
		keepSub();
		setSub( false );
	} );

	// Escape closes the submenu and the menu.
	window.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' ) {
			setSub( false );
			setOpen( false );
		}
	} );

	// With the menu open, Tab only cycles through the menu and the close button (modal dialog).
	document.addEventListener( 'keydown', ( e ) => {
		if ( ! state.open || e.key !== 'Tab' ) {
			return;
		}
		const focusables = [
			toggle,
			...menu.querySelectorAll(
				"a[href], button:not([disabled]), [tabindex]:not([tabindex='-1'])"
			),
		].filter( ( el ) => el.getClientRects().length > 0 );
		if ( ! focusables.length ) {
			return;
		}
		const first = focusables[ 0 ];
		const last = focusables[ focusables.length - 1 ];
		const active = header.ownerDocument.activeElement;
		const outside = ! focusables.includes( active );
		if ( e.shiftKey && ( active === first || outside ) ) {
			e.preventDefault();
			last.focus();
		} else if ( ! e.shiftKey && ( active === last || outside ) ) {
			e.preventDefault();
			first.focus();
		}
	} );

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', onResize );

	// On desktop the pill does not exist until the bar collapses.
	const mm = gsap.matchMedia();
	mm.add( '(min-width: 1180px)', () => {
		if ( ! state.morphed ) {
			gsap.set( toggle, { autoAlpha: 0 } );
		}
	} );

	onScroll();
	header.classList.add( 'is-ready' );
}

document.querySelectorAll( '[data-ci-header]' ).forEach( initHeader );
