<?php
/**
 * The footer for the public site.
 *
 * Columns: brand, the "Footer" menu (product links), the "Footer: Company"
 * menu (About, Contact, Terms, Privacy) and the hub sign-in. Until menus are
 * assigned in Appearance → Menus, sensible defaults are shown.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package native-leads
 */

$native_leads_cta     = native_leads_dashboard_cta();
$native_leads_email   = apply_filters( 'native_leads_contact_email', get_option( 'admin_email' ) );
$native_leads_terms   = native_leads_page( 'term-and-conditions' ) ? native_leads_page( 'term-and-conditions' ) : native_leads_page( 'terms-and-conditions' );
$native_leads_privacy = (int) get_option( 'wp_page_for_privacy_policy' );
?>

	<footer id="colophon" class="site-footer">
		<div class="container site-footer__inner">
			<div class="site-footer__about">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php get_template_part( 'template-parts/dashboard/brand' ); ?></a>
				<p class="muted">
					<?php
					$native_leads_description = get_bloginfo( 'description', 'display' );
					echo $native_leads_description ? esc_html( $native_leads_description ) : esc_html__( 'Every sign-up from our landing pages, with its campaign and CRM status, in one private hub.', 'native-leads' );
					?>
				</p>
				<?php if ( $native_leads_email ) : ?>
					<a class="site-footer__mail" href="mailto:<?php echo esc_attr( antispambot( $native_leads_email ) ); ?>">
						<?php echo native_leads_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( antispambot( $native_leads_email ) ); ?>
					</a>
				<?php endif; ?>
			</div>

			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Product', 'native-leads' ); ?>">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Product', 'native-leads' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => 'native_leads_menu_fallback',
					)
				);
				?>
			</nav>

			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Company', 'native-leads' ); ?>">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Company', 'native-leads' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer-company',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => 'native_leads_menu_fallback',
					)
				);
				?>
			</nav>

			<?php if ( $native_leads_cta ) : ?>
				<div class="site-footer__cta">
					<h2 class="site-footer__heading"><?php esc_html_e( 'Leads hub', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'Sign in to see today’s leads.', 'native-leads' ); ?></p>
					<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
						<?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo esc_html( $native_leads_cta['label'] ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<div class="container site-footer__base">
			<p>
				&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>.
				<?php esc_html_e( 'All rights reserved.', 'native-leads' ); ?>
			</p>
			<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'native-leads' ); ?>">
				<?php if ( $native_leads_terms ) : ?>
					<a href="<?php echo esc_url( get_permalink( $native_leads_terms ) ); ?>"><?php esc_html_e( 'Terms', 'native-leads' ); ?></a>
				<?php endif; ?>
				<?php if ( $native_leads_privacy && 'publish' === get_post_status( $native_leads_privacy ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $native_leads_privacy ) ); ?>"><?php esc_html_e( 'Privacy', 'native-leads' ); ?></a>
				<?php endif; ?>
				<a class="site-footer__top" href="#page"><?php esc_html_e( 'Back to top ↑', 'native-leads' ); ?></a>
			</nav>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
