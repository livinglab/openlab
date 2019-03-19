<?php

/**
 * Adds custom embeds
 */

/**
 * Adds [openprocessing] shortcode
 */
function openlab_openprocessing_shortcode( $atts ) {
	$html = '';

	$errors = array();
	foreach ( array( 'id', 'height', 'width' ) as $key ) {
		$v = isset( $atts[ $key ] ) ? (int) $atts[ $key ] : 0;
		if ( ! $v ) {
			switch ( $key ) {
				case 'id':
					$errors[] = 'You must provide the numeric id of the openprocessing.org sketch. In a URL like "http://openprocessing.org/sketch/12345", the id is <strong>12345</strong>';
					break;

				default:
					$errors[] = sprintf( 'You didn&#8217;t provide the following necessary attribute: <strong>%s</strong>', $key );
					break;
			}
		}
	}

	if ( empty( $errors ) ) {
		$height = (int) $atts['height'];
		$width  = (int) $atts['width'];
		$html   = sprintf(
			'<iframe width="%s" height="%s" scrolling="no" frameborder="0" src="http://www.openprocessing.org/sketch/%s/embed/?width=%s&height=%s&border=true"></iframe>',
			$width + 28, // iframe needs padding
			$height + 50,
			(int) $atts['id'],
			$width,
			$height
		);
	} else {
		$estring = '';
		foreach ( $errors as $e ) {
			$estring .= '<li>' . $e . '</li>';
		}

		$html = sprintf( '<em>Your openprocessing.org sketch could not be displayed, because of the following errors: <ul>%s</ul></em>', $estring );
	}

	return $html;
}
add_shortcode( 'openprocessing', 'openlab_openprocessing_shortcode' );

/**
 * Register auto-embed handlers.
 */
function openlab_register_embed_handlers() {
	wp_embed_register_handler( 'screencast', '#https?://([^\.]+)\.screencast\.com/#i', 'openlab_embed_handler_screencast' );

	wp_embed_register_handler(
		'pinterest',
		'#'
		. 'https?://'
		. '(?:www\.)?'
		. '(?:[a-z]{2}\.)?'
		. 'pinterest\.[a-z.]+/'
		. '([^/]+)'
		. '(/[^/]+)?'
		. '#',
		'openlab_pinterest_embed_handler'
	);
}
add_action( 'init', 'openlab_register_embed_handlers' );

/**
 * screencast.com embed callback.
 */
function openlab_embed_handler_screencast( $matches, $attr, $url, $rawattr ) {
	$cached = wp_cache_get( 'screencast_embed_url_v2_' . $url );
	if ( false === $cached ) {
		// This is the worst thing in the whole world.
		$r = wp_remote_get( $url );
		$b = wp_remote_retrieve_body( $r );
		$b = htmlspecialchars_decode( $b );

		$embed_url = '';
		if ( preg_match( '|<iframe[^>]+src="([^"]+screencast\.com[^"]+)"|', $b, $url_matches ) ) {
			$embed_url = str_replace( '/tsc_player.swf', '', $url_matches[1] );
		}

		wp_cache_set( 'screencast_embed_url_v2_' . $url, $embed_url );
	} else {
		$embed_url = $cached;
	}

	// Get height/width from URL params, if available.
	$height = 450;
	$width  = 800;
	$query  = parse_url( $url, PHP_URL_QUERY );
	if ( $query ) {
		parse_str( $query, $parts );

		if ( $parts['height'] ) {
			$height = intval( $parts['height'] );
		}

		if ( $parts['width'] ) {
			$width = intval( $parts['width'] );
		}
	}

	$template = '<iframe class="tscplayer_inline embeddedObject" name="tsc_player" scrolling="no" frameborder="0" type="text/html" style="overflow:hidden;" src="%s" height="%s" width="%s" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

	$html = sprintf( $template, set_url_scheme( $embed_url ), $height, $width );
	return $html;
}

/**
 * Stolen from Jetpack.
 */
function openlab_pinterest_embed_handler( $matches, $attr, $url ) {
	// Pinterest's JS handles making the embed
	$script_src = '//assets.pinterest.com/js/pinit.js';
	wp_enqueue_script( 'pinterest-embed', $script_src, array(), false, true );

	$path = parse_url( $url, PHP_URL_PATH );
	if ( 0 === strpos( $path, '/pin/' ) ) {
		$embed_type = 'embedPin';
	} elseif ( 0 === strpos( $path, '/topics/' ) ) {
		// Pinterest oEmbed doesn't support topics.
		return $url;
	} elseif ( preg_match( '#^/([^/]+)/?$#', $path ) ) {
		$embed_type = 'embedUser';
	} elseif ( preg_match( '#^/([^/]+)/([^/]+)/?$#', $path ) ) {
		$embed_type = 'embedBoard';
	} else {
		if ( current_user_can( 'edit_posts' ) ) {
			return __( 'Sorry, that Pinterest URL was not recognized.', 'jetpack' );
		}
		return;
	}

	$return = sprintf( '<a data-pin-do="%s" href="%s"></a>', esc_attr( $embed_type ), esc_url( $url ) );

	// If we're generating an embed view for the WordPress Admin via ajax...
	if ( doing_action( 'wp_ajax_parse-embed' ) ) {
		$return .= sprintf( '<script src="%s"></script>', esc_url( $script_src ) );
	}

	return $return;
}
