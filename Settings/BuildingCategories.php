<?php

class BuildingCatagories {
    private $number_of_building_categories;
    
    public function __construct(){
        if( !get_option( 'number_of_building_categories' ) ){
            update_option( 'number_of_building_categories', 1 );
        }

        $this->number_of_building_categories = get_option( 'number_of_building_categories' );

        $this->init();
    }
    
    function init(){
        // Setting up the hooks
        add_action( "admin_menu", array( $this, "building_categories_register_options_page" ) );
        add_action( "admin_init", array( $this, "display_building_category_settings" ) );

        // Setting up the AJAX calls
        add_action( 'wp_ajax_add_new_building_category', array( $this, 'add_new_building_category_runner' ) );
        add_action( 'wp_ajax_remove_building_category', array( $this, 'remove_building_category_runner' ) );
    }

    function building_categories_register_options_page(){
        // Instantiating the building categories page
        add_options_page( "Building Categories", "Building Categories", "administrator", "building-categories", array( $this, "building_categories_options_page" ) );
    }

    function building_categories_options_page(){
        // including frameworks we need for the settings page
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_style(
            'jquery-ui-styles',
            '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
        );
        ?>
        <h1>Hi there!</h1>
        <?php submit_button( "Add Building Category", "button-secondary", '', true, array( 'id' => 'add-building-category' ) ); ?>
        <?php submit_button( "Remove Building Category", "button-secondary", '', true, array( 'id' => 'remove-building-category' ) ); ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var add_new_building_category = {
                'action': 'add_new_building_category'
            };
            var remove_building_category = {
                'action': 'remove_building_category'
            };
            jQuery('#add-building-category').click(function() {
                jQuery.post(ajaxurl, add_new_building_category, function(response) {
                    alert(response);
                    window.location.reload(true);
                });
            });
            jQuery('#remove-building-category').click(function() {
                jQuery.post(ajaxurl, remove_building_category, function(response) {
                    alert(response);
                    window.location.reload(true);
                });
            });
        });
        </script>
        Number of building categories: <?php echo $this->number_of_building_categories; ?>
        <script type="text/javascript">
        jQuery( function($) {
            $( "#accordion" ).accordion();
        } );
        </script>
        <form method="post" action="options.php">
            <div id="accordion">
                <?php
                for( $i=0; $i<$this->number_of_building_categories; $i++ ){
                ?>
                <h3>Category <?php echo $i+1; ?></h3>
                <div>
                <?php
                    //add_settings_section callback is displayed here. For every new section we need to call settings_fields.
                    settings_fields("building_category");
                            
                    // all the add_settings_field callbacks is displayed here
                    do_settings_sections("building-category-options");
                ?>
                </div>
                <?php
                }
                ?>
            </div>
        </form>
        <?php
        submit_button();
    }

    function display_building_category_settings(){
        add_settings_section( "building_category", "Building Category Options", array( $this, "display_building_category_options_content" ), "building-category-options" );

        add_settings_field("category_title", "Title", array( $this, "display_building_category_title_field" ), "building-category-options", "building_category");
        add_settings_field("category_youtube_url", "YouTube URL", array( $this, "display_building_category_youtube_url_field" ), "building-category-options", "building_category");
        add_settings_field("category_description", "Description", array( $this, "display_building_category_description_field" ), "building-category-options", "building_category");
        add_settings_field("category_learn_more_url", "Learn More URL", array( $this, "display_building_category_learn_more_url_field" ), "building-category-options", "building_category");

        register_setting("building_category", "category_title");
        register_setting("building_category", "category_youtube_url");
        register_setting("building_category", "category_description");
        register_setting("building_category", "category_learn_more_url");
    }

    function display_building_category_options_content(){
        echo "Building category description section.";
    }

    function display_building_category_title_field(){
        ?>
            <input type="text" name="category_title" id="category_title" value="<?php echo get_option( 'category_title' ); ?>" />
        <?php
    }

    function display_building_category_youtube_url_field(){
        ?>
            <input type="text" name="category_youtube_url" id="category_youtube_url" value="<?php echo get_option( 'category_youtube_url' ); ?>" />
        <?php
    }

    function display_building_category_description_field(){
        ?>
            <textarea name="category_description" id="category_description" cols="50" rows="10"><?php echo get_option( 'category_description' ); ?></textarea>
        <?php
    }

    function display_building_category_learn_more_url_field(){
        ?>
            <input type="text" name="category_learn_more_url" id="category_learn_more_url" value="<?php echo get_option( 'category_learn_more_url' ); ?>" />
        <?php
    }

    function add_new_building_category_runner(){
        $this->increment_number_of_building_categories();
        echo "Added new building category successfully!";

        wp_die();
    }

    function remove_building_category_runner(){
        try{
            $this->decrement_number_of_building_categories();
            echo "Removed building category successfully!";
        } catch( Exception $e ){
            echo "Error: " . $e->getMessage() . ".";
        }

        wp_die();
    }

    function increment_number_of_building_categories(){
        $this->number_of_building_categories++;
        update_option( 'number_of_building_categories', $this->number_of_building_categories );
    }

    function decrement_number_of_building_categories(){
        if( $this->number_of_building_categories!=1 ){
            $this->number_of_building_categories--;
            update_option( 'number_of_building_categories', $this->number_of_building_categories );
        } else{
            throw new Exception( 'It is not possible to remove the last remaining building category' );
        }
    }
}