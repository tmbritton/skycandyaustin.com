<?php
	/**
	 * Starkers functions and definitions
	 *
	 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
	 *
 	 * @package 	WordPress
 	 * @subpackage 	Starkers
 	 * @since 		Starkers 4.0
	 */

	/* ========================================================================================================================
	
	Required external files
	
	======================================================================================================================== */

	require_once( 'external/starkers-utilities.php' );

	/* ========================================================================================================================
	
	Theme specific settings

	Uncomment register_nav_menus to enable a single menu with the title of "Primary Navigation" in your theme
	
	======================================================================================================================== */

	add_theme_support('post-thumbnails');
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size( 'tiny', 50, 50, TRUE );
		add_image_size( 'pod', 100, 120, TRUE );
		add_image_size( 'slide', 720, 250, TRUE );
		add_image_size( 'masonry', 150, 150, FALSE );
	}	
	register_nav_menus(array('primary' => 'Primary Navigation'));

	/* ========================================================================================================================
	
	Actions and Filters
	
	======================================================================================================================== */

	add_action( 'wp_enqueue_scripts', 'starkers_script_enqueuer' );

	add_filter( 'body_class', array( 'Starkers_Utilities', 'add_slug_to_body_class' ) );

	/* ======================================================================================================================== 
	
	Custom Post Types - include custom post types and taxonimies here
	
	======================================================================================================================== */

	require_once( 'custom-post-types/promos.php' );
	require_once( 'custom-post-types/classes.php' );
	require_once( 'custom-post-types/people.php' );
	/* ========================================================================================================================
	
	Scripts
	
	======================================================================================================================== */

	/**
	 * Add scripts via wp_head()
	 *
	 * @return void
	 * @author Keir Whitaker
	 */

	function starkers_script_enqueuer() {

		wp_register_style( 'screen', get_stylesheet_directory_uri().'/style.css', '', '', 'screen' );
    wp_enqueue_style( 'screen' );

    wp_register_script( 'slideshow', get_template_directory_uri().'/js/vendor/jquery.flexslider-min.js', array('jquery'), 1, TRUE );
	}	

	function skycandy_zurb_enqueue_script() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'skycandy', get_template_directory_uri().'/js/skycandy.js', array('jquery', 'slideshow'), 1, TRUE );
	}
	add_action( 'wp_enqueue_scripts', 'skycandy_zurb_enqueue_script' );

	/* ========================================================================================================================
	
	Comments
	
	======================================================================================================================== */

	/**
	 * Custom callback for outputting comments 
	 *
	 * @return void
	 * @author Keir Whitaker
	 */
	function starkers_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; 
		?>
		<?php if ( $comment->comment_approved == '1' ): ?>	
		<li>
			<article id="comment-<?php comment_ID() ?>">

				<?php echo get_avatar( $comment ); ?>
				<?php 
					$args = array(
						'reply_text' => 'Reply to Comment',
						'max_depth' => 5,
					); 
				?>
				<span class="reply_link">
					<?php comment_reply_link( $args, $comment->comment_ID, $comment->comment_post_ID ); ?>
				</span>
				<h4><?php comment_author_link() ?></h4>

				<time><a href="#comment-<?php comment_ID() ?>" pubdate><?php comment_date() ?> at <?php comment_time() ?></a></time>
				<?php comment_text() ?>
			</article>
		<?php endif;
	}

	/* ========================================================================================================================
	
	Widgets
	
	======================================================================================================================== */
	/**
	 * Register our sidebars and widgetized areas.
	 *
	 */
	require_once( 'parts/widgets.php' );
	
	function theH1() {
		?>
		<h1>
			<a itemprop="url" href="<?php echo home_url(); ?>">
				<img src="<?php bloginfo('template_url'); ?>/images/logo-white-stacked.png" alt="<?php bloginfo( 'name' ); ?>" />
			</a>
			<span itemprop="description">
				<?php echo get_bloginfo( 'description' ); ?>
			</span>	
		</h1>
		<?php
	}

	function sk_meta_description() {
		$description = get_bloginfo( 'description' );
		$excerpt = get_the_excerpt();
		if ( strlen($description) > strlen($excerpt)) {
			$out = $description;
		} else {
			$out = $excerpt;
		}
		return $out;
	}

	function get_homepage_slideshow() {
		wp_enqueue_script( 'slideshow' );
		$slides = array();
		$i = 0;
		$args = array(
			'post_type' => 'promo',
		);
		$promos = new WP_Query( $args );
		if( $promos->have_posts() ) {
			while( $promos->have_posts() ) {
				$promos->the_post();
				$slides[$i]['title'] = get_the_title();
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($promos->post->ID), 'slide');
				$slides[$i]['image'] = $thumbnail[0];
				$url = get_post_meta( $promos->post->ID, 'promo_url', true );
				if($url == '') {
					$slides[$i]['url'] = get_permalink($promos->post->ID);
				} else {
					$slides[$i]['url'] = $url;
				}
				$i++;
			}
		}
		else {
			echo 'Oh ohm no promos!';
		}
		
		foreach($slides as $slide) {
			?>
			<li>
				<a href="<?php echo $slide['url']; ?>"><img src="<?php echo $slide['image']; ?>" title="<?php echo $slide['title']; ?>"></a>
				<p class="flex-caption"><?php echo $slide['title']; ?></p>
			</li>
			<?php
		}
	 	wp_reset_query();
    wp_reset_postdata();
	}	
	
	function get_home_page_posts() {
		add_filter('post_limits', 'post_query_limit_sidebar');
		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish'
		);
		$posts = new WP_Query( $args );
		if( $posts->have_posts() ) {
			while( $posts->have_posts() ) {
				$posts->the_post();
				$url = get_permalink($posts->post->ID);
				$title = $posts->post->post_title;
				if($thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($posts->post->ID), 'tiny')) {
					$image = $thumbnail[0];
				} else {
					$image = get_bloginfo('template_url') . '/images/skycandy-default.jpg';
				}
				?>
				<li class="clearfix"><img src="<?php echo $image; ?>"/><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
			<?php	
			}		
		}
		remove_filter('post_limits', 'post_query_limit_sidebar');
		wp_reset_query();
		wp_reset_postdata();
	}

	function sky_candy_get_teacher_list() {
		$args = array(
			'post_type' => 'people',
			'post_status' => 'publish',
			'orderby' => 'title', 
			'order' => 'ASC',
			'role' => 'teacher-2'
		);
		$posts = new WP_Query( $args );
		if( $posts->have_posts() ) {
			while( $posts->have_posts() ) {
				$posts->the_post();
				$url = get_permalink($posts->post->ID);
				$title = $posts->post->post_title;
				if($thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($posts->post->ID), 'tiny')) {
					$image = $thumbnail[0];
				} else {
					$image = get_bloginfo('template_url') . '/images/skycandy-default.jpg';
				}
				?>
				<li class="clearfix"><a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>"/></a><a href="<?php echo $url; ?>"><?php echo $title; ?></a></li>
			<?php	
			}		
		}
		wp_reset_query();
		wp_reset_postdata();
	}
	
	function post_query_limit_sidebar() {
		return 'LIMIT 4';
	}

  function post_query_limit_images() {
    return 'LIMIT 20';
  }
	
