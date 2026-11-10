<?php
/**
 * The default template for pages.
 *
 * The leads dashboard is not rendered here: it uses its own page template
 * (page-templates/dashboard.php, "Leads Dashboard").
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				?>
				<div class="container container--narrow">
					<?php comments_template(); ?>
				</div>
				<?php
			endif;

		endwhile;
		?>

	</main><!-- #main -->

<?php
get_footer();
