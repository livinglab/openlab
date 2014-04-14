<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes( 'xhtml' ); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<meta name="robots" content="noodp,noydir" />
<link rel="Shortcut Icon" href="<?php echo get_stylesheet_directory_uri() ?>/images/favicon.ico" type="image/x-icon" />

<title><?php bloginfo( 'name' ); ?></title>

<?php do_action( 'bp_head' ) ?>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<div id="content-wrap">
    <?php do_action( 'bp_before_header' ) ?>
    	<div id="header">
				<?php do_action( 'bp_header' ) ?>
		</div><!-- #header -->
        
		<?php do_action( 'bp_after_header' ) ?>
		<?php do_action( 'bp_before_container' ) ?>
        
        <div id="inner">
        	<div class="wrap">
            	<div id="content-sidebar-wrap">