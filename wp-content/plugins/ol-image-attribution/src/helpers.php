<?php
/**
 * Helper functions.
 */

namespace OpenLab\ImageAttribution\Helpers;

/**
 * Get list of licenses.
 *
 * @return array $licenses
 */
function get_licenses() {
	$licenses = [
		'u' => [
			'label' => 'Unknown',
			'url'   => '',
		],
		'c' => [
			'label' => 'Standard Copyright',
			'url'   => '',
		],
		'pd' => [
			'label' => 'Public Domain',
			'url'   => '',
		],
		'fu' => [
			'label' => 'Fair Use',
			'url'   => '',
		],
		'cc-by' => [
			'label' => 'CC BY',
			'url'   => 'https://creativecommons.org/licenses/by/4.0',
		],
		'cc-by-sa' => [
			'label' => 'CC BY-SA',
			'url'   => 'https://creativecommons.org/licenses/by-sa/4.0',
		],
		'cc-by-nd' => [
			'label' => 'CC BY-ND',
			'url'   => 'https://creativecommons.org/licenses/by-nd/4.0',
		],
		'cc-by-nc' => [
			'label' => 'CC BY-NC',
			'url'   => 'https://creativecommons.org/licenses/by-nc/4.0',
		],
		'cc-by-nc-sa' => [
			'label' => 'CC BY-NC-SA',
			'url'   => 'https://creativecommons.org/licenses/by-nc-sa/4.0',
		],
		'cc-by-nc-nd' => [
			'label' => 'CC BY-NC-ND',
			'url'   => 'https://creativecommons.org/licenses/by-nc-nd/4.0',
		],
	];

	return $licenses;
}

/**
 * Output select element for licenses.
 *
 * @param \WP_Post $post
 * @return string $html
 */
function get_licenses_select( \WP_Post $post ) {
	$license  = get_post_meta( $post->ID, '_wp_attachment_license', true );
	$licenses = get_licenses();

	$html = '<select name="attachments[' . $post->ID . '][license]" id="attachments[' . $post->ID . '][license]">';

	foreach ( $licenses as $id => $data ) {
		$html .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $id ),
			selected( $license, $id, false ),
			esc_html( $data['label'] )
		);
	}

	$html .= '</select>';

	return $html;
}

/**
 * Generate anchor link or just return title.
 *
 * @param string $title
 * @param string $url
 * @return string
 */
function get_the_link_or_title( $title, $url ) {
	if ( empty( $url ) ) {
		return $title;
	}

	return sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $title ) );
}

/**
 * Generate image attribution HTML.
 *
 * @param int $post_id
 * @return string
 */
function get_the_image_attribution( $post_id ) {
	$post  = get_post( $post_id );
	$parts = [];

	// Get image attribution data.
	$data_author     = get_post_meta( $post->ID, '_wp_attachment_author', true );
	$data_author_uri = get_post_meta( $post->ID, '_wp_attachment_author_uri', true );
	$license_id      = get_post_meta( $post->ID, '_wp_attachment_license', true );

	if ( ! empty( $post->post_title ) ) {
		$title = get_the_link_or_title(
			$post->post_title,
			wp_get_attachment_url( $post->ID)
		);

		$parts['title'] = sprintf( "%s", $title );
	}

	if ( ! empty( $data_author ) ) {
		$author = get_the_link_or_title( $data_author, $data_author_uri );

		$parts['author'] = sprintf( 'by %s', $author );
	}

	$licenses = get_licenses();
	if ( ! empty( $licenses[ $license_id ] ) ) {
		$license = get_the_link_or_title(
			$licenses[ $license_id ]['label'],
			$licenses[ $license_id ]['url']
		);

		$parts['license'] = sprintf( 'is licensed under %s.', $license );
	}

	return implode( ' ', $parts );
}

/**
 * Parse post content and extract used image IDs.
 *
 * Based on wp_make_content_images_responsive().
 *
 * @param int    $post_id
 * @param string $content
 * @return array $ids
 */
function get_the_attached_image_ids( $post_id, $content ) {
	$cached = get_post_meta( $post_id, '_wp_attached_media_cache', true );

	if ( $cached ) {
		return $cached;
	}

	if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) ) {
		return $content;
	}

	$ids = [];
	foreach ( $matches['0'] as $image ) {
		// Extract attachment ID from class name.
		if ( preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) ) {
			$ids[] = (int) $class_id[1];
		}
	}

	$ids = array_unique( array_filter( $ids ) );

	// Cache results.
	update_post_meta( $post_id, '_wp_attached_media_cache', $ids );

	return $ids;
}
