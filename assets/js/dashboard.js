/**
 * Leads dashboard behaviour. The page works without it (filters submit with
 * the Apply buttons); this adds auto-apply, the mobile sidebar, the lead
 * drawer, chart tooltips, the "/" search shortcut and the theme switch.
 */
( function () {
	'use strict';

	document.documentElement.classList.add( 'js' );

	/* ---------- theme ---------- */

	document.querySelectorAll( '[data-theme-toggle]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
			document.documentElement.dataset.theme = next;
			try {
				localStorage.setItem( 'native-leads-theme', next );
			} catch ( e ) {}
		} );
	} );

	/* ---------- mobile sidebar ---------- */

	var sidebar = document.querySelector( '[data-sidebar]' );
	var scrim = document.querySelector( '.sidebar-scrim' );
	var menuBtn = document.querySelector( '[data-sidebar-open]' );

	var setSidebar = function ( open ) {
		if ( ! sidebar ) {
			return;
		}
		sidebar.classList.toggle( 'is-open', open );
		scrim.hidden = ! open;
		if ( menuBtn ) {
			menuBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
	};

	if ( menuBtn ) {
		menuBtn.addEventListener( 'click', function () {
			setSidebar( true );
		} );
	}
	document.querySelectorAll( '[data-sidebar-close]' ).forEach( function ( el ) {
		el.addEventListener( 'click', function () {
			setSidebar( false );
		} );
	} );
	if ( sidebar ) {
		sidebar.querySelectorAll( 'a[href^="#"]' ).forEach( function ( a ) {
			a.addEventListener( 'click', function () {
				setSidebar( false );
			} );
		} );
	}

	/* ---------- sidebar: highlight the section in view ---------- */

	var navLinks = document.querySelectorAll( '.sidenav__link[href^="#"]' );
	if ( navLinks.length && 'IntersectionObserver' in window ) {
		var leadsCard = document.getElementById( 'leads' );
		if ( leadsCard ) {
			new IntersectionObserver(
				function ( entries ) {
					var inLeads = entries[ 0 ].isIntersecting;
					navLinks.forEach( function ( a ) {
						var isLeads = a.getAttribute( 'href' ) === '#leads';
						a.classList.toggle( 'is-current', inLeads ? isLeads : ! isLeads );
					} );
				},
				{ rootMargin: '-40% 0px -40% 0px' }
			).observe( leadsCard );
		}
	}

	/* ---------- filters ---------- */

	var form = document.querySelector( '[data-filters]' );
	if ( form ) {
		var dates = document.querySelector( '[data-dates]' );
		var search = form.querySelector( '[data-search]' );
		var timer;

		var submit = function () {
			// Drop empty fields so the URL stays short and shareable.
			// form.elements includes the range controls in the header (form="").
			Array.prototype.forEach.call( form.elements, function ( el ) {
				if ( el.name && ! el.value ) {
					el.disabled = true;
				}
			} );
			form.submit();
		};

		form.querySelectorAll( '[data-autosubmit]' ).forEach( function ( el ) {
			el.addEventListener( 'change', submit );
		} );

		document.querySelectorAll( '[data-range]' ).forEach( function ( el ) {
			el.addEventListener( 'change', function () {
				if ( el.value === 'custom' ) {
					dates.hidden = false;
					dates.querySelector( 'input' ).focus();
					return;
				}
				submit();
			} );
		} );

		if ( search ) {
			var initial = search.value;
			search.addEventListener( 'input', function () {
				clearTimeout( timer );
				timer = setTimeout( function () {
					if ( search.value.trim() !== initial ) {
						submit();
					}
				}, 600 );
			} );

			// Keep the caret at the end after the page reloads with a search.
			if ( initial ) {
				search.focus( { preventScroll: true } );
				search.setSelectionRange( initial.length, initial.length );
			}

			// "/" jumps to search, like most dashboards.
			document.addEventListener( 'keydown', function ( e ) {
				var tag = ( e.target.tagName || '' ).toLowerCase();
				if ( e.key === '/' && ! e.ctrlKey && ! e.metaKey && tag !== 'input' && tag !== 'select' && tag !== 'textarea' ) {
					e.preventDefault();
					search.focus();
					search.select();
				}
			} );
		}

		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			submit();
		} );
	}

	/* ---------- chart tooltip ---------- */

	var tip = document.querySelector( '[data-chart-tip]' );
	if ( tip ) {
		var show = function ( col ) {
			var box = col.getBoundingClientRect();
			var dot = col.firstElementChild.getBoundingClientRect();
			tip.textContent = col.dataset.tip;
			tip.style.left = box.left + box.width / 2 + 'px';
			tip.style.top = Math.max( box.top, dot.top ) + 'px';
			tip.hidden = false;
		};
		var hide = function () {
			tip.hidden = true;
		};
		document.querySelectorAll( '.chart__col' ).forEach( function ( col ) {
			col.addEventListener( 'mouseenter', function () {
				show( col );
			} );
			col.addEventListener( 'focus', function () {
				show( col );
			} );
			col.addEventListener( 'mouseleave', hide );
			col.addEventListener( 'blur', hide );
		} );
		window.addEventListener( 'scroll', hide, { passive: true } );
	}

	/* ---------- lead drawer ---------- */

	var drawer = document.querySelector( '[data-drawer]' );
	if ( ! drawer ) {
		return;
	}

	var current = null;
	var opener = null;
	var actions = {
		call: drawer.querySelector( '[data-action="call"]' ),
		whatsapp: drawer.querySelector( '[data-action="whatsapp"]' ),
		email: drawer.querySelector( '[data-action="email"]' ),
	};
	var prefers = drawer.querySelector( '[data-prefers]' );

	var open = function ( row ) {
		var lead;
		try {
			lead = JSON.parse( row.dataset.lead );
		} catch ( e ) {
			return;
		}
		current = lead;
		opener = row;

		drawer.querySelectorAll( '[data-f]' ).forEach( function ( el ) {
			var value = lead[ el.dataset.f ];
			el.textContent = value === null || value === undefined ? '' : String( value );
		} );
		drawer.querySelector( '.drawer__head .badge' ).className = 'badge badge--' + lead.crm;
		drawer.querySelector( '[data-f="phone"]' ).textContent = lead.digits ? '+' + lead.phone : '';

		actions.call.href = 'tel:+' + lead.digits;
		actions.whatsapp.href = 'https://wa.me/' + lead.digits;
		actions.email.href = 'mailto:' + lead.email;

		// Highlight the way the lead asked to be contacted.
		var map = { call: 'call', phone: 'call', whatsapp: 'whatsapp', email: 'email' };
		var preferred = map[ lead.contact ] || '';
		Object.keys( actions ).forEach( function ( key ) {
			actions[ key ].classList.toggle( 'btn--primary', key === preferred );
			actions[ key ].classList.toggle( 'btn--ghost', key !== preferred );
		} );
		prefers.hidden = ! preferred;
		prefers.textContent = preferred ? '★ ' + drawer.dataset.prefersLabel.replace( '%s', lead.contact_label ) : '';

		drawer.hidden = false;
		document.body.style.overflow = 'hidden';
		drawer.querySelector( '.drawer__head .icon-btn' ).focus();
	};

	var close = function () {
		drawer.hidden = true;
		document.body.style.overflow = '';
		if ( opener ) {
			opener.focus();
		}
	};

	document.querySelectorAll( 'tr[data-lead]' ).forEach( function ( row ) {
		row.addEventListener( 'click', function ( e ) {
			// Let email / phone links work without opening the drawer.
			if ( e.target.closest( 'a' ) ) {
				return;
			}
			open( row );
		} );
		row.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Enter' || e.key === ' ' ) {
				e.preventDefault();
				open( row );
			}
		} );
	} );

	drawer.querySelectorAll( '[data-drawer-close]' ).forEach( function ( el ) {
		el.addEventListener( 'click', close );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( drawer.hidden ) {
			if ( e.key === 'Escape' && sidebar && sidebar.classList.contains( 'is-open' ) ) {
				setSidebar( false );
			}
			return;
		}
		if ( e.key === 'Escape' ) {
			close();
		}
		// Keep Tab inside the open drawer.
		if ( e.key === 'Tab' ) {
			var focusable = drawer.querySelectorAll( 'a[href], button' );
			var first = focusable[ 0 ];
			var last = focusable[ focusable.length - 1 ];
			if ( e.shiftKey && document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		}
	} );

	var copyBtn = drawer.querySelector( '[data-copy]' );
	copyBtn.addEventListener( 'click', function () {
		if ( ! current || ! navigator.clipboard ) {
			return;
		}
		var profile = [ current.occupation, current.age ].filter( Boolean ).join( ', ' );
		var text = [
			current.name + ' (' + current.ref + ')',
			current.email,
			'+' + current.phone,
			profile,
			current.source + ( current.utm_campaign ? ' · ' + current.utm_campaign : '' ),
			current.received,
		]
			.filter( Boolean )
			.join( '\n' );
		var label = copyBtn.querySelector( 'span' );
		var original = label.textContent;
		navigator.clipboard.writeText( text ).then( function () {
			label.textContent = '✓';
			setTimeout( function () {
				label.textContent = original;
			}, 1500 );
		} );
	} );
} )();
