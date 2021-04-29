<?php
function homey_enqueue_styles() {
    
    // enqueue parent styles
    wp_enqueue_style('homey-parent-theme', get_template_directory_uri() .'/style.css');
    
    // enqueue child styles
    wp_enqueue_style('homey-child-theme', get_stylesheet_directory_uri() .'/style.css', array('homey-parent-theme'));
    
}
add_action('wp_enqueue_scripts', 'homey_enqueue_styles');

$path_child_theme = get_stylesheet_directory();

// Partial overrides for placehold.it
require($path_child_theme . '/override__placeholdit.php');

// Overrides for add-listing function
require($path_child_theme . '/override__add-listing.php');

// Overrides for parent theme JS scripts
require($path_child_theme . '/override__javascript.php')
?>