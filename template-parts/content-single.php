<?php
/**
 * Template part: a full single post.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="page-hero page-hero--entry">
		<div class="container container--narrow">
			<a class="back-link" href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ? get_post_type_archive_link( 'post' ) : home_url( '/' ) ); ?>">
				<?php echo native_leads_icon( 'back' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'All posts', 'native-leads' ); ?>
			</a>
			<?php if ( 'post' === get_post_type() ) : ?>
				<div class="post-card__top"><?php native_leads_category_pill(); ?></div>
			<?php endif; ?>
			<?php the_title( '<h1 class="page-hero__title">', '</h1>' ); ?>
			<?php if ( has_excerpt() ) : ?>
				<p class="page-hero__lead muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<?php
			if ( 'post' === get_post_type() ) {
				native_leads_entry_meta();
			}
			?>
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

		<?php
		$native_leads_tags = get_the_tags();
		if ( $native_leads_tags || get_edit_post_link() ) :
			?>
		<footer class="entry__footer">
			<?php if ( $native_leads_tags ) : ?>
				<ul class="tag-list" aria-label="<?php esc_attr_e( 'Tags', 'native-leads' ); ?>">
					<?php foreach ( $native_leads_tags as $native_leads_tag ) : ?>
						<li><a class="pill" href="<?php echo esc_url( get_tag_link( $native_leads_tag ) ); ?>">#<?php echo esc_html( $native_leads_tag->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			edit_post_link(
				esc_html__( 'Edit post', 'native-leads' ),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer>
		<?php endif; ?>

		<?php if ( 'post' === get_post_type() && get_the_author_meta( 'description' ) ) : ?>
			<aside class="author-box">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', '', array( 'class' => 'author-box__avatar' ) ); ?>
				<div>
					<p class="faint"><?php esc_html_e( 'Written by', 'native-leads' ); ?></p>
					<h2><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></h2>
					<p class="muted"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
				</div>
			</aside>
		<?php endif; ?>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
