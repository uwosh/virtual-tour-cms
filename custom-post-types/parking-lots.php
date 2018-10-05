<?php

function create_parking_lots_post_type(){
        register_post_type( 'parking-lots',
            array(
                'labels' => array(
                    'name' => __( 'Parking Lots' ),
                    'singular_name' => __( 'Parking Lots'),
                    'add_new' => __( 'Add Parking Lots' ),
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
                'menu_icon' => 'dashincons-pressthis',
                'show_in_rest' => true,
                'register_meta_box_cb' => 'parkings_lots_meta_box_callback',
                'rewrite' => array('slug' => 'emergency-phones'),

            )

        );
}
add_action( 'init', 'create_parking_lots_post_type' );



?>