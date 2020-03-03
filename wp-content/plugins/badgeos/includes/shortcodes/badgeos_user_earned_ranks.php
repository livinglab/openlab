<?php
/**
 * Register [badgeos_user_earned_ranks] shortcode.
 *
 * @since 1.4.0
 */
function badgeos_user_earned_ranks_shortcode() {
    global $wpdb;
    // Setup a custom array of rank types
    $badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
    $rank_types = get_posts( array(
        'post_type'      =>	$badgeos_settings['ranks_main_post_type'],
        'posts_per_page' =>	-1,
    ) );

    $types = array(  );
    foreach( $rank_types as $type ) {
        $types[ $type->post_name ] = $type->post_title;
    }

    $posts = get_posts();
    $post_list = array();
    foreach( $posts as $post ) {
        $post_list[ $post->ID ] = $post->post_title;
    }

    badgeos_register_shortcode( array(
        'name'            => __( 'User Earned Ranks', 'badgeos' ),
        'description'     => __( 'Output a list of Ranks.', 'badgeos' ),
        'slug'            => 'badgeos_user_earned_ranks',
        'output_callback' => 'badgeos_earned_ranks_shortcode',
        'attributes'      => array(
            'rank_type' => array(
                'name'        => __( 'Rank Type(s)', 'badgeos' ),
                'description' => __( 'Single, or comma-separated list of, Rank type(s) to display.', 'badgeos' ),
                'type'        => 'select',
                'values'      => $types,
                'default'     => '',
            ),
            'limit' => array(
                'name'        => __( 'Limit', 'badgeos' ),
                'description' => __( 'Number of Ranks to display.', 'badgeos' ),
                'type'        => 'text',
                'default'     => 10,
            ),
            'show_search' => array(
                'name'        => __( 'Show Search', 'badgeos' ),
                'description' => __( 'Display a search input.', 'badgeos' ),
                'type'        => 'select',
                'values'      => array(
                    'true'  => __( 'True', 'badgeos' ),
                    'false' => __( 'False', 'badgeos' )
                ),
                'default'     => 'true',
            ),
            'orderby' => array(
                'name'        => __( 'Order By', 'badgeos' ),
                'description' => __( 'Parameter to use for sorting.', 'badgeos' ),
                'type'        => 'select',
                'values'      => array(
                    'rank_id'         => __( 'Rank ID', 'badgeos' ),
                    'rank_title'      => __( 'Rank Title', 'badgeos' ),
                    'dateadded'       => __( 'Award Date', 'badgeos' ),
                    'rand()'       => __( 'Random', 'badgeos' ),
                ),
                'default'     => 'menu_order',
            ),
            'order' => array(
                'name'        => __( 'Order', 'badgeos' ),
                'description' => __( 'Sort order.', 'badgeos' ),
                'type'        => 'select',
                'values'      => array( 'ASC' => __( 'Ascending', 'badgeos' ), 'DESC' => __( 'Descending', 'badgeos' ) ),
                'default'     => 'ASC',
            ),
        ),
    ) );
}
add_action( 'init', 'badgeos_user_earned_ranks_shortcode' );

/**
 * Earned rank List Shortcode.
 *
 * @since  1.0.0
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function badgeos_earned_ranks_shortcode( $atts = array () ){

    $key = 'badgeos_user_earned_ranks';
    if( is_array( $atts ) && count( $atts ) > 0 ) {
        foreach( $atts as $index => $value ) {
            $key .= "_".strval( $value ) ;
        }
    }

    global $user_ID;
    extract( shortcode_atts( array(
        'rank_type'   => 'all',
        'limit'       => '10',
        'show_search' => true,
        'user_id'     => get_current_user_id(),
        'orderby'     => 'ID',
        'order'       => 'ASC'
    ), $atts, 'badgeos_user_earned_ranks' ) );

    wp_enqueue_style( 'badgeos-front' );
    wp_enqueue_script( 'badgeos-achievements' );

    if ( 'all' == $rank_type ) {
        $post_type_plural = __( 'User Earned Ranks', 'badgeos' );
    } else {
        $types = explode( ',', $rank_type );
        $badge_post_type = get_post_type_object( $types[0] );
        $type_name = '';
        if( $badge_post_type ) {
            if( isset( $badge_post_type->labels ) ) {
                if( isset( $badge_post_type->labels->name ) ) {
                    $type_name = $badge_post_type->labels->name;
                }
            }
        }
        $post_type_plural = ( 1 == count( $types ) && !empty( $types[0] ) ) ? $type_name : __( 'User Earned ranks', 'badgeos' );
    }

    $ranks_html = '';

    $ranks_html .= '<div id="badgeos-ranks-filters-wrap">';
    // Search
    if ( $show_search != 'false' ) {

        $search = isset( $_POST['earned_ranks_list_search'] ) ? $_POST['earned_ranks_list_search'] : '';
        $ranks_html .= '<div id="badgeos-ranks-search">';
        $ranks_html .= '<form id="earned_ranks_list_search_go_form" class="earned_ranks_list_search_go_form" action="'. get_permalink( get_the_ID() ) .'" method="post">';
        $ranks_html .= sprintf( __( 'Search: %s', 'badgeos' ), '<input type="text" id="earned_ranks_list_search" name="earned_ranks_list_search" class="earned_ranks_list_search" value="'. $search .'">' );
        $ranks_html .= '<input type="button" id="earned_ranks_list_search_go" name="earned_ranks_list_search_go" class="earned_ranks_list_search_go" value="' . esc_attr__( 'Go', 'badgeos' ) . '">';
        $ranks_html .= '</form>';
        $ranks_html .= '</div>';
    }

    $ranks_html .= '</div><!-- #badgeos-ranks-filters-wrap -->';

    // Content Container
    $ranks_html .= '<div id="badgeos-earned-ranks-container"></div>';

    // Hidden fields and Load More button
    $ranks_html .= '<input type="hidden" class="badgeos_earned_ranks_offset" id="badgeos_earned_ranks_offset" value="0">';
    $ranks_html .= '<input type="hidden" class="badgeos_ranks_count"  id="badgeos_ranks_count" value="0">';
    $ranks_html .= '<input type="button" class="earned_ranks_list_load_more" value="' . esc_attr__( 'Load More', 'badgeos' ) . '" style="display:none;">';
    $ranks_html .= '<div class="badgeos-earned-ranks-spinner"></div>';

    $maindiv = '<div class="badgeos_earned_rank_main_container" data-url="'.esc_url( admin_url( 'admin-ajax.php', 'relative' ) ).'" data-rank_type="'.$rank_type.'" data-limit="'.$limit.'" data-show_search="'.$show_search.'" data-user_id="'.$user_id.'" data-orderby="'.$orderby.'" data-order="'.$order.'">';
    $maindiv .= $ranks_html;
    $maindiv .= '</div>';


    // Reset Post Data
    wp_reset_postdata();

    // Save a global to prohibit multiple shortcodes
    return $maindiv;
}