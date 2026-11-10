<?php
/**
 * The main template file: the blog index, and the fallback for any view
 * without a more specific template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

get_header();

$native_leads_title = is_home() && ! is_front_page() ? single_post_title( '', false ) : __( 'Latest posts', 'native-leads' );
?>

	<main id="primary" class="site-main">

		<header class="page-hero">
			<div class="container">
				<p class="eyebrow"><?php esc_html_e( 'Blog', 'native-leads' ); ?></p>
				<h1 class="page-hero__title"><?php echo esc_html( $native_leads_title ); ?></h1>
				<p class="page-hero__lead muted"><?php esc_html_e( 'News, guides and updates from the team.', 'native-leads' ); ?></p>
			</div>
		</header>

		<div class="container section section--tight">
			<?php if ( have_posts() ) : ?>

				<div class="post-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', get_post_type() );
					endwhile;
					?>
				</div>

				<?php native_leads_pagination(); ?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>
		</div>

	</main><!-- #main -->

<?php
get_footer();
