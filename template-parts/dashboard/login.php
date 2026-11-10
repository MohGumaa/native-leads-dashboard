<?php
/**
 * Sign-in screen shown to visitors on the protected dashboard page.
 *
 * @package native-leads
 */

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$native_leads_failed    = isset( $_GET['login'] ) && 'failed' === $_GET['login'];
$native_leads_dashboard = native_leads_dashboard_page_url();
?>
<main class="auth">
	<div class="auth__grid" aria-hidden="true"></div>
	<div class="auth__glow" aria-hidden="true"></div>

	<section class="auth__card">
		<?php get_template_part( 'template-parts/dashboard/brand' ); ?>

		<h1><?php esc_html_e( 'Welcome back', 'native-leads' ); ?></h1>
		<p class="auth__lead"><?php esc_html_e( 'Sign in to see the leads from your landing pages.', 'native-leads' ); ?></p>

		<?php if ( $native_leads_failed ) : ?>
			<p class="notice notice--error" role="alert"><?php esc_html_e( 'That username or password is not right. Try again.', 'native-leads' ); ?></p>
		<?php endif; ?>

		<form class="auth__form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
			<label class="field">
				<span><?php esc_html_e( 'Username or email', 'native-leads' ); ?></span>
				<input type="text" name="log" autocomplete="username" required autofocus>
			</label>
			<label class="field">
				<span><?php esc_html_e( 'Password', 'native-leads' ); ?></span>
				<input type="password" name="pwd" autocomplete="current-password" required>
			</label>
			<div class="auth__row">
				<label class="check">
					<input type="checkbox" name="rememberme" value="forever">
					<span><?php esc_html_e( 'Keep me signed in', 'native-leads' ); ?></span>
				</label>
				<a href="<?php echo esc_url( wp_lostpassword_url( $native_leads_dashboard ) ); ?>"><?php esc_html_e( 'Forgot password?', 'native-leads' ); ?></a>
			</div>
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( $native_leads_dashboard ); ?>">
			<button type="submit" class="btn btn--primary btn--block"><?php esc_html_e( 'Sign in', 'native-leads' ); ?></button>
		</form>
	</section>

	<p class="auth__foot"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; <?php esc_html_e( 'Back to site', 'native-leads' ); ?></a> &nbsp;·&nbsp; &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
</main>
