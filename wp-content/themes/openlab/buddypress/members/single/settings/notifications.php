<?php
/**
 * Members settings - email notifications settings
 *
 * */
    do_action('bp_before_member_settings_template');
    ?>
    <?php echo openlab_submenu_markup(); ?>

    <div id="item-body" role="main">

        <?php do_action('bp_template_content') ?>

        <div class="panel panel-default">
            <div class="panel-body">
        <form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form panel-form" id="settings-form">
            <p><?php _e('Send a notification by email when:', 'buddypress'); ?></p>

            <?php do_action('bp_notification_settings');
            do_action('bp_members_notification_settings_before_submit'); ?>
            </div>
        </div>

            <div class="submit">
                <input type="submit" name="submit" value="<?php _e('Save Changes', 'buddypress'); ?>" id="submit" class="auto btn btn-primary" />
            </div>

    <?php do_action('bp_members_notification_settings_after_submit');
    wp_nonce_field('bp_settings_notifications'); ?>

        </form>
    <?php do_action('bp_after_member_body'); ?>
    </div><!-- #item-body -->
    <?php
    do_action('bp_after_member_settings_template');