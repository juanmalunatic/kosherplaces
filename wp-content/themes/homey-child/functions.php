<?php
function homey_enqueue_styles() {
    
    // enqueue parent styles
    wp_enqueue_style('homey-parent-theme', get_template_directory_uri() .'/style.css');
    
    // enqueue child styles
    wp_enqueue_style('homey-child-theme', get_stylesheet_directory_uri() .'/style.css', array('homey-parent-theme'));
    
}
add_action('wp_enqueue_scripts', 'homey_enqueue_styles');

// Disable versions (?x.x.x) on CSS files (development only)

function remove_css_js_version( $src ) {
    if( strpos( $src, '?ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'remove_css_js_version', 9999 );

$path_child_theme = get_stylesheet_directory();

// Partial overrides for placehold.it
require($path_child_theme . '/override__placeholdit.php');

// Overrides for add-listing function
require($path_child_theme . '/override__add-listing.php');

// Overrides for parent theme JS scripts
require($path_child_theme . '/override__javascript.php')

?>