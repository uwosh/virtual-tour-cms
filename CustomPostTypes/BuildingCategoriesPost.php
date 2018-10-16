<?php

class BuildingCatagoriesPost{
    private $slug;
    private $singular_label;
    private $plural_label;
    private $youtube_url;
    private $learn_more_url;

    public function __construct(){
        // Declaring variables for the custom post type
        $this->slug = "building-categories";
        $this->singular_label = "Building Category";
        $this->plural_label = "Building Categories";

        // Declaring meta boxes for the custom post type
        $this->youtube_url = new URL($this->slug, "YouTube", "youtube");
        $this->learn_more_url = new URL($this->slug, "Learn More", "learn_more");

        $this->init();
    }

    function init(){
        // Hooking up our building categories custom post type to theme setup
        add_action( 'init', array( $this, 'create_emergency_phones_post_type' ) );
    }

    // Create building categories Custom Post Type
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
                'menu_icon' => 'dashicons-category',
                'show_in_rest' => true,
                'register_meta_box_cb' => array( $this, 'meta_box_callback' ),
                'rewrite' => array('slug' => $this->slug),
            )
        );
    }
    
    // Meta box setup callback function
    function meta_box_callback(){
        add_meta_box( $this->slug . '-youtube-url', $this->singular_label . ' URL', array( $this->youtube_url, 'create_meta_box' ), $this->slug, 'normal');
        add_meta_box( $this->slug . '-learn-more-url', $this->singular_label . ' URL', array( $this->learn_more_url, 'create_meta_box' ), $this->slug, 'normal');
    }
}