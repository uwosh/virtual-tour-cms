<?php

// Includes all MetaBox types
foreach ( glob( get_template_directory() . "/MetaBoxes/*.php" ) as $filename ){
    include( $filename );
}

// Includes all files in the CustomPostTypes directory
foreach ( glob( get_template_directory() . "/CustomPostTypes/*.php" ) as $filename ){
    include( $filename );
}

// Instantiating the custom posts
$emergency_phones = new EmergencyPhones();
$parking_lots = new ParkingLots();
$buildings = new Buildings();