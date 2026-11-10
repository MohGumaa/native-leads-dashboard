<?php
/**
 * native-leads functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package native-leads
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function native_leads_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on native-leads, use a find and replace
		* to change 'native-leads' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'native-leads', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'native-leads' ),
			'footer' => esc_html__( 'Footer', 'native-leads' ),
		)
	);

	// Block content: core block styles, wide/full images, responsive embeds.
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'native_leads_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function native_leads_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'native_leads_content_width', 760 );
}
add_action( 'after_setup_theme', 'native_leads_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function native_leads_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'native-leads' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'native-leads' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'native_leads_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function native_leads_scripts() {
	$uri = get_template_directory_uri();

	// Tokens, buttons and brand shared by the dashboard and the public site.
	wp_enqueue_style( 'native-leads-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Tajawal:wght@400;500;700&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'native-leads-base', $uri . '/assets/css/base.css', array( 'native-leads-fonts' ), _S_VERSION );

	// The leads dashboard page (or its sign-in screen) is a full-screen app.
	if ( native_leads_is_dashboard() ) {
		wp_enqueue_style( 'native-leads-dashboard', $uri . '/assets/css/dashboard.css', array( 'native-leads-base' ), _S_VERSION );
		wp_enqueue_script( 'native-leads-dashboard', $uri . '/assets/js/dashboard.js', array(), _S_VERSION, true );
		return;
	}

	wp_enqueue_style( 'native-leads-site', $uri . '/assets/css/site.css', array( 'native-leads-base' ), _S_VERSION );
	wp_enqueue_script( 'native-leads-site', $uri . '/assets/js/site.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'native_leads_scripts' );

/**
 * Apply the saved light/dark choice before first paint, on every screen.
 */
function native_leads_theme_boot() {
	?>
	<meta name="color-scheme" content="dark light">
	<script>
		document.documentElement.classList.add( 'js' );
		try { var t = localStorage.getItem( 'native-leads-theme' ); if ( t ) { document.documentElement.dataset.theme = t; } } catch ( e ) {}
	</script>
	<?php
}
add_action( 'wp_head', 'native_leads_theme_boot', 0 );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Leads dashboard: data queries, access and CSV export.
 */
require get_template_directory() . '/inc/leads.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

