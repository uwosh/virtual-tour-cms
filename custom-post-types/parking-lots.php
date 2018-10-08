<?php

function create_parking_lots_post_type(){
        register_post_type( 'parking-lots',
            array(
                'labels' => array(
                    'name' => __( 'Parking Lots' ),
                    'singular_name' => __( 'Parking Lots'),
                    'add_new' => __( 'Add Parking Lot' ),
                    'add_new_item' => __( 'Add New Parking Lot' ),
                    'edit_item' => __( 'Edit Parking Lot' ),
                    'new_item' => __( 'New Parking Lot' ),
                    'view_item' => __( 'View Parking Lot' ),
                    'view_items' => __( 'View Parking Lots' ),
                    'search_items' => __( 'Search Parking Lots' ),
                    'not_found' => __( 'No Parking Lots found' ),
                    'not_found_in_trash' => __( 'No Parking Lots found in Trash' ),
                    'all_items' => __( 'All Parking Lots' ),
                    'archives' => __( 'Parking Lot Archives' ),
                    'attributes' => __( 'Parking Lot Attributes' ),
                    'insert_into_item' => __( 'Insert into Parking Lot' ),
                    'uploaded_to_this_item' => __( 'Uploaded to this Parking Lot' ),
                    'filter_items_list' => __( 'Filter parking lots list' ),
                    'items_list_navigation' => __( 'Parkings lot list navigation' ),
                    'items_list' => __( 'Parking lots list' ),

                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => 'dashicons-location',
                'show_in_rest' => true,
                'register_meta_box_cb' => 'parkings_lots_meta_box_callback',
                'rewrite' => array('slug' => 'parking-lots'),

            )

        );
}
add_action( 'init', 'create_parking_lots_post_type' );

function parking_lots_meta_box_callback(){
    add_meta_box('parking-lot-info', 'Parking Lot Info', 'parking_lot_info_meta_box', 'parking-lots', 'side');

}


function parking_lot_info_meta_box( $post ){
    // make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'location_meta_box_nonce' );

    $latitude = get_post_meta( $post->ID, '_latitude', true );
    $longitude = get_post_meta( $post->ID, '_longitude', true );
    $accessible_parking = get_post_meta($post->ID, '_accessibilty', true);
    ?>
    
    Latitude: <input type="number" step="any" name="latitude" value="<?php echo $latitude; ?>" placeholder="Enter a latitude" />
    Longitude: <input type="number" step="any" name="longitude" value="<?php echo $longitude; ?>" placeholder="Enter a longitude" />
    Accessible Parking?: <input type="radio" id="accessible_parking" name="accesible_parking" />

    <?php
}

function parking_lot_info_save_meta_box( $post_id ){
    // verify taxonomies meta box nonce
    if ( !isset( $_POST['location_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['info_meta_box_nonce'], basename( __FILE__ ) ) ){
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
    
    //accessible parking radiobutton
    if( isset( $_REQUEST['accessible_parking'])){
        update_post_meta( $post_id, '_accessibility');
    }
}
// Hooking the function to save data when the update button is clicked
add_action( 'save_post_parking-lots', 'parking_lot_info_save_meta_box' );

// Configure REST API to include parking lot info data
function send_parking_lot_info_to_rest(){
    register_rest_field( 'parking-lots', 'location', array(
            'get_callback' => 'parse_parking_lot_info_meta_data_for_api'
        )
    );
}
// Hooking the REST API to include the emergency phone location meta data
add_action( 'rest_api_init', 'send_parking_lot_info_to_rest' );

// Function that grabs the emergency phones meta data for the REST API
function parse_parking_lot_info_meta_data_for_api( $post ) {
    return array(
        'latitude' => get_post_meta( $post["id"], '_latitude', true ),
        'longitude' => get_post_meta( $post["id"], '_longitude', true ),
        'accessible_parking' => get_post_meta( $post["id"], '_accesibility', true)
    );
}



?>