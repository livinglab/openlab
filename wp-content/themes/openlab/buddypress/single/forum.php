<?php
do_action('bp_before_group_forum_content');
if (bp_is_group_forum_topic_edit()) {
    get_template_part('groups/single/forum/edit');
} elseif (bp_is_group_forum_topic()) {
    get_template_part('groups/single/forum/topic');
} else {
    ?>
    <div class="item-list-tabs no-ajax" id="bpsubnav" role="navigation">
        <ul>
    <?php if (is_user_logged_in()) : ?>
                <li class="selected-page">Discussion:</li>
                <li class="new-topic"><a href="#post-new" class="show-hide-new"><?php _e('New Topic', 'buddypress') ?></a></li>
            <?php endif ?>

    <?php do_action('bp_forums_directory_group_sub_types'); ?>

            <li id="forums-order-select" class="last filter">
                <label for="forums-order-by"><?php _e('Order By:', 'buddypress'); ?></label>
                <select id="forums-order-by">
                    <option value="active"><?php _e('Last Active', 'buddypress'); ?></option>
                    <option value="popular"><?php _e('Most Posts', 'buddypress'); ?></option>
                    <option value="unreplied"><?php _e('Unreplied', 'buddypress'); ?></option>

    <?php do_action('bp_forums_directory_order_options'); ?>
                </select>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
    <div class="forums single-forum" role="main">
    <?php get_template_part('forums/forums-loop'); ?>
    </div><!-- .forums.single-forum -->

<?php
}

do_action('bp_after_group_forum_content');

if (!bp_is_group_forum_topic_edit() && !bp_is_group_forum_topic() && !bp_group_is_user_banned() && ( ( is_user_logged_in() && 'public' == bp_get_group_status() ) || bp_group_is_member() )) :
    ?>
    <div id="new-topic-post">
        <form action="" method="post" id="forum-topic-form" class="standard-form">


            <?php do_action('bp_before_group_forum_post_new') ?>

    <?php if (bp_groups_auto_join() && !bp_group_is_member()) : ?>
                <p><?php _e('You will auto join this group when you start a new topic.', 'buddypress') ?></p>
    <?php endif; ?>

            <p id="post-new"></p>
            <h4><?php _e('Post a New Topic:', 'buddypress') ?></h4>

            <label><?php _e('Title:', 'buddypress') ?></label>
            <input type="text" name="topic_title" id="topic_title" value="" />

            <label><?php _e('Content:', 'buddypress') ?></label>
            <textarea name="topic_text" id="topic_text"></textarea>

            <label><?php _e('Tags (comma separated):', 'buddypress') ?></label>
            <input type="text" name="topic_tags" id="topic_tags" value="" />

    <?php do_action('bp_after_group_forum_post_new') ?>

            <div class="submit">
                <input type="submit" name="submit_topic" id="submit" value="<?php _e('Post Topic', 'buddypress') ?>" />
            </div>

    <?php wp_nonce_field('bp_forums_new_topic') ?>

        </form><!-- #forum-topic-form -->
    </div><!-- #new-topic-post -->

<?php endif; ?>
