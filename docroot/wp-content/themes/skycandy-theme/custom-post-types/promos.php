<?php
function custom_post_promo() {
	$labels = array(
		'name'               => _x( 'Promos', 'post type general name' ),
		'singular_name'      => _x( 'Promo', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'promo' ),
		'add_new_item'       => __( 'Add New Promo' ),
		'edit_item'          => __( 'Edit Promo' ),
		'new_item'           => __( 'New Promo' ),
		'all_items'          => __( 'All Promos' ),
		'view_item'          => __( 'View Promo' ),
		'search_items'       => __( 'Search Promos' ),
		'not_found'          => __( 'No promos found' ),
		'not_found_in_trash' => __( 'No promos found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Promos'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Promos for the home page slideshow',
		'public'        => true,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'has_archive'   => false,
	);
	register_post_type( 'promo', $args );	
}
add_action( 'init', 'custom_post_promo' );

function promo_updated_messages( $messages ) {
	global $post, $post_ID;
	$messages['promo'] = array(
		0 => '', 
		1 => sprintf( __('Promo updated. <a href="%s">View promo</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('Promo updated.'),
		5 => isset($_GET['revision']) ? sprintf( __('promo restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Promo published. <a href="%s">View promo</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('promo saved.'),
		8 => sprintf( __('Promo submitted. <a target="_blank" href="%s">Preview promo</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('Promo scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview promo</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('Promo draft updated. <a target="_blank" href="%s">Preview promo</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'promo_updated_messages' );

function promo_url_box() {
    add_meta_box( 
        'promo_url_box',
        __( 'Promo Link URL', 'skycandy_promo' ),
        'promo_url_callback',
        'promo',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'promo_url_box' );

function promo_url_callback( $post ) {
	wp_nonce_field( 'sky_candy_promo', 'promo_url_nonce' );
	$default = esc_attr( get_post_meta( $post->ID, 'promo_url', true ) );
	echo '<p>Enter the url you would like this promo to link to from the home page slideshow. If this field is left blank, the slide will link to a page containing the content from the edit box above.</p>';
	echo '<label for="promo_url">URL: </label>';
	echo '<input type="text" name="promo_url" class="widefat" size="30" value="' . $default . '"></input>';
}

add_action( 'save_post', 'product_price_box_save' );
function product_price_box_save( $post_id ) {
	if(isset($_POST['post_type']) && $_POST['post_type'] == 'promo') {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}	

		if ( !wp_verify_nonce( $_POST['promo_url_nonce'], 'sky_candy_promo' ) ) {
			return;
		}
	
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		}
		if ($_POST['promo_url']) {
			$url = esc_url($_POST['promo_url']);
			update_post_meta( $post_id, 'promo_url', $url );
		}
	}
}

?>