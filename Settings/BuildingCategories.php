<?php

class BuildingCatagories {
    public function __construct(){
        $this->init();
    }
    
    function init(){
        // Setting up the hooks
        add_action( "admin_menu", array( $this, "building_categories_register_options_page" ) );
        add_action("admin_init", array( $this, "display_building_category_settings" ) );
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
        <form method="post" action="options.php">
        <?php
            //add_settings_section callback is displayed here. For every new section we need to call settings_fields.
            settings_fields("building_category");
                    
            // all the add_settings_field callbacks is displayed here
            do_settings_sections("building-category-options");
        
            // Add the submit button to serialize the options
            submit_button(); 
        ?>
        </form>
        <script>
        jQuery( function($) {
            $( "#accordion" ).accordion();
        } );
        </script>
        <div id="accordion">
            <h3>Section 1</h3>
            <div>
                <p>
                Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
                ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
                amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
                odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
                </p>
            </div>
            <h3>Section 2</h3>
            <div>
                <p>
                Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
                purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
                velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
                suscipit faucibus urna.
                </p>
            </div>
            <h3>Section 3</h3>
            <div>
                <p>
                Nam enim risus, molestie et, porta ac, aliquam ac, risus. Quisque lobortis.
                Phasellus pellentesque purus in massa. Aenean in pede. Phasellus ac libero
                ac tellus pellentesque semper. Sed ac felis. Sed commodo, magna quis
                lacinia ornare, quam ante aliquam nisi, eu iaculis leo purus venenatis dui.
                </p>
                <ul>
                    <li>List item one</li>
                    <li>List item two</li>
                    <li>List item three</li>
                </ul>
            </div>
            <h3>Section 4</h3>
            <div>
                <p>
                Cras dictum. Pellentesque habitant morbi tristique senectus et netus
                et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in
                faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia
                mauris vel est.
                </p>
                <p>
                Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus.
                Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
                inceptos himenaeos.
                </p>
            </div>
        </div>
        <?php
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
}