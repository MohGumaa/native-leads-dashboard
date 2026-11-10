<?php
/**
 * Brand panel on the left of the sign-in, no-access and setup screens.
 *
 * @package native-leads
 */

?>
<aside class="auth__aside" aria-hidden="true">
	<div class="auth__aside-top">
		<span class="brand brand--light">
			<?php echo native_leads_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="brand__text">
				<strong><?php bloginfo( 'name' ); ?></strong>
				<small><?php esc_html_e( 'Leads hub', 'native-leads' ); ?></small>
			</span>
		</span>
	</div>

	<div class="auth__pitch">
		<p class="auth__eyebrow"><?php esc_html_e( 'Private leads hub', 'native-leads' ); ?></p>
		<h2><?php esc_html_e( 'Every sign-up from every ad, in one calm view.', 'native-leads' ); ?></h2>
		<ul>
			<li><?php echo native_leads_icon( 'pulse' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Live numbers for each landing page and campaign', 'native-leads' ); ?></li>
			<li><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'CRM delivery status on every lead', 'native-leads' ); ?></li>
			<li><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Never indexed, never cached', 'native-leads' ); ?></li>
		</ul>
	</div>

	<div class="auth__mock">
		<div class="auth__mock-kpi">
			<small><?php esc_html_e( 'Leads this week', 'native-leads' ); ?></small>
			<strong>1,284</strong>
			<svg viewBox="0 0 120 36" preserveAspectRatio="none"><path d="M0 30 L15 26 L30 28 L45 20 L60 22 L75 14 L90 16 L105 8 L120 4" /></svg>
		</div>
		<div class="auth__mock-kpi">
			<small><?php esc_html_e( 'Delivered to CRM', 'native-leads' ); ?></small>
			<strong>98%</strong>
			<span class="auth__mock-bar"><span style="width: 98%"></span></span>
		</div>
	</div>
</aside>
