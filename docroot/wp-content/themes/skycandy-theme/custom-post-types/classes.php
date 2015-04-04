<?php
function custom_post_class() {
	$labels = array(
		'name'               => _x( 'Classes', 'post type general name' ),
		'singular_name'      => _x( 'Class', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'class' ),
		'add_new_item'       => __( 'Add New Class' ),
		'edit_item'          => __( 'Edit Class' ),
		'new_item'           => __( 'New Class' ),
		'all_items'          => __( 'All Classes' ),
		'view_item'          => __( 'View Class' ),
		'search_items'       => __( 'Search Classes' ),
		'not_found'          => __( 'No classes found' ),
		'not_found_in_trash' => __( 'No classes found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Classes'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Class descriptions',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => true,
	);
	register_post_type( 'class', $args );	
}
add_action( 'init', 'custom_post_class' );

function class_updated_messages( $messages ) {
	global $post, $post_ID;
	$messages['class'] = array(
		0 => '', 
		1 => sprintf( __('Class updated. <a href="%s">View class</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Class updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('promo restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Class published. <a href="%s">View class</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('class saved.'),
		8 => sprintf( __('Class submitted. <a target="_blank" href="%s">Preview class</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Class scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview class</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Class draft updated. <a target="_blank" href="%s">Preview class</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'class_updated_messages' );

/**
* Add box for Mindbody URL to the subscribe form.
*
**/
function mindbody_url_box() {
    add_meta_box( 
        'mindbody_url_box',
        __( 'Mindbody Registration URL', 'skycandy_class' ),
        'mindbody_url_callback',
        'class',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mindbody_url_box' );

function mindbody_url_callback( $post ) {
	wp_nonce_field( 'sky_candy_class', 'mindbody_url_nonce' );
	$default = esc_attr( get_post_meta( $post->ID, 'mindbody_url', true ) );
	echo '<p>Enter the URL to register for this class on MindBody.</p>';
	echo '<label for="mindbody_url">URL: </label>';
	echo '<input type="text" name="mindbody_url" class="widefat" size="30" value="' . $default . '"></input>';
}

add_action( 'save_post', 'mindbody_url_save' );
function mindbody_url_save( $post_id ) {
	if(isset($_POST['post_type']) && $_POST['post_type'] == 'class') {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}	

		if ( !wp_verify_nonce( $_POST['mindbody_url_nonce'], 'sky_candy_class' ) ) {
			return;
		}
	
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		}
		if($_POST['mindbody_url']) {
			$url = esc_url($_POST['mindbody_url']);
			update_post_meta( $post_id, 'mindbody_url', $url );
		}
	}
}

/**
* Add box for price to class content type.
*
**/
function skycandy_class_price() {
    add_meta_box( 
        'price_box',
        __( 'Class Price', 'skycandy_class_price' ),
        'skycandy_class_price_callback',
        'class',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'skycandy_class_price' );

function skycandy_class_price_callback( $post ) {
	wp_nonce_field( 'sky_candy_class', 'skycandy_class_price_nonce' );
	$default = esc_attr( get_post_meta( $post->ID, 'class_price', true ) );
	echo '<p>Enter the price for this class.</p>';
	echo '<label for="mindbody_url">Price: </label>';
	echo '<input type="text" name="class_price" class="widefat" size="30" value="' . $default . '"></input>';
}

add_action( 'save_post', 'skycandy_class_price_save' );
function skycandy_class_price_save( $post_id ) {
	if(isset($_POST['post_type']) && $_POST['post_type'] == 'class') {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}	

		if ( !wp_verify_nonce( $_POST['skycandy_class_price_nonce'], 'sky_candy_class' ) ) {
			return;
		}
	
		if ( 'class' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		}
		if ($_POST['class_price']) {
			$price = preg_replace("/[^0-9,.]/", "", $_POST['class_price']);
			update_post_meta( $post_id, 'class_price', $price );
		}
	}
}


/**
 * Add custom taxonomy for 6-week series, drop-in, etc
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_taxonomies() {
	// Add new "Locations" taxonomy to Posts
	register_taxonomy('class_type', 'class', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => false,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Class Types', 'taxonomy general name' ),
			'singular_name' => _x( 'Class Type', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Classes By Type' ),
			'all_items' => __( 'All Class Types' ),
			'parent_item' => __( 'Parent Class Type' ),
			'parent_item_colon' => __( 'Parent Class Type:' ),
			'edit_item' => __( 'Edit Class Type' ),
			'update_item' => __( 'Update Class Type' ),
			'add_new_item' => __( 'Add New Class Type' ),
			'new_item_name' => __( 'New Class Type Name' ),
			'menu_name' => __( 'Class Types' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'class_type', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		),
	));
}
add_action( 'init', 'add_custom_taxonomies', 0 );
?>