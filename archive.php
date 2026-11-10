<?php
/**
 * The template for archive pages: categories, tags, authors and dates.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

get_header();

if ( is_category() ) {
	$native_leads_kind = __( 'Category', 'native-leads' );
} elseif ( is_tag() ) {
	$native_leads_kind = __( 'Tag', 'native-leads' );
} elseif ( is_author() ) {
	$native_leads_kind = __( 'Author', 'native-leads' );
} elseif ( is_date() ) {
	$native_leads_kind = __( 'Archive', 'native-leads' );
} else {
	$native_leads_kind = __( 'Collection', 'native-leads' );
}
?>

	<main id="primary" class="site-main">

		<header class="page-hero">
			<div class="container">
				<p class="eyebrow"><?php echo esc_html( $native_leads_kind ); ?></p>
				<h1 class="page-hero__title"><?php echo wp_kses_post( get_the_archive_title() ); ?></h1>
				<?php the_archive_description( '<div class="page-hero__lead muted">', '</div>' ); ?>
				<?php if ( have_posts() ) : ?>
					<p class="page-hero__count">
						<?php
						/* translators: %s: number of posts */
						echo esc_html( sprintf( _n( '%s post', '%s posts', (int) $wp_query->found_posts, 'native-leads' ), number_format_i18n( $wp_query->found_posts ) ) );
						?>
					</p>
				<?php endif; ?>
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
