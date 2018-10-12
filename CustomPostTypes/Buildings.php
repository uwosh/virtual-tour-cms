<?php

class Buildings{
    private $slug;
    private $singular_label;
    private $plural_label;
    private $location_marker;
    private $address;
    private $building_has_accessible_entrance;
    private $full_image;
    private $tooltip_image;
    private $categories;
    private $tour;
    private $sustainability;
    private $bathrooms;
    private $dining;

    public function __construct(){
        // Declaring variables for the custom post type
        $this->slug = "buildings";
        $this->singular_label = "Building";
        $this->plural_label = "Buildings";

        // Declaring meta boxes for the custom post type
        $this->location_marker = new Marker( $this->slug, $this->singular_label );
        $this->address = new Address( $this->slug, $this->singular_label );
        $this->building_has_accessible_entrance = new IsAccessible( $this->slug, $this->singular_label );
        $this->full_image = new FullImage( $this->slug, $this->singular_label );
        $this->tooltip_image = new TooltipImage( $this->slug, $this->singular_label );
        $this->categories = new BuildingCategories( $this->slug, $this->singular_label );
        $this->tour = new DetailPage( $this->slug, $this->singular_label, 'tour' );
        $this->sustainability = new DetailPage( $this->slug, $this->singular_label, 'sustainability' );
        $this->bathrooms = new DetailPage( $this->slug, $this->singular_label, 'bathrooms' );
        $this->dining = new DetailPage( $this->slug, $this->singular_label, 'dining' );

        $this->init();
    }

    function init(){
        // Hooking up our buildings custom post type to theme setup
        add_action( 'init', array( $this, 'create_buildings_post_type' ) );
    }

    // Create Buildings Custom Post Type
    function create_buildings_post_type() {
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
                'menu_icon' => 'dashicons-building',
                'show_in_rest' => true,
                'register_meta_box_cb' => array( $this, 'meta_box_callback' ),
                'rewrite' => array('slug' => $this->slug),
            )
        );
    }
    
    // Meta box setup callback function
    function meta_box_callback(){
        add_meta_box( $this->slug . '-location', $this->singular_label . ' Location', array( $this->location_marker, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-address', $this->singular_label . ' Address', array( $this->address, 'create_meta_box' ), $this->slug, 'normal');
        add_meta_box( $this->slug . '-accessible-entrance', $this->singular_label . ' Has Accessible Entrances', array( $this->building_has_accessible_entrance, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-full-image', $this->singular_label . ' Full Image', array( $this->full_image, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-tooltip-image', $this->singular_label . ' Tooltip Image', array( $this->tooltip_image, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-categories', $this->singular_label . ' Categories', array( $this->categories, 'create_meta_box' ), $this->slug, 'side');
        add_meta_box( $this->slug . '-tour', $this->singular_label . ' Tour', array( $this->tour, 'create_meta_box' ), $this->slug, 'normal');
        add_meta_box( $this->slug . '-sustainability', $this->singular_label . ' Sustainability', array( $this->sustainability, 'create_meta_box' ), $this->slug, 'normal');
        add_meta_box( $this->slug . '-bathrooms', $this->singular_label . ' Bathrooms', array( $this->bathrooms, 'create_meta_box' ), $this->slug, 'normal');
        add_meta_box( $this->slug . '-dining', $this->singular_label . ' Dining', array( $this->dining, 'create_meta_box' ), $this->slug, 'normal');
    }
}