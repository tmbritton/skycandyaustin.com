<?php
/**
 * The Template for displaying single promo posts
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package 	WordPress
 * @subpackage 	Starkers
 * @since 		Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>
<div class="wrap" id="main">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<article>

	<h2><?php the_title(); ?></h2>
	<?php $thumbnail = get_the_post_thumbnail( $post->ID, 'slide'); 
			echo $thumbnail;
	?>
	<?php the_content(); ?>			

</article>
<?php endwhile; ?>
</div><!--main-->


<div class="sidebar">
<ul>
	<?php dynamic_sidebar('promos_sidebar'); ?> 
</ul>
</div>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>