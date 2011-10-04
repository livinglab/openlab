<?php do_action( 'bp_before_member_header' ) ?>
<?php bp_displayed_user_avatar( 'type=full' ) ?>
<h2 class="fn"><a href="<?php bp_user_link() ?>"><?php bp_displayed_user_fullname() ?></a> <span class="activity"><?php bp_last_activity( bp_displayed_user_id() ) ?></span></h2>
<?php do_action( 'bp_before_member_header_meta' ) ?>
<div id="item-meta">
	<?php if ( bp_is_active( 'activity' ) ) : ?>
		<div id="latest-update">
			<?php bp_activity_latest_update( bp_displayed_user_id() ) ?>
		</div>
	<?php endif; ?>
	<div id="item-buttons">
		<?php do_action( 'bp_member_header_actions' ); ?>
	</div><!-- #item-buttons -->
	<?php do_action( 'bp_profile_header_meta' ) ?>
</div>
<?php do_action( 'bp_after_member_header' ); do_action( 'template_notices' ); ?>