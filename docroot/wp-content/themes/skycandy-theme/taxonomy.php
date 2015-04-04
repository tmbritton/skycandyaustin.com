<?php
/**
 * The template used to display Taxonomy Archive pages
 *
 * Please see /external/starkers-utilities.php for info on Starkers_Utilities::get_template_parts()
 *
 * @package   WordPress
 * @subpackage  Starkers
 * @since     Starkers 4.0
 */
?>
<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/html-header', 'parts/shared/header' ) ); ?>

<?php if ( have_posts() ): ?>
<h2><?php echo single_tag_title( '', false ); ?></h2>
<ol>
<?php while ( have_posts() ) : the_post(); ?>
  <li>
    <article class="post">
          <?php 
            if ( has_post_thumbnail() ) {
              //var_dump($post);
              $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium');
              ?>
              <a href="<?php the_permalink(); ?>">
                <img src="<?php echo $thumbnail[0]; ?>" class="post-thumb" />           
              </a>
              <?php
            } 
          ?>
          <h3><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
          
          <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"></a>

          <?php the_excerpt(); ?>
    </article>
  </li>
<?php endwhile; ?>
</ol>
<?php else: ?>
<h2>No posts to display in <?php echo single_tag_title( '', false ); ?></h2>
<?php endif; ?>

<?php Starkers_Utilities::get_template_parts( array( 'parts/shared/footer','parts/shared/html-footer' ) ); ?>