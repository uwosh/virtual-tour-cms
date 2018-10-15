<?php

// Includes all MetaBox types
foreach ( glob( get_template_directory() . "/MetaBoxes/*.php" ) as $filename ){
    include( $filename );
}

// Includes all files in the CustomPostTypes directory
foreach ( glob( get_template_directory() . "/CustomPostTypes/*.php" ) as $filename ){
    include( $filename );
}

// Includes all files in the Settings directory
foreach ( glob( get_template_directory() . "/Settings/*.php" ) as $filename ){
    include( $filename );
}

// Instantiating the custom posts
$emergency_phones = new EmergencyPhones();
$parking_lots = new ParkingLots();
$buildings = new Buildings();

// Creating settings pages
function building_categories_register_options_page(){
    // Instantiating the building categories page
    $building_categories = new BuildingCatagories();
    add_options_page( "Building Categories", "Building Categories", "administrator", "building-categories", array( $building_categories, "building_categories_options_page" ) );
}
add_action( "admin_menu", "building_categories_register_options_page" );