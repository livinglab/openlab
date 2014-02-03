<?php

//show blog and pages on menu
class WDS_Group_Extension extends BP_Group_Extension {

	var $enable_nav_item = true;
	var $enable_create_step = false;
	public function __construct() {
		global $bp;
		$group_id=$bp->groups->current_group->id;
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if($wds_bp_group_site_id!=""){
		  $this->name = 'Activity';
		  $this->slug = 'activity';
  		  $this->nav_item_position = 10;
		}
	}

	public function create_screen( $group_id = null ) {
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	public function create_screen_save( $group_id = null ) {
		global $bp;

		check_admin_referer( 'groups_create_save_' . $this->slug );

		groups_update_groupmeta( $bp->groups->new_group_id, 'my_meta_name', 'value' );
	}

	public function edit_screen( $group_id = null ) {
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false; ?>

		<h2><?php echo esc_attr( $this->name ) ?></h2>
        <?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}

	public function edit_screen_save( $group_id = null ) {
		global $bp;

		if ( !isset( $_POST['save'] ) )
			return false;

		check_admin_referer( 'groups_edit_save_' . $this->slug );

		/* Insert your edit screen save code here */

		/* To post an error/success message to the screen, use the following */
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );

		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}

	function display() {
		global $bp;
		gconnect_locate_template( array( 'groups/single/group-header.php' ), true );
		gconnect_locate_template( array( 'groups/single/activity.php' ), true );

		/*$group_id=$bp->groups->current_group->id;
		$wds_bp_group_site_id=groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
		if($wds_bp_group_site_id!=""){
		  switch_to_blog($wds_bp_group_site_id);
		  $pages = get_pages();
		  ?>
		  <div role="navigation" id="subnav" class="item-list-tabs no-ajax">
			  <ul>
				 <?php foreach ($pages as $pagg) {?>
					<li class="current"><a href="?page=<?php echo $pagg->ID;?>"><?php echo $pagg->post_title;?></a></li>
				  <?php }?>
			  </ul>
		  </div>
		  <?php
		  if($_GET['page']){
			  $id=$_GET['page'];
			  $post = get_post($id);
			  echo $post->post_content;
		  }
		  restore_current_blog();
		}*/
	}

	function widget_display() { ?>
		<div class=&quot;info-group&quot;>
			<h4><?php echo esc_attr( $this->name ) ?></h4>
		</div>
		<?php
	}

}
//bp_register_group_extension( 'WDS_Group_Extension' );

//Change "Group" to something else
class bpass_Translation_Mangler {
 /*
  * Filter the translation string before it is displayed.
  *
  * This function will choke if we try to load it when not viewing a group page or in a group loop
  * So we bail in cases where neither of those things is present, by checking $groups_template
  */
 static function filter_gettext($translation, $text, $domain) {
   global $bp, $groups_template;

   if ( empty( $groups_template->group ) && empty( $bp->groups->current_group ) ) {
   	return $translation;
   }

   if ( !empty( $groups_template->group->id ) ) {
   	$group_id = $groups_template->group->id;
   } else if ( !empty( $bp->groups->current_group->id ) ) {
   	$group_id = $bp->groups->current_group->id;
   } else {
   	return $translation;
   }


   if ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
   	$grouptype = $_COOKIE['wds_bp_group_type'];
   } else {
   	$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
   }

   $uc_grouptype = ucfirst($grouptype);
   $plural_grouptype = $grouptype . 's';
   $translations = get_translations_for_domain( 'bp-ass' );

