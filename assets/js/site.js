/**
 * Public site behaviour: light/dark switch and the mobile menu.
 */
( function () {
	'use strict';

	/* ---------- theme ---------- */

	document.querySelectorAll( '[data-theme-toggle]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
			document.documentElement.dataset.theme = next;
			try {
				localStorage.setItem( 'native-leads-theme', next );
			} catch ( e ) {}
		} );
	} );

	/* ---------- mobile menu ---------- */

	var toggle = document.querySelector( '[data-nav-toggle]' );
	var nav = document.querySelector( '[data-nav]' );
	if ( ! toggle || ! nav ) {
		return;
	}

	var setOpen = function ( open ) {
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		nav.classList.toggle( 'is-open', open );
	};

	toggle.addEventListener( 'click', function () {
		setOpen( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && nav.classList.contains( 'is-open' ) ) {
			setOpen( false );
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( nav.classList.contains( 'is-open' ) && ! nav.contains( e.target ) && ! toggle.contains( e.target ) ) {
			setOpen( false );
		}
	} );

	// Close after following an in-page link (e.g. "#features").
	nav.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( 'a' ) ) {
			setOpen( false );
		}
	} );
} )();
