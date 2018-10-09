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

        $street = get_post_meta( $post->ID, '_street', true );
        $city = get_post_meta( $post->ID, '_city', true );
        $state = get_post_meta( $post->ID, '_state', true );
        $zipcode = get_post_meta( $post->ID, '_zipcode', true );

        ?>
        
        Street: 
        City: 
        State:  
        Zipcode: <input type="number" pattern="[0-9]{5}" name="longitude" value="<?php echo $zipcode; ?>" placeholder="Enter a longitude" />
        <?php
    }

    
}

?>