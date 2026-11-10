<?php
/**
 * The footer for the public site.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package native-leads
 */

$native_leads_cta = native_leads_dashboard_cta();
?>

	<footer id="colophon" class="site-footer">
		<div class="container site-footer__inner">
			<div class="site-footer__about">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php get_template_part( 'template-parts/dashboard/brand' ); ?></a>
				<p class="muted">
					<?php
					$native_leads_description = get_bloginfo( 'description', 'display' );
					echo $native_leads_description ? esc_html( $native_leads_description ) : esc_html__( 'Every registration from your landing pages, in one private dashboard.', 'native-leads' );
					?>
				</p>
			</div>

			<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'native-leads' ); ?>">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Menu', 'native-leads' ); ?></h2>
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

			<?php if ( $native_leads_cta ) : ?>
				<div class="site-footer__cta">
					<h2 class="site-footer__heading"><?php esc_html_e( 'Leads hub', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'Sign in to see today’s leads.', 'native-leads' ); ?></p>
					<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
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
			<a class="site-footer__top" href="#page"><?php esc_html_e( 'Back to top ↑', 'native-leads' ); ?></a>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
