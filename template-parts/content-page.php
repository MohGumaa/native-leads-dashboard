<?php
/**
 * Template part: page content in page.php.
 *
 * Long pages (six or more h2 headings, e.g. Terms or Privacy) get a sticky
 * table of contents beside the text and a "last updated" date.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package native-leads
 */

$native_leads_toc     = native_leads_page_toc( get_the_ID() );
$native_leads_long    = count( $native_leads_toc ) >= 6;
$native_leads_parent  = wp_get_post_parent_id( get_the_ID() );
$native_leads_contact = native_leads_page( 'contact-us' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="page-head">
		<div class="page-head__bg" aria-hidden="true"></div>
		<div class="container page-head__inner">
			<nav class="crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'native-leads' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'native-leads' ); ?></a>
				<?php if ( $native_leads_parent ) : ?>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_permalink( $native_leads_parent ) ); ?>"><?php echo esc_html( get_the_title( $native_leads_parent ) ); ?></a>
				<?php endif; ?>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<?php the_title( '<h1 class="page-head__title">', '</h1>' ); ?>
			<?php if ( has_excerpt() ) : ?>
				<p class="page-head__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<?php if ( $native_leads_long ) : ?>
				<p class="page-head__meta">
					<?php echo native_leads_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php
					printf(
						/* translators: %s: date */
						esc_html__( 'Last updated %s', 'native-leads' ),
						'<time datetime="' . esc_attr( get_the_modified_date( DATE_W3C ) ) . '">' . esc_html( get_the_modified_date() ) . '</time>'
					);
					?>
				</p>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
		<figure class="container entry__cover">
			<?php the_post_thumbnail( 'full' ); ?>
		</figure>
	<?php endif; ?>

	<div class="container page-body<?php echo $native_leads_long ? ' page-body--toc' : ''; ?>">
		<?php if ( $native_leads_long ) : ?>
			<aside class="toc" aria-labelledby="toc-title">
				<p id="toc-title" class="toc__title"><?php esc_html_e( 'On this page', 'native-leads' ); ?></p>
				<ol>
					<?php foreach ( $native_leads_toc as $native_leads_item ) : ?>
						<li><a href="#<?php echo esc_attr( $native_leads_item['id'] ); ?>"><?php echo esc_html( $native_leads_item['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ol>
			</aside>
		<?php endif; ?>

		<div class="page-body__main">
			<div class="prose">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'native-leads' ),
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php if ( $native_leads_contact && ! is_page( $native_leads_contact->ID ) ) : ?>
				<aside class="help-card">
					<span class="help-card__icon"><?php echo native_leads_icon( 'chat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<div>
						<h2><?php esc_html_e( 'Still have a question?', 'native-leads' ); ?></h2>
						<p class="muted"><?php esc_html_e( 'Our team is happy to help with campaigns, partnerships or your data.', 'native-leads' ); ?></p>
					</div>
					<a class="btn btn--primary" href="<?php echo esc_url( get_permalink( $native_leads_contact ) ); ?>"><?php esc_html_e( 'Contact us', 'native-leads' ); ?> <?php echo native_leads_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</aside>
			<?php endif; ?>

			<?php if ( get_edit_post_link() ) : ?>
				<footer class="entry__footer">
					<?php edit_post_link( esc_html__( 'Edit page', 'native-leads' ), '<span class="edit-link">', '</span>' ); ?>
				</footer>
			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
