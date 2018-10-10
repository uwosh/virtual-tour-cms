<?php

class ToolTipImage {
    private $slug;
    private $label;

    /**
     * Set up the Tooltip Image class
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
     * Build the tooltip image meta box
     *
     * @param post $post The post object
     */
    function create_meta_box( $post ){
        // make sure the form request comes from WordPress
        wp_nonce_field( basename( __FILE__ ), 'tooltip_image_meta_box_nonce' );

        $tooltip_image = get_post_meta( $post->ID, '_tooltip_image', true );

        ?>

        <script>
            function image_add_image(key){
                var $wrapper = jQuery('#'+key+'_wrapper');

                image_uploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('Select Building Tooltip Image','yourdomain'); ?>',
                    button: {
                        text: '<?php _e('Select Building Tooltip Image','yourdomain'); ?>'
                    },
                    multiple: false
                });
                
                image_uploader.on('select', function() {

                    var attachment = image_uploader.state().get('selection').first().toJSON();
                    var img_url = attachment['url'];
                    var img_id = attachment['id'];
                    $wrapper.find('input#'+key).val(img_id);
                    $wrapper.find('img').attr('src',img_url);
                    $wrapper.find('img').show();
                    $wrapper.find('a.remove-image').show();
                });
                
                image_uploader.on('open', function(){
                    var selection = image_uploader.state().get('selection');
                    var selected = $wrapper.find('input#'+key).val();
                    if(selected){
                        selection.add(wp.media.attachment(selected));
                    }
                });
                
                image_uploader.open();
                
                return false;
            }

            function image_remove_image(key){
                var $wrapper = jQuery('#'+key+'_wrapper');
                $wrapper.find('input#'+key).val('');
                $wrapper.find('img').hide();
                $wrapper.find('a.remove-image').hide();
                return false;
            }
        </script>

        <div class="image_wrapper" id="tooltip_image_wrapper" style="margin-bottom:20px;">
            <img src="<?php echo ( $tooltip_image!='' ? wp_get_attachment_image_src( $tooltip_image )[0] : '' ); ?>" style="width:100%;display: <?php echo ( $tooltip_image!='' ? 'block' : 'none' ); ?>" alt="">
            <a class="add-image" onclick="image_add_image('tooltip_image');" style="cursor: pointer"><?php _e( 'Set Building Tooltip Image', 'yourdomain' ); ?></a><br>
            <a class="remove-image" style="color:#a00;cursor:pointer;display: <?php echo ( $tooltip_image!='' ? 'block' : 'none' ); ?>" onclick="image_remove_image('tooltip_image');"><?php _e( 'Remove Building Tooltip Image','yourdomain' ); ?></a>
            <input type="hidden" name="tooltip_image" id="tooltip_image" value="<?php echo $tooltip_image; ?>" />
        </div>

        <?php
    }

    function save_meta_box($post_id){
        // verify taxonomies meta box nonce
        if ( !isset( $_POST['tooltip_image_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['tooltip_image_meta_box_nonce'], basename( __FILE__ ) ) ){
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
    
        if( isset( $_POST['tooltip_image_meta_box_nonce'] ) ){
            if( isset( $_POST["tooltip_image"] ) && intval( $_POST["tooltip_image"] ) != '' ){
                // save the tooltip image
                update_post_meta( $post_id, "_tooltip_image", intval( $_POST["tooltip_image"] ) );
            }else{
                // delete the tooltip image
                delete_post_meta( $post_id, '_tooltip_image' );
            }
        }
    }

    // Configure REST API to include location data
    function send_data_to_rest(){
        register_rest_field( $this->slug, 'tooltip_image', array(
                'get_callback' => array( $this, 'parse_meta_data_for_api' )
            )
        );
    }

    // Function that grabs the meta data for the REST API
    function parse_meta_data_for_api( $post ) {
        $tooltip_image = get_post_meta( $post["id"], '_tooltip_image', true );
        if( $tooltip_image === "" ) {
            return null;
        }
        return (int) $tooltip_image;
    }
}