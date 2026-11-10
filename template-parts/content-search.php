<?php
/**
 * Template part: one search result row.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

$native_leads_type = get_post_type_object( get_post_type() );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'result' ); ?>>
	<a class="result__link" href="<?php the_permalink(); ?>">
		<span class="result__icon"><?php echo native_leads_icon( 'page' === get_post_type() ? 'doc' : 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="result__body">
			<span class="result__meta faint">
				<?php echo esc_html( $native_leads_type ? $native_leads_type->labels->singular_name : '' ); ?>
				<?php if ( 'post' === get_post_type() ) : ?>
					· <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php endif; ?>
			</span>
			<?php the_title( '<h2 class="result__title">', '</h2>' ); ?>
			<span class="result__excerpt muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></span>
		</span>
		<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
</article><!-- #post-<?php the_ID(); ?> -->
