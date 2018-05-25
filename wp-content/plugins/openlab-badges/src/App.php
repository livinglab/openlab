<?php

namespace OpenLab\Badges;

class App {
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
	}

	public static function register_taxonomy() {
		$labels = array(
			'name'               => __( 'Badges', 'openlab-badges' ),
			'all_items'          => __( 'All Badges', 'openlab-badges' ),
			'singular_name'      => __( 'Badge', 'openlab-badges' ),
			'add_new_item'       => __( 'Add New Badge', 'openlab-badges' ),
			'edit_item'          => __( 'Edit Badge', 'openlab-badges' ),
			'new_item'           => __( 'New Badge', 'openlab-badges' ),
			'view_item'          => __( 'View Badge', 'openlab-badges' ),
			'search_items'       => __( 'Search Badges', 'openlab-badges' ),
		);

		register_taxonomy( 'openlab_badge', 'group', array(
			'label'     => __( 'Badges', 'openlab-badges' ),
			'public'    => false,
			'show_ui'   => true,
			'labels'    => $labels,
			'menu_icon' => 'dashicons-shield',
		) );
	}
}
