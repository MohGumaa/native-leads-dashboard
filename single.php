<?php
/**
 * The template for single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package native-leads
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'single' );
			?>

			<div class="container container--narrow">
				<?php
				the_post_navigation(
					array(
						'prev_text' => '<span class="nav-subtitle">' . esc_html__( '← Previous', 'native-leads' ) . '</span> <span class="nav-title">%title</span>',
						'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next →', 'native-leads' ) . '</span> <span class="nav-title">%title</span>',
					)
				);

				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
			</div>

			<?php
		endwhile;
		?>

	</main><!-- #main -->

<?php
get_footer();
