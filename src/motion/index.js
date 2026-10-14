/**
 * Site motion system (GSAP + ScrollTrigger + SplitText + DrawSVG).
 *
 * One set of rules for every page, with no per-element markup:
 *  - Headings (.ci-h1, .ci-h2, [data-anim="mask"]): lines rise behind a mask.
 *  - Supporting text (.ci-eyebrow, .ci-lead, .ci-text, .ci-link, .ci-btn-row, .ci-breadcrumbs,
 *    [data-anim="rise"]): rises and fades in, in order, after the title.
 *  - Grids (.ci-cards > *, [data-anim-stagger] > *): staggered entrance.
 *  - Covers ([data-cover]): the card reveals, arcs draw, discs bounce and bars grow; then a
 *    subtle continuous motion and scroll parallax.
 *  - Area icons ([data-area-icon]) and decorations (.ci-arc, [data-anim="pop"]).
 *  - [data-anim-css]: above-the-fold content animated by CSS (motion.css) so the LCP is not
 *    delayed; ignored here.
 *  - [data-anim-off]: section with animations turned off in the editor; ignored entirely.
 *
 * When the browser asks for reduced motion, or the page stops painting, everything stays visible
 * in its final state.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

// ScrollTrigger, SplitText and DrawSVG are already registered in ci-theme-gsap.

const HEADING = '.ci-h1, .ci-h2, [data-anim="mask"]';
const RISE =
	'.ci-eyebrow, .ci-lead, .ci-text, .ci-link, .ci-btn-row, .ci-breadcrumbs, [data-anim="rise"]';
const ITEM = '.ci-cards > *, [data-anim-stagger] > *';
const REVEAL = `${ HEADING }, ${ RISE }, ${ ITEM }`;

const EASE = 'expo.out';

/**
 * Animatable elements inside the roots, except those in sections with animations turned off.
 *
 * @param {Element[]} roots    Roots (main and footer).
 * @param {string}    selector Selector.
 * @return {Element[]} Elements.
 */
const pick = ( roots, selector ) =>
	roots
		.flatMap( ( r ) => Array.from( r.querySelectorAll( selector ) ) )
		.filter( ( el ) => ! el.closest( '[data-anim-off]' ) );

/* ── Text and grids ─────────────────────────────────────────── */

function setupReveals( roots ) {
	// If an element sits inside another animated element, its container animates it.
	const items = pick( roots, REVEAL ).filter(
		( el ) =>
			! el.parentElement?.closest( REVEAL ) &&
			! el.closest( '[data-anim-css]' )
	);
	const splits = new Map();
	const split = ( el ) => {
		const result = SplitText.create( el, {
			type: 'lines',
			mask: 'lines',
			linesClass: 'ci-split-line',
		} );
		splits.set( el, result );
		gsap.set( result.lines, { yPercent: 110 } );
		return result;
	};

	// What is above the fold enters right away, in order; the rest, on scroll.
	const vh = window.innerHeight;
	const now = items.filter( ( el ) => el.getBoundingClientRect().top < vh );
	const later = items.filter( ( el ) => ! now.includes( el ) );

	items.forEach( ( el ) => {
		if ( el.matches( HEADING ) ) {
			// Splitting into lines measures the text: headings further down are split when they appear.
			if ( now.includes( el ) ) {
				split( el );
			} else {
				gsap.set( el, { autoAlpha: 0 } );
			}
		} else if ( el.matches( ITEM ) ) {
			gsap.set( el, { autoAlpha: 0, y: 48 } );
		} else {
			gsap.set( el, { autoAlpha: 0, y: 22 } );
		}
	} );

	const play = ( batch ) => {
		const tl = gsap.timeline();
		batch.forEach( ( el, i ) => {
			const at = i * 0.09;
			let lines = splits.get( el );
			if ( ! lines && el.matches( HEADING ) ) {
				lines = split( el );
				gsap.set( el, { autoAlpha: 1 } );
			}
			if ( lines ) {
				const done = lines;
				tl.to(
					done.lines,
					{
						yPercent: 0,
						duration: 1.2,
						ease: EASE,
						stagger: 0.1,
						onComplete: () => done.revert(),
					},
					at
				);
			} else {
				tl.to(
					el,
					{
						autoAlpha: 1,
						y: 0,
						duration: el.matches( ITEM ) ? 1.1 : 0.9,
						ease: EASE,
						clearProps: 'transform',
					},
					at
				);
			}
		} );
	};

	if ( now.length ) {
		play( now );
	}
	if ( later.length ) {
		ScrollTrigger.batch( later, {
			start: 'top 90%',
			once: true,
			onEnter: ( batch ) => play( batch ),
		} );
	}
}

