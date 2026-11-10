<?php
/**
 * Front page: introduction to the leads dashboard.
 *
 * The dashboard itself lives on the page that uses the "Leads Dashboard"
 * template (page-templates/dashboard.php); every call to action here links
 * to it. If a static front page is set, its content is shown under the hero.
 *
 * @package native-leads
 */

get_header();

$native_leads_cta     = native_leads_dashboard_cta();
$native_leads_can_set = ! $native_leads_cta && current_user_can( 'edit_pages' );

// Illustrative numbers for the preview card only; not read from the leads table.
$native_leads_bars = array( 38, 52, 44, 61, 48, 70, 57, 66, 80, 63, 74, 92, 71, 86 );
?>

	<main id="primary" class="site-main">

		<section class="hero">
			<div class="hero__glow" aria-hidden="true"></div>
			<div class="container hero__inner">
				<div class="hero__copy">
					<p class="eyebrow"><?php echo native_leads_icon( 'pulse' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Leads hub for your ad landing pages', 'native-leads' ); ?></p>
					<h1 class="hero__title">
						<?php
						printf(
							/* translators: %s: highlighted words */
							esc_html__( 'Every sign-up from every ad, %s', 'native-leads' ),
							'<span class="text-accent">' . esc_html__( 'tracked in one place.', 'native-leads' ) . '</span>'
						);
						?>
					</h1>
					<p class="hero__lead">
						<?php esc_html_e( 'Native Leads reads every registration your landing pages save, with its UTM trail and CRM delivery status, and turns it into a live, searchable hub: see which ads work, catch leads the CRM missed, and follow up in one click.', 'native-leads' ); ?>
					</p>
					<div class="hero__actions">
						<?php if ( $native_leads_cta ) : ?>
							<a class="btn btn--primary btn--lg" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
								<?php echo esc_html( $native_leads_cta['label'] ); ?>
								<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php elseif ( $native_leads_can_set ) : ?>
							<a class="btn btn--primary btn--lg" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>">
								<?php esc_html_e( 'Set up the dashboard page', 'native-leads' ); ?>
								<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<a class="btn btn--ghost btn--lg" href="#features"><?php esc_html_e( 'See what’s inside', 'native-leads' ); ?></a>
					</div>
					<?php if ( $native_leads_can_set ) : ?>
						<p class="hero__hint">
							<?php esc_html_e( 'Create a page, choose the “Leads Dashboard” template in the Page settings, and publish. This button will then open it.', 'native-leads' ); ?>
						</p>
					<?php endif; ?>
					<ul class="hero__points">
						<li><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Private, sign-in only', 'native-leads' ); ?></li>
						<li><?php echo native_leads_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Excel-ready CSV', 'native-leads' ); ?></li>
						<li><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'CRM delivery tracking', 'native-leads' ); ?></li>
					</ul>
				</div>

				<div class="preview" aria-hidden="true">
					<div class="preview__bar">
						<span></span><span></span><span></span>
						<em><?php esc_html_e( 'Leads', 'native-leads' ); ?></em>
					</div>
					<div class="preview__body">
						<div class="preview__kpis">
							<div class="preview__kpi preview__kpi--hero"><small><?php esc_html_e( 'Leads in range', 'native-leads' ); ?></small><strong>1,284</strong><span class="trend trend--up"><span>▲</span> 18%</span></div>
							<div class="preview__kpi"><small><?php esc_html_e( 'Today', 'native-leads' ); ?></small><strong>46</strong><span class="trend trend--up"><span>▲</span> 9%</span></div>
							<div class="preview__kpi"><small><?php esc_html_e( 'Delivered to CRM', 'native-leads' ); ?></small><strong>97%</strong><span class="kpi__note">4 <?php esc_html_e( 'pending', 'native-leads' ); ?></span></div>
						</div>
						<div class="preview__chart">
							<small><?php esc_html_e( 'Leads per day', 'native-leads' ); ?></small>
							<div class="preview__bars">
								<?php foreach ( $native_leads_bars as $native_leads_h ) : ?>
									<span style="height: <?php echo esc_attr( $native_leads_h ); ?>%"></span>
								<?php endforeach; ?>
							</div>
						</div>
						<ul class="preview__rows">
							<li><span class="avatar avatar--sm">SA</span><span><b>Sara Ahmed</b><i>global · snapchat</i></span><span class="badge badge--sent"><?php esc_html_e( 'Delivered', 'native-leads' ); ?></span></li>
							<li><span class="avatar avatar--sm">OK</span><span><b>Omar Khalid</b><i>prospect · tiktok</i></span><span class="badge badge--pending"><?php esc_html_e( 'Pending', 'native-leads' ); ?></span></li>
							<li><span class="avatar avatar--sm">LM</span><span><b>Lina Mansour</b><i>invest · google</i></span><span class="badge badge--sent"><?php esc_html_e( 'Delivered', 'native-leads' ); ?></span></li>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section id="features" class="section">
			<div class="container">
				<header class="section__head">
					<p class="eyebrow"><?php esc_html_e( 'What’s inside', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'Everything you need to work your leads', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'One screen built around the questions a sales team asks every morning.', 'native-leads' ); ?></p>
				</header>

				<div class="features">
					<?php
					$native_leads_features = array(
						array( 'pulse', __( 'Live headline numbers', 'native-leads' ), __( 'Leads in range, today vs yesterday, this week vs last week, on the database’s own clock.', 'native-leads' ) ),
						array( 'send', __( 'CRM delivery', 'native-leads' ), __( 'See how many leads reached the affiliate CRM, and jump straight to the ones still pending or failed.', 'native-leads' ) ),
						array( 'megaphone', __( 'Ads that work', 'native-leads' ), __( 'Leads by landing page, traffic source, campaign and country. Click any row to filter everything to it.', 'native-leads' ) ),
						array( 'filter', __( 'Search & filters', 'native-leads' ), __( 'Find anyone by name, email, phone or LD- reference. Narrow by date, page, traffic, country or CRM status.', 'native-leads' ) ),
						array( 'phone', __( 'One-click follow-up', 'native-leads' ), __( 'Open any lead to call, WhatsApp or email. Their preferred contact method is highlighted, with the full UTM trail beside it.', 'native-leads' ) ),
						array( 'download', __( 'Excel-ready export', 'native-leads' ), __( 'Download exactly what you filtered as CSV. Arabic names open correctly in Excel.', 'native-leads' ) ),
					);
					foreach ( $native_leads_features as $native_leads_feature ) :
						?>
						<article class="feature">
							<span class="feature__icon"><?php echo native_leads_icon( $native_leads_feature[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php echo esc_html( $native_leads_feature[1] ); ?></h3>
							<p class="muted"><?php echo esc_html( $native_leads_feature[2] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section id="how-it-works" class="section section--alt">
			<div class="container">
				<header class="section__head">
					<p class="eyebrow"><?php esc_html_e( 'How it works', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'From form submit to follow-up', 'native-leads' ); ?></h2>
				</header>

				<ol class="steps">
					<li class="step">
						<span class="step__num">1</span>
						<h3><?php esc_html_e( 'A visitor registers', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'A visitor from an ad fills in the form. The page saves the lead, its UTM tags and spcid to the wpcq_leads table.', 'native-leads' ); ?></p>
					</li>
					<li class="step">
						<span class="step__num">2</span>
						<h3><?php esc_html_e( 'It is sent to the CRM', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'The lead is created in the affiliate CRM. Failed sends are retried by cron, and every attempt is recorded on the row.', 'native-leads' ); ?></p>
					</li>
					<li class="step">
						<span class="step__num">3</span>
						<h3><?php esc_html_e( 'Your team follows up', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'The lead shows up here as soon as you refresh. Call or message from its card, and export the list when you need it.', 'native-leads' ); ?></p>
					</li>
				</ol>

				<div class="pipeline">
					<p class="pipeline__label"><?php esc_html_e( 'Every lead carries its CRM status', 'native-leads' ); ?></p>
					<div class="pipeline__flow">
						<span class="badge badge--pending"><?php esc_html_e( 'Pending', 'native-leads' ); ?></span>
						<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="badge badge--sent"><?php esc_html_e( 'Delivered', 'native-leads' ); ?></span>
						<span class="pipeline__or"><?php esc_html_e( 'or', 'native-leads' ); ?></span>
						<span class="badge badge--failed"><?php esc_html_e( 'Failed', 'native-leads' ); ?></span>
						<span class="pipeline__or"><?php esc_html_e( '· retried by cron ·', 'native-leads' ); ?></span>
						<span class="badge"><?php esc_html_e( 'Not sent', 'native-leads' ); ?></span>
					</div>
				</div>
			</div>
		</section>

		<section id="security" class="section">
			<div class="container split">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Private by default', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'Your leads stay behind a sign-in', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'The dashboard page asks for a WordPress sign-in. It is never indexed by search engines and never cached.', 'native-leads' ); ?></p>
				</div>
				<ul class="checklist">
					<li><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Administrators', 'native-leads' ); ?></strong> <?php esc_html_e( 'always have access.', 'native-leads' ); ?></span></li>
					<li><?php echo native_leads_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Team members', 'native-leads' ); ?></strong> <?php esc_html_e( 'get in once you give their role the “view leads” permission.', 'native-leads' ); ?></span></li>
					<li><?php echo native_leads_icon( 'database' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Your database', 'native-leads' ); ?></strong> <?php esc_html_e( 'is the only place the leads live. No third-party service.', 'native-leads' ); ?></span></li>
				</ul>
			</div>
		</section>

		<?php
		// Content of the static front page, if one is set and has content.
		if ( 'page' === get_option( 'show_on_front' ) ) :
			while ( have_posts() ) :
				the_post();
				if ( '' !== trim( get_the_content() ) ) :
					?>
					<section class="section section--alt">
						<div class="container container--narrow prose">
							<?php the_content(); ?>
						</div>
					</section>
					<?php
				endif;
			endwhile;
		endif;
		?>

		<?php if ( $native_leads_cta ) : ?>
			<section class="section">
				<div class="container">
					<div class="cta-band">
						<div>
							<h2><?php esc_html_e( 'Ready to see today’s leads?', 'native-leads' ); ?></h2>
							<p><?php esc_html_e( 'Sign in with your WordPress account to open the dashboard.', 'native-leads' ); ?></p>
						</div>
						<a class="btn btn--dark btn--lg" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
							<?php echo esc_html( $native_leads_cta['label'] ); ?>
							<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
				</div>
			</section>
		<?php endif; ?>

	</main><!-- #main -->

<?php
get_footer();
