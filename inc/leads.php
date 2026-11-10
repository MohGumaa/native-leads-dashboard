<?php
/**
 * Leads data layer: reads the table the native-leads landing pages write to.
 *
 * The landing pages (global, invest, prospect…) insert into `wpcq_leads`,
 * whose columns use the Affiliate API names (FirstName, Source, UtmSource,
 * CrmStatus…). Use the `native_leads_leads_table` filter to point elsewhere.
 *
 * CreatedDate is written by MySQL (CURRENT_TIMESTAMP), so it is in the
 * database server's clock, not WordPress's timezone. Every "today" / "last
 * 7 days" calculation is anchored to the database's NOW() for that reason.
 *
 * @package native-leads
 */

const NATIVE_LEADS_PER_PAGE = 25;

/**
 * Page template that renders the dashboard. Assign it to any page.
 */
const NATIVE_LEADS_DASHBOARD_TEMPLATE = 'page-templates/dashboard.php';

/**
 * Is the current request the leads dashboard page?
 */
function native_leads_is_dashboard() {
	return is_page_template( NATIVE_LEADS_DASHBOARD_TEMPLATE );
}

/**
 * Permalink of the dashboard page: the current page when on it, otherwise the
 * first published page using the template. Empty string when there is none.
 */
function native_leads_dashboard_page_url() {
	static $url = null;
	if ( native_leads_is_dashboard() ) {
		return get_permalink( get_queried_object_id() );
	}
	if ( null === $url ) {
		$ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'orderby'        => 'menu_order ID',
				'order'          => 'ASC',
				'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => NATIVE_LEADS_DASHBOARD_TEMPLATE, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		$url = $ids ? get_permalink( $ids[0] ) : '';
	}
	return $url;
}

/**
 * Full name of the leads table. The landing pages hard-code `wpcq_leads`,
 * which does not follow this site's WordPress prefix.
 */
function native_leads_table() {
	return apply_filters( 'native_leads_leads_table', 'wpcq_leads' );
}

/**
 * Who may open the leads dashboard. Administrators always can; give other
 * roles the `native_leads_view_leads` capability (e.g. with the Members plugin).
 */
function native_leads_can_view_leads() {
	return is_user_logged_in() && ( current_user_can( 'native_leads_view_leads' ) || current_user_can( 'manage_options' ) );
}

/**
 * Grant the capability to administrators when the theme is activated.
 */
function native_leads_add_caps() {
	$role = get_role( 'administrator' );
	if ( $role ) {
		$role->add_cap( 'native_leads_view_leads' );
	}
}
add_action( 'after_switch_theme', 'native_leads_add_caps' );

/**
 * Does the leads table exist in this database?
 */
function native_leads_table_exists() {
	global $wpdb;
	$table = native_leads_table();
	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) === $table;
}

/**
 * The database's current time, the clock CreatedDate is written in.
 */
function native_leads_now() {
	static $now = null;
	if ( null === $now ) {
		global $wpdb;
		$value = $wpdb->get_var( 'SELECT NOW()' );
		$now   = new DateTimeImmutable( $value ? $value : 'now' );
	}
	return $now;
}

/**
 * Midnight today, in the database clock.
 */
function native_leads_today() {
	return native_leads_now()->setTime( 0, 0 );
}

/**
 * Date range presets shown in the header.
 */
function native_leads_date_ranges() {
	return array(
		'all'       => __( 'All time', 'native-leads' ),
		'today'     => __( 'Today', 'native-leads' ),
		'yesterday' => __( 'Yesterday', 'native-leads' ),
		'7d'        => __( '7 days', 'native-leads' ),
		'30d'       => __( '30 days', 'native-leads' ),
		'month'     => __( 'This month', 'native-leads' ),
		'custom'    => __( 'Custom', 'native-leads' ),
	);
}

/**
 * Columns the table can be sorted by: request key => SQL ORDER BY expression.
 */
function native_leads_sortable_columns() {
	return array(
		'date'    => 'CreatedDate',
		'name'    => 'FirstName',
		'source'  => 'Source',
		'country' => 'Country',
		'crm'     => 'CrmStatus',
	);
}

/**
 * CRM delivery statuses written by affiliate.php, in display order.
 */
