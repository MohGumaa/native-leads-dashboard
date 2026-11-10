<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package native-leads
 */

if ( ! function_exists( 'native_leads_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function native_leads_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'native-leads' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'native_leads_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function native_leads_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'native-leads' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'native_leads_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function native_leads_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'native-leads' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'native-leads' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'native-leads' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'native-leads' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'native-leads' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'native-leads' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'native_leads_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function native_leads_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

/**
 * Inline SVG icon (stroke style, 24px grid) used across the public templates.
 *
 * @param string $name Icon name.
 * @return string Markup, safe to echo.
 */
function native_leads_icon( $name ) {
	$paths = array(
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'back'     => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'    => '<path d="M18 6 6 18M6 6l12 12"/>',
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'chat'     => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 21l1.9-5.4A8 8 0 1 1 21 12z"/>',
		'chart'    => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
		'pulse'    => '<path d="M3 12h4l3-8 4 16 3-8h4"/>',
		'layers'   => '<path d="m12 3 9 5-9 5-9-5 9-5z"/><path d="m3 13 9 5 9-5"/>',
		'filter'   => '<path d="M3 5h18l-7 8v6l-4 2v-8L3 5z"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
		'download' => '<path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>',
		'lock'     => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
		'moon'     => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>',
		'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
		'database' => '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
		'home'     => '<path d="M3 11 12 3l9 8M5 9.5V21h14V9.5"/>',
		'doc'       => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9l-6-6z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
		'grid'      => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
		'users'     => '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M16 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6.3"/>',
		'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M4.9 4.9 7 7m10 10 2.1 2.1M2 12h3m14 0h3M4.9 19.1 7 17m10-10 2.1-2.1"/>',
		'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
		'send'      => '<path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/>',
		'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1zM15 8a5 5 0 0 1 0 8M18 5a9 9 0 0 1 0 14"/>',
		'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
		'refresh'   => '<path d="M21 12a9 9 0 1 1-2.6-6.4M21 3v6h-6"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'copy'      => '<rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v1"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="i-' . esc_attr( $name ) . '" viewBox="0 0 24 24" aria-hidden="true">' . $paths[ $name ] . '</svg>';
}

/**
 * Where the "dashboard" button in the header and on the front page goes,
 * and what it says, for the current visitor.
 *
 * @return array{url:string,label:string}|null Null when no dashboard page exists yet.
 */
function native_leads_dashboard_cta() {
	$url = native_leads_dashboard_page_url();
	if ( ! $url ) {
		return null;
	}
	return array(
		'url'   => $url,
		'label' => is_user_logged_in() ? __( 'Open dashboard', 'native-leads' ) : __( 'Sign in', 'native-leads' ),
	);
}

/**
 * Default links for the primary and footer menus until a menu is assigned
 * in Appearance → Menus: home plus the front page's sections.
 *
 * @param array $args wp_nav_menu() arguments.
 */
function native_leads_menu_fallback( $args ) {
	$links = array(
		array( home_url( '/' ), __( 'Home', 'native-leads' ), is_front_page() ),
		array( home_url( '/#features' ), __( 'Features', 'native-leads' ), false ),
		array( home_url( '/#how-it-works' ), __( 'How it works', 'native-leads' ), false ),
		array( home_url( '/#security' ), __( 'Security', 'native-leads' ), false ),
	);
	if ( 'footer' === $args['theme_location'] && native_leads_dashboard_page_url() ) {
		$links[] = array( native_leads_dashboard_page_url(), __( 'Dashboard', 'native-leads' ), false );
	}

	echo '<ul' . ( $args['menu_id'] ? ' id="' . esc_attr( $args['menu_id'] ) . '"' : '' ) . ' class="' . esc_attr( $args['menu_class'] ) . '">';
	foreach ( $links as $link ) {
		printf(
			'<li class="menu-item%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			$link[2] ? ' current-menu-item' : '',
			esc_url( $link[0] ),
			$link[2] ? ' aria-current="page"' : '',
			esc_html( $link[1] )
		);
	}
	echo '</ul>';
}

/**
 * Date · author · reading time row for posts.
 */
function native_leads_entry_meta() {
	// Split on whitespace: str_word_count() does not count Arabic words.
	$words   = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	$minutes = max( 1, (int) round( $words / 220 ) );
	?>
	<div class="entry-meta">
		<span><?php echo native_leads_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
		<span><?php echo native_leads_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></span>
		<span>
			<?php
			echo native_leads_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			/* translators: %s: minutes */
			echo esc_html( sprintf( _n( '%s min read', '%s min read', $minutes, 'native-leads' ), number_format_i18n( $minutes ) ) );
			?>
		</span>
	</div>
	<?php
}

/**
 * First category of the current post as a small pill link.
 */
function native_leads_category_pill() {
	$cats = get_the_category();
	if ( $cats ) {
		printf( '<a class="pill" href="%s">%s</a>', esc_url( get_category_link( $cats[0] ) ), esc_html( $cats[0]->name ) );
	}
}

/**
 * Numbered pagination for post lists, styled like the dashboard pager.
 */
function native_leads_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => esc_html__( '← Prev', 'native-leads' ),
			'next_text'          => esc_html__( 'Next →', 'native-leads' ),
			'screen_reader_text' => esc_html__( 'Posts navigation', 'native-leads' ),
		)
	);
}

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
