<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package native-leads
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function native_leads_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'native_leads_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function native_leads_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'native_leads_pingback_header' );

/**
 * Archive headers show the kind ("Category", "Tag"…) as an eyebrow above
 * the title, so drop WordPress's "Category:" prefix from the title itself.
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * WordPress ignores the #fragment when marking the current menu item, so
 * every "/#section" link would be highlighted on the home page. Only the
 * plain link should be.
 *
 * @param array   $classes Menu item classes.
 * @param WP_Post $item    Menu item.
 */
function native_leads_menu_anchor_classes( $classes, $item ) {
	if ( false !== strpos( $item->url, '#' ) ) {
		$classes = array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent' ) );
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'native_leads_menu_anchor_classes', 10, 2 );

/**
 * Same fix for the aria-current attribute.
 *
 * @param array   $atts Link attributes.
 * @param WP_Post $item Menu item.
 */
function native_leads_menu_anchor_atts( $atts, $item ) {
	if ( false !== strpos( $item->url, '#' ) ) {
		unset( $atts['aria-current'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'native_leads_menu_anchor_atts', 10, 2 );

/**
 * Shorter "[…]" excerpt ending.
 */
add_filter(
	'excerpt_more',
	static function () {
		return '…';
	}
);