function native_leads_crm_statuses() {
	return array(
		'sent'    => __( 'Delivered', 'native-leads' ),
		'pending' => __( 'Pending', 'native-leads' ),
		'failed'  => __( 'Failed', 'native-leads' ),
		'off'     => __( 'Not sent', 'native-leads' ),
	);
}

/**
 * Label for a CRM status, including ones this theme does not know.
 *
 * @param string $status CrmStatus value.
 */
function native_leads_crm_label( $status ) {
	$all = native_leads_crm_statuses();
	return $all[ $status ] ?? ucfirst( (string) $status );
}

/**
 * Read and sanitize the filters from the query string.
 *
 * Uses q / pg / sort / dir instead of s / paged / orderby / order so
 * WordPress does not treat the dashboard as a search or archive page.
 */
function native_leads_get_filters() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only filters.
	$get = wp_unslash( $_GET );

	$range = isset( $get['range'] ) ? sanitize_key( $get['range'] ) : 'all';
	if ( ! array_key_exists( $range, native_leads_date_ranges() ) ) {
		$range = 'all';
	}

	$date = static function ( $key ) use ( $get ) {
		$value = isset( $get[ $key ] ) ? sanitize_text_field( $get[ $key ] ) : '';
		return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
	};

	$sort = isset( $get['sort'] ) ? sanitize_key( $get['sort'] ) : 'date';
	if ( ! array_key_exists( $sort, native_leads_sortable_columns() ) ) {
		$sort = 'date';
	}

	$text = static function ( $key ) use ( $get ) {
		return isset( $get[ $key ] ) && is_string( $get[ $key ] ) ? trim( sanitize_text_field( $get[ $key ] ) ) : '';
	};

	$country = strtoupper( $text( 'country' ) );

	$filters = array(
		'range'   => $range,
		'from'    => $date( 'from' ),
		'to'      => $date( 'to' ),
		'source'  => $text( 'source' ),
		'channel'  => $text( 'channel' ),
		'campaign' => $text( 'campaign' ),
		'country' => preg_match( '/^[A-Z]{2}$/', $country ) ? $country : '',
		'crm'     => isset( $get['crm'] ) ? sanitize_key( $get['crm'] ) : '',
		'q'       => $text( 'q' ),
		'sort'    => $sort,
		'dir'     => ( isset( $get['dir'] ) && 'asc' === strtolower( $get['dir'] ) ) ? 'asc' : 'desc',
		'pg'      => isset( $get['pg'] ) ? max( 1, absint( $get['pg'] ) ) : 1,
	);
	// phpcs:enable

	return $filters;
}

/**
 * Start and end (exclusive) of the selected date range, in the database
 * clock, as 'Y-m-d H:i:s' strings. Null means unbounded.
 *
 * @param array $f Filters.
 * @return array{0:?string,1:?string}
 */
function native_leads_range_bounds( $f ) {
	$today = native_leads_today();
	$fmt   = 'Y-m-d H:i:s';

	switch ( $f['range'] ) {
		case 'today':
			return array( $today->format( $fmt ), $today->modify( '+1 day' )->format( $fmt ) );
		case 'yesterday':
			return array( $today->modify( '-1 day' )->format( $fmt ), $today->format( $fmt ) );
		case '7d':
			return array( $today->modify( '-6 days' )->format( $fmt ), $today->modify( '+1 day' )->format( $fmt ) );
		case '30d':
			return array( $today->modify( '-29 days' )->format( $fmt ), $today->modify( '+1 day' )->format( $fmt ) );
		case 'month':
			return array( $today->modify( 'first day of this month' )->format( $fmt ), $today->modify( '+1 day' )->format( $fmt ) );
		case 'custom':
			$from = $f['from'] ? ( new DateTimeImmutable( $f['from'] ) )->format( $fmt ) : null;
			$to   = $f['to'] ? ( new DateTimeImmutable( $f['to'] ) )->modify( '+1 day' )->format( $fmt ) : null;
			return array( $from, $to );
	}
	return array( null, null );
}

/**
 * WHERE clause (already prepared) for the filters.
 *
 * @param array $f      Filters.
 * @param array $ignore Filter keys to leave out ('date', 'source', 'crm'…),
 *                      e.g. so a breakdown still lists every landing page.
 */
