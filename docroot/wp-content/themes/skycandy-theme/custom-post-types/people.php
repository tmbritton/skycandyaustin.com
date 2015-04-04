<?php
function custom_post_people() {
	$labels = array(
		'name'               => _x( 'People', 'post type general name' ),
		'singular_name'      => _x( 'Person', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'person' ),
		'add_new_item'       => __( 'Add New Person' ),
		'edit_item'          => __( 'Edit Person' ),
		'new_item'           => __( 'New Person' ),
		'all_items'          => __( 'All People' ),
		'view_item'          => __( 'View Person' ),
		'search_items'       => __( 'Search People' ),
		'not_found'          => __( 'No people found' ),
		'not_found_in_trash' => __( 'No people found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'People'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'People associated with Sky Candy, teachers, studio company members, etc...',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt'),
		'has_archive'   => false,
	);
	register_post_type( 'people', $args );	
}
add_action( 'init', 'custom_post_people' );

function people_updated_messages( $messages ) {
	global $post, $post_ID;
	$messages['people'] = array(
		0 => '', 
		1 => sprintf( __('Person updated. <a href="%s">View person</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Person updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('person restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Person published. <a href="%s">View person</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('person saved.'),
		8 => sprintf( __('Person submitted. <a target="_blank" href="%s">Preview person</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Publish scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview promo</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Person draft updated. <a target="_blank" href="%s">Preview person</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'people_updated_messages' );

function sky_candy_classes_taught() {
    add_meta_box( 
        'sky_candy_classes_taught',
        __( 'Classes Taught', 'sky_candy_classes_taught' ),
        'sky_candy_classes_taught_callback',
        'people',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'sky_candy_classes_taught' );

function sky_candy_classes_taught_callback( $post ) {
	wp_nonce_field( 'sky_candy_people', 'sky_candy_classes_taught_nonce' );
	?>
	<p>Please check the classes that <?php echo $post->post_title; ?> teaches.</p>
	<fieldset>
	<legend>Select Classes:</legend>
	<?php sky_candy_get_classes($post->ID) ?>
</fieldset>
	<?php
}

function sky_candy_get_classes($post) {
	$defaults = get_post_meta( $post, 'sky_candy_classes_taught', FALSE );
	$args = array(
	'posts_per_page'  => -1,
	'numberposts'     => -1,
	'offset'          => 0,
	'category'        => '',
	'orderby'         => 'title',
	'order'           => 'ASC',
	'include'         => '',
	'exclude'         => '',
	'meta_key'        => '',
	'meta_value'      => '',
	'post_type'       => 'class',
	'post_mime_type'  => '',
	'post_parent'     => '',
	'post_status'     => 'publish',
	'suppress_filters' => true );
	$classes_array = get_posts( $args );
	foreach( $classes_array as $post ) :	setup_postdata($post); ?>
		<input id="<?php echo $post->post_name; ?>" type="checkbox" name="classes[]" value="<?php echo $post->ID ?>" <?php if(in_array($post->ID, $defaults)){ echo ' checked=checked'; } ?> />
		<label for="<?php echo $post->post_name; ?>"><?php echo $post->post_title; ?></label>
		<br /> 
	<?php endforeach; 
}	

add_action( 'save_post', 'sky_candy_classes_taught_save' );
function sky_candy_classes_taught_save( $post_id ) {
	if(isset($_POST['post_type']) && $_POST['post_type'] == 'people') {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}	

		if ( !wp_verify_nonce( $_POST['sky_candy_classes_taught_nonce'], 'sky_candy_people' ) ) {
			return;
		}
	
		if ( 'post' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		}
		delete_post_meta($post_id, 'sky_candy_classes_taught');
  	if($classes = $_POST['classes']) {
		  foreach($classes as $class) {
			  add_post_meta( $post_id, 'sky_candy_classes_taught', $class );
		  }
    }  
	}
}

/**
 * Add custom taxonomy for the person's role at Sky Candy 
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_taxonomy_role() {
	// Add new "Locations" taxonomy to Posts
	register_taxonomy('role', 'people', array(
		// Hierarchical taxonomy (like categories)
		'hierarchical' => false,
		// This array of options controls the labels displayed in the WordPress Admin UI
		'labels' => array(
			'name' => _x( 'Role', 'taxonomy general name' ),
			'singular_name' => _x( 'Role', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Roles' ),
			'all_items' => __( 'All Roles' ),
			'parent_item' => __( 'Parent Role' ),
			'parent_item_colon' => __( 'Parent Role:' ),
			'edit_item' => __( 'Edit Role' ),
			'update_item' => __( 'Update Role' ),
			'add_new_item' => __( 'Add Role' ),
			'new_item_name' => __( 'New Role Name' ),
			'menu_name' => __( 'Roles' ),
		),
		// Control the slugs used for this taxonomy
		'rewrite' => array(
			'slug' => 'role', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		),
	));
}
add_action( 'init', 'add_taxonomy_role', 0 );

/**
 * Add custom taxonomy for the person's role at Sky Candy 
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_taxonomy_apparati() {
  // Add new "Locations" taxonomy to Posts
  register_taxonomy('apparatus', 'people', array(
    // Hierarchical taxonomy (like categories)
    'hierarchical' => false,
    // This array of options controls the labels displayed in the WordPress Admin UI
    'labels' => array(
      'name' => _x( 'Performance Apparatuses', 'taxonomy general name' ),
      'singular_name' => _x( 'Apparatus', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Apparatuses' ),
      'all_items' => __( 'All Apparatuses' ),
      'parent_item' => __( 'Parent Apparatus' ),
      'parent_item_colon' => __( 'Parent Apparatus:' ),
      'edit_item' => __( 'Edit Apparatus' ),
      'update_item' => __( 'Update Apparatus' ),
      'add_new_item' => __( 'Add Apparatus' ),
      'new_item_name' => __( 'New Apparatus Name' ),
      'menu_name' => __( 'Apparatuses' ),
    ),  
    // Control the slugs used for this taxonomy
    'rewrite' => array(
      'slug' => 'apparatus', // This controls the base slug that will display before each term
      'with_front' =>  true, // Don't display the category base before "/locations/"
      'hierarchical' => false // This will allow URL's like "/locations/boston/cambridge/"
    ),  
  )); 
}
add_action( 'init', 'add_taxonomy_apparati', 0 );
?>
