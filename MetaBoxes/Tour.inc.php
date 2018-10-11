<?php

class Tour {
    private $slug;
    private $label;

    /**
     * Set up the Tour class
     *
     * @param string $slug the slug for the custom post type associated with this tour instance.
     * @param string $label the nice-to-read text label for this instance of tour.
     */
    public function __construct( $slug, $label ){
        // Instantiating the class variables
        $this->slug = $slug;
        $this->label = $label;

        $this->init();
    }

    function init(){
        // Hooking the function to save data when the update button is clicked
        add_action( 'save_post_' . $this->slug, array( $this, 'save_meta_box' ) );

        // Hooking the REST API to include the tour meta data
        add_action( 'rest_api_init', array( $this, 'send_data_to_rest' ) );
    }

    /**
     * Build the tour meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), 'tour_meta_box_nonce' );

        $tour_enabled = get_post_meta( $post->ID, '_tour_enabled', true );
        // TODO: make tour description content area variable to store tour page info

        ?>
        <input type="radio" name="tour_enabled" value="1" <?php checked( $tour_enabled, '1' ); ?> />Enable
		<input type="radio" name="tour_enabled" value="0" <?php checked( $tour_enabled, '0' ); ?> />Disable

        <!-- TODO: make HTML to have a TinyMCE editor window for tour info page that only shows if the enable radio button is active -->

        <?php
    }

    /**
     * Store tour meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['tour_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['tour_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store tour meta box fields
        if ( isset( $_REQUEST['tour_enabled'] ) ) {
            update_post_meta( $post_id, '_tour_enabled', sanitize_text_field( $_POST['tour_enabled'] ) );
        }

        // TODO: store tour info page into post
    }

    // Configure REST API to include tour data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'tour', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $tour_enabled = get_post_meta( $post["id"], '_tour_enabled', true );
        if( $tour_enabled === "1" ){
            return "Tour content to go here"; // TODO build tour into stored into API
        } else{
            return null;
        }
    }
}