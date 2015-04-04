<?php
function skycandy_register_sidebars() {
	$args = array(
		'name' => 'Home Page Pods',
		'id' => 'home_page_pods',
		'before_widget' => '<li>',
		'after_widget' => '</li>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	);
	register_sidebar($args);

	$args = array(
		'name' => 'Home Page Sidebar',
		'id' => 'home_page_sidebar',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);	

	$args = array(
		'name' => 'Pages Sidebar',
		'id' => 'pages_sidebar',
		'before_widget' => '<li>',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);

	$args = array(
		'name' => 'Blog Posts Sidebar',
		'id' => 'posts_sidebar',
		'before_widget' => '<li>',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);

	$args = array(
		'name' => 'Classes Sidebar',
		'id' => 'classes_sidebar',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);	

	$args = array(
		'name' => 'People Sidebar',
		'id' => 'people_sidebar',
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);	

	$args = array(
		'name' => 'Promos Sidebar',
		'id' => 'promos_sidebar',
		'before_widget' => '<li>',
		'after_widget' => '</li>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	);
	register_sidebar($args);			
}


function register_post_widget() {  
  register_widget( 'SK_Posts_Widget' );
  register_widget( 'Sky_Candy_Latest_Posts_Widget' );
  register_widget( 'Sky_Candy_People_List' );
  register_widget( 'Sky_Candy_Performance_Apparatus' );
  register_widget( 'Sky_Candy_Classes_Taught' );
} 

class SK_Posts_Widget extends WP_Widget {

	function SK_Posts_Widget() {
		$widget_ops = array( 'classname' => 'home_page_pod', 'description' => __('Home Page Posts', 'sk-posts') );
		
		$control_ops = array( 'width' => 350, 'height' => 350, 'id_base' => 'sk-posts' );
		
		$this->WP_Widget( 'sk-posts', __('Home Page Posts', 'sk-posts'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		//Our variables from the widget settings.
		$cta = $instance['cta'];
		$post_id = $instance['select_post'];
		$post = get_post( $post_id, 'OBJECT' );
		$url = get_permalink($post_id);

		if($thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'pod')) {
			$image = $thumbnail[0];
		} else {
			$image = get_bloginfo('template_url') . '/images/skycandy-pod.png';
		}
		?>
		<li class="pod">
			<a href="<?php echo $url; ?>"><img src="<?php echo $image; ?>" alt="<?php echo $post->post_title; ?>" /></a>
			<h2><?php echo $post->post_title; ?></h2>
			
			<?php if(strlen($post->post_excerpt) > 1){
				?>
				<p class="excerpt"><?php echo $post->post_excerpt; ?></p>
				<?php
			}
			?>
			<p class="cta"><a href="<?php echo $url; ?>"><?php echo $cta; ?></a></p>
		</li>
		<?php
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['select_post'] = $new_instance['select_post'];
		$instance['cta'] = strip_tags( $new_instance['cta'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'show_info' => true, 'post_id' => 0 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>Choose the blog post to appear in this home page pod.</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'select_post' ); ?>"><?php _e('Select post to display:', 'sk-posts'); ?></label><br />
			<select id="<?php echo $this->get_field_id( 'select_post' ); ?>" name="<?php echo $this->get_field_name( 'select_post' ); ?>">
				<?php $this->get_posts_options($instance['select_post']); ?>
			</select>
			
		<p>
			<label for="<?php echo $this->get_field_id( 'cta' ); ?>"><?php _e('Call To Action:', 'sk-posts'); ?></label>
			<input id="<?php echo $this->get_field_id( 'cta' ); ?>" name="<?php echo $this->get_field_name( 'cta' ); ?>" value="<?php echo $instance['cta']; ?>" style="width:94%;" />
		</p>
		
	<?php
	}
	
	function get_posts_options($selected) {
		// The Query
		echo '<option value="0">Please Select a Post</option>';
		$args = array(
			'orderby' => 'title',
			'order' => 'ASC',
			'post_type' => array('post', 'page', 'class', 'people'),
			'posts_per_page' => -1,
			'post_status' => 'publish'
		);
		$query = new WP_Query( $args );

		// The Loop
		while ( $query->have_posts() ) :
			$query->the_post();
			//var_dump($query->post);
			echo '<option value="'. $query->post->ID .'"';
			if($query->post->ID == $selected) {
				echo ' selected="selected" ';
			}
			echo '>' . get_the_title() . '</option>';
		endwhile;
	}
}

/**
 * Adds Foo_Widget widget.
 */
class Sky_Candy_Latest_Posts_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'sk_latest_posts', // Base ID
			'Latest Posts With Thumbnails', // Name
			array( 'description' => __( 'Latest Blog Posts With Thumbnails, custom Sky Candy widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		//echo __( 'Hello, World!', 'text_domain' );
		?>
		<ul class="latest-blog-posts">
		<?php
		get_home_page_posts();
		?>
		</ul>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Sky_Candy_Latest_Posts_Widget

/**
 * Adds Foo_Widget widget.
 */
class Sky_Candy_People_List extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'sk_people_list', // Base ID
			'Sky Candy Instructors List', // Name
			array( 'description' => __( 'Sky Candy Instructors in alphabetical order with thumbnail images, custom Sky Candy widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		//echo __( 'Hello, World!', 'text_domain' );
		?>
		<ul class="instructor_list">
		<?php
		sky_candy_get_teacher_list();
		?>
		</ul>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Sky_Candy_People_List

/**
 * Adds Foo_Widget widget.
 */
class Sky_Candy_Performance_Apparatus extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'sk_perfomance_apparatus', // Base ID
			'Sky Candy Performance Apparatus', // Name
			array( 'description' => __( 'Performance apparatuses of an person, custom Sky Candy widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $post;
		$title = apply_filters( 'widget_title', $instance['title'] );
		if(has_term( '', 'apparatus' )) {

			echo $args['before_widget'];
			if (!empty($title )) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			the_terms($post->ID, 'apparatus', '<p class="apparatuses">', '<br />', '</p>');
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if (isset($instance[ 'title' ])) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Sky_Candy_Performance_Apparatus

/**
 * Adds Foo_Widget widget.
 */
class Sky_Candy_Classes_Taught extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'sk_classes_taught', // Base ID
			'Sky Candy Classes Taught', // Name
			array( 'description' => __( 'Classes taught by a person, custom Sky Candy widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $post;
		global $wpdb;
		$classes = $wpdb->get_results("
			SELECT meta_value
  			FROM $wpdb->postmeta
    			LEFT JOIN $wpdb->posts
      			ON $wpdb->postmeta.meta_value = $wpdb->posts.ID
  			WHERE $wpdb->postmeta.post_id = $post->ID
  				AND $wpdb->postmeta.meta_key = 'sky_candy_classes_taught'
  			ORDER BY $wpdb->posts.post_title ASC
  	");
  	if ($classes) { 

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $args['before_widget'];
			if (!empty($title )) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			else {
				echo $args['before_title'] . 'Classes Taught' . $args['after_title'];
			}
			echo '<ul class="classes-taught">';
			foreach($classes as $class) {
				$post_object = get_post( $class->meta_value );
				$permalink = get_permalink( $post_object->ID ); ?>
					<li class="clearfix"><a href="<?php echo $permalink; ?>"><?php echo $post_object->post_title; ?></a></li>
				<?php
			}
			echo '</ul>';	
			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if (isset($instance[ 'title' ])) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Sky_Candy_Classes_Taught

add_action( 'widgets_init', 'skycandy_register_sidebars' );
add_action( 'widgets_init', 'register_post_widget' );	
?>