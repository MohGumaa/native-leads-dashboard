<?php
/**
 * The template for search results.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package native-leads
 */

get_header();
?>

	<main id="primary" class="site-main">

		<header class="page-hero">
			<div class="container container--narrow">
				<p class="eyebrow"><?php esc_html_e( 'Search', 'native-leads' ); ?></p>
				<h1 class="page-hero__title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Results for %s', 'native-leads' ), '<span class="text-accent">“' . get_search_query() . '”</span>' );
					?>
				</h1>
				<p class="page-hero__count">
					<?php
					/* translators: %s: number of results */
					echo esc_html( sprintf( _n( '%s result', '%s results', (int) $wp_query->found_posts, 'native-leads' ), number_format_i18n( $wp_query->found_posts ) ) );
					?>
				</p>
				<?php get_search_form(); ?>
			</div>
		</header>

		<div class="container container--narrow section section--tight">
			<?php if ( have_posts() ) : ?>

				<div class="result-list">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'search' );
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
