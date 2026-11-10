<?php
/**
 * Template part: post card, used in the blog index and archives.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<?php the_post_thumbnail( 'medium_large', array( 'alt' => '' ) ); ?>
		<?php else : ?>
			<span class="post-card__placeholder"><?php echo native_leads_icon( 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endif; ?>
	</a>

	<div class="post-card__body">
		<div class="post-card__top">
			<?php if ( 'post' === get_post_type() ) : ?>
				<?php native_leads_category_pill(); ?>
			<?php endif; ?>
			<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
				<span class="pill pill--accent"><?php esc_html_e( 'Featured', 'native-leads' ); ?></span>
			<?php endif; ?>
		</div>

		<?php the_title( '<h2 class="post-card__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<p class="post-card__excerpt muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>

		<footer class="post-card__foot">
			<time class="faint" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<a class="link-arrow" href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Read', 'native-leads' ); ?>
				<span class="screen-reader-text"><?php the_title(); ?></span>
				<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</footer>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
