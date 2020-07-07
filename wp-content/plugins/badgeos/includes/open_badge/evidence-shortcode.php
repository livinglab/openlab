<?php
/**
 * Register the [badgeos_achievement] shortcode.
 */
function badgeos_register_ob_evidence_shortcode() {
	badgeos_register_shortcode( array(
		'name'            => __( 'Achievement Evidence', 'badgeos' ),
		'slug'            => 'badgeos_evidence',
		'output_callback' => 'badgeos_openbadge_evidence_shortcode',
		'description'     => __( "Render a single achievement's evidence.", 'badgeos' ),
		'attributes'      => array(
			
		),
	) );
}
add_action( 'init', 'badgeos_register_ob_evidence_shortcode' );

/**
 * Single Achievement Shortcode.
 *
 * @param  array $atts Shortcode attributes.
 * @return string 	   HTML markup.
 */
function badgeos_openbadge_evidence_shortcode( $atts = array() ) {

    global $wpdb;
    
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    
    /**
     * get the post id
     */
	$atts = shortcode_atts( array(
	  'show_sharing_opt' => 'Yes',
	), $atts, 'badgeos_evidence' ); 
    
    $achievement_id = 0;
    if( ! empty( $_REQUEST['bg'] ) ) {
        $achievement_id 	= sanitize_text_field( $_REQUEST['bg'] );
    }
    
    $entry_id = 0;
    if( ! empty( $_REQUEST['eid'] ) ) {
        $entry_id  	        = sanitize_text_field( $_REQUEST['eid'] );
    }
    
    $user_id = 0;
    if( ! empty( $_REQUEST['uid'] ) ) {
        $user_id  	        = sanitize_text_field( $_REQUEST['uid'] );
    }
    
    /**
     * return if entry_id not specified
     */
	if ( empty( $entry_id ) )
      return;
    
    /**
     * return if user_id not specified
     */
    if ( empty( $user_id ) )
      return;
    
    $output = '';
    
    $recs = $wpdb->get_results( "select * from ".$wpdb->prefix."badgeos_achievements where ID='".$achievement_id."' and  entry_id='".$entry_id."' and  user_id='".$user_id."'" );
    if( count( $recs ) > 0 ) {

        $rec = $recs[0];

        $expiration          = ( get_post_meta( $achievement_id, '_open_badge_expiration', true ) ? get_post_meta( $achievement_id, '_open_badge_expiration', true ) : '0' );
        $expiration_type     = ( get_post_meta( $achievement_id, '_open_badge_expiration_type', true ) ? get_post_meta( $achievement_id, '_open_badge_expiration_type', true ) : 'Day' );

        $user = get_user_by( 'ID', $user_id );
        $achievement = get_post( $rec->ID );
        wp_enqueue_style( 'badgeos-front' );
        wp_enqueue_script( 'badgeos-achievements' ); 
        
        $dirs = wp_upload_dir();
        $baseurl = trailingslashit( $dirs[ 'baseurl' ] );
        $basedir = trailingslashit( $dirs[ 'basedir' ] );
        $badge_directory = trailingslashit( $basedir.'user_badges/'.$user_id );
        $badge_url = trailingslashit( $baseurl.'user_badges/'.$user_id );
        ?>
            <div class="evidence_main">
                <div class="left_col">
                    <?php if( ! empty( $rec->image ) && file_exists( $badge_directory.$rec->image ) ) { ?>
                        <img src="<?php echo $badge_url.$rec->image;?>" with="100%" />
                    <?php } else { ?>
                        <?php echo badgeos_get_achievement_post_thumbnail( $achievement_id, 'full' ); ?>
                    <?php  } ?>
                    
                    <div class="verification"> 
                        <input id="open-badgeos-verification" href="javascript:;" data-bg="<?php echo $achievement_id;?>" data-eid="<?php echo $entry_id;?>" data-uid="<?php echo $user_id;?>" class="verify-open-badge" value="<?php echo _e( 'Verify', 'badgeos' );?>" type="button" />
                    </div>
                </div>
                <div class="right_col">
                    <h3 class="title"><?php echo $rec->achievement_title;?></h3>        
                    <p>
                        <?php echo $achievement->post_content;?>
                    </p>
                    <div class="user_name"><strong><?php echo _e( 'Receiver', 'badgeos' );?>:</strong> <?php echo $user->display_name;?></div>
                    <div class="issue_date"><strong><?php echo _e( 'Issue Date', 'badgeos' );?>:</strong> <?php echo date( get_option('date_format'), strtotime( $rec->date_earned ) );?></div>
                    <?php if( intval( $expiration ) > 0 ) { ?>
                        <div class="issue_date"><strong><?php echo _e( 'Expiry Date', 'badgeos' );?>:</strong> <?php echo date( get_option('date_format'), strtotime( '+'.$expiration.' '.$expiration_type, strtotime( $rec->date_earned ) ) );?></div>
                    <?php } else { ?>
                        <div class="issue_date"><strong><?php echo _e( 'Expiry Date', 'badgeos' );?>:</strong> <?php echo _e( 'None', 'badgeos' );?></div>
                    <?php } ?>
                </div>
            </div>
            <div id="badgeos-open-badge-verification-popup-box" style="display:none">
                <div class="badgeos-ob-verification-results">
                    <ul id="badgeos-ob-verification-res-list">
                    </ul>
                </div>
            </div>
        <?php
    }
    
    /**
     * Return our rendered achievement
     */
	return $output;
}