/* ── Covers ──────────────────────────────────────────────────── */

function setupCover( cover ) {
	if ( cover.dataset.animCss ) {
		return; // animated by CSS
	}
	const isFill = cover.dataset.coverFill === 'true';
	const svg = cover.querySelector( 'svg' );
	const q = ( sel ) => Array.from( cover.querySelectorAll( sel ) );
	const arcs = q( '[data-shape="arc"], [data-shape="ring"]' );
	const discs = q( '[data-shape="disc"]' );
	const bars = q( '[data-shape="bar"]' );
	const texts = q( '[data-shape="text"]' );
	const img = cover.querySelector( 'img' );

	const tl = gsap.timeline( {
		paused: true,
		defaults: { ease: 'power3.inOut' },
	} );

	if ( ! isFill ) {
		tl.fromTo(
			cover,
			{ clipPath: 'inset(100% 0% 0% 0% round 8px)' },
			{
				clipPath: 'inset(0% 0% 0% 0% round 8px)',
				duration: 1.1,
				ease: 'expo.inOut',
				clearProps: 'clipPath',
			},
			0
		);
	}
	if ( img ) {
		tl.from(
			img,
			{
				scale: 1.18,
				duration: 1.6,
				ease: 'expo.out',
				clearProps: 'transform',
			},
			0.15
		);
	}

	if ( svg ) {
		if ( arcs.length ) {
			tl.from(
				arcs,
				{ drawSVG: '0%', duration: 1.6, stagger: 0.15 },
				isFill ? 0.1 : 0.35
			);
		}
		if ( discs.length ) {
			tl.from(
				discs,
				{
					scale: 0,
					transformOrigin: '50% 50%',
					duration: 1.1,
					ease: 'back.out(1.8)',
					stagger: 0.12,
				},
				isFill ? 0.5 : 0.6
			);
		}
		if ( bars.length ) {
			tl.from(
				bars,
				{
					scaleY: 0,
					transformOrigin: ( _, el ) =>
						el.getAttribute( 'data-origin' ) === 'top'
							? '50% 0%'
							: '50% 100%',
					duration: 1,
					ease: 'expo.out',
					stagger: 0.1,
				},
				isFill ? 0.8 : 0.75
			);
		}
		if ( texts.length ) {
			tl.from(
				texts,
				{ autoAlpha: 0, y: 12, duration: 1, ease: EASE },
				0.7
			);
		}
	}

	// Subtle continuous motion (rotation/scale only: vertical movement belongs to the parallax).
	const idle = gsap.timeline( {
		paused: true,
		repeat: -1,
		yoyo: true,
		defaults: { ease: 'sine.inOut' },
	} );
	arcs.forEach( ( a, i ) =>
		idle.to(
			a,
			{
				rotation: i % 2 ? -5 : 5,
				transformOrigin: '50% 50%',
				duration: 5 + i,
			},
			0
		)
	);
	discs.forEach( ( d, i ) =>
		idle.to(
			d,
			{ scale: 1.05, transformOrigin: '50% 50%', duration: 3 + i * 0.7 },
			0.4 * i
		)
	);

	// The loop only runs while the cover is on screen and its entrance has finished.
	let visible = false;
	tl.eventCallback( 'onComplete', () => {
		if ( visible ) {
			idle.play();
		}
	} );
	ScrollTrigger.create( {
		trigger: cover,
		start: 'top bottom',
		end: 'bottom top',
		onToggle: ( self ) => {
			visible = self.isActive;
			if ( tl.progress() === 1 ) {
				if ( visible ) {
					idle.play();
				} else {
					idle.pause();
				}
			}
		},
	} );
	ScrollTrigger.create( {
		trigger: cover,
		start: 'top 88%',
		once: true,
		onEnter: () => void tl.play(),
	} );

	// Parallax: each shape moves according to its depth.
	const layers = q( '[data-depth]' );
	if ( layers.length ) {
		const amp = isFill ? 10 : 6;
		gsap.fromTo(
			layers,
			{
				y: ( _, el ) =>
					-amp * Number( el.getAttribute( 'data-depth' ) || 0.5 ),
			},
			{
				y: ( _, el ) =>
					amp * Number( el.getAttribute( 'data-depth' ) || 0.5 ),
				ease: 'none',
				scrollTrigger: {
					trigger: cover,
					start: 'top bottom',
					end: 'bottom top',
					scrub: 0.8,
				},
			}
		);
	}
}

