<?php

class IsAccessibleParkingLot {
    private $slug;
    private $label;

    /**
     * Set up the Lot Type class
     *
     * @param string $slug the slug for the custom post type associated with this lot type instance.
     * @param string $label the nice-to-read text label for this instance of lot type.
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

        // Hooking the REST API to include the location meta data
        add_action( 'rest_api_init', array( $this, 'send_data_to_rest' ) );
    }

    /**
     * Build the location meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), 'is_accessible_parking_lot_meta_box_nonce' );

        $isAccessibleParkingLot = get_post_meta( $post->ID, '_is_accessible_parking_lot', true );

        ?>

        <input type="radio" name="isAccessibleParkingLot" value="1" <?php checked( $isAccessibleParkingLot, '1' ); ?> /> Yes<br />
		<input type="radio" name="isAccessibleParkingLot" value="0" <?php checked( $isAccessibleParkingLot, '0' ); ?> /> No
        
        <?php
    }

    /**
     * Store location meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['is_accessible_parking_lot_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['is_accessible_parking_lot_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store accessibility meta box fields
        if ( isset( $_REQUEST['isAccessibleParkingLot'] ) ) {
            update_post_meta( $post_id, '_is_accessible_parking_lot', sanitize_text_field( $_POST['isAccessibleParkingLot'] ) );
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'is_accessible_parking_lot', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $isAccessibleParkingLot = get_post_meta( $post["id"], '_is_accessible_parking_lot', true );
        if( $isAccessibleParkingLot === "1" ){
            return true;
        } else{
            return false;
        }
    }
}