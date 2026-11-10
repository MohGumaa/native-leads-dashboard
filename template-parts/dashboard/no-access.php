<?php
/**
 * Shown to signed-in users who lack the `native_leads_view_leads` capability.
 *
 * @package native-leads
 */

?>
<main class="auth">
	<?php get_template_part( 'template-parts/dashboard/auth-aside' ); ?>

	<div class="auth__main">

	<section class="auth__card">
		<?php get_template_part( 'template-parts/dashboard/brand' ); ?>

		<h1><?php esc_html_e( 'No access yet', 'native-leads' ); ?></h1>
		<p class="auth__lead">
			<?php
			printf(
				/* translators: %s: user display name */
				esc_html__( 'You are signed in as %s, but this account cannot view leads. Ask an administrator to give you access.', 'native-leads' ),
				'<strong>' . esc_html( wp_get_current_user()->display_name ) . '</strong>'
			);
			?>
		</p>
		<a class="btn btn--ghost btn--block" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'native-leads' ); ?></a>
	</section>
	</div>
</main>
