<?php

/**
 * Load custom scripts.
 */
add_action(
	'wp_enqueue_scripts',
	function() {
		wp_enqueue_script( 'openlab-education-pro', content_url( 'mu-plugins/theme-fixes/education-pro/education-pro.js', array( 'jquery' ) ) );
	}
);

/**
 * Disable auto-update support for the theme.
 *
 * We manage the theme independently. This also prevents 'Updates' section from appearing
 * on the theme's Settings panel.
 */
remove_theme_support( 'genesis-auto-updates' );

/**
 * Remove unused Settings metaboxes.
 */
add_action(
	'load-toplevel_page_genesis',
	function() {
		remove_meta_box( 'genesis-theme-settings-adsense', get_current_screen(), 'main'  );
		remove_meta_box( 'genesis-theme-settings-scripts', get_current_screen(), 'main'  );
	},
	50
);

/**
 * Move Genesis 'Theme Settings' Customizer panel higher in the order.
 */
add_filter(
	'genesis_customizer_theme_settings_config',
	function( $config ) {
		$config['genesis']['priority'] = 25;
		return $config;
	}
);

/**
 * More Customizer mods.
 */
add_action(
	'customize_register',
	function( $wp_customize ) {
		// Reordering.
		$wp_customize->add_section( 'static_front_page', array(
			'title'          => __( 'Homepage Settings' ),
			'priority'       => 65,
			'description'    => __( 'You can choose what&#8217;s displayed on the homepage of your site. It can be posts in reverse chronological order (classic blog), or a fixed/static page. To set a static homepage, you first need to create two Pages. One will become the homepage, and the other will be where your posts are displayed.' ),
			'active_callback' => array( $wp_customize, 'has_published_pages' ),
		) );

		$wp_customize->add_section( 'colors', array(
			'title'    => __( 'Background Color' ),
			'priority' => 120,
		) );

		$wp_customize->add_section( 'background_image', array(
			'title'          => __( 'Background Image' ),
			'theme_supports' => 'custom-background',
			'priority'       => 130,
		) );

		$wp_customize->remove_section( 'custom_css' );

		// 'Theme Settings' subsections.
		$wp_customize->remove_section( 'genesis_adsense' );
		$wp_customize->remove_section( 'genesis_scripts' );
	}
);

// Add support for additional color style options.
remove_theme_support( 'genesis-style-selector' );
add_theme_support( 'genesis-style-selector', array(
	'education-pro-blue'   => 'Blue',
	'education-pro-green'  => 'Green',
	'education-pro-red'    => 'Red',
) );

// Convert "Header Right" widget area to a nav area.
unregister_sidebar( 'header-right' );
register_nav_menu( 'title-menu', 'Main Nav' );
add_action(
	'genesis_header_right',
	function() {
		add_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
		add_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
		echo genesis_get_nav_menu( [ 'theme_location' => 'title-menu' ] );
		remove_filter( 'wp_nav_menu_args', 'genesis_header_menu_args' );
		remove_filter( 'wp_nav_menu', 'genesis_header_menu_wrap' );
	}
);

add_filter(
	'genesis_get_layouts',
	function( $layouts ) {
		$keys = [ 'content-sidebar', 'sidebar-content', 'full-width-content' ];
		return array_filter(
			$layouts,
			function( $k ) use ( $keys ) {
				return in_array( $k, $keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
);

remove_theme_support( 'genesis-footer-widgets' );
$deregister_sidebars = [ 'home-featured', 'home-top', 'home-middle', 'home-bottom', 'sidebar-alt' ];
foreach ( $deregister_sidebars as $deregister_sidebar ) {
	unregister_sidebar( $deregister_sidebar );
}

/**
 * Modify Genesis default nav areas.
 *
 * - Rename 'primary'.
 * - Remove 'secondary' (footer menu).
 *
 * Must come after 'after_setup_theme' to follow Genesis nav registration.
 */
add_action(
	'after_setup_theme',
	function() {
		register_nav_menu( 'primary', 'Top Menu' );
		unregister_nav_menu( 'secondary' );
	},
	20
);

/**
 * Don't add dynamic nav items in the 'title-menu' location.
 */
add_filter(
	'openlab_add_dynamic_nav_items',
	function( $retval, $args ) {
		if ( 'title-menu' === $args->theme_location ) {
			$retval = false;
		}

		return $retval;
	},
	10,
	2
);

register_default_headers( [
	'circles' => [
		'url'           => content_url( 'mu-plugins/theme-fixes/education-pro/images/1circles.png' ),
		'thumbnail_url' => content_url( 'mu-plugins/theme-fixes/education-pro/images/1circles.png' ),
		'description'   => 'Circles',
	],
] );

add_action(
	'wp_head',
	function() {
		$print_css_url = content_url( 'mu-plugins/theme-fixes/education-pro/print.css' );
		?>
<link rel="stylesheet" href="<?php echo esc_attr( $print_css_url ); ?>" type="text/css" media="print" />
		<?php
	}
);

remove_action( 'wp_head', 'genesis_custom_header_style' );
add_action( 'wp_head', 'openlab_custom_header_style' );
/**
 * Custom header callback.
 *
 * It outputs special CSS to the document head, modifying the look of the header based on user input.
 *
 * @since 1.6.0
 *
 * @return void Return early if `custom-header` not supported, user specified own callback, or no options set.
 */
function openlab_custom_header_style() {

	// Do nothing if custom header not supported.
	if ( ! current_theme_supports( 'custom-header' ) ) {
		return;
	}

	// Do nothing if user specifies their own callback.
	if ( get_theme_support( 'custom-header', 'wp-head-callback' ) ) {
		return;
	}

	$output = '';

	$header_image = get_header_image();
	$text_color   = get_header_textcolor();

	// If no options set, don't waste the output. Do nothing.
	if ( empty( $header_image ) && ! display_header_text() && get_theme_support( 'custom-header', 'default-text-color' ) === $text_color ) {
		return;
	}

	$header_selector = get_theme_support( 'custom-header', 'header-selector' );
	$title_selector  = genesis_html5() ? '.custom-header .site-title' : '.custom-header #title';
	$desc_selector   = genesis_html5() ? '.custom-header .site-description' : '.custom-header #description';

	// Header selector fallback.
	if ( ! $header_selector ) {
		$header_selector = genesis_html5() ? '.custom-header .site-header' : '.custom-header #header';
	}

	$gradient = 'rgba(170,36,24,0.7)';
	$scheme   = genesis_get_option( 'style_selection' );
	switch ( $scheme ) {
		case 'education-pro-blue' :
			$gradient = 'rgba(186,208,222,0.7)';
		break;

		case 'education-pro-red' :
			$gradient = 'rgba(219,47,31,0.7)';
		break;

		case 'education-pro-green' :
			$gradient = 'rgba(209,222,186,0.7)';
		break;
	}

	// Header image CSS, if exists.
	if ( $header_image ) {
		$output .= sprintf( '%s { background: linear-gradient( %s, %s ), url(%s) no-repeat !important; }', $header_selector, $gradient, $gradient, esc_url( $header_image ) );
	}

	// Header text color CSS, if showing text.
	if ( display_header_text() && get_theme_support( 'custom-header', 'default-text-color' ) !== $text_color ) {
		$output .= sprintf( '%2$s a, %2$s a:hover, %3$s { color: #%1$s !important; }', esc_html( $text_color ), esc_html( $title_selector ), esc_html( $desc_selector ) );
	}

	if ( $output ) {
		printf( '<style type="text/css">%s</style>' . "\n", $output );
	}

}
