<?php

class URL {
    private $slug;
    private $label;
    private $area_name;

    /**
     * Set up the URL class
     *
     * @param string $slug the slug for the custom post type associated with this URL instance.
     * @param string $label the nice-to-read text label for this instance of URL.
     */
    public function __construct( $slug, $label, $area_name ){
        // Instantiating the class variables
        $this->slug = $slug;
        $this->label = $label;
        $this->area_name = $area_name;

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
        wp_nonce_field( basename( __FILE__ ), 'URL_meta_box_nonce' );

        $url = get_post_meta( $post->ID, $this->area_name . "_url", true );

        ?>
        
        <?php echo $this->label; ?> URL: <input type="text" step="any" name="<?php echo $this->area_name . "_url"; ?>" style="width: 100%; max-width: 1000px;" id="<?php echo $this->area_name . "_url"; ?>" value="<?php echo $url; ?>" placeholder="Enter a <?php echo $this->label ?> url" />

        <?php
    }

    /**
     * Store location meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['URL_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['URL_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        // URL string
        if ( isset( $_REQUEST[$this->area_name . "_url"] ) ) {
            update_post_meta( $post_id, $this->area_name . "_url", sanitize_text_field( $_POST[$this->area_name . "_url"] ) );
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, $this->area_name . "_url", array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        return get_post_meta( $post["id"], $this->area_name . "_url" );
    }
}
