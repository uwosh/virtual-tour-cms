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
            'rewrite' => array('slug' => 'emergency-phones'),
        )
    );
}

// Hooking up our emergency phones custom post type to theme setup
add_action( 'init', 'create_emergency_phones_post_type' );