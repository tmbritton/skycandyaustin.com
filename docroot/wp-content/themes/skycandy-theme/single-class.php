<?php
/**
 * The Template for displaying all single posts
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

<?php $url = get_post_meta($post->ID, 'mindbody_url', TRUE); ?>
<article>

	<h2><?php the_title(); ?></h2>
	<a class="class_register" href="<?php echo $url; ?>">Register for <?php echo $post->post_title; ?></a>
	<?php $thumbnail = get_the_post_thumbnail( $post->ID, 'large'); 
			echo $thumbnail;
	?>
	<?php the_content(); ?>			

</article>
<?php endwhile; ?>
</div><!--main-->


<div class="sidebar">
	<?php include('parts/taught-by.php'); ?>
<ul>
	<?php dynamic_sidebar('classes_sidebar'); ?> 
</ul>
</div>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>