function post_query_limit_classes() {
	return 'LIMIT 100';
}

function get_classes() {
	$args = array(
    'post_type' => 'class',
    'post_status' => 'publish',
		'orderby' => 'title',
		'order' => 'ASC',
		'posts_per_page' => -1
  );
	$classes = new WP_Query( $args );
	?>
	<ul class="class-descriptions">
	<?php
	if ( $classes->have_posts() ) :
		while ( $classes->have_posts() ) :
			$classes->the_post();
			?>
			<li><a href="<?php echo get_permalink( $classes->post->ID ) ?>"><?php echo $classes->post->post_title; ?></a></li>
			<?php
		endwhile;
	endif;
	?>
	</ul>
	<?php
	wp_reset_query();
  wp_reset_postdata();
}

function get_theme_menu_name( $theme_location ) {
	if( ! $theme_location ) return false;
 
	$theme_locations = get_nav_menu_locations();
	if( ! isset( $theme_locations[$theme_location] ) ) return false;
 
	$menu_obj = get_term( $theme_locations[$theme_location], 'nav_menu' );
	if( ! $menu_obj ) $menu_obj = false;
	if( ! isset( $menu_obj->name ) ) return false;
 
	return $menu_obj->name;
}
	/* ========================================================================================================================
	
	Shortcodes
	
	======================================================================================================================== */

