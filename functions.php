<?php

// Includes all MetaBox types
foreach ( glob( get_template_directory() . "/MetaBoxes/*.php" ) as $filename ){
    include( $filename );
}

// Includes all files in the CustomPostTypes directory
foreach ( glob( get_template_directory() . "/CustomPostTypes/*.php" ) as $filename ){
    include( $filename );
}

$emergency_phones = new EmergencyPhones();