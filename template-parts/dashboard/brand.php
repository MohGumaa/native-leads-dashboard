<?php
/**
 * Logo mark + name, shared by the dashboard, the sign-in card and the site header.
 *
 * @package native-leads
 */

?>
<span class="brand">
	<?php echo native_leads_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span class="brand__text">
		<strong><?php bloginfo( 'name' ); ?></strong>
		<small><?php esc_html_e( 'Leads hub', 'native-leads' ); ?></small>
	</span>
</span>
