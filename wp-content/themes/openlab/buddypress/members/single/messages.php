<div class="submenu"><div class="submenu-text">My Messages: </div><?php echo openlab_my_messages_submenu(); ?></div>
<?php if ( 'compose' == bp_current_action() ) : ?>
	<?php locate_template( array( 'buddypress/members/single/messages/compose.php' ), true ) ?>

<?php elseif ( 'view' == bp_current_action() ) : ?>
	<?php locate_template( array( 'buddypress/members/single/messages/single.php' ), true ) ?>

<?php else : ?>

	<?php do_action( 'bp_before_member_messages_content' ) ?>

	<div class="messages">
		<?php if ( 'notices' == bp_current_action() ) : ?>
			<?php locate_template( array( 'buddypress/members/single/messages/notices-loop.php' ), true ) ?>

		<?php else : ?>
			<?php locate_template( array( 'buddypress/members/single/messages/messages-loop.php' ), true ) ?>

		<?php endif; ?>
	</div><!-- .messages -->

	<?php do_action( 'bp_after_member_messages_content' ) ?>

<?php endif; ?>