function native_leads_where_sql( $f, $ignore = array() ) {
	global $wpdb;
	$where = array( '1=1' );

	if ( ! in_array( 'date', $ignore, true ) ) {
		list( $from, $to ) = native_leads_range_bounds( $f );
		if ( $from ) {
			$where[] = $wpdb->prepare( 'CreatedDate >= %s', $from );
		}
		if ( $to ) {
			$where[] = $wpdb->prepare( 'CreatedDate < %s', $to );
		}
	}

	$exact = array(
		'source'  => 'Source',
		'country'  => 'Country',
		'crm'      => 'CrmStatus',
		'campaign' => 'UtmCampaign',
	);
	foreach ( $exact as $key => $column ) {
		if ( '' !== $f[ $key ] && ! in_array( $key, $ignore, true ) ) {
			$where[] = $wpdb->prepare( "{$column} = %s", $f[ $key ] ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- whitelisted column.
		}
	}

	// "direct" stands for leads that came without a utm_source.
	if ( '' !== $f['channel'] && ! in_array( 'channel', $ignore, true ) ) {
		$where[] = 'direct' === $f['channel']
			? "(UtmSource IS NULL OR UtmSource = '')"
			: $wpdb->prepare( 'UtmSource = %s', $f['channel'] );
	}

	if ( '' !== $f['q'] && ! in_array( 'q', $ignore, true ) ) {
		$like    = '%' . $wpdb->esc_like( $f['q'] ) . '%';
		$where[] = $wpdb->prepare(
			"(CONCAT_WS(' ', FirstName, LastName) LIKE %s OR Email LIKE %s OR Phone LIKE %s OR AffTransactionId LIKE %s OR UtmCampaign LIKE %s OR Description LIKE %s OR SubAffiliateId LIKE %s)",
			$like,
			$like,
			$like,
			$like,
			$like,
			$like,
			$like
		);
	}

	return implode( ' AND ', $where );
}

/**
 * One page of leads plus the total that matches the filters.
 *
 * @param array    $f        Filters.
 * @param int|null $per_page Rows per page; null returns every matching row.
 * @return array{rows:array,total:int}
 */
function native_leads_query( $f, $per_page = NATIVE_LEADS_PER_PAGE ) {
	global $wpdb;
	$table  = native_leads_table();
	$where  = native_leads_where_sql( $f );
	$column = native_leads_sortable_columns()[ $f['sort'] ];
	$dir    = 'asc' === $f['dir'] ? 'ASC' : 'DESC';

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $where is prepared, table/column are whitelisted.
	$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$table}` WHERE {$where}" );
	$sql   = "SELECT * FROM `{$table}` WHERE {$where} ORDER BY {$column} {$dir}, Id {$dir}";
	if ( null !== $per_page ) {
		$sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $per_page, ( $f['pg'] - 1 ) * $per_page );
	}
	$rows = $wpdb->get_results( $sql, ARRAY_A );
	// phpcs:enable

	return array(
		'rows'  => $rows ? $rows : array(),
		'total' => $total,
	);
}

/**
 * Headline numbers for the KPI cards. Every filter applies; the date range
 * only applies to the "in range" figures and the CRM split.
 *
 * @param array $f Filters.
 */
function native_leads_stats( $f ) {
	global $wpdb;
	$table = native_leads_table();
	$where = native_leads_where_sql( $f, array( 'date' ) );
	$today = native_leads_today();
	$fmt   = 'Y-m-d H:i:s';

	$t0 = $today->format( $fmt );
	$y0 = $today->modify( '-1 day' )->format( $fmt );
	$w0 = $today->modify( '-6 days' )->format( $fmt );
	$p0 = $today->modify( '-13 days' )->format( $fmt );

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT
				SUM(CreatedDate >= %s) AS today,
				SUM(CreatedDate >= %s AND CreatedDate < %s) AS yesterday,
				SUM(CreatedDate >= %s) AS week,
				SUM(CreatedDate >= %s AND CreatedDate < %s) AS prev_week
			FROM `{$table}` WHERE {$where}",
			$t0,
			$y0,
			$t0,
			$w0,
			$p0,
			$w0
		),
		ARRAY_A
	);

	$range_where = native_leads_where_sql( $f );
	$in_range    = $wpdb->get_row(
		"SELECT COUNT(*) AS total, COUNT(DISTINCT NULLIF(UtmCampaign, '')) AS campaigns, COUNT(DISTINCT Source) AS pages FROM `{$table}` WHERE {$range_where}",
		ARRAY_A
	);
	// phpcs:enable

	return array(
		'total'     => (int) $in_range['total'],
		'campaigns' => (int) $in_range['campaigns'],
		'pages'     => (int) $in_range['pages'],
		'today'     => (int) $row['today'],
		'yesterday' => (int) $row['yesterday'],
		'week'      => (int) $row['week'],
		'prev_week' => (int) $row['prev_week'],
	);
}

/**
 * Leads per day for the chart. Follows every filter; "all time" and long
 * ranges are capped to the last 60 days so the chart stays readable.
 *
 * @param array $f Filters.
 * @return array<string,int> 'Y-m-d' => count, oldest first, missing days filled with 0.
 */
function native_leads_daily_counts( $f ) {
	global $wpdb;
	$table = native_leads_table();
	$today = native_leads_today();

	list( $from, $to ) = native_leads_range_bounds( $f );
	$end               = $to ? ( new DateTimeImmutable( $to ) )->modify( '-1 day' ) : $today;
	$start             = $from ? new DateTimeImmutable( $from ) : $end->modify( '-29 days' );
	if ( $start < $end->modify( '-59 days' ) ) {
		$start = $end->modify( '-59 days' );
	}
	// One-day ranges get the week before them for context.
	if ( $start >= $end ) {
		$start = $end->modify( '-6 days' );
	}

	$where = native_leads_where_sql( $f, array( 'date' ) ) . $wpdb->prepare(
		' AND CreatedDate >= %s AND CreatedDate < %s',
		$start->format( 'Y-m-d 00:00:00' ),
		$end->modify( '+1 day' )->format( 'Y-m-d 00:00:00' )
	);
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$rows = $wpdb->get_results( "SELECT DATE(CreatedDate) AS d, COUNT(*) AS n FROM `{$table}` WHERE {$where} GROUP BY DATE(CreatedDate)", OBJECT_K );

	$days = array();
	for ( $d = $start; $d <= $end; $d = $d->modify( '+1 day' ) ) {
		$key          = $d->format( 'Y-m-d' );
		$days[ $key ] = isset( $rows[ $key ] ) ? (int) $rows[ $key ]->n : 0;
	}
	return $days;
}

/**
 * Lead counts grouped by one column, for the breakdown cards. The card's
 * own filter is ignored so every option stays visible and clickable.
 *
 * @param array  $f     Filters.
 * @param string $group 'source', 'channel', 'country', 'campaign' or 'crm'.
 * @param int    $limit Max rows.
 * @return array<string,int> value => count, biggest first. '' = no value.
 */
function native_leads_breakdown( $f, $group, $limit = 8 ) {
	global $wpdb;
	$map = array(
		'source'   => array( 'Source', 'source' ),
		'channel'  => array( "COALESCE(NULLIF(UtmSource, ''), 'direct')", 'channel' ),
		'country'  => array( 'Country', 'country' ),
		'campaign' => array( 'UtmCampaign', 'campaign' ),
		'crm'      => array( 'CrmStatus', 'crm' ),
	);
	if ( ! isset( $map[ $group ] ) ) {
		return array();
	}
	list( $expr, $ignore ) = $map[ $group ];

	$table = native_leads_table();
	$where = native_leads_where_sql( $f, array( $ignore ) );
	if ( 'campaign' === $group ) {
		$where .= " AND UtmCampaign IS NOT NULL AND UtmCampaign <> ''";
	}
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- whitelisted expression, prepared where.
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT {$expr} AS k, COUNT(*) AS n FROM `{$table}` WHERE {$where} GROUP BY k ORDER BY n DESC, k ASC LIMIT %d", $limit ), ARRAY_A );
	return array_map( 'intval', wp_list_pluck( $rows, 'n', 'k' ) );
}

/**
 * Every distinct value of a column, for the filter dropdowns.
 *
 * @param string $key 'source', 'channel', 'country' or 'crm'.
 */
function native_leads_distinct_values( $key ) {
	global $wpdb;
	$columns = array(
		'source'  => 'Source',
		'channel' => 'UtmSource',
		'country' => 'Country',
		'crm'     => 'CrmStatus',
	);
	if ( ! isset( $columns[ $key ] ) ) {
		return array();
	}
	$column = $columns[ $key ];
	$table  = native_leads_table();
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	return $wpdb->get_col( "SELECT DISTINCT {$column} FROM `{$table}` WHERE {$column} IS NOT NULL AND {$column} <> '' ORDER BY {$column}" );
}

/**
 * Split the Description column ("Age: 34 | Occupation: Engineer | Contact:
 * whatsapp") into fields. Unlabelled parts are kept under 'note'.
 *
 * @param string|null $description Description column.
 * @return array{age:string,occupation:string,contact:string,note:string}
 */
function native_leads_parse_description( $description ) {
	$out = array(
		'age'        => '',
		'occupation' => '',
		'contact'    => '',
		'note'       => '',
	);
	$notes = array();
	foreach ( explode( '|', (string) $description ) as $part ) {
		$part = trim( $part );
		if ( '' === $part ) {
			continue;
		}
		if ( preg_match( '/^([A-Za-z ]+):\s*(.*)$/u', $part, $m ) ) {
			$key = strtolower( trim( $m[1] ) );
			if ( isset( $out[ $key ] ) && 'note' !== $key ) {
				$out[ $key ] = trim( $m[2] );
				continue;
			}
		}
		$notes[] = $part;
	}
	$out['note'] = implode( ' | ', $notes );
	return $out;
}

/**
 * "global-invest" -> "Global Invest".
 *
 * @param string $source Source slug.
 */
function native_leads_source_label( $source ) {
	return ucwords( str_replace( array( '-', '_' ), ' ', (string) $source ) );
}

/**
 * "snapchat" -> "Snapchat", '' / 'direct' -> "Direct".
 *
 * @param string $channel utm_source value.
 */
function native_leads_channel_label( $channel ) {
	if ( '' === (string) $channel || 'direct' === $channel ) {
		return __( 'Direct', 'native-leads' );
	}
	return ucwords( str_replace( array( '-', '_' ), ' ', $channel ) );
}

/**
 * Country name for a 2-letter code, for the markets these pages target.
 * Unknown codes are returned as they are.
 *
 * @param string $code Country code.
 */
function native_leads_country_name( $code ) {
	$names = array(
		'SA' => __( 'Saudi Arabia', 'native-leads' ),
		'AE' => __( 'United Arab Emirates', 'native-leads' ),
		'KW' => __( 'Kuwait', 'native-leads' ),
		'QA' => __( 'Qatar', 'native-leads' ),
		'BH' => __( 'Bahrain', 'native-leads' ),
		'OM' => __( 'Oman', 'native-leads' ),
		'EG' => __( 'Egypt', 'native-leads' ),
		'JO' => __( 'Jordan', 'native-leads' ),
		'IQ' => __( 'Iraq', 'native-leads' ),
		'MA' => __( 'Morocco', 'native-leads' ),
		'DZ' => __( 'Algeria', 'native-leads' ),
		'TN' => __( 'Tunisia', 'native-leads' ),
		'LB' => __( 'Lebanon', 'native-leads' ),
		'SD' => __( 'Sudan', 'native-leads' ),
		'LY' => __( 'Libya', 'native-leads' ),
		'YE' => __( 'Yemen', 'native-leads' ),
	);
	$code = strtoupper( (string) $code );
	return $names[ $code ] ?? $code;
}

/**
 * Dashboard URL with the current filters, changed by $args (null removes a key).
 *
 * @param array $f    Current filters.
 * @param array $args Overrides.
 */
function native_leads_dashboard_url( $f, $args = array() ) {
	$defaults = array(
		'range'   => 'all',
		'sort'    => 'date',
		'dir'     => 'desc',
		'pg'      => 1,
		'source'  => '',
		'channel'  => '',
		'campaign' => '',
		'country'  => '',
		'crm'     => '',
		'q'       => '',
		'from'    => '',
		'to'      => '',
	);
	$params   = array_merge( $f, $args );
	foreach ( $params as $key => $value ) {
		if ( null === $value || ( isset( $defaults[ $key ] ) && (string) $defaults[ $key ] === (string) $value ) ) {
			unset( $params[ $key ] );
		}
	}
	if ( 'custom' !== ( $params['range'] ?? 'all' ) ) {
		unset( $params['from'], $params['to'] );
	}
	return add_query_arg( array_map( 'rawurlencode', $params ), native_leads_dashboard_page_url() );
}

/**
 * Columns written to the CSV export, in order: header => row key.
 * CRM debug columns (response body, HTTP code) are left out.
 */
function native_leads_csv_columns() {
	return array(
		'Reference'    => 'AffTransactionId',
		'First name'   => 'FirstName',
		'Last name'    => 'LastName',
		'Email'        => 'Email',
		'Phone'        => 'Phone',
		'Country'      => 'Country',
		'Age'          => 'age',
		'Occupation'   => 'occupation',
		'Contact via'  => 'contact',
		'Landing page' => 'Source',
		'utm_source'   => 'UtmSource',
		'utm_medium'   => 'UtmMedium',
		'utm_campaign' => 'UtmCampaign',
		'utm_content'  => 'UtmContent',
		'utm_term'     => 'UtmTerm',
		'Campaign ID'  => 'Campaignid',
		'spcid'        => 'SubAffiliateId',
		'IP address'   => 'IpAddress',
		'IP country'   => 'IpCountry',
		'CRM status'   => 'CrmStatus',
		'CRM lead ID'  => 'CrmLeadId',
		'Received'     => 'CreatedDate',
	);
}

/**
 * Stream the filtered leads as CSV (?export=csv on the dashboard).
 */
function native_leads_maybe_export_csv() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- checked below.
	if ( ! native_leads_is_dashboard() || ! isset( $_GET['export'] ) ) {
		return;
	}
	if ( ! native_leads_can_view_leads() || ! check_admin_referer( 'native_leads_export' ) || ! native_leads_table_exists() ) {
		wp_die( esc_html__( 'You are not allowed to export leads.', 'native-leads' ), 403 );
	}

	$leads   = native_leads_query( native_leads_get_filters(), null )['rows'];
	$columns = native_leads_csv_columns();

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=leads-' . native_leads_now()->format( 'Y-m-d-His' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // BOM so Excel shows Arabic names correctly.
	fputcsv( $out, array_keys( $columns ) );
	foreach ( $leads as $lead ) {
		$lead += native_leads_parse_description( $lead['Description'] );
		$row   = array();
		foreach ( $columns as $key ) {
			$v = $lead[ $key ] ?? '';
			// Stop spreadsheet apps from running a cell as a formula.
			$row[] = is_string( $v ) && preg_match( '/^[=+\-@]/', $v ) ? "'" . $v : $v;
		}
		fputcsv( $out, $row );
	}
	fclose( $out );
	exit;
}
add_action( 'template_redirect', 'native_leads_maybe_export_csv' );

/**
 * A wrong password on the dashboard's sign-in form returns to that form
 * with an error instead of falling through to wp-login.php.
 */
function native_leads_login_failed_redirect() {
	$referer = wp_get_referer();
	$page_id = $referer ? url_to_postid( $referer ) : 0;
	if ( $page_id && NATIVE_LEADS_DASHBOARD_TEMPLATE === get_page_template_slug( $page_id ) ) {
		wp_safe_redirect( add_query_arg( 'login', 'failed', get_permalink( $page_id ) ) );
		exit;
	}
}
add_action( 'wp_login_failed', 'native_leads_login_failed_redirect' );

/**
 * Remind administrators to create the dashboard page until one exists.
 */
function native_leads_dashboard_page_notice() {
	if ( ! current_user_can( 'edit_pages' ) || native_leads_dashboard_page_url() ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Native Leads: no page uses the “Leads Dashboard” template yet, so the leads dashboard is not reachable.', 'native-leads' ),
		esc_url( admin_url( 'post-new.php?post_type=page' ) ),
		esc_html__( 'Create the dashboard page', 'native-leads' )
	);
}
add_action( 'admin_notices', 'native_leads_dashboard_page_notice' );

/**
 * The dashboard is a full-screen app: no admin bar over it.
 */
add_filter(
	'show_admin_bar',
	static function ( $show ) {
		return native_leads_is_dashboard() ? false : $show;
	}
);

/**
 * Keep the leads dashboard out of search engines and caches.
 */
add_action(
	'template_redirect',
	static function () {
		if ( native_leads_is_dashboard() ) {
			nocache_headers();
		}
	}
);
add_filter(
	'wp_robots',
	static function ( $robots ) {
		if ( native_leads_is_dashboard() ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}
		return $robots;
	}
);
