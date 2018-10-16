<?php

class Address{
    private $slug;
    private $label;

    /**
     * Set up the Address class
     *
     * @param string $slug the slug for the custom post type associated with this Address instance.
     * @param string $label the nice-to-read text label for this instance of Address.
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
     * Build the Address meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), 'address_meta_box_nonce' );

        $states = array( 'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming' );
        $states = array_reverse($states);

        $street = get_post_meta( $post->ID, '_street', true );
        $city = get_post_meta( $post->ID, '_city', true );
        $state = get_post_meta( $post->ID, '_state', true );
        if( !empty( $state ) ){
            $state = $state[0]; // array of length one, so select first element
        }
        $zip = get_post_meta( $post->ID, '_zip', true );

        ?>
        <style>
        .street-input {
            width: 100%;
            max-width: 500px;
        }
        .inline {
            display: inline-block;
        }
        </style>
        <div>
            Street:<br />
            <input class="street-input" type="text" name="street" value="<?php echo $street; ?>" placeholder="Enter a street address" />
        </div>
        <div>
            <div class="inline">
                City:<br />
                <input type="text" name="city" value="<?php echo $city; ?>" placeholder="Enter a city" />
            </div>
            <div class="inline">
                State:<br />
                <select name="state[]">
                    <option value="" <?php echo ($state==="Select a state") ? "selected" : "" ?>>Select a state</option>
                    <?php
                    foreach( $states as $current_state ){
                    ?>
                    <option value="<?php echo $current_state; ?>" <?php echo ($state===$current_state) ? "selected" : "" ?>><?php echo $current_state; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="inline">
                Zip:<br />
                <input type="number" name="zip" pattern="[0-9]{5}" maxlength="5" value="<?php echo $zip; ?>" placeholder="Enter a zip code" />
            </div>
        </div>
        <?php
    }

    /**
     * Store address meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['address_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['address_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store address meta box fields
        // street string
        if ( isset( $_REQUEST['street'] ) ) {
            update_post_meta( $post_id, '_street', sanitize_text_field( $_POST['street'] ) );
        }
        
        // city string
        if ( isset( $_REQUEST['city'] ) ) {
            update_post_meta( $post_id, '_city', sanitize_text_field( $_POST['city'] ) );
        }

        // state array
        if ( isset( $_POST['state'] ) ) {
            update_post_meta( $post_id, '_state', $_POST['state'] );
        }

        // zip string
        if ( isset( $_REQUEST['zip'] ) ) {
            update_post_meta( $post_id, '_zip', sanitize_text_field( $_POST['zip'] ) );
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'address', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $street = get_post_meta( $post["id"], '_street', true );
        $city = get_post_meta( $post["id"], '_city', true );
        $state = get_post_meta( $post["id"], '_state', true )[0]; // returns an array with one element, so grab first element
        $zip = get_post_meta( $post["id"], '_zip', true );

        return array(
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'zip' => $zip
        );
    }
}

?>