/* ── Area icons ──────────────────────────────────────────────── */

function setupAreaIcon( icon ) {
	const q = ( sel ) => Array.from( icon.querySelectorAll( sel ) );
	const tl = gsap.timeline( { paused: true } );
	// Each icon uses only some pieces: only the animations with elements are added.
	const add = ( sel, vars, at ) => {
		const els = q( sel );
		if ( els.length ) {
			tl.from( els, vars, at );
		}
	};
	add(
		'[data-icon="grow-up"]',
		{
			scaleY: 0,
			transformOrigin: '50% 100%',
			duration: 0.9,
			ease: 'expo.out',
			stagger: 0.12,
		},
		0
	);
	add(
		'[data-icon="grow-x"]',
		{
			scaleX: 0,
			transformOrigin: '50% 50%',
			duration: 0.9,
			ease: 'expo.out',
		},
		0.15
	);
	add(
		'[data-icon="fade"]',
		{ autoAlpha: 0, duration: 1.4, ease: 'power2.out', stagger: 0.2 },
		0
	);
	add(
		'[data-icon="draw"]',
		{
			drawSVG: '0%',
			duration: ( _, el ) =>
				Number( el.getAttribute( 'data-duration' ) || 1.1 ),
			ease: 'power3.inOut',
			stagger: 0.18,
		},
		0.25
	);
	add(
		'[data-icon="pop"]',
		{
			scale: 0,
			transformOrigin: '50% 50%',
			duration: 0.8,
			ease: 'back.out(2.2)',
		},
		0.35
	);
	add(
		'[data-icon="drop"]',
		{ y: -34, autoAlpha: 0, duration: 1.2, ease: 'bounce.out' },
		0.45
	);
	add(
		'[data-icon="rise"]',
		{ y: 16, autoAlpha: 0, duration: 1.4, ease: 'expo.out' },
		0.6
	);

	const loop = gsap.timeline( {
		paused: true,
		repeat: -1,
		repeatDelay: 0.6,
	} );
	q( '[data-loop="bob"]' ).forEach( ( el ) =>
		loop.to(
			el,
			{ y: -4, duration: 1.1, ease: 'sine.inOut', yoyo: true, repeat: 1 },
			0
		)
	);
	q( '[data-loop="pulse"]' ).forEach( ( el ) =>
		loop.to(
			el,
			{
				scale: 1.4,
				transformOrigin: '50% 50%',
				duration: 0.9,
				ease: 'sine.inOut',
				yoyo: true,
				repeat: 1,
			},
			0
		)
	);
	const waves = q( '[data-loop="wave"]' );
	if ( waves.length ) {
		loop.to(
			waves,
			{
				opacity: 0.2,
				duration: 0.45,
				ease: 'sine.inOut',
				stagger: 0.2,
				yoyo: true,
				repeat: 1,
			},
			0
		);
	}

	let visible = false;
	tl.eventCallback( 'onComplete', () => {
		if ( visible ) {
			loop.play();
		}
	} );
	ScrollTrigger.create( {
		trigger: icon,
		start: 'top 90%',
		end: 'bottom top',
		onEnter: () => void tl.play(),
		onToggle: ( self ) => {
			visible = self.isActive;
			if ( tl.progress() === 1 ) {
				if ( visible ) {
					loop.play();
				} else {
					loop.pause();
				}
			}
		},
	} );
}

/* ── Details: counters, lines, arcs and decorative dots ───────── */

