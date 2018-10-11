<?php

class BuildingCategories {
    private $slug;
    private $label;

    /**
     * Set up the Building Categories class
     *
     * @param string $slug the slug for the custom post type associated with this building categories instance.
     * @param string $label the nice-to-read text label for this instance of building categories.
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

        // Hooking the REST API to include the building categories meta data
        add_action( 'rest_api_init', array( $this, 'send_data_to_rest' ) );
    }

    /**
     * Build the building categories meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), 'building_categories_meta_box_nonce' );

        $building_categories = array( "Academic Facilities", "Athletics", "Academic Services", "Campus Services", "Residence Halls", "Dining", "Student Recreation" );
        $selected_building_categories = ( get_post_meta( $post->ID, '_building_categories', true ) ) ? get_post_meta( $post->ID, '_building_categories', true ) : array();

        foreach( $building_categories as $building_category ){
            ?>
            <input type="checkbox" name="building_categories[]" value="<?php echo $building_category; ?>" <?php checked( ( in_array( $building_category, $selected_building_categories ) ) ? $building_category : '', $building_category ); ?> /><?php echo $building_category; ?><br />
            <?php
        }
    }

    /**
     * Store building categories meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['building_categories_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['building_categories_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        // building_categories array
        if( isset( $_POST['building_categories'] ) ){
            $building_categories = (array) $_POST['building_categories'];
            // sanitize array
            $building_categories = array_map( 'sanitize_text_field', $building_categories );
            // save data
            update_post_meta( $post_id, '_building_categories', $building_categories );
        }else{
            // delete data
            delete_post_meta( $post_id, '_building_categories' );
        }
    }

    // Configure REST API to include building categories data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'building_categories', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $selected_building_categories = ( get_post_meta( $post["id"], '_building_categories', true ) ) ? get_post_meta( $post["id"], '_building_categories', true ) : array();
        return $selected_building_categories;
    }
}