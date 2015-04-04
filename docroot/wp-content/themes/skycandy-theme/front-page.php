<?php
/**
 * The Front Page template file
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage Sky Candy	
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<div class="wrap" id="main">
	<div id="home_page_slideshow" class="flexslider">
		<ul class="slides">
			<?php get_homepage_slideshow(); ?>
		</ul>
	</div>

<ul class="home-page-pods">
	<?php dynamic_sidebar('home_page_pods'); ?> 
</ul>
</div><!--main-->

<div class="sidebar">	
		<!--<h2>Latest Blog Posts</h2>
		<ul class="latest-blog-posts">
			<?php get_home_page_posts(); ?>
			<?php get_theme_menu_name('') ?>
		</ul>	

		<h2>Class Descriptions</h2>
		<?php get_classes(); ?> -->
		<?php dynamic_sidebar('home_page_sidebar'); ?> 
</div><!--sidebar-->
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer') ); ?>