function setupDetails( roots ) {
	// Starts at 0 while hidden; if the animation is cancelled (see watchdog), reverting the
	// context restores the real number (it never stays at "+0").
	const counters = pick( roots, '[data-anim="count"]' ).map( ( el ) => ( {
		el,
		text: el.textContent || '',
	} ) );
	counters.forEach( ( { el, text } ) => {
		const m = text.match( /^(\D*)(\d+)(\D*)$/ );
		if ( ! m ) {
			return;
		}
		const obj = { v: 0 };
		el.textContent = `${ m[ 1 ] }0${ m[ 3 ] }`;
		gsap.to( obj, {
			v: Number( m[ 2 ] ),
			duration: 1.8,
			ease: 'power2.out',
			delay: 0.3,
			onUpdate: () => {
				el.textContent = `${ m[ 1 ] }${ Math.round( obj.v ) }${ m[ 3 ] }`;
			},
			scrollTrigger: { trigger: el, start: 'top 92%', once: true },
		} );
	} );

	pick( roots, '[data-anim="line"]' ).forEach( ( el ) => {
		const tip = el.parentElement?.querySelector( '[data-anim="tip"]' );
		const tl = gsap.timeline( {
			scrollTrigger: { trigger: el, start: 'top 90%', once: true },
		} );
		tl.from( el, {
			scaleX: 0,
			transformOrigin: '0% 50%',
			duration: 1.3,
			ease: 'expo.inOut',
			delay: 0.2,
		} );
		if ( tip ) {
			tl.from(
				tip,
				{ x: -10, autoAlpha: 0, duration: 0.6, ease: 'expo.out' },
				'-=0.35'
			);
		}
	} );

	pick( roots, '.ci-arc' ).forEach( ( el ) => {
		const trigger = el.parentElement ?? el;
		gsap.from( el, {
			rotation: -70,
			scale: 0.85,
			autoAlpha: 0,
			duration: 1.8,
			ease: 'expo.out',
			scrollTrigger: { trigger, start: 'top 85%', once: true },
		} );
		gsap.to( el, {
			rotation: '+=40',
			ease: 'none',
			immediateRender: false,
			scrollTrigger: {
				trigger,
				start: 'top bottom',
				end: 'bottom top',
				scrub: 1,
			},
		} );
	} );

	pick( roots, '[data-anim="pop"]' ).forEach( ( el ) => {
		gsap.from( el, {
			scale: 0,
			duration: 1.1,
			ease: 'back.out(2)',
			delay: 0.35,
			scrollTrigger: {
				trigger: el.parentElement ?? el,
				start: 'top 85%',
				once: true,
			},
		} );
	} );

	return () =>
		counters.forEach( ( { el, text } ) => ( el.textContent = text ) );
}

/* ── Boot ─────────────────────────────────────────────────────── */

function start() {
	const html = document.documentElement;
	const ready = () => html.classList.add( 'ci-motion-ready' );

	if (
		! html.classList.contains( 'ci-motion' ) ||
		window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches
	) {
		ready();
		return;
	}

	let ctx = null;

	// Yields the main thread between stages: several short tasks instead of a long one.
	const pause = () =>
		window.scheduler?.yield
			? window.scheduler.yield()
			: new Promise( ( resolve ) => window.setTimeout( resolve, 0 ) );

	const setup = async () => {
		const roots = [
			document.querySelector( 'main' ),
			document.querySelector( '.ci-footer' ),
		].filter( Boolean );
		const each = ( selector, fn ) =>
			roots.forEach( ( r ) =>
				r
					.querySelectorAll( selector )
					.forEach(
						( el ) => ! el.closest( '[data-anim-off]' ) && fn( el )
					)
			);
		const current = gsap.context( () => setupReveals( roots ) );
		ctx = current;
		await pause();
		if ( ctx !== current ) {
			return; // The watchdog already revealed everything.
		}
		current.add( () => each( '[data-cover]', setupCover ) );
		await pause();
		if ( ctx !== current ) {
			return;
		}
		current.add( () => {
			each( '[data-area-icon]', setupAreaIcon );
			return setupDetails( roots );
		} );
		ready();
		ScrollTrigger.refresh();
	};

	// SplitText needs the fonts loaded to measure the lines.
	if ( document.fonts?.status === 'loaded' ) {
		setup();
	} else {
		document.fonts.ready.then( setup );
	}

	// If the page stops painting (background window, screenshots, prerender) everything is shown.
	let last = performance.now();
	let raf = window.requestAnimationFrame( function beat() {
		last = performance.now();
		raf = window.requestAnimationFrame( beat );
	} );
	const watchdog = window.setInterval( () => {
		if ( ! document.hidden && performance.now() - last > 1200 ) {
			ctx?.revert();
			ctx = null;
			ready();
			window.clearInterval( watchdog );
			window.cancelAnimationFrame( raf );
		}
	}, 300 );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', start );
} else {
	start();
}
