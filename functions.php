<?php

// Create Emergency Phones Custom Post Type
function create_emergency_phones_post_type() {
    register_post_type( 'emergency-phones',
    // Options
        array(
            'labels' => array(
                'name' => __( 'Emergency Phones' ),
                'singular_name' => __( 'Emergency Phone' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-phone',
            'rewrite' => array('slug' => 'emergency-phones'),
        )
    );
}

// Hooking up our function to theme setup
add_action( 'init', 'create_emergency_phones_post_type' );