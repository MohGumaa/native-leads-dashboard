<?php
/**
 * Search form, used by get_search_form().
 *
 * @package native-leads
 */

$native_leads_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $native_leads_id ); ?>"><?php esc_html_e( 'Search for:', 'native-leads' ); ?></label>
	<?php echo native_leads_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<input type="search" id="<?php echo esc_attr( $native_leads_id ); ?>" class="search-form__input" placeholder="<?php esc_attr_e( 'Search articles and pages…', 'native-leads' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="btn btn--primary btn--sm"><?php esc_html_e( 'Search', 'native-leads' ); ?></button>
</form>
