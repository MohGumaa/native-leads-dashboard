<?php
/**
 * Template part: empty state when a list has no posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

?>

<section class="empty-state">
	<span class="empty-state__icon"><?php echo native_leads_icon( is_search() ? 'search' : 'doc' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

		<h2><?php esc_html_e( 'No posts yet', 'native-leads' ); ?></h2>
		<p class="muted"><?php esc_html_e( 'Ready to publish your first post?', 'native-leads' ); ?></p>
		<a class="btn btn--primary" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Write a post', 'native-leads' ); ?></a>

	<?php elseif ( is_search() ) : ?>

		<h2><?php esc_html_e( 'Nothing matched your search', 'native-leads' ); ?></h2>
		<p class="muted"><?php esc_html_e( 'Try different or fewer keywords.', 'native-leads' ); ?></p>

	<?php else : ?>

		<h2><?php esc_html_e( 'Nothing here yet', 'native-leads' ); ?></h2>
		<p class="muted"><?php esc_html_e( 'We couldn’t find anything at this address. A search might help.', 'native-leads' ); ?></p>
		<?php get_search_form(); ?>

	<?php endif; ?>
</section>
