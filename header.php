<?php
/**
 * The header for the public site (everything except the dashboard page).
 *
 * Displays the <head> section and the sticky top bar: brand, primary menu,
 * theme switch and the dashboard button.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package native-leads
 */

$native_leads_cta = native_leads_dashboard_cta();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class( 'native-leads-app native-leads-site' ); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'native-leads' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-header__inner container">
			<a class="site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				if ( has_custom_logo() ) {
					echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'site-logo' ) );
					echo '<span class="screen-reader-text">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
				} else {
					get_template_part( 'template-parts/dashboard/brand' );
				}
				?>
			</a>

			<nav id="site-navigation" class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'native-leads' ); ?>" data-nav>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'site-nav__menu',
						'container'      => false,
						'fallback_cb'    => 'native_leads_menu_fallback',
						'depth'          => 2,
					)
				);
				?>
			</nav>

			<div class="site-header__actions">
				<button type="button" class="icon-btn" data-theme-toggle aria-label="<?php esc_attr_e( 'Switch light / dark theme', 'native-leads' ); ?>">
					<?php echo native_leads_icon( 'sun' ) . native_leads_icon( 'moon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<?php if ( $native_leads_cta ) : ?>
					<a class="btn btn--primary btn--sm site-header__cta" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>"><?php echo esc_html( $native_leads_cta['label'] ); ?></a>
				<?php endif; ?>
				<button type="button" class="icon-btn site-header__toggle" aria-controls="site-navigation" aria-expanded="false" data-nav-toggle>
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'native-leads' ); ?></span>
					<?php echo native_leads_icon( 'menu' ) . native_leads_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
	</header><!-- #masthead -->
