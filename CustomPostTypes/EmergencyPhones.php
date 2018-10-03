<?php

$slug = "emergency-phones";
$singular_label = "Emergency Phone";
$plural_label = "Emergency Phones";

// Create Emergency Phones Custom Post Type
function create_emergency_phones_post_type() {
    global $slug;
    global $singular_label;
    global $plural_label;

    register_post_type( $slug,
    // Options
        array(
            'labels' => array(
                'name' => __( $plural_label ),
                'singular_name' => __( $singular_label ),
                'add_new' => __( 'Add ' . $singular_label ),
                'add_new_item' => __( 'Add New ' . $singular_label ),
                'edit_item' => __( 'Edit ' . $singular_label ),
                'new_item' => __( 'New ' . $singular_label ),
                'view_item' => __( 'View ' . $singular_label ),
                'view_items' => __( 'View ' . $plural_label ),
                'search_items' => __( 'Search ' . $plural_label ),
                'not_found' => __( 'No ' . $plural_label . ' found' ),
                'not_found_in_trash' => __( 'No ' . $plural_label . ' found in Trash' ),
                'all_items' => __( 'All ' . $plural_label ),
                'archives' => __( $singular_label . ' Archives' ),
                'attributes' => __( $singular_label . ' Attributes' ),
                'insert_into_item' => __( 'Insert into ' . $singular_label ),
                'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular_label ),
                'filter_items_list' => __( 'Filter ' . $plural_label . ' list' ),
                'items_list_navigation' => __( $plural_label . ' list navigation' ),
                'items_list' => __( $plural_label . ' list' ),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-phone',
            'show_in_rest' => true,
            'register_meta_box_cb' => 'meta_box_callback', 
            'rewrite' => array('slug' => $slug),
        )
    );
}
// Hooking up our emergency phones custom post type to theme setup
add_action( 'init', 'create_emergency_phones_post_type' );

// Meta box setup callback function
function meta_box_callback(){
    global $slug;
    global $singular_label;
    global $plural_label;
    $emergency_phone_marker = new Marker( $slug, $singular_label );

    add_meta_box( $slug . '-address', $singular_label . ' Location', array( $emergency_phone_marker, 'location_meta_box' ), $slug, 'side');
}