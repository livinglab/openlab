<?php
/**
 * The template for displaying Tag Archive pages.
 *
 */

    get_header();
?>

    <div id="container" class="tag-page">
	<?php weaver_put_wvr_widgetarea('sitewide-top-widget-area','ttw-site-top-widget'); ?>
	<?php weaver_put_wvr_widgetarea('postpages-widget-area','ttw-top-widget','ttw_hide_special_posts'); ?>
	<div id="content" role="main">

	    <h1 id="tag-title" class="page-title">
	    <?php printf( __( 'Tag Archives: %s', WEAVER_TRANS ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?>
	    </h1>

<?php
/* Run the loop for the tag archive to output the posts
 * If you want to overload this in a child theme then include a file
 * called loop-tag.php and that will be used instead.
 */
 get_template_part( 'loop', 'tag' );
?>

	    </div><!-- #content -->
	    <?php weaver_put_wvr_widgetarea('sitewide-bottom-widget-area','ttw-site-bot-widget'); ?>
	</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
