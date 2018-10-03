<?php

// Includes all files in the CustomPostTypes directory
foreach ( glob( get_template_directory() . "/CustomPostTypes/*.php" ) as $filename ){
    include( $filename );
}