   switch($text){
	case "How do you want to read this group?":
	     return $translations->translate( "How do you want to read this $grouptype?" );
	     break;
	case "I will read this group on the web":
	     return $translations->translate( "I will read this $grouptype on the web" );
	     break;
	case "Send all group activity as it arrives":
	     return $translations->translate( "Send all $grouptype activity as it arrives" );
	     break;
	case "Your email notifications are set to %s for this group.":
	     return $translations->translate( "Your email notifications are set to %s for this $grouptype." );
	     break;
	case "When new users join this group, their default email notification settings will be:":
	     return $translations->translate( "When new users join this $grouptype, their default email notification settings will be:" );
	     break;
	case "No Email (users will read this group on the web - good for any group - the default)":
	     return $translations->translate( "No Email (users will read this $grouptype on the web - good for any $grouptype - the default)" );
	     break;
	case "Weekly Summary Email (the week's topics - good for large groups)":
	     return $translations->translate( "Weekly Summary Email (the week\'s topics - good for large $plural_grouptype)" );
	     break;
	case "Daily Digest Email (all daily activity bundles in one email - good for medium-size groups)":
	     return $translations->translate( "Daily Digest Email (all daily activity bundles in one email - good for medium-size $plural_grouptype)" );
	     break;
	case "New Topics Email (new topics are sent as they arrive, but not replies - good for small groups)":
	     return $translations->translate( "New Topics Email (new topics are sent as they arrive, but not replies - good for small $plural_grouptype)" );
	     break;
	case "All Email (send emails about everything - recommended only for working groups)":
	     return $translations->translate( "All Email (send emails about everything - recommended only for working $plural_grouptype)" );
	     break;
	case "Group Email Settings":
		return $translations->translate( "$uc_grouptype Email Settings" );
	     	break;
	case "To change the email notification settings for your groups go to %s and click change for each group.":
	     return $translations->translate( "To change the email notification settings for your $plural_grouptype go to %s and click change for each $grouptype." );
	     break;
	case "Send an email notice to everyone in the group":
		return $translations->translate( "Send an email notice to everyone in the $grouptype" );
		break;
	case "You can use the form below to send an email notice to all group members.":
		return $translations->translate( "You can use the form below to send an email notice to all $grouptype members." );
		break;
	case "Everyone in the group will receive the email -- regardless of their email settings -- so use with caution":
		return $translations->translate( "Everyone in the $grouptype will receive the email -- regardless of their email settings -- so use with caution" );
		break;
	case " - sent from the group ":
		return $translations->translate( " - sent from the $grouptype " );
		break;
	case "Send an email when a new member join the group.":
		return $translations->translate( "Send an email when a new member joins the $grouptype." );
		break;
	case "Email this notice to everyone in the group":
		return $translations->translate( "Email this notice to everyone in the $grouptype" );
		break;
	case "This is a notice from the group '%s':

\"%s\"


To view this group log in and follow the link below:
%s

---------------------
":
		return $translations->translate( "This is a notice from the $grouptype '%s':

\"%s\"


To view this $grouptype log in and follow the link below:
%s

---------------------
" );
		break;
  }
  return $translation;
 }
}
add_filter('gettext', array('bpass_Translation_Mangler', 'filter_gettext'), 10, 4);

/**
 * Put the group type in email notification subject lines
 */
function openlab_group_type_in_notification_subject( $subject ) {

   if ( !empty( $groups_template->group->id ) ) {
   	$group_id = $groups_template->group->id;
   } else if ( !empty( $bp->groups->current_group->id ) ) {
   	$group_id = $bp->groups->current_group->id;
   } else {
   	return $subject;
   }


   if ( isset( $_COOKIE['wds_bp_group_type'] ) ) {
   	$grouptype = $_COOKIE['wds_bp_group_type'];
   } else {
   	$grouptype = groups_get_groupmeta( $group_id, 'wds_group_type' );
   }

   return str_replace( 'in the group', 'in the ' . $grouptype, $subject );
}
add_filter( 'ass_clean_subject', 'openlab_group_type_in_notification_subject' );

/**
 * Add members to wpms website if attached to bp group and they are a group member
 *
 * @todo With an updated of BP Groupblog, this should not be necssary. As it is, it adds a lot of
 *       overhead, and should be rewritten to avoid PHP warnings.
 */
add_action('bp_actions','wds_add_group_members_2_blog');
function wds_add_group_members_2_blog(){
	global $wpdb, $user_ID, $bp;

	if ( $group_id = bp_get_current_group_id() ) {
	     $blog_id = groups_get_groupmeta($group_id, 'wds_bp_group_site_id' );
	}

	if($user_ID!=0 && !empty( $group_id ) && !empty( $blog_id ) ){
		switch_to_blog($blog_id);
		if(!is_user_member_of_blog($blog_id)){
		      $sql="SELECT user_title FROM {$bp->groups->table_name}_members WHERE group_id = $group_id and user_id=$user_ID AND is_confirmed='1'";
		      $rs = $wpdb->get_results( $sql );
		      if ( count( $rs ) > 0 ) {
			      foreach( $rs as $r ) {
				      $user_title = $r->user_title;
			      }
			      if($user_title=="Group Admin"){
				      $role="administrator";
			      }elseif($user_title=="Group Mod"){
				      $role="editor";
			      }else{
				      $role="author";
			      }
			      add_user_to_blog( $blog_id, $user_ID, $role );
		      }
		}
		restore_current_blog();
	}
}

/**
 * Allow super admins to edit any BuddyPress Doc
 */
