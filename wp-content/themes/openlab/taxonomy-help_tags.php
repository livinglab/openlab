<?php
/*
* Help tags template
*
*/

/**begin layout**/
get_header(); ?>

	<div id="content" class="hfeed">
            <div class="col-sm-18">
		<?php openlab_help_tags_loop(); ?>
            </div>
            <?php openlab_bp_sidebar('help'); ?>
	</div>
<?php get_footer(); 
/**end layout**/