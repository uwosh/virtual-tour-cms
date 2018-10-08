<?php

class Marker {
    private $slug;
    private $label;

    /**
     * Set up the Marker class
     *
     * @param string $slug the slug for the custom post type associated with this marker instance.
     * @param string $label the nice-to-read text label for this instance of marker.
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
        wp_nonce_field( basename( __FILE__ ), 'marker_meta_box_nonce' );

        $latitude = get_post_meta( $post->ID, '_latitude', true );
        $longitude = get_post_meta( $post->ID, '_longitude', true );

        ?>
        
        Latitude: <input type="number" step="any" name="latitude" value="<?php echo $latitude; ?>" placeholder="Enter a latitude" />
        Longitude: <input type="number" step="any "name="longitude" value="<?php echo $longitude; ?>" placeholder="Enter a longitude" />

        <?php
    }

    /**
     * Store location meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['marker_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['marker_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store location meta box fields
        // latitude string
        if ( isset( $_REQUEST['latitude'] ) ) {
            update_post_meta( $post_id, '_latitude', sanitize_text_field( $_POST['latitude'] ) );
        }
        
        // longitude string
        if ( isset( $_REQUEST['longitude'] ) ) {
            update_post_meta( $post_id, '_longitude', sanitize_text_field( $_POST['longitude'] ) );
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'location', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        return array(
            'latitude' => get_post_meta( $post["id"], '_latitude', true ),
            'longitude' => get_post_meta( $post["id"], '_longitude', true )
        );
    }
}