function openlab_allow_super_admins_to_edit_bp_docs( $user_can, $action ) {
	global $bp;

	if ( 'edit' == $action ) {
		if ( is_super_admin() || bp_loggedin_user_id() == get_the_author_meta( 'ID' ) || $user_can ) {
			$user_can = true;
			$bp->bp_docs->current_user_can[$action] = 'yes';
		} else {
			$user_can = false;
			$bp->bp_docs->current_user_can[$action] = 'no';
		}
	}

	return $user_can;
}
add_filter( 'bp_docs_current_user_can', 'openlab_allow_super_admins_to_edit_bp_docs', 10, 2 );

/**
 * When a Notice is sent, send an email to all members
 */
function openlab_send_notice_email( $subject, $message ) {
	global $wpdb;

	$to = get_option( 'admin_email' );
	//$to = 'boonebgorges@gmail.com'; // for testing
	$subject = 'Message from OpenLab: ' . $subject;

	$emails = $wpdb->get_col( $wpdb->prepare( "SELECT user_email FROM $wpdb->users WHERE spam = 0" ) );

	// For testing - limits recipients to Boone
	/*
	foreach( $emails as $key => $e ) {
		if ( false === strpos( $e, 'boonebgorges' ) ) {
			unset( $emails[$key] );
		}
	}*/

	$emails = implode( ',', $emails );

	$headers = array( 'bcc:' . $emails );

	wp_mail( $to, $subject, $message, $headers );
}
add_filter( 'messages_send_notice', 'openlab_send_notice_email', 10, 2 );

/**
 * Redirect profile edit to the correct field group
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/172
 */
function openlab_redirect_to_profile_edit_group() {
	if ( bp_is_user_profile_edit() ) {
		if ( !bp_action_variables( 1 ) ) {
			 $account_type=bp_get_profile_field_data( 'field=Account Type&user_id=' . bp_displayed_user_id() );
			  if($account_type=="Student"){
				  $pgroup="2";
			  }elseif($account_type=="Faculty"){
				  $pgroup="3";
			  }elseif($account_type=="Alumni"){
				  $pgroup="4";
			  }elseif($account_type=="Staff"){
				  $pgroup="5";
			  }else{
				  $pgroup="1";
			  }

			bp_core_redirect( bp_displayed_user_domain() . 'profile/edit/group/' . $pgroup . '/' );
		}
	}
}
add_action( 'bp_actions', 'openlab_redirect_to_profile_edit_group', 1 );

/**
 * Add the group type to the form action of the group creation forms
 */
function openlab_group_type_in_creation_form_action( $action ) {
	if ( false === strpos( $action, '?type=' ) && isset( $_GET['type'] ) ) {
		$action = add_query_arg( 'type', $_GET['type'], $action );
	}

	return $action;
}
add_action( 'bp_get_group_creation_form_action', 'openlab_group_type_in_creation_form_action' );

/**
 * When creating a group, if you fill in the wrong details, you should be redirected with the
 * correct group type appended to the URL.
 *
 * See http://openlab.citytech.cuny.edu/redmine/issues/326
 */
function openlab_group_creation_redirect( $redirect ) {
	if ( bp_is_group_create() ) {
		if ( false === strpos( $redirect, '?type=' ) && isset( $_GET['type'] ) ) {
			$redirect = add_query_arg( 'type', $_GET['type'], $redirect );
		}
	}

	return $redirect;
}
add_filter( 'wp_redirect', 'openlab_group_creation_redirect' );

/**
 * When getting a blog avatar in the context of the Featured Content widget, test to see whether
 * the blog is associated with a group. If so, fetch the group avatar instead
 */
function openlab_swap_featured_blog_avatar_with_group_avatar( $avatar, $blog_id ) {
	global $wpdb, $bp;

	$group_id = openlab_get_group_id_by_blog_id( $blog_id );

	if ( $group_id ) {
		$group_avatar = bp_core_fetch_avatar( array( 'item_id' => $group_id, 'object' => 'group', 'html' => false, 'type' => 'full' ) );

		if ( !empty( $group_avatar ) ) {
			$avatar = preg_replace( '/(src=").*?(")/', "$1" . $group_avatar . "$2", $avatar );
		}
	}
	return $avatar;
}
add_filter( 'cac_featured_content_blog_avatar', 'openlab_swap_featured_blog_avatar_with_group_avatar', 10, 2 );

/**
 * Blogs must be public in order for BP to record their activity. Only at save time
 */
add_filter( 'bp_is_blog_public', create_function( '', 'return 1;' ) );

/**
 * Make sure the comment-dupe data doesn't get saved in the comments activity
 */
