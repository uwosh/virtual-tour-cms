<?php

class ParkingLots{
    private $slug;
    private $singular_label;
    private $plural_label;
    private $location_marker;
    private $lot_type;

    public function __construct(){
        // Declaring variables for the custom post type
        $this->slug = "parking-lots";
        $this->singular_label = "Parking Lot";
        $this->plural_label = "Parking Lots";

        // Declaring meta boxes for the custom post type
        $this->location_marker = new Marker( $this->slug, $this->singular_label );
        $this->lot_type = new LotType( $this->slug, $this->singular_label );

        $this->init();
    }

    function init(){
        // Hooking up our emergency phones custom post type to theme setup
        add_action( 'init', array( $this, 'create_emergency_phones_post_type' ) );
    }

    // Create Emergency Phones Custom Post Type
    function create_emergency_phones_post_type() {
        register_post_type( $this->slug,
        // Options
            array(
                'labels' => array(
                    'name' => __( $this->plural_label ),
                    'singular_name' => __( $this->singular_label ),
                    'add_new' => __( 'Add ' . $this->singular_label ),
                    'add_new_item' => __( 'Add New ' . $this->singular_label ),
                    'edit_item' => __( 'Edit ' . $this->singular_label ),
                    'new_item' => __( 'New ' . $this->singular_label ),
                    'view_item' => __( 'View ' . $this->singular_label ),
                    'view_items' => __( 'View ' . $this->plural_label ),
                    'search_items' => __( 'Search ' . $this->plural_label ),
                    'not_found' => __( 'No ' . $this->plural_label . ' found' ),
                    'not_found_in_trash' => __( 'No ' . $this->plural_label . ' found in Trash' ),
                    'all_items' => __( 'All ' . $this->plural_label ),
                    'archives' => __( $this->singular_label . ' Archives' ),
                    'attributes' => __( $this->singular_label . ' Attributes' ),
                    'insert_into_item' => __( 'Insert into ' . $this->singular_label ),
                    'uploaded_to_this_item' => __( 'Uploaded to this ' . $this->singular_label ),
                    'filter_items_list' => __( 'Filter ' . $this->plural_label . ' list' ),
                    'items_list_navigation' => __( $this->plural_label . ' list navigation' ),
                    'items_list' => __( $this->plural_label . ' list' ),
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => 'dashicons-location',
                'show_in_rest' => true,
                'register_meta_box_cb' => array( $this, 'meta_box_callback' ),
                'rewrite' => array('slug' => $this->slug),
            )
        );
    }
    
    // Meta box setup callback function
    function meta_box_callback(){
        add_meta_box( $this->slug . '-location', $this->singular_label . ' Location', array( $this->location_marker, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-types', $this->singular_label . ' Types', array( $this->lot_type, 'create_meta_box' ), $this->slug, 'side');
    }
}