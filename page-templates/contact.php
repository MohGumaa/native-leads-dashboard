<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * Contact page: ways to reach the team beside the page's own content.
 * The email address defaults to the site admin email; change it with the
 * `native_leads_contact_email` filter.
 *
 * @package native-leads
 */

get_header();

$native_leads_email   = apply_filters( 'native_leads_contact_email', get_option( 'admin_email' ) );
$native_leads_privacy = (int) get_option( 'wp_page_for_privacy_policy' );
$native_leads_hub     = native_leads_dashboard_page_url();
?>

	<main id="primary" class="site-main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<header class="page-head page-head--center">
					<div class="page-head__bg" aria-hidden="true"></div>
					<div class="container page-head__inner">
						<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'native-leads' ); ?>">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'native-leads' ); ?></a>
							<span aria-hidden="true">/</span>
							<span aria-current="page"><?php the_title(); ?></span>
						</nav>
						<?php the_title( '<h1 class="page-head__title">', '</h1>' ); ?>
						<?php if ( has_excerpt() ) : ?>
							<p class="page-head__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
					</div>
				</header>

				<div class="container contact">
					<div class="contact__cards">
						<a class="contact-card contact-card--primary" href="mailto:<?php echo esc_attr( antispambot( $native_leads_email ) ); ?>">
							<span class="contact-card__icon"><?php echo native_leads_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="contact-card__label"><?php esc_html_e( 'Email us', 'native-leads' ); ?></span>
							<strong><?php echo esc_html( antispambot( $native_leads_email ) ); ?></strong>
							<span class="contact-card__note"><?php esc_html_e( 'General questions, campaigns and partnerships.', 'native-leads' ); ?></span>
						</a>
						<?php if ( $native_leads_privacy && 'publish' === get_post_status( $native_leads_privacy ) ) : ?>
							<a class="contact-card" href="<?php echo esc_url( get_permalink( $native_leads_privacy ) ); ?>">
								<span class="contact-card__icon"><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact-card__label"><?php esc_html_e( 'Your data', 'native-leads' ); ?></span>
								<strong><?php esc_html_e( 'Privacy requests', 'native-leads' ); ?></strong>
								<span class="contact-card__note"><?php esc_html_e( 'See, correct or delete what we hold about you.', 'native-leads' ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $native_leads_hub ) : ?>
							<a class="contact-card" href="<?php echo esc_url( $native_leads_hub ); ?>">
								<span class="contact-card__icon"><?php echo native_leads_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<span class="contact-card__label"><?php esc_html_e( 'Team', 'native-leads' ); ?></span>
								<strong><?php esc_html_e( 'Leads hub sign-in', 'native-leads' ); ?></strong>
								<span class="contact-card__note"><?php esc_html_e( 'For team members with access.', 'native-leads' ); ?></span>
							</a>
						<?php endif; ?>
					</div>

					<div class="contact__body prose">
						<?php the_content(); ?>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	</main><!-- #main -->

<?php
get_footer();
