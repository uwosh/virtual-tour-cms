<?php

class LotType {
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
        wp_nonce_field( basename( __FILE__ ), 'lot_type_meta_box_nonce' );

        $lot_types = array( "Employee Lot", "Commuter Lot", "Visitor Lot", "Staff Lot", "Resident Lot", "Restricted Lot", "Reserved Lot", "Remote Lot", "Evans Hall Residents", "Stewart Hall Residents", "Fletcher Hall Residents", "Event Parking" );
        $selected_lot_types = ( get_post_meta( $post->ID, '_lot_types', true ) ) ? get_post_meta( $post->ID, '_lot_types', true ) : array();

        foreach( $lot_types as $lot_type ){
            ?>
            <input type="checkbox" name="lot_types[]" value="<?php echo $lot_type; ?>" <?php checked( ( in_array( $lot_type, $selected_lot_types ) ) ? $lot_type : '', $lot_type ); ?> /><?php echo $lot_type; ?><br />
            <?php
        }
    }

    /**
     * Store location meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['lot_type_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['lot_type_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store lot type meta box fields
        // lot_types array
        if( isset( $_POST['lot_types'] ) ){
            $lot_types = (array) $_POST['lot_types'];
            // sanitize array
            $lot_types = array_map( 'sanitize_text_field', $lot_types );
            // save data
            update_post_meta( $post_id, '_lot_types', $lot_types );
        }else{
            // delete data
            delete_post_meta( $post_id, '_lot_types' );
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'lot_types', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $selected_lot_types = ( get_post_meta( $post["id"], '_lot_types', true ) ) ? get_post_meta( $post["id"], '_lot_types', true ) : array();
        return $selected_lot_types;
    }
}