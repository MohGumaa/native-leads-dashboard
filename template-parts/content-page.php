<?php
/**
 * Template part: page content in page.php.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="page-hero page-hero--entry">
		<div class="container container--narrow">
			<?php
			$native_leads_parent = wp_get_post_parent_id( get_the_ID() );
			if ( $native_leads_parent ) :
				?>
				<a class="back-link" href="<?php echo esc_url( get_permalink( $native_leads_parent ) ); ?>">
					<?php echo native_leads_icon( 'back' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo esc_html( get_the_title( $native_leads_parent ) ); ?>
				</a>
			<?php endif; ?>
			<?php the_title( '<h1 class="page-hero__title">', '</h1>' ); ?>
			<?php if ( has_excerpt() ) : ?>
				<p class="page-hero__lead muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
		<figure class="container entry__cover">
			<?php the_post_thumbnail( 'full' ); ?>
		</figure>
	<?php endif; ?>

	<div class="container container--narrow">
		<div class="prose">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'native-leads' ),
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php if ( get_edit_post_link() ) : ?>
			<footer class="entry__footer">
				<?php edit_post_link( esc_html__( 'Edit page', 'native-leads' ), '<span class="edit-link">', '</span>' ); ?>
			</footer>
		<?php endif; ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