function openlab_pre_save_comment_activity( $content ) {
	return preg_replace( "/disabledupes\{.*\}disabledupes/", "", $content );
}
add_filter( 'bp_blogs_activity_new_comment_content', 'openlab_pre_save_comment_activity' );

/**
 * Auto-enable BuddyPress Docs for all group types
 */
add_filter( 'bp_docs_force_enable_at_group_creation', '__return_true' );

/**
 * Don't send friend requests when accepting Invite Anyone invitations
 *
 * @see #666
 */
add_filter( 'invite_anyone_send_friend_requests_on_acceptance', '__return_false' );

/**
 * Bust the home page activity transients when new items are posted
 */
function openlab_clear_home_page_transients() {
	delete_site_transient( 'openlab_home_group_activity_items_course' );
	delete_site_transient( 'openlab_home_group_activity_items_project' );
	delete_site_transient( 'openlab_home_group_activity_items_club' );
	delete_site_transient( 'openlab_home_group_activity_items_portfolio' );
}
add_action( 'bp_activity_after_save', 'openlab_clear_home_page_transients' );

/**
 * Fix the busted redirect on group subscription settings
 */
function openlab_fix_group_sub_settings_redirect( $redirect ) {
	if ( bp_get_root_domain() === $redirect && groups_get_current_group() && bp_is_current_action( 'notifications' ) && ! empty( $_POST ) ) {
		$redirect = bp_get_group_permalink( groups_get_current_group() ) . 'notifications/';
	}
	return $redirect;
}
add_filter( 'wp_redirect', 'openlab_fix_group_sub_settings_redirect' );

/**
 * Remove the Sitewide Notices sitewide box added by BP theme compat
 *
 * @see #923
 */
function openlab_remove_sitewide_notices() {
	global $wp_filter;

	// hackkkkkkkkk
	if ( isset( $wp_filter['wp_footer'][9999] ) ) {
		foreach ( $wp_filter['wp_footer'][9999] as $fname => $filter ) {
			if ( false !== strpos( $fname, 'sitewide_notices' ) ) {
				remove_action( 'wp_footer', $fname, 9999 );
			}
		}
	}
}
add_action( 'wp_footer', 'openlab_remove_sitewide_notices' );

/**
 * Force BP Docs to have comments open
 *
 * I guess old ones get closed automatically
 */
function openlab_force_doc_comments_open( $open, $post_id ) {
        $_post = get_post( $post_id );
        if ( 'bp_doc' === $_post->post_type ) {
                $open = true;
        }
        return $open;
}
add_action( 'comments_open', 'openlab_force_doc_comments_open', 10, 2 );

/**
 * Filter the signup activation email
 */
function openlab_activation_email_content( $message ) {
	// Swap 'key' with 'activationk', because Microsoft filters the URL
	// param 'key'. Oy.
	$message = str_replace( '?key=', '?activationk=', $message );

        $message .= '
If clicking the link does not work, try to copy the link, paste it into your browser, and press the enter key or go.';
        return $message;
}
add_filter( 'bp_core_activation_signup_user_notification_message', 'openlab_activation_email_content' );

function openlab_screen_activation() {
	global $bp;

	if ( !bp_is_current_component( 'activate' ) )
		return false;

	// Check if an activation key has been passed
	if ( isset( $_GET['activationk'] ) ) {

		// Activate the signup
		$user = apply_filters( 'bp_core_activate_account', bp_core_activate_signup( $_GET['activationk'] ) );

		// If there were errors, add a message and redirect
		if ( !empty( $user->errors ) ) {
			bp_core_add_message( $user->get_error_message(), 'error' );
			bp_core_redirect( trailingslashit( bp_get_root_domain() . '/' . $bp->pages->activate->slug ) );
		}

		// Check for an uploaded avatar and move that to the correct user folder
		if ( is_multisite() )
			$hashed_key = wp_hash( $_GET['activationk'] );
		else
			$hashed_key = wp_hash( $user );

		// Check if the avatar folder exists. If it does, move rename it, move
		// it and delete the signup avatar dir
		if ( file_exists( bp_core_avatar_upload_path() . '/avatars/signups/' . $hashed_key ) )
			@rename( bp_core_avatar_upload_path() . '/avatars/signups/' . $hashed_key, bp_core_avatar_upload_path() . '/avatars/' . $user );

		bp_core_add_message( __( 'Your account is now active!', 'buddypress' ) );

		$bp->activation_complete = true;
	}

	bp_core_load_template( apply_filters( 'bp_core_template_activate', array( 'activate', 'registration/activate' ) ) );
}
remove_action( 'bp_screens', 'bp_core_screen_activation' );
add_action( 'bp_screens', 'openlab_screen_activation' );