function skycandy_list_classes($atts){
	add_filter('post_limits', 'post_query_limit_classes');
	$args = array(
    'post_type' => 'class',
    'post_status' => 'publish',
		'orderby' => 'title',
		'order' => 'ASC',
		'class_type' => $atts['type']
  );
	$classes = new WP_Query( $args );
	?>
	<ul class="skycandy_classes_shortcode">
	<?php
	if ( $classes->have_posts() ) :
		while ( $classes->have_posts() ) :
			$classes->the_post();
			$url = get_post_meta( $classes->post->ID, 'mindbody_url', true );
			?>
			<li class="clearfix" itemprop="event" itemscope itemtype="http://schema.org/EducationEvent">
				<h3 itemprop="name"><?php echo $classes->post->post_title; ?></h3>
				<?php 
					if($thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($classes->post->ID), 'medium')) {
						?>
						<a href="<?php echo get_permalink( $classes->post->ID ) ?>"><img itemprop="image" src="<?php echo $thumbnail[0]; ?>" alt="" /></a>
						<?php
					}
				?>				
				<?php 
					if ($url) {
						?>
						<a class="class_register" href="<?php echo $url; ?>">Register for <?php echo $classes->post->post_title; ?></a>
						<?php
					}
				?>	
				<?php the_content('Read more...'); ?>
			</li>
			<?php
		endwhile;
	endif;
	?>
	</ul>
	<?php
	wp_reset_query();
  wp_reset_postdata();
  remove_filter('post_limits', 'post_query_limit_classes');
}

add_shortcode( 'skycandy_classes', 'skycandy_list_classes' );

/* Forms */

add_action( 'init', 'ninja_forms_contact_form' );
function ninja_forms_contact_form(){
	add_action( 'ninja_forms_pre_process', 'ninja_forms_pre_actions' );
}
 
function ninja_forms_pre_actions(){
	global $ninja_forms_processing;
	switch($ninja_forms_processing->get_form_ID()) {
		case '1': 
			sky_candy_contact_form_callback();
			break;
	}
}

function sky_candy_contact_form_callback(){
	global $ninja_forms_processing;
	$fields = $ninja_forms_processing->get_all_fields();
	switch ($fields[4]) {
		case 'Scheduling class':
			$admin_emails = array('info@skycandyaustin.com');
			break;
		case 'Booking a workshop':
			$admin_emails = array('winnie@skycandyaustin.com');
			break;
		case 'Finding performers for an event':
			$admin_emails = array('joanna@skycandyaustin.com');
			break;
		case 'Questions about classes':
			$admin_emails = array('info@skycandyaustin.com');
			break;
		case 'Renting space':
			$admin_emails = array('info@skycandyaustin.com');
			break;
		case 'Problems with website':
			$admin_emails = array('winnie@skycandyaustin.com');
			break;
		case 'Other':
			$admin_emails = array('info@skycandyaustin.com');
			break;
	}
	$ninja_forms_processing->update_form_setting('admin_mailto', $admin_emails);
}
