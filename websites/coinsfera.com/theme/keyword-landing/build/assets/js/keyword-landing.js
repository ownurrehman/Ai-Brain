/**
 * Keyword Landing template behaviour.
 *
 * No dependencies. Loaded only on this template, deferred in the footer.
 * The FAQ accordion is native details/summary, so it needs no script at all.
 *
 * @package Coinsfera_WordPress_Theme
 */
( function () {
	'use strict';

	var reveals = document.querySelectorAll( '.cfkl-reveal' );
	var sticky = document.querySelector( '[data-cfkl-sticky]' );
	var hero = document.querySelector( '.cfkl-hero' );

	function showAll() {
		Array.prototype.forEach.call( reveals, function ( el ) {
			el.classList.add( 'is-visible' );
		} );
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		showAll();
		return;
	}

	var revealObserver = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					revealObserver.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -12% 0px', threshold: 0.08 }
	);

	Array.prototype.forEach.call( reveals, function ( el ) {
		revealObserver.observe( el );
	} );

	/**
	 * Show the mobile call to action once the hero has scrolled out of view.
	 */
	if ( sticky && hero ) {
		new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						sticky.classList.remove( 'is-visible' );
						return;
					}

					sticky.removeAttribute( 'hidden' );
					sticky.classList.add( 'is-visible' );
				} );
			},
			{ threshold: 0 }
		).observe( hero );
	}
}() );
