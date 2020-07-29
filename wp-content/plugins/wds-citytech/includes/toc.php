<?php
/**
 * Modifications for Easy TOC plugin.
 */

namespace OpenLab\TOC;

const VERSION = '1.2.1';

/**
 * Inject the entry title right before the widget is rendered.
 *
 * @param string $title The widget title.
 * @param array $args   The remaining arguments.
 */
add_filter( 'widget_title', function( $title = '', ...$args ) {
	// Check for `$id_base` of ToC widget.
	if ( ! isset( $args[1] ) || 'ezw_tco' !== $args[1] ) {
		return $title;
	}

	// Provide default title fallback.
	if ( empty( $title ) ) {
		$title = 'Contents';
	}

	add_filter( 'ez_toc_extract_headings_content', __NAMESPACE__ . '\\prepend_title' );

	return $title;
}, 10, 3 );

/**
 * Reprend entry title to the content.
 * Allows to be parsed as ToC.
 *
 * @param string $content
 * @return string
 */
function prepend_title( $content ) {
	return sprintf( '<h1 class="entry-title">%s</h1>%s', get_the_title(), $content );
};

/**
 * Adds the 'id' attribute that is used by ToC.
 *
 * @param  array $attributes
 * @return array attributes
 */
function add_anchor( $attributes ) {
	$attributes['id'] = sanitize_title_with_dashes( get_the_title() );

	return $attributes;
}
add_filter( 'genesis_attr_entry-title', __NAMESPACE__ . '\\add_anchor' );

/**
 * Don't load default Easy TOC script.
 *
 * @return void
 */
function deregister_default_script() {
	wp_deregister_script( 'ez-toc-js' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\deregister_default_script', 20 );

/**
 * Conditionally enqueue our assets when the widget is used.
 *
 * @return void
 */
function enqueue_assets() {
	wp_enqueue_script(
		'openalab-toc-script',
		plugins_url( 'assets/js/openlab-toc.js', __DIR__ ),
		[ 'jquery' ],
		VERSION,
		true
	);

	wp_enqueue_style(
		'openalab-toc-style',
		plugins_url( 'assets/css/openlab-toc.css', __DIR__ ),
		[],
		VERSION
	);
}
add_action( 'ez_toc_after_widget', __NAMESPACE__ . '\\enqueue_assets' );
