<?php
/**
 * Front page: introduction to the Native Leads hub.
 *
 * The dashboard itself lives on the page that uses the "Leads Dashboard"
 * template (page-templates/dashboard.php); every call to action here links
 * to it. Numbers in the preview are illustrative and never read from the
 * leads table, so nothing private shows on the public site.
 *
 * @package native-leads
 */

get_header();

$native_leads_cta     = native_leads_dashboard_cta();
$native_leads_can_set = ! $native_leads_cta && current_user_can( 'edit_pages' );
$native_leads_contact = native_leads_page( 'contact-us' );
$native_leads_about   = native_leads_page( 'about-us' );
?>

	<main id="primary" class="site-main home">

		<!-- ========== Hero ========== -->
		<section class="hero">
			<div class="hero__bg" aria-hidden="true"></div>
			<div class="container hero__inner">
				<div class="hero__copy">
					<p class="hero__badge">
						<span><?php esc_html_e( 'New', 'native-leads' ); ?></span>
						<?php esc_html_e( 'CRM delivery tracking on every lead', 'native-leads' ); ?>
					</p>
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
						<?php esc_html_e( 'Native Leads reads every registration your landing pages save, with its UTM trail and CRM status, and turns it into a calm, searchable hub. See which ads work, catch leads the CRM missed, and follow up in one click.', 'native-leads' ); ?>
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
						<p class="hero__hint"><?php esc_html_e( 'Create a page, choose the “Leads Dashboard” template in the Page settings, and publish. This button will then open it.', 'native-leads' ); ?></p>
					<?php endif; ?>
					<ul class="hero__points">
						<li><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Private, sign-in only', 'native-leads' ); ?></li>
						<li><?php echo native_leads_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Excel-ready CSV', 'native-leads' ); ?></li>
						<li><?php echo native_leads_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Arabic-ready', 'native-leads' ); ?></li>
					</ul>
				</div>

				<!-- Illustrative preview of the dashboard. -->
				<div class="mock" aria-hidden="true">
					<div class="mock__side">
						<span class="mock__logo"><?php echo native_leads_logo_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="mock__nav is-on"></span>
						<span class="mock__nav"></span>
						<span class="mock__nav"></span>
						<span class="mock__sep"></span>
						<span class="mock__nav mock__nav--sm"></span>
						<span class="mock__nav mock__nav--sm"></span>
						<span class="mock__nav mock__nav--sm"></span>
					</div>
					<div class="mock__main">
						<div class="mock__top">
							<strong><?php esc_html_e( 'Leads overview', 'native-leads' ); ?></strong>
							<span class="mock__chips"><i class="is-on"><?php esc_html_e( '7 days', 'native-leads' ); ?></i><i><?php esc_html_e( '30 days', 'native-leads' ); ?></i></span>
						</div>
						<div class="mock__kpis">
							<div class="mock__kpi mock__kpi--hero">
								<small><?php esc_html_e( 'Leads in range', 'native-leads' ); ?></small>
								<strong>1,284</strong>
								<svg viewBox="0 0 120 30" preserveAspectRatio="none"><path d="M0 26 L15 22 L30 24 L45 16 L60 18 L75 11 L90 13 L105 6 L120 3" /></svg>
							</div>
							<div class="mock__kpi">
								<small><?php esc_html_e( 'Today', 'native-leads' ); ?></small>
								<strong>46</strong>
								<span class="trend trend--up"><span>▲</span> 9%</span>
							</div>
							<div class="mock__kpi">
								<small><?php esc_html_e( 'Delivered', 'native-leads' ); ?></small>
								<strong>98%</strong>
								<span class="mock__meter"><span></span></span>
							</div>
						</div>
						<div class="mock__chart">
							<small><?php esc_html_e( 'Leads per day', 'native-leads' ); ?></small>
							<svg viewBox="0 0 300 90" preserveAspectRatio="none">
								<defs>
									<linearGradient id="mock-area" x1="0" y1="0" x2="0" y2="1">
										<stop offset="0" stop-color="currentColor" stop-opacity="0.28" />
										<stop offset="1" stop-color="currentColor" stop-opacity="0" />
									</linearGradient>
								</defs>
								<path class="mock__area" d="M0 70 L25 62 L50 66 L75 48 L100 54 L125 40 L150 44 L175 30 L200 36 L225 22 L250 28 L275 14 L300 10 L300 90 L0 90 Z" />
								<path class="mock__line" d="M0 70 L25 62 L50 66 L75 48 L100 54 L125 40 L150 44 L175 30 L200 36 L225 22 L250 28 L275 14 L300 10" />
							</svg>
						</div>
						<ul class="mock__rows">
							<li><span class="avatar avatar--sm">SA</span><span><b>Sara Ahmed</b><i>stocks-offer · snapchat</i></span><span class="badge badge--sent"><?php esc_html_e( 'Delivered', 'native-leads' ); ?></span></li>
							<li><span class="avatar avatar--sm">OK</span><span><b>Omar Khalid</b><i>funds-offer · tiktok</i></span><span class="badge badge--pending"><?php esc_html_e( 'Pending', 'native-leads' ); ?></span></li>
						</ul>
					</div>
					<div class="mock__float">
						<span class="mock__float-icon"><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span><b><?php esc_html_e( 'Lead delivered', 'native-leads' ); ?></b><i><?php esc_html_e( 'CRM accepted LD-7K3QX9AB', 'native-leads' ); ?></i></span>
					</div>
				</div>
			</div>
		</section>

		<!-- ========== Value strip ========== -->
		<section class="strip" aria-label="<?php esc_attr_e( 'At a glance', 'native-leads' ); ?>">
			<div class="container strip__inner">
				<div class="strip__item"><strong>1</strong><span><?php esc_html_e( 'private hub for every page', 'native-leads' ); ?></span></div>
				<div class="strip__item"><strong>7</strong><span><?php esc_html_e( 'tracking fields on every lead', 'native-leads' ); ?></span></div>
				<div class="strip__item"><strong>4</strong><span><?php esc_html_e( 'CRM delivery states', 'native-leads' ); ?></span></div>
				<div class="strip__item"><strong>0</strong><span><?php esc_html_e( 'third-party data services', 'native-leads' ); ?></span></div>
			</div>
		</section>

		<!-- ========== Features (bento) ========== -->
		<section id="features" class="section">
			<div class="container">
				<header class="section__head">
					<p class="eyebrow"><?php esc_html_e( 'What’s inside', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'Everything your team needs to work its leads', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'One screen built around the questions a sales team asks every morning.', 'native-leads' ); ?></p>
				</header>

				<div class="bento">
					<article class="bento__card bento__card--wide">
						<div class="bento__text">
							<span class="feature__icon"><?php echo native_leads_icon( 'pulse' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php esc_html_e( 'Live numbers, on the right clock', 'native-leads' ); ?></h3>
							<p class="muted"><?php esc_html_e( 'Leads in range, today vs yesterday and this week vs last, worked out on the database’s own clock so “today” is always today.', 'native-leads' ); ?></p>
						</div>
						<div class="bento__art bento__art--chart" aria-hidden="true">
							<svg viewBox="0 0 300 120" preserveAspectRatio="none">
								<path class="mock__area" d="M0 96 L30 88 L60 92 L90 70 L120 76 L150 58 L180 62 L210 40 L240 46 L270 24 L300 18 L300 120 L0 120 Z" />
								<path class="mock__line" d="M0 96 L30 88 L60 92 L90 70 L120 76 L150 58 L180 62 L210 40 L240 46 L270 24 L300 18" />
							</svg>
						</div>
					</article>

					<article class="bento__card">
						<span class="feature__icon"><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php esc_html_e( 'CRM delivery', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'See how many leads reached the affiliate CRM, and jump to the ones still pending or failed.', 'native-leads' ); ?></p>
						<div class="bento__ring" aria-hidden="true"><strong>98%</strong></div>
					</article>

					<article class="bento__card">
						<span class="feature__icon"><?php echo native_leads_icon( 'megaphone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php esc_html_e( 'Ads that work', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Leads by landing page, traffic source, campaign and country. Click a row to filter everything.', 'native-leads' ); ?></p>
						<ul class="bento__rank" aria-hidden="true">
							<li style="--w: 82%"><span>Snapchat</span><b>62%</b></li>
							<li style="--w: 48%"><span>TikTok</span><b>27%</b></li>
							<li style="--w: 22%"><span>Google</span><b>11%</b></li>
						</ul>
					</article>

					<article class="bento__card">
						<span class="feature__icon"><?php echo native_leads_icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php esc_html_e( 'Search & filters', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Find anyone by name, email, phone or LD- reference. Narrow by date, page, traffic, country or CRM status.', 'native-leads' ); ?></p>
					</article>

					<article class="bento__card">
						<span class="feature__icon"><?php echo native_leads_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php esc_html_e( 'One-click follow-up', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Call, WhatsApp or email from the lead’s card. Their preferred contact method is highlighted.', 'native-leads' ); ?></p>
					</article>
				</div>
			</div>
		</section>

		<!-- ========== How it works ========== -->
		<section id="how-it-works" class="section section--alt">
			<div class="container">
				<header class="section__head">
					<p class="eyebrow"><?php esc_html_e( 'How it works', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'From ad click to follow-up', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'Nothing to sync and no extra service in the middle.', 'native-leads' ); ?></p>
				</header>

				<ol class="timeline">
					<?php
					$native_leads_steps = array(
						array( 'megaphone', __( 'A visitor clicks an ad', 'native-leads' ), __( 'The ad link carries utm_* tags and a partner click ID (spcid).', 'native-leads' ) ),
						array( 'doc', __( 'They fill in the form', 'native-leads' ), __( 'The landing page checks every field and saves the lead with its tracking to your database.', 'native-leads' ) ),
						array( 'send', __( 'It goes to the CRM', 'native-leads' ), __( 'The lead is created in the affiliate CRM. Failed sends are retried, and every attempt is recorded.', 'native-leads' ) ),
						array( 'users', __( 'Your team follows up', 'native-leads' ), __( 'It shows up in the hub as soon as you refresh. Call or message from its card.', 'native-leads' ) ),
					);
					foreach ( $native_leads_steps as $native_leads_i => $native_leads_step ) :
						?>
						<li class="timeline__step">
							<span class="timeline__icon"><?php echo native_leads_icon( $native_leads_step[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="timeline__num"><?php echo esc_html( sprintf( '%02d', $native_leads_i + 1 ) ); ?></span>
							<h3><?php echo esc_html( $native_leads_step[1] ); ?></h3>
							<p class="muted"><?php echo esc_html( $native_leads_step[2] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>

				<div class="pipeline">
					<p class="pipeline__label"><?php esc_html_e( 'Every lead carries its CRM status', 'native-leads' ); ?></p>
					<div class="pipeline__flow">
						<span class="badge badge--pending"><?php esc_html_e( 'Pending', 'native-leads' ); ?></span>
						<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="badge badge--sent"><?php esc_html_e( 'Delivered', 'native-leads' ); ?></span>
						<span class="pipeline__or"><?php esc_html_e( 'or', 'native-leads' ); ?></span>
						<span class="badge badge--failed"><?php esc_html_e( 'Failed', 'native-leads' ); ?></span>
						<span class="pipeline__or"><?php esc_html_e( '· retried ·', 'native-leads' ); ?></span>
						<span class="badge"><?php esc_html_e( 'Not sent', 'native-leads' ); ?></span>
					</div>
				</div>
			</div>
		</section>

		<!-- ========== Who it's for ========== -->
		<section id="teams" class="section">
			<div class="container">
				<header class="section__head">
					<p class="eyebrow"><?php esc_html_e( 'Who it’s for', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'One hub, every team that touches a lead', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'From the person buying the ad to the person making the call, everyone works from the same numbers.', 'native-leads' ); ?></p>
				</header>

				<div class="roles">
					<?php
					$native_leads_roles = array(
						array( 'megaphone', __( 'Media buyers', 'native-leads' ), __( 'See which source, campaign and creative brings sign-ups, and move budget to what works.', 'native-leads' ) ),
						array( 'phone', __( 'Sales teams', 'native-leads' ), __( 'Fresh leads with their preferred contact method, ready to call or message in one click.', 'native-leads' ) ),
						array( 'chart', __( 'Managers', 'native-leads' ), __( 'Daily numbers, week-on-week trends and a clean CSV for the weekly report.', 'native-leads' ) ),
						array( 'send', __( 'Operations', 'native-leads' ), __( 'Spot leads the CRM missed, read its answer, and know every lead was delivered.', 'native-leads' ) ),
					);
					foreach ( $native_leads_roles as $native_leads_role ) :
						?>
						<article class="role">
							<span class="role__icon"><?php echo native_leads_icon( $native_leads_role[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php echo esc_html( $native_leads_role[1] ); ?></h3>
							<p class="muted"><?php echo esc_html( $native_leads_role[2] ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ========== Security ========== -->
		<section id="security" class="section section--alt">
			<div class="container split">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Private by default', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'Your leads stay behind a sign-in', 'native-leads' ); ?></h2>
					<p class="muted"><?php esc_html_e( 'The hub asks for a WordPress sign-in. It is never indexed by search engines and never cached, and nothing on this public page reads your leads.', 'native-leads' ); ?></p>
				</div>
				<ul class="checklist">
					<li><?php echo native_leads_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Administrators', 'native-leads' ); ?></strong> <?php esc_html_e( 'always have access.', 'native-leads' ); ?></span></li>
					<li><?php echo native_leads_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Team members', 'native-leads' ); ?></strong> <?php esc_html_e( 'get in once their role has the “view leads” permission.', 'native-leads' ); ?></span></li>
					<li><?php echo native_leads_icon( 'database' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Your database', 'native-leads' ); ?></strong> <?php esc_html_e( 'is the only place the leads live.', 'native-leads' ); ?></span></li>
				</ul>
			</div>
		</section>

		<!-- ========== FAQ ========== -->
		<section id="faq" class="section">
			<div class="container faq">
				<header class="faq__head">
					<p class="eyebrow"><?php esc_html_e( 'FAQ', 'native-leads' ); ?></p>
					<h2><?php esc_html_e( 'Questions, answered', 'native-leads' ); ?></h2>
					<p class="muted">
						<?php esc_html_e( 'Something else on your mind?', 'native-leads' ); ?>
						<?php if ( $native_leads_contact ) : ?>
							<a class="link-arrow" href="<?php echo esc_url( get_permalink( $native_leads_contact ) ); ?>"><?php esc_html_e( 'Contact us', 'native-leads' ); ?> <?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php endif; ?>
					</p>
				</header>
				<div class="faq__list">
					<?php
					$native_leads_faq = array(
						array( __( 'Where do the leads come from?', 'native-leads' ), __( 'From our landing pages. Each sign-up is saved to this site’s own database, with the page it came from and its campaign tags.', 'native-leads' ) ),
						array( __( 'Who can see the leads?', 'native-leads' ), __( 'Only signed-in administrators, and team members whose role has the “view leads” permission. The page is never indexed or cached.', 'native-leads' ) ),
						array( __( 'What does “Pending” or “Failed” mean?', 'native-leads' ), __( 'It is the lead’s delivery status in the affiliate CRM. Pending leads are waiting to be sent; failed ones are retried automatically, and the CRM’s answer is shown on the lead.', 'native-leads' ) ),
						array( __( 'Can I export the leads?', 'native-leads' ), __( 'Yes. Export exactly what you have filtered as a CSV file. Arabic names open correctly in Excel.', 'native-leads' ) ),
						array( __( 'Does it work on a phone?', 'native-leads' ), __( 'Yes. The hub adapts to phones and tablets: the menu slides in and each lead becomes a card.', 'native-leads' ) ),
					);
					foreach ( $native_leads_faq as $native_leads_q ) :
						?>
						<details class="faq__item">
							<summary><?php echo esc_html( $native_leads_q[0] ); ?><span class="faq__toggle" aria-hidden="true"></span></summary>
							<p class="muted"><?php echo esc_html( $native_leads_q[1] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
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

		<!-- ========== CTA ========== -->
		<section class="section section--cta">
			<div class="container">
				<div class="cta-band">
					<div>
						<h2><?php esc_html_e( 'Ready to see today’s leads?', 'native-leads' ); ?></h2>
						<p><?php esc_html_e( 'Sign in with your WordPress account to open the hub.', 'native-leads' ); ?></p>
					</div>
					<div class="cta-band__actions">
						<?php if ( $native_leads_cta ) : ?>
							<a class="btn btn--dark btn--lg" href="<?php echo esc_url( $native_leads_cta['url'] ); ?>">
								<?php echo esc_html( $native_leads_cta['label'] ); ?>
								<?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
						<?php if ( $native_leads_about ) : ?>
							<a class="btn btn--outline-light btn--lg" href="<?php echo esc_url( get_permalink( $native_leads_about ) ); ?>"><?php esc_html_e( 'About us', 'native-leads' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>

	</main><!-- #main -->

<?php
get_footer();
