<?php
/**
 * The leads dashboard: sidebar, KPIs, daily chart, CRM delivery,
 * breakdowns, filters and the leads table.
 *
 * @package native-leads
 */

$nl_user = wp_get_current_user();

if ( ! native_leads_table_exists() ) :
	?>
	<main class="auth">
		<section class="auth__card">
			<?php get_template_part( 'template-parts/dashboard/brand' ); ?>
			<h1><?php esc_html_e( 'Leads table not found', 'native-leads' ); ?></h1>
			<p class="auth__lead">
				<?php
				printf(
					/* translators: %s: database table name */
					esc_html__( 'The dashboard reads leads from the %s table, which does not exist in this database yet. Run sql/schema.sql from the landing pages repo, or submit one lead, then reload.', 'native-leads' ),
					'<code>' . esc_html( native_leads_table() ) . '</code>'
				);
				?>
			</p>
		</section>
	</main>
	<?php
	return;
endif;

$f          = native_leads_get_filters();
$result     = native_leads_query( $f );
$stats      = native_leads_stats( $f );
$daily      = native_leads_daily_counts( $f );
$by_source  = native_leads_breakdown( $f, 'source', 20 );
$by_channel = native_leads_breakdown( $f, 'channel', 6 );
$by_camp    = native_leads_breakdown( $f, 'campaign', 6 );
$by_country = native_leads_breakdown( $f, 'country', 6 );
$by_crm     = native_leads_breakdown( $f, 'crm', 10 );
$ranges     = native_leads_date_ranges();
$crm_states = native_leads_crm_statuses();
$now        = native_leads_now();

$total_pages = max( 1, (int) ceil( $result['total'] / NATIVE_LEADS_PER_PAGE ) );
$first_row   = $result['total'] ? ( $f['pg'] - 1 ) * NATIVE_LEADS_PER_PAGE + 1 : 0;
$last_row    = min( $result['total'], $f['pg'] * NATIVE_LEADS_PER_PAGE );

$range_label = $ranges[ $f['range'] ];
if ( 'all' === $f['range'] ) {
	$range_label = __( 'All time', 'native-leads' );
} elseif ( 'custom' === $f['range'] && ( $f['from'] || $f['to'] ) ) {
	$range_label = trim( ( $f['from'] ? mysql2date( 'M j, Y', $f['from'] ) : '…' ) . ' – ' . ( $f['to'] ? mysql2date( 'M j, Y', $f['to'] ) : __( 'today', 'native-leads' ) ) );
} elseif ( in_array( $f['range'], array( '7d', '30d' ), true ) ) {
	/* translators: %s: "7 days" or "30 days" */
	$range_label = sprintf( __( 'Last %s', 'native-leads' ), $range_label );
}

$filter_keys = array( 'q', 'source', 'channel', 'campaign', 'country', 'crm' );
$has_filters = 'all' !== $f['range'];
foreach ( $filter_keys as $key ) {
	$has_filters = $has_filters || '' !== $f[ $key ];
}
$export_url = wp_nonce_url( add_query_arg( 'export', 'csv', native_leads_dashboard_url( $f, array( 'pg' => null ) ) ), 'native_leads_export' );

$hour     = (int) $now->format( 'G' );
$greeting = $hour < 12 ? __( 'Good morning', 'native-leads' ) : ( $hour < 18 ? __( 'Good afternoon', 'native-leads' ) : __( 'Good evening', 'native-leads' ) );

/**
 * Percentage change, or null when there is nothing to compare against.
 */
$delta = static function ( $now_value, $before ) {
	return $before > 0 ? (int) round( ( $now_value - $before ) / $before * 100 ) : null;
};

/**
 * Up/down pill for a KPI card.
 */
$trend = static function ( $pct, $label ) {
	if ( null === $pct ) {
		return '<span class="kpi__note">' . esc_html( $label ) . '</span>';
	}
	$dir = $pct > 0 ? 'up' : ( $pct < 0 ? 'down' : 'flat' );
	$sym = 'up' === $dir ? '▲' : ( 'down' === $dir ? '▼' : '•' );
	return sprintf(
		'<span class="trend trend--%1$s"><span aria-hidden="true">%2$s</span> %3$s%%</span> <span class="kpi__note">%4$s</span>',
		esc_attr( $dir ),
		$sym,
		esc_html( abs( $pct ) ),
		esc_html( $label )
	);
};

/**
 * Sortable column header link.
 */
$sort_link = static function ( $key, $label ) use ( $f ) {
	$active = $f['sort'] === $key;
	$dir    = $active && 'desc' === $f['dir'] ? 'asc' : 'desc';
	printf(
		'<a class="th-sort%1$s" href="%2$s#leads" aria-sort="%3$s">%4$s<svg viewBox="0 0 10 14" aria-hidden="true"><path d="M5 1 1.5 5h7zM5 13 1.5 9h7z"/></svg></a>',
		$active ? ' is-' . esc_attr( $f['dir'] ) : '',
		esc_url( native_leads_dashboard_url( $f, array( 'sort' => $key, 'dir' => $dir, 'pg' => 1 ) ) ),
		$active ? ( 'asc' === $f['dir'] ? 'ascending' : 'descending' ) : 'none',
		esc_html( $label )
	);
};

/**
 * Two-letter initials for the avatar.
 */
$initials = static function ( $first, $last = '' ) {
	$out  = mb_substr( trim( (string) $first ), 0, 1 );
	$next = mb_substr( trim( (string) $last ), 0, 1 );
	// Arabic letters join, and mixing scripts reads oddly: use one letter then.
	$arabic = '/\p{Arabic}/u';
	if ( '' !== $next && $last !== $first && ! preg_match( $arabic, $out ) && ! preg_match( $arabic, $next ) ) {
		$out .= $next;
	}
	return mb_strtoupper( $out );
};

