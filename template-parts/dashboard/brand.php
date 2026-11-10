<?php
/**
 * Logo mark + name, shared by the dashboard, the sign-in card and the site header.
 *
 * @package native-leads
 */

?>
<span class="brand">
	<svg class="brand__mark" viewBox="0 0 24 24" aria-hidden="true">
		<path d="M2.5 13h4l2.5-6 4 11 2.5-6.5" />
		<circle class="dot" cx="19.5" cy="11.5" r="2.5" />
	</svg>
	<span class="brand__text">
		<strong><?php bloginfo( 'name' ); ?></strong>
		<small><?php esc_html_e( 'Leads hub', 'native-leads' ); ?></small>
	</span>
</span>
