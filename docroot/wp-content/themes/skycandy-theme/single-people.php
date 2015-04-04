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
<article itemprop="employee" itemscope itemtype="http://schema.org/Person">

	<h2 itemprop="name"><?php the_title(); ?></h2>
	<?php the_terms( $post->ID, 'role', '<p class="role"><span itemprop="jobTitle">', '</span>,<span itemprop="jobTitle"> ', '</span></p>'  ); ?> 
	<?php the_post_thumbnail( 'large', array('itemprop' => 'image') ); ?> 
	
	<?php the_content(); ?>			

</article>
<?php endwhile; ?>
</div><!--main-->


<div class="sidebar">
	<?php dynamic_sidebar('people_sidebar'); ?> 
</div>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>