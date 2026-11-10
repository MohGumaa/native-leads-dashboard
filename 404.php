<?php
/**
 * The template for 404 (not found) pages.
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package native-leads
 */

get_header();

$native_leads_cta    = native_leads_dashboard_cta();
$native_leads_recent = wp_get_recent_posts(
	array(
		'numberposts' => 3,
		'post_status' => 'publish',
	),
	OBJECT
);
?>

	<main id="primary" class="site-main">

		<section class="not-found">
			<div class="not-found__glow" aria-hidden="true"></div>
			<div class="container container--narrow not-found__inner">
				<p class="not-found__code" aria-hidden="true">404</p>
				<h1 class="page-hero__title"><?php esc_html_e( 'This page went cold', 'native-leads' ); ?></h1>
				<p class="page-hero__lead muted"><?php esc_html_e( 'The link may be old, or the page has moved. Search the site, or head somewhere useful.', 'native-leads' ); ?></p>

				<?php get_search_form(); ?>

				<div class="not-found__actions">
					<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo native_leads_icon( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php esc_html_e( 'Back to home', 'native-leads' ); ?>
					</a>
					<?php if ( $native_leads_cta ) : ?>
						<a class="btn btn--ghost" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
							<?php echo native_leads_icon( 'chart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo esc_html( $native_leads_cta['label'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( $native_leads_recent ) : ?>
					<div class="not-found__recent">
						<h2 class="faint"><?php esc_html_e( 'Recent posts', 'native-leads' ); ?></h2>
						<ul>
							<?php foreach ( $native_leads_recent as $native_leads_post ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $native_leads_post ) ); ?>">
										<span><?php echo esc_html( get_the_title( $native_leads_post ) ); ?></span>
										<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</section>

	</main><!-- #main -->

<?php
get_footer();
