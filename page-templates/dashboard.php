<?php
/**
 * Template Name: Leads Dashboard
 * Template Post Type: page
 *
 * The protected leads dashboard. Assign this template to a page (e.g.
 * "Dashboard"); the page's own content is not shown.
 *
 * Visitors get the sign-in screen, signed-in users without the
 * `native_leads_view_leads` capability get a no-access screen.
 *
 * @package native-leads
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'native-leads-app' ); ?>>
<?php
wp_body_open();

if ( ! is_user_logged_in() ) {
	get_template_part( 'template-parts/dashboard/login' );
} elseif ( ! native_leads_can_view_leads() ) {
	get_template_part( 'template-parts/dashboard/no-access' );
} else {
	get_template_part( 'template-parts/dashboard/leads' );
}

wp_footer();
?>
</body>
</html>
