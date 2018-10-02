<?php

// Create Emergency Phones Custom Post Type
function create_emergency_phones_post_type() {
    register_post_type( 'emergency-phones',
    // Options
        array(
            'labels' => array(
                'name' => __( 'Emergency Phones' ),
                'singular_name' => __( 'Emergency Phone' ),
                'add_new' => __( 'Add Emergency Phone' ),
                'add_new_item' => __( 'Add New Emergency Phone' ),
                'edit_item' => __( 'Edit Emergency Phone' ),
                'new_item' => __( 'New Emergency Phone' ),
                'view_item' => __( 'View Emergency Phone' ),
                'view_items' => __( 'View Emergency Phones' ),
                'search_items' => __( 'Search Emergency Phones' ),
                'not_found' => __( 'No Emergency Phones found' ),
                'not_found_in_trash' => __( 'No Emergency Phones found in Trash' ),
                'all_items' => __( 'All Emergency Phones' ),
                'archives' => __( 'Emergency Phone Archives' ),
                'attributes' => __( 'Emergency Phone Attributes' ),
                'insert_into_item' => __( 'Insert into Emergency Phone' ),
                'uploaded_to_this_item' => __( 'Uploaded to this emergency phone' ),
                'filter_items_list' => __( 'Filter emergency phones list' ),
                'items_list_navigation' => __( 'Emergency phones list navigation' ),
                'items_list' => __( 'Emergency phones list' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-phone',
            'show_in_rest' => true,
            'register_meta_box_cb' => 'emergency_phones_meta_box_callback',
            'rewrite' => array('slug' => 'emergency-phones'),
        )
    );
}
// Hooking up our emergency phones custom post type to theme setup
add_action( 'init', 'create_emergency_phones_post_type' );

// Meta box setup callback function
function emergency_phones_meta_box_callback(){
    add_meta_box('emergency-phone-address', 'Emergency Phone Location', 'emergency_phone_location_meta_box', 'emergency-phones', 'side');
}

/**
 * Build the emergency phone location meta box
 *
 * @param post $post The post object
 */
function emergency_phone_location_meta_box( $post ){
    // make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'location_meta_box_nonce' );

    $latitude = get_post_meta( $post->ID, '_latitude', true );
    $longitude = get_post_meta( $post->ID, '_longitude', true );
    ?>
    
    Latitude: <input type="number" name="latitude" value="<?php echo $latitude; ?>" placeholder="Enter a latitude" />
    Longitude: <input type="number" name="longitude" value="<?php echo $longitude; ?>" placeholder="Enter a longitude" />

    <?php
}

/**
 * Store emergency phone location meta box data
 *
 * @param int $post_id The post ID.
 */
function emergency_phone_location_save_meta_box( $post_id ){
    // verify taxonomies meta box nonce
    if ( !isset( $_POST['location_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['location_meta_box_nonce'], basename( __FILE__ ) ) ){
        return;
    }

    // return if autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
		return;
    }
    
    // Check the user's permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
    }
    
    // store location meta box fields
	// latitude string
	if ( isset( $_REQUEST['latitude'] ) ) {
		update_post_meta( $post_id, '_latitude', sanitize_text_field( $_POST['latitude'] ) );
	}
	
	// longitude string
	if ( isset( $_REQUEST['longitude'] ) ) {
		update_post_meta( $post_id, '_longitude', sanitize_text_field( $_POST['longitude'] ) );
	}
}
// Hooking the function to save data when the update button is clicked
add_action( 'save_post_emergency-phones', 'emergency_phone_location_save_meta_box' );

// Configure REST API to include emergency phone location data
function send_emergency_phone_location_to_rest(){
    register_rest_field( 'emergency-phones', 'location', array(
            'get_callback' => 'parse_emergency_phones_location_meta_data_for_api'
        )
    );
}
// Hooking the REST API to include the emergency phone location meta data
add_action( 'rest_api_init', 'send_emergency_phone_location_to_rest' );

// Function that grabs the emergency phones meta data
function parse_emergency_phones_location_meta_data_for_api( $post ) {
    return array(
        'latitude' => get_post_meta( $post["id"], '_latitude', true ),
        'longitude' => get_post_meta( $post["id"], '_longitude', true )
    );
}