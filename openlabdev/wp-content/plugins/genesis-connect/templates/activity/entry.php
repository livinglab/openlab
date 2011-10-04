<?php do_action( 'bp_before_activity_entry' ) ?>

<li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">
	<div class="activity-avatar">
		<?php bp_activity_avatar( 'type=full&width=100&height=100' ) ?>
	</div>

	<div class="activity-content">
		<div class="activity-header">
			<?php bp_activity_action() ?>
		</div>

		<?php if ( bp_get_activity_content_body() ) : ?>
			<div class="activity-inner">
				<?php bp_activity_content_body() ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'bp_activity_entry_content' ) ?>

		<div class="activity-meta">
			<?php if ( is_user_logged_in() ) : ?>
				<?php if ( bp_activity_can_comment() ) : ?>
					<a href="#acomment-<?php bp_activity_id() ?>" class="acomment-reply" id="acomment-comment-<?php bp_activity_id() ?>"><?php _e( 'Reply', 'buddypress' ) ?> (<span><?php bp_activity_comment_count() ?></span>)</a>
				<?php endif; ?>
				<?php if ( !bp_get_activity_is_favorite() ) : ?>
					<a href="" class="fav" title="<?php _e( 'Mark as Favorite', 'buddypress' ) ?>"><?php _e( 'Favorite', 'buddypress' ) ?></a>
				<?php else : ?>
					<a href="" class="unfav" title="<?php _e( 'Remove Favorite', 'buddypress' ) ?>"><?php _e( 'Remove Favorite', 'buddypress' ) ?></a>
				<?php endif; ?>
			<?php endif;?>

			<?php do_action( 'bp_activity_entry_meta' ) ?>
		</div>
	</div>

	<?php if ( 'activity_comment' == bp_get_activity_type() ) : ?>
		<div class="activity-inreplyto">
			<strong><?php _e( 'In reply to', 'buddypress' ) ?></strong> - <?php bp_activity_parent_content() ?> &middot;
			<a href="<?php bp_activity_thread_permalink() ?>" class="view" title="<?php _e( 'View Thread / Permalink', 'buddypress' ) ?>">#</a>
		</div>
	<?php endif; ?>

	<?php do_action( 'bp_before_activity_entry_comments' ) ?>

	<?php if ( bp_activity_can_comment() ) : ?>
		<div class="activity-comments">
			<?php bp_activity_comments() ?>

			<?php if ( is_user_logged_in() ) : ?>
			<form action="" method="post" name="activity-comment-form" id="ac-form-<?php bp_activity_id() ?>" class="ac-form">
				<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ) ?></div>
				<div class="ac-reply-content">
					<div class="ac-textarea">
						<textarea id="ac-input-<?php bp_activity_id() ?>" class="ac-input" name="ac-input-<?php bp_activity_id() ?>"></textarea>
					</div>
					<input type="submit" name="ac-form-submit" value="<?php _e( 'Post', 'buddypress' ) ?> &rarr;" /> &nbsp; <?php _e( 'or press esc to cancel.', 'buddypress' ) ?>
				</div>
				<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ) ?>
			</form>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'bp_after_activity_entry_comments' ) ?>
</li>

<?php do_action( 'bp_after_activity_entry' ) ?>

