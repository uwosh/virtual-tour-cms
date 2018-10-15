<?php

class DetailPage {
    private $slug;
    private $label;
    private $area_name;

    /**
     * Set up the Detail Page class
     *
     * @param string $slug the slug for the custom post type associated with this detail page instance.
     * @param string $label the nice-to-read text label for this instance of detail page.
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

        // Hooking the REST API to include the detail page meta data
        add_action( 'rest_api_init', array( $this, 'send_data_to_rest' ) );
    }

    /**
     * Build the detail page meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), $this->area_name . '_meta_box_nonce' );

        $is_enabled = get_post_meta( $post->ID, '_' . $this->area_name . '_is_enabled', true );
        $description = get_post_meta( $post->ID, '_' . $this->area_name . '_description', true );

        ?>

        <input type="radio" id="<?php echo $this->area_name; ?>_radio_enable" name="<?php echo $this->area_name; ?>_is_enabled" value="1" onclick="show_<?php echo $this->area_name; ?>_wp_editor()" <?php checked( $is_enabled, '1' ); ?>/>
        <label for="<?php echo $this->area_name; ?>radio_enable">Enable</label>
	    <input type="radio" id="<?php echo $this->area_name; ?>_radio_disable" name="<?php echo $this->area_name; ?>_is_enabled" value="0" onclick="hide_<?php echo $this->area_name; ?>_wp_editor()" <?php checked( $is_enabled, '0' ); ?> />
        <label for="<?php echo $this->area_name; ?>_radio_disable">Disable</label>



        <!-- TinyMCE editor window for detail page info page  -->
        <!-- resource: https://codex.wordpress.org/Function_Reference/wp_editor -->        
        <?php 
            $editor_id = $this->area_name . '_editor';
            wp_editor( $description, $editor_id ); 
        ?>

        <style>
        #wp-<?php echo $this->area_name; ?>_editor-wrap{
            display: none;
            padding-top: 20px;
        }
        </style>
        
        <!-- editor only shows if the enable radio button is active -->
        <script type="text/javascript">

            var <?php echo $this->area_name; ?>_editor_id = "<?php echo $editor_id; ?>";
            var <?php echo $this->area_name; ?>_is_enabled = <?php echo ( $is_enabled == "" ? "0" : $is_enabled ); ?>;


            window.addEventListener("load", function(event){
                if(<?php echo $this->area_name; ?>_is_enabled == 1){
                    show_<?php echo $this->area_name; ?>_wp_editor();
                }
            });

            function show_<?php echo $this->area_name; ?>_wp_editor(){
                $("#wp-" + <?php echo $this->area_name; ?>_editor_id + "-wrap").show();
            }

            function hide_<?php echo $this->area_name; ?>_wp_editor(){
                $("#wp-" + <?php echo $this->area_name; ?>_editor_id + "-wrap").hide();
            }

        </script>
        <?php
    }

    /**
     * Store detail page meta box data
     *
     * @param int $post_id The post ID.
     */
    function save_meta_box( $post_id ){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST[$this->area_name . '_meta_box_nonce'] ) || !wp_verify_nonce( $_POST[$this->area_name . '_meta_box_nonce'], basename( __FILE__ ) ) ){
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
        
        // store detail page meta box fields
        // store the data if the detail page is enabled
        if ( isset( $_REQUEST[$this->area_name . '_is_enabled'] ) ) {
            update_post_meta( $post_id, '_' . $this->area_name . '_is_enabled', sanitize_text_field( $_POST[$this->area_name . '_is_enabled'] ) );
        }

        // store the detail page description info from the TinyMCE editor
        if ( isset( $_REQUEST[$this->area_name . '_editor'] ) ) {
            update_post_meta( $post_id, '_' . $this->area_name . '_description', $_POST[$this->area_name . '_editor'] ); // we don't sanitize the text field here because we want to keep the HTML formatting
        }
    }

    // Configure REST API to include detail page data
    function send_data_to_rest(){
        register_rest_field( $this->slug, $this->area_name, array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $is_enabled = get_post_meta( $post["id"], '_' . $this->area_name . '_is_enabled', true );
        $description = get_post_meta( $post["id"], '_' . $this->area_name . '_description', true );
        if( $is_enabled === "1" ){
            return $description;
        } else{
            return null;
        }
    }
}
