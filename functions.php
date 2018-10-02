<?php

// Includes all files in the custom-post-types directory
foreach ( glob( get_template_directory() . "/custom-post-types/*.php" ) as $filename ){
    include( $filename );
}