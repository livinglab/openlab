<?php if(has_nav_menu('johannes_menu_secondary_1')): ?>
	<?php 
		wp_nav_menu( array(
	            'theme_location' => 'johannes_menu_secondary_1',
	            'container'=> 'nav',
	            'menu_class' => 'johannes-menu johannes-menu-secondary-1' ) ); 
	 ?>
<?php else: ?>
	<nav>
		<?php get_template_part('template-parts/header/elements/menu-placeholder'); ?>
	</nav>
<?php endif; ?>