/**
 * One breakdown card (traffic sources, campaigns, countries).
 *
 * @param string   $title   Card title.
 * @param array    $items   value => count.
 * @param string   $key     Filter key the rows toggle.
 * @param callable $label   value => label HTML (escaped).
 * @param string   $icon    Icon name for the header.
 * @param string   $empty   Text when there are no rows.
 */
$breakdown = static function ( $title, $items, $key, $label, $icon, $empty ) use ( $f, $range_label ) {
	$sum = max( 1, array_sum( $items ) );
	?>
	<article class="card breakdown">
		<header class="card__head">
			<span class="card__icon"><?php echo native_leads_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div>
				<h2><?php echo esc_html( $title ); ?></h2>
				<p class="muted"><?php echo esc_html( $range_label ); ?></p>
			</div>
		</header>
		<?php if ( $items ) : ?>
			<ul class="rank">
				<?php
				$i = 0;
				foreach ( $items as $value => $count ) :
					$value  = (string) $value;
					$active = $f[ $key ] === $value;
					$pct    = round( $count / $sum * 100 );
					++$i;
					?>
					<li>
						<a class="rank__row<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( $key => $active ? '' : $value, 'pg' => 1 ) ) ); ?>" style="--pct: <?php echo esc_attr( round( $count / $sum * 100, 1 ) ); ?>%">
							<span class="rank__pos"><?php echo esc_html( $i ); ?></span>
							<span class="rank__name"><?php echo $label( $value ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the callback. ?></span>
							<span class="rank__count"><?php echo esc_html( number_format_i18n( $count ) ); ?><small><?php echo esc_html( $pct ); ?>%</small></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="breakdown__empty muted"><?php echo esc_html( $empty ); ?></p>
		<?php endif; ?>
	</article>
	<?php
};

/* ---------- chart geometry ---------- */

$daily_max = max( $daily );
$intervals = min( 4, max( 1, $daily_max ) );
$raw_step  = max( 1, $daily_max / $intervals );
$magnitude = 10 ** floor( log10( $raw_step ) );
$step      = $magnitude;
foreach ( array( 1, 2, 5, 10 ) as $mult ) {
	if ( $mult * $magnitude >= $raw_step ) {
		$step = $mult * $magnitude;
		break;
	}
}
$chart_top = max( 1, $step * $intervals );
$daily_sum = array_sum( $daily );
$day_count = count( $daily );
$points    = array();
$i         = 0;
foreach ( $daily as $count ) {
	$points[] = round( ( $i + 0.5 ) / $day_count * 1000, 2 ) . ',' . round( 200 - $count / $chart_top * 200, 2 );
	++$i;
}
$line_path = 'M' . implode( ' L', $points );
$area_path = $line_path . ' L' . round( ( $day_count - 0.5 ) / $day_count * 1000, 2 ) . ',200 L' . round( 0.5 / $day_count * 1000, 2 ) . ',200 Z';

/* ---------- CRM delivery ---------- */

$crm_total = array_sum( $by_crm );
$crm_order = array_unique( array_merge( array_keys( $crm_states ), array_keys( $by_crm ) ) );
$crm_sent  = $by_crm['sent'] ?? 0;
$crm_rate  = $crm_total ? (int) round( $crm_sent / $crm_total * 100 ) : 0;
$crm_issue = ( $by_crm['failed'] ?? 0 ) + ( $by_crm['pending'] ?? 0 );

/* ---------- labels for the breakdown cards ---------- */

$label_channel = static function ( $v ) {
	return esc_html( native_leads_channel_label( $v ) );
};
$label_camp    = static function ( $v ) {
	return '<span class="mono">' . esc_html( $v ) . '</span>';
};
$label_country = static function ( $v ) {
	return '<span class="cc">' . esc_html( $v ) . '</span>' . esc_html( native_leads_country_name( $v ) );
};

$active_chips = array(
	'source'   => array( __( 'Landing page', 'native-leads' ), native_leads_source_label( $f['source'] ) ),
	'channel'  => array( __( 'Traffic', 'native-leads' ), native_leads_channel_label( $f['channel'] ) ),
	'campaign' => array( __( 'Campaign', 'native-leads' ), $f['campaign'] ),
	'country'  => array( __( 'Country', 'native-leads' ), native_leads_country_name( $f['country'] ) ),
	'crm'      => array( __( 'CRM', 'native-leads' ), native_leads_crm_label( $f['crm'] ) ),
	'q'        => array( __( 'Search', 'native-leads' ), '“' . $f['q'] . '”' ),
);
?>
<div class="shell">
	<aside class="sidebar" id="sidebar" data-sidebar>
		<div class="sidebar__top">
			<a class="sidebar__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php get_template_part( 'template-parts/dashboard/brand' ); ?></a>
			<button type="button" class="icon-btn sidebar__close" data-sidebar-close aria-label="<?php esc_attr_e( 'Close menu', 'native-leads' ); ?>"><?php echo native_leads_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>

		<nav class="sidenav" aria-label="<?php esc_attr_e( 'Dashboard', 'native-leads' ); ?>">
			<a class="sidenav__link is-current" href="#overview"><?php echo native_leads_icon( 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Overview', 'native-leads' ); ?></span></a>
			<a class="sidenav__link" href="#leads"><?php echo native_leads_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Leads', 'native-leads' ); ?></span><em><?php echo esc_html( number_format_i18n( $result['total'] ) ); ?></em></a>
			<a class="sidenav__link" href="<?php echo esc_url( $export_url ); ?>"><?php echo native_leads_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Export CSV', 'native-leads' ); ?></span></a>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<a class="sidenav__link" href="<?php echo esc_url( admin_url() ); ?>"><?php echo native_leads_icon( 'settings' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'WordPress admin', 'native-leads' ); ?></span></a>
			<?php endif; ?>
		</nav>

		<section class="sidepages" aria-labelledby="sidepages-title">
			<h2 id="sidepages-title" class="sidebar__label"><?php esc_html_e( 'Landing pages', 'native-leads' ); ?></h2>
			<ul>
				<li>
					<a class="sidepage<?php echo '' === $f['source'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( 'source' => '', 'pg' => 1 ) ) ); ?>">
						<span class="sidepage__dot sidepage__dot--all" aria-hidden="true"></span>
						<span class="sidepage__name"><?php esc_html_e( 'All pages', 'native-leads' ); ?></span>
						<span class="sidepage__count"><?php echo esc_html( number_format_i18n( array_sum( $by_source ) ) ); ?></span>
					</a>
				</li>
				<?php foreach ( $by_source as $source => $count ) : ?>
					<?php $active = $f['source'] === (string) $source; ?>
					<li>
						<a class="sidepage<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( 'source' => $active ? '' : (string) $source, 'pg' => 1 ) ) ); ?>">
							<span class="sidepage__dot" aria-hidden="true"></span>
							<span class="sidepage__name"><?php echo esc_html( native_leads_source_label( $source ) ); ?></span>
							<span class="sidepage__count"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! $by_source ) : ?>
				<p class="faint sidepages__empty"><?php esc_html_e( 'No leads in this range yet.', 'native-leads' ); ?></p>
			<?php endif; ?>
		</section>

		<div class="sidebar__foot">
			<div class="me">
				<span class="avatar avatar--sm" aria-hidden="true"><?php echo esc_html( $initials( $nl_user->first_name ? $nl_user->first_name : $nl_user->display_name, $nl_user->last_name ) ); ?></span>
				<span class="me__text">
					<strong><?php echo esc_html( $nl_user->display_name ); ?></strong>
					<small><?php echo esc_html( $nl_user->user_email ); ?></small>
				</span>
			</div>
			<div class="sidebar__tools">
				<button type="button" class="icon-btn" data-theme-toggle aria-label="<?php esc_attr_e( 'Switch light / dark theme', 'native-leads' ); ?>" title="<?php esc_attr_e( 'Light / dark', 'native-leads' ); ?>">
					<?php echo native_leads_icon( 'sun' ) . native_leads_icon( 'moon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<a class="icon-btn" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'Sign out', 'native-leads' ); ?>" title="<?php esc_attr_e( 'Sign out', 'native-leads' ); ?>">
					<?php echo native_leads_icon( 'logout' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>
	</aside>
	<div class="sidebar-scrim" data-sidebar-close hidden></div>

	<div class="main">
		<header class="mainbar">
			<button type="button" class="icon-btn mainbar__menu" data-sidebar-open aria-controls="sidebar" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'native-leads' ); ?>"><?php echo native_leads_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>

			<div class="mainbar__title">
				<p class="muted">
					<?php
					printf(
						/* translators: 1: greeting, 2: user first name */
						esc_html__( '%1$s, %2$s', 'native-leads' ),
						esc_html( $greeting ),
						esc_html( $nl_user->first_name ? $nl_user->first_name : $nl_user->display_name )
					);
					?>
				</p>
				<h1><?php esc_html_e( 'Leads overview', 'native-leads' ); ?></h1>
			</div>

			<div class="mainbar__range">
				<fieldset class="segmented">
					<legend class="screen-reader-text"><?php esc_html_e( 'Date range', 'native-leads' ); ?></legend>
					<?php foreach ( $ranges as $key => $label ) : ?>
						<label>
							<input type="radio" name="range" value="<?php echo esc_attr( $key ); ?>" form="nl-filters" <?php checked( $f['range'], $key ); ?> data-range>
							<span><?php echo esc_html( $label ); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
				<div class="dates" data-dates <?php echo 'custom' === $f['range'] ? '' : 'hidden'; ?>>
					<label><span><?php esc_html_e( 'From', 'native-leads' ); ?></span><input type="date" name="from" form="nl-filters" value="<?php echo esc_attr( $f['from'] ); ?>" max="<?php echo esc_attr( $now->format( 'Y-m-d' ) ); ?>"></label>
					<label><span><?php esc_html_e( 'To', 'native-leads' ); ?></span><input type="date" name="to" form="nl-filters" value="<?php echo esc_attr( $f['to'] ); ?>" max="<?php echo esc_attr( $now->format( 'Y-m-d' ) ); ?>"></label>
					<button type="submit" form="nl-filters" class="btn btn--ghost btn--sm"><?php esc_html_e( 'Apply', 'native-leads' ); ?></button>
				</div>
			</div>

			<div class="mainbar__actions">
				<a class="icon-btn" href="<?php echo esc_url( native_leads_dashboard_url( $f ) ); ?>" aria-label="<?php esc_attr_e( 'Refresh', 'native-leads' ); ?>" title="<?php esc_attr_e( 'Refresh', 'native-leads' ); ?>"><?php echo native_leads_icon( 'refresh' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $export_url ); ?>"><?php echo native_leads_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Export', 'native-leads' ); ?></span></a>
			</div>
		</header>

		<main class="content" id="overview">
			<?php if ( $has_filters ) : ?>
				<div class="active-filters">
					<span class="faint"><?php esc_html_e( 'Showing', 'native-leads' ); ?></span>
					<span class="fchip fchip--range"><?php echo native_leads_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $range_label ); ?></span>
					<?php foreach ( $active_chips as $key => $chip ) : ?>
						<?php if ( '' !== $f[ $key ] ) : ?>
							<a class="fchip" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( $key => '', 'pg' => 1 ) ) ); ?>" title="<?php esc_attr_e( 'Remove filter', 'native-leads' ); ?>">
								<small><?php echo esc_html( $chip[0] ); ?></small> <?php echo esc_html( $chip[1] ); ?>
								<?php echo native_leads_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
					<a class="link-reset" href="<?php echo esc_url( native_leads_dashboard_page_url() ); ?>"><?php esc_html_e( 'Reset all', 'native-leads' ); ?></a>
				</div>
			<?php endif; ?>

			<section class="kpis" aria-label="<?php esc_attr_e( 'Summary', 'native-leads' ); ?>">
				<article class="kpi kpi--hero">
					<h2 class="kpi__label"><?php esc_html_e( 'Leads in range', 'native-leads' ); ?></h2>
					<p class="kpi__value"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></p>
					<p class="kpi__foot">
						<span class="kpi__note">
							<?php
							echo esc_html(
								/* translators: %s: number of landing pages */
								sprintf( _n( '%s page', '%s pages', $stats['pages'], 'native-leads' ), number_format_i18n( $stats['pages'] ) )
								. ' · ' .
								/* translators: %s: number of campaigns */
								sprintf( _n( '%s campaign', '%s campaigns', $stats['campaigns'], 'native-leads' ), number_format_i18n( $stats['campaigns'] ) )
							);
							?>
						</span>
					</p>
				</article>
				<article class="kpi">
					<h2 class="kpi__label"><?php echo native_leads_icon( 'sun' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Today', 'native-leads' ); ?></h2>
					<p class="kpi__value"><?php echo esc_html( number_format_i18n( $stats['today'] ) ); ?></p>
					<p class="kpi__foot">
						<?php
						/* translators: %s: number of leads yesterday */
						echo $trend( $delta( $stats['today'], $stats['yesterday'] ), sprintf( __( 'vs %s yesterday', 'native-leads' ), number_format_i18n( $stats['yesterday'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in $trend.
						?>
					</p>
				</article>
				<article class="kpi">
					<h2 class="kpi__label"><?php echo native_leads_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Last 7 days', 'native-leads' ); ?></h2>
					<p class="kpi__value"><?php echo esc_html( number_format_i18n( $stats['week'] ) ); ?></p>
					<p class="kpi__foot">
						<?php
						/* translators: %s: number of leads in the previous 7 days */
						echo $trend( $delta( $stats['week'], $stats['prev_week'] ), sprintf( __( 'vs %s the week before', 'native-leads' ), number_format_i18n( $stats['prev_week'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</p>
				</article>
				<article class="kpi">
					<h2 class="kpi__label"><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Delivered to CRM', 'native-leads' ); ?></h2>
					<p class="kpi__value"><?php echo esc_html( number_format_i18n( $crm_rate ) ); ?><small>%</small></p>
					<p class="kpi__foot">
						<?php if ( $crm_issue ) : ?>
							<a class="trend trend--down" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( 'crm' => ( $by_crm['failed'] ?? 0 ) ? 'failed' : 'pending', 'pg' => 1 ) ) ); ?>#leads">
								<?php
								/* translators: %s: number of leads not delivered yet */
								echo esc_html( sprintf( _n( '%s needs attention', '%s need attention', $crm_issue, 'native-leads' ), number_format_i18n( $crm_issue ) ) );
								?>
							</a>
						<?php else : ?>
							<span class="kpi__note">
								<?php
								/* translators: %s: number of delivered leads */
								echo esc_html( sprintf( __( '%s delivered, none waiting', 'native-leads' ), number_format_i18n( $crm_sent ) ) );
								?>
							</span>
						<?php endif; ?>
					</p>
				</article>
			</section>

			<section class="insights">
				<article class="card chart">
					<header class="card__head">
						<span class="card__icon"><?php echo native_leads_icon( 'pulse' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<h2><?php esc_html_e( 'Leads per day', 'native-leads' ); ?></h2>
							<p class="muted">
								<?php
								printf(
									/* translators: 1: first day, 2: last day */
									esc_html__( '%1$s – %2$s', 'native-leads' ),
									esc_html( mysql2date( 'M j', array_key_first( $daily ) ) ),
									esc_html( mysql2date( 'M j', array_key_last( $daily ) ) )
								);
								?>
							</p>
						</div>
						<p class="chart__total">
							<strong><?php echo esc_html( number_format_i18n( $daily_sum ) ); ?></strong>
							<span class="muted"><?php echo esc_html( _n( 'lead', 'leads', $daily_sum, 'native-leads' ) ); ?></span>
						</p>
					</header>
					<div class="chart__plot">
						<div class="chart__grid" aria-hidden="true">
							<?php for ( $t = $intervals; $t >= 0; $t-- ) : ?>
								<span data-v="<?php echo esc_attr( number_format_i18n( $step * $t ) ); ?>"></span>
							<?php endfor; ?>
						</div>
						<svg class="chart__svg" viewBox="0 0 1000 200" preserveAspectRatio="none" aria-hidden="true">
							<defs>
								<linearGradient id="nl-area" x1="0" y1="0" x2="0" y2="1">
									<stop offset="0" stop-color="currentColor" stop-opacity="0.35" />
									<stop offset="1" stop-color="currentColor" stop-opacity="0" />
								</linearGradient>
							</defs>
							<path class="chart__area" d="<?php echo esc_attr( $area_path ); ?>" />
							<path class="chart__line" d="<?php echo esc_attr( $line_path ); ?>" />
						</svg>
						<ol class="chart__cols" style="--days: <?php echo (int) $day_count; ?>">
							<?php foreach ( $daily as $day => $count ) : ?>
								<?php
								$label = sprintf(
									/* translators: 1: date, 2: number of leads */
									_n( '%1$s · %2$s lead', '%1$s · %2$s leads', $count, 'native-leads' ),
									mysql2date( 'D, M j', $day ),
									number_format_i18n( $count )
								);
								?>
								<li class="chart__col" tabindex="0" data-tip="<?php echo esc_attr( $label ); ?>" aria-label="<?php echo esc_attr( $label ); ?>">
									<span class="chart__dot" style="bottom: <?php echo esc_attr( round( $count / $chart_top * 100, 2 ) ); ?>%"></span>
								</li>
							<?php endforeach; ?>
						</ol>
					</div>
					<div class="chart__axis" aria-hidden="true">
						<?php
						$keys = array_keys( $daily );
						$mid  = $keys[ (int) floor( ( count( $keys ) - 1 ) / 2 ) ];
						?>
						<span><?php echo esc_html( mysql2date( 'M j', $keys[0] ) ); ?></span>
						<?php if ( count( $keys ) > 2 ) : ?>
							<span><?php echo esc_html( mysql2date( 'M j', $mid ) ); ?></span>
						<?php endif; ?>
						<span><?php echo esc_html( mysql2date( 'M j', end( $keys ) ) ); ?></span>
					</div>
				</article>

				<article class="card crm">
					<header class="card__head">
						<span class="card__icon"><?php echo native_leads_icon( 'send' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<h2><?php esc_html_e( 'CRM delivery', 'native-leads' ); ?></h2>
							<p class="muted"><?php esc_html_e( 'Affiliate API sync', 'native-leads' ); ?></p>
						</div>
					</header>
					<?php if ( $crm_total ) : ?>
						<div class="crm__ring" style="--rate: <?php echo esc_attr( $crm_rate ); ?>">
							<strong><?php echo esc_html( $crm_rate ); ?>%</strong>
							<span class="muted"><?php esc_html_e( 'delivered', 'native-leads' ); ?></span>
						</div>
						<div class="crm__bar" aria-hidden="true">
							<?php foreach ( $crm_order as $state ) : ?>
								<?php if ( ! empty( $by_crm[ $state ] ) ) : ?>
									<span class="crm__seg crm__seg--<?php echo esc_attr( $state ); ?>" style="flex-grow: <?php echo esc_attr( $by_crm[ $state ] ); ?>"></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
						<ul class="crm__legend">
							<?php foreach ( $crm_order as $state ) : ?>
								<?php
								$n      = $by_crm[ $state ] ?? 0;
								$active = $f['crm'] === $state;
								?>
								<li>
									<a class="crm__item crm__item--<?php echo esc_attr( $state ); ?><?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( native_leads_dashboard_url( $f, array( 'crm' => $active ? '' : $state, 'pg' => 1 ) ) ); ?>#leads">
										<span class="crm__key" aria-hidden="true"></span>
										<span><?php echo esc_html( native_leads_crm_label( $state ) ); ?></span>
										<strong><?php echo esc_html( number_format_i18n( $n ) ); ?></strong>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p class="breakdown__empty muted"><?php esc_html_e( 'No leads in this range.', 'native-leads' ); ?></p>
					<?php endif; ?>
				</article>
			</section>

			<section class="breakdowns">
				<?php
				$breakdown( __( 'Traffic sources', 'native-leads' ), $by_channel, 'channel', $label_channel, 'megaphone', __( 'No leads in this range.', 'native-leads' ) );
				$breakdown( __( 'Top campaigns', 'native-leads' ), $by_camp, 'campaign', $label_camp, 'target', __( 'No tagged campaigns in this range.', 'native-leads' ) );
				$breakdown( __( 'Countries', 'native-leads' ), $by_country, 'country', $label_country, 'globe', __( 'No leads in this range.', 'native-leads' ) );
				?>
			</section>

			<section class="card table-card" id="leads">
				<form class="filters" id="nl-filters" method="get" action="<?php echo esc_url( native_leads_dashboard_page_url() ); ?>#leads" data-filters>
					<div class="filters__head">
						<div>
							<h2><?php esc_html_e( 'All leads', 'native-leads' ); ?></h2>
							<p class="muted">
								<?php
								if ( $result['total'] ) {
									printf(
										/* translators: 1: first row number, 2: last row number, 3: total leads */
										esc_html__( 'Showing %1$s–%2$s of %3$s', 'native-leads' ),
										esc_html( number_format_i18n( $first_row ) ),
										esc_html( number_format_i18n( $last_row ) ),
										esc_html( number_format_i18n( $result['total'] ) )
									);
								} else {
									esc_html_e( 'No matching leads', 'native-leads' );
								}
								?>
							</p>
						</div>
						<label class="search">
							<?php echo native_leads_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="screen-reader-text"><?php esc_html_e( 'Search leads', 'native-leads' ); ?></span>
							<input type="search" name="q" value="<?php echo esc_attr( $f['q'] ); ?>" placeholder="<?php esc_attr_e( 'Name, email, phone, reference…', 'native-leads' ); ?>" data-search>
							<kbd aria-hidden="true">/</kbd>
						</label>
					</div>

					<div class="filters__row">
						<label class="select">
							<span><?php esc_html_e( 'Landing page', 'native-leads' ); ?></span>
							<select name="source" data-autosubmit>
								<option value=""><?php esc_html_e( 'All', 'native-leads' ); ?></option>
								<?php foreach ( native_leads_distinct_values( 'source' ) as $v ) : ?>
									<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $f['source'], $v ); ?>><?php echo esc_html( native_leads_source_label( $v ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="select">
							<span><?php esc_html_e( 'Traffic', 'native-leads' ); ?></span>
							<select name="channel" data-autosubmit>
								<option value=""><?php esc_html_e( 'All', 'native-leads' ); ?></option>
								<option value="direct" <?php selected( $f['channel'], 'direct' ); ?>><?php esc_html_e( 'Direct', 'native-leads' ); ?></option>
								<?php foreach ( native_leads_distinct_values( 'channel' ) as $v ) : ?>
									<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $f['channel'], $v ); ?>><?php echo esc_html( native_leads_channel_label( $v ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="select">
							<span><?php esc_html_e( 'Country', 'native-leads' ); ?></span>
							<select name="country" data-autosubmit>
								<option value=""><?php esc_html_e( 'All', 'native-leads' ); ?></option>
								<?php foreach ( native_leads_distinct_values( 'country' ) as $v ) : ?>
									<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $f['country'], $v ); ?>><?php echo esc_html( native_leads_country_name( $v ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<label class="select">
							<span><?php esc_html_e( 'CRM', 'native-leads' ); ?></span>
							<select name="crm" data-autosubmit>
								<option value=""><?php esc_html_e( 'All', 'native-leads' ); ?></option>
								<?php foreach ( array_unique( array_merge( array_keys( $crm_states ), native_leads_distinct_values( 'crm' ) ) ) as $v ) : ?>
									<option value="<?php echo esc_attr( $v ); ?>" <?php selected( $f['crm'], $v ); ?>><?php echo esc_html( native_leads_crm_label( $v ) ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<?php if ( '' !== $f['campaign'] ) : ?>
							<input type="hidden" name="campaign" value="<?php echo esc_attr( $f['campaign'] ); ?>">
						<?php endif; ?>
						<input type="hidden" name="sort" value="<?php echo esc_attr( $f['sort'] ); ?>">
						<input type="hidden" name="dir" value="<?php echo esc_attr( $f['dir'] ); ?>">
						<button type="submit" class="btn btn--ghost btn--sm filters__apply"><?php esc_html_e( 'Apply', 'native-leads' ); ?></button>
					</div>
				</form>

				<?php if ( $result['rows'] ) : ?>
					<div class="table-wrap">
						<table class="leads">
							<thead>
								<tr>
									<th scope="col"><?php $sort_link( 'name', __( 'Lead', 'native-leads' ) ); ?></th>
									<th scope="col"><?php esc_html_e( 'Contact', 'native-leads' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Profile', 'native-leads' ); ?></th>
									<th scope="col"><?php $sort_link( 'source', __( 'Landing page', 'native-leads' ) ); ?></th>
									<th scope="col"><?php esc_html_e( 'Campaign', 'native-leads' ); ?></th>
									<th scope="col"><?php $sort_link( 'crm', __( 'CRM', 'native-leads' ) ); ?></th>
									<th scope="col"><?php $sort_link( 'date', __( 'Received', 'native-leads' ) ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $result['rows'] as $lead ) : ?>
									<?php
									$created = new DateTimeImmutable( $lead['CreatedDate'] );
									$profile = native_leads_parse_description( $lead['Description'] );
									$name    = trim( $lead['FirstName'] . ( $lead['LastName'] !== $lead['FirstName'] ? ' ' . $lead['LastName'] : '' ) );
									$digits  = preg_replace( '/\D+/', '', $lead['Phone'] );
									$crm     = (string) $lead['CrmStatus'];
									$lead_js = array(
										'name'         => $name,
										'initials'     => $initials( $lead['FirstName'], $lead['LastName'] ),
										'ref'          => $lead['AffTransactionId'],
										'email'        => $lead['Email'],
										'phone'        => $lead['Phone'],
										'digits'       => $digits,
										'country'      => native_leads_country_name( $lead['Country'] ) . ' (' . $lead['Country'] . ')',
										'age'          => $profile['age'],
										'occupation'   => $profile['occupation'],
										'contact'      => strtolower( $profile['contact'] ),
										'contact_label' => ucfirst( $profile['contact'] ),
										'note'         => $profile['note'],
										'source'       => native_leads_source_label( $lead['Source'] ),
										'utm_source'   => $lead['UtmSource'],
										'utm_medium'   => $lead['UtmMedium'],
										'utm_campaign' => $lead['UtmCampaign'],
										'utm_content'  => $lead['UtmContent'],
										'utm_term'     => $lead['UtmTerm'],
										'campaign_id'  => $lead['Campaignid'],
										'spcid'        => $lead['SubAffiliateId'],
										'affiliate'    => $lead['AffiliateId'],
										'referrer'     => $lead['Referrer'],
										'received'     => mysql2date( 'l, M j, Y · g:i a', $lead['CreatedDate'] ),
										'ip'           => trim( $lead['IpAddress'] . ( $lead['IpCountry'] ? ' (' . $lead['IpCountry'] . ')' : '' ) ),
										'language'     => $lead['Language'],
										'terms'        => $lead['AcceptTerms'] ? __( 'Accepted', 'native-leads' ) : __( 'Not accepted', 'native-leads' ),
										'crm'          => $crm,
										'crm_label'    => native_leads_crm_label( $crm ),
										'crm_id'       => $lead['CrmLeadId'],
										'crm_http'     => $lead['CrmHttpCode'],
										'crm_attempts' => $lead['CrmAttempts'],
										'crm_sent'     => $lead['CrmSentAt'] ? mysql2date( 'M j, Y · g:i a', $lead['CrmSentAt'] ) : '',
										'crm_response' => 'sent' === $crm ? '' : mb_substr( (string) $lead['CrmResponse'], 0, 400 ),
									);
									$ago     = $now->getTimestamp() - $created->getTimestamp();
									?>
									<tr data-lead="<?php echo esc_attr( wp_json_encode( $lead_js ) ); ?>" tabindex="0">
										<td data-label="<?php esc_attr_e( 'Lead', 'native-leads' ); ?>">
											<div class="person">
												<span class="avatar" aria-hidden="true"><?php echo esc_html( $lead_js['initials'] ); ?></span>
												<span>
													<strong class="person__name" dir="auto"><?php echo esc_html( $name ); ?></strong>
													<span class="person__ref"><?php echo esc_html( $lead['AffTransactionId'] ); ?></span>
												</span>
											</div>
										</td>
										<td data-label="<?php esc_attr_e( 'Contact', 'native-leads' ); ?>">
											<a class="contact" href="mailto:<?php echo esc_attr( $lead['Email'] ); ?>"><?php echo esc_html( $lead['Email'] ); ?></a>
											<span class="contact--line">
												<a class="contact contact--phone" href="tel:+<?php echo esc_attr( $digits ); ?>" dir="ltr">+<?php echo esc_html( $lead['Phone'] ); ?></a>
												<span class="cc" title="<?php echo esc_attr( native_leads_country_name( $lead['Country'] ) ); ?>"><?php echo esc_html( $lead['Country'] ); ?></span>
											</span>
										</td>
										<td data-label="<?php esc_attr_e( 'Profile', 'native-leads' ); ?>">
											<?php if ( $profile['age'] || $profile['occupation'] ) : ?>
												<span class="profile">
													<span dir="auto"><?php echo esc_html( $profile['occupation'] ? $profile['occupation'] : '—' ); ?></span>
													<small>
														<?php
														$bits = array();
														if ( $profile['age'] ) {
															/* translators: %s: age in years */
															$bits[] = sprintf( __( '%s yrs', 'native-leads' ), $profile['age'] );
														}
														if ( $profile['contact'] ) {
															/* translators: %s: preferred contact method, e.g. whatsapp */
															$bits[] = sprintf( __( 'prefers %s', 'native-leads' ), $profile['contact'] );
														}
														echo esc_html( implode( ' · ', $bits ) );
														?>
													</small>
												</span>
											<?php else : ?>
												<span class="faint">—</span>
											<?php endif; ?>
										</td>
										<td data-label="<?php esc_attr_e( 'Landing page', 'native-leads' ); ?>"><span class="tag"><?php echo esc_html( $lead_js['source'] ); ?></span></td>
										<td data-label="<?php esc_attr_e( 'Campaign', 'native-leads' ); ?>">
											<?php if ( $lead['UtmSource'] || $lead['UtmCampaign'] ) : ?>
												<span class="campaign">
													<span><?php echo esc_html( native_leads_channel_label( (string) $lead['UtmSource'] ) ); ?></span>
													<small><?php echo esc_html( (string) $lead['UtmCampaign'] ); ?></small>
												</span>
											<?php else : ?>
												<span class="faint"><?php esc_html_e( 'Direct', 'native-leads' ); ?></span>
											<?php endif; ?>
										</td>
										<td data-label="<?php esc_attr_e( 'CRM', 'native-leads' ); ?>"><span class="badge badge--<?php echo esc_attr( $crm ); ?>"><?php echo esc_html( $lead_js['crm_label'] ); ?></span></td>
										<td data-label="<?php esc_attr_e( 'Received', 'native-leads' ); ?>">
											<time datetime="<?php echo esc_attr( $created->format( 'Y-m-d\TH:i:s' ) ); ?>" title="<?php echo esc_attr( $lead_js['received'] ); ?>">
												<span><?php echo esc_html( mysql2date( 'M j, Y', $lead['CreatedDate'] ) ); ?></span>
												<small>
													<?php
													echo esc_html(
														$ago >= 0 && $ago < DAY_IN_SECONDS
															/* translators: %s: human time difference */
															? sprintf( __( '%s ago', 'native-leads' ), human_time_diff( $created->getTimestamp(), $now->getTimestamp() ) )
															: mysql2date( 'g:i a', $lead['CreatedDate'] )
													);
													?>
												</small>
											</time>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<?php if ( $total_pages > 1 ) : ?>
						<nav class="pager" aria-label="<?php esc_attr_e( 'Pages', 'native-leads' ); ?>">
							<?php
							$pg = $f['pg'];
							if ( $pg > 1 ) {
								printf( '<a class="pager__btn" href="%s#leads" rel="prev">%s</a>', esc_url( native_leads_dashboard_url( $f, array( 'pg' => $pg - 1 ) ) ), esc_html__( '← Prev', 'native-leads' ) );
							}
							$shown = array_unique( array_filter( array( 1, $pg - 1, $pg, $pg + 1, $total_pages ), static fn( $n ) => $n >= 1 && $n <= $total_pages ) );
							sort( $shown );
							$prev = 0;
							foreach ( $shown as $n ) {
								if ( $n - $prev > 1 ) {
									echo '<span class="pager__gap">…</span>';
								}
								if ( $n === $pg ) {
									printf( '<span class="pager__btn is-current" aria-current="page">%s</span>', esc_html( number_format_i18n( $n ) ) );
								} else {
									printf( '<a class="pager__btn" href="%s#leads">%s</a>', esc_url( native_leads_dashboard_url( $f, array( 'pg' => $n ) ) ), esc_html( number_format_i18n( $n ) ) );
								}
								$prev = $n;
							}
							if ( $pg < $total_pages ) {
								printf( '<a class="pager__btn" href="%s#leads" rel="next">%s</a>', esc_url( native_leads_dashboard_url( $f, array( 'pg' => $pg + 1 ) ) ), esc_html__( 'Next →', 'native-leads' ) );
							}
							?>
						</nav>
					<?php endif; ?>
				<?php else : ?>
					<div class="empty">
						<?php echo native_leads_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<h3><?php esc_html_e( 'No leads match these filters', 'native-leads' ); ?></h3>
						<p class="muted"><?php esc_html_e( 'Try a wider date range or a different search.', 'native-leads' ); ?></p>
						<?php if ( $has_filters ) : ?>
							<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( native_leads_dashboard_page_url() ); ?>"><?php esc_html_e( 'Reset filters', 'native-leads' ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</section>

		</main>
	</div>
</div>

<div class="chart-tip" role="tooltip" hidden data-chart-tip></div>

<?php /* translators: %s: preferred contact method, e.g. Whatsapp */ ?>
<div class="drawer" hidden data-drawer data-prefers-label="<?php esc_attr_e( 'Prefers to be contacted by %s', 'native-leads' ); ?>">
	<div class="drawer__scrim" data-drawer-close></div>
	<aside class="drawer__panel" role="dialog" aria-modal="true" aria-labelledby="drawer-name">
		<header class="drawer__head">
			<span class="avatar avatar--lg" data-f="initials"></span>
			<div>
				<h2 id="drawer-name" dir="auto" data-f="name"></h2>
				<p><span class="person__ref" data-f="ref"></span> <span class="badge" data-f="crm_label"></span></p>
			</div>
			<button type="button" class="icon-btn" data-drawer-close aria-label="<?php esc_attr_e( 'Close', 'native-leads' ); ?>">
				<?php echo native_leads_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</header>

		<div class="drawer__actions">
			<a class="btn btn--ghost btn--sm" data-action="call"><?php echo native_leads_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Call', 'native-leads' ); ?></a>
			<a class="btn btn--ghost btn--sm" data-action="whatsapp" target="_blank" rel="noopener"><?php echo native_leads_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>WhatsApp</a>
			<a class="btn btn--ghost btn--sm" data-action="email"><?php echo native_leads_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Email', 'native-leads' ); ?></a>
			<button type="button" class="btn btn--ghost btn--sm" data-copy><?php echo native_leads_icon( 'copy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Copy', 'native-leads' ); ?></span></button>
		</div>
		<p class="drawer__hint" data-prefers hidden></p>

		<div class="drawer__body">
			<section>
				<h3><?php esc_html_e( 'Contact', 'native-leads' ); ?></h3>
				<dl>
					<dt><?php esc_html_e( 'Email', 'native-leads' ); ?></dt><dd data-f="email"></dd>
					<dt><?php esc_html_e( 'Phone', 'native-leads' ); ?></dt><dd dir="ltr" data-f="phone"></dd>
					<dt><?php esc_html_e( 'Country', 'native-leads' ); ?></dt><dd data-f="country"></dd>
					<dt><?php esc_html_e( 'Age', 'native-leads' ); ?></dt><dd data-f="age"></dd>
					<dt><?php esc_html_e( 'Occupation', 'native-leads' ); ?></dt><dd dir="auto" data-f="occupation"></dd>
					<dt><?php esc_html_e( 'Prefers', 'native-leads' ); ?></dt><dd data-f="contact_label"></dd>
					<dt><?php esc_html_e( 'Note', 'native-leads' ); ?></dt><dd dir="auto" data-f="note"></dd>
				</dl>
			</section>
			<section>
				<h3><?php esc_html_e( 'Source & campaign', 'native-leads' ); ?></h3>
				<dl>
					<dt><?php esc_html_e( 'Landing page', 'native-leads' ); ?></dt><dd data-f="source"></dd>
					<dt>utm_source</dt><dd data-f="utm_source"></dd>
					<dt>utm_medium</dt><dd data-f="utm_medium"></dd>
					<dt>utm_campaign</dt><dd data-f="utm_campaign"></dd>
					<dt>utm_content</dt><dd data-f="utm_content"></dd>
					<dt>utm_term</dt><dd data-f="utm_term"></dd>
					<dt><?php esc_html_e( 'Campaign ID', 'native-leads' ); ?></dt><dd data-f="campaign_id"></dd>
					<dt>spcid</dt><dd data-f="spcid"></dd>
					<dt><?php esc_html_e( 'Affiliate', 'native-leads' ); ?></dt><dd data-f="affiliate"></dd>
					<dt><?php esc_html_e( 'Referrer', 'native-leads' ); ?></dt><dd class="small" data-f="referrer"></dd>
				</dl>
			</section>
			<section>
				<h3><?php esc_html_e( 'CRM delivery', 'native-leads' ); ?></h3>
				<dl>
					<dt><?php esc_html_e( 'Status', 'native-leads' ); ?></dt><dd data-f="crm_label"></dd>
					<dt><?php esc_html_e( 'CRM lead ID', 'native-leads' ); ?></dt><dd class="mono" data-f="crm_id"></dd>
					<dt><?php esc_html_e( 'Attempts', 'native-leads' ); ?></dt><dd data-f="crm_attempts"></dd>
					<dt><?php esc_html_e( 'Last sent', 'native-leads' ); ?></dt><dd data-f="crm_sent"></dd>
					<dt><?php esc_html_e( 'HTTP code', 'native-leads' ); ?></dt><dd class="mono" data-f="crm_http"></dd>
					<dt><?php esc_html_e( 'CRM answer', 'native-leads' ); ?></dt><dd class="small mono" data-f="crm_response"></dd>
				</dl>
			</section>
			<section>
				<h3><?php esc_html_e( 'Submission', 'native-leads' ); ?></h3>
				<dl>
					<dt><?php esc_html_e( 'Received', 'native-leads' ); ?></dt><dd data-f="received"></dd>
					<dt><?php esc_html_e( 'IP address', 'native-leads' ); ?></dt><dd class="mono" data-f="ip"></dd>
					<dt><?php esc_html_e( 'Language', 'native-leads' ); ?></dt><dd data-f="language"></dd>
					<dt><?php esc_html_e( 'Terms', 'native-leads' ); ?></dt><dd data-f="terms"></dd>
				</dl>
			</section>
		</div>
	</aside>
</div>
