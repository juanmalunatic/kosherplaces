<?php

// Replace placehold.it by its new domain, placeholder.com

// Overrides on homey/template-parts exist as template files:
//   homey-child/template-parts/dashboard/sidebar-listing.php
//   homey-child/template-parts/dashboard/profile/verification.php

// Overrides on wp-content/plugins/homey-core are in the "homey-child.php" mu-plugin.

// Overrides on homey/framework/functions/helper.php are the following:

function homey_get_image_placeholder($featured_image_size)
{

    global $_wp_additional_image_sizes;
    $title_img_text = get_bloginfo('name');
    $feat_img_width = 0;
    $feat_img_height = 0;

    if (in_array($featured_image_size, array('thumbnail', 'medium', 'large'))) {

        $feat_img_width = get_option($featured_image_size . '_size_w');
        $feat_img_height = get_option($featured_image_size . '_size_h');

    } elseif (isset($_wp_additional_image_sizes[$featured_image_size])) {

        $feat_img_width = $_wp_additional_image_sizes[$featured_image_size]['width'];
        $feat_img_height = $_wp_additional_image_sizes[$featured_image_size]['height'];

    }

    if (intval($feat_img_width) > 0 && intval($feat_img_height) > 0) {
        // Only change:
        return '<img src="https://via.placeholder.com/' . esc_attr($feat_img_width) . 'x' . esc_attr($feat_img_height) . '?text=' . urlencode($title_img_text) . '" alt="' . esc_attr__('placeholder', 'homey') . '" />';
    }

    return '';
}

function homey_get_image_placeholder_url($image_size)
{

    global $_wp_additional_image_sizes;
    $img_width = 0;
    $img_height = 0;
    $img_text = get_bloginfo('name');

    if (in_array($image_size, array('thumbnail', 'medium', 'large'))) {

        $img_width = get_option($image_size . '_size_w');
        $img_height = get_option($image_size . '_size_h');

    } elseif (isset($_wp_additional_image_sizes[$image_size])) {

        $img_width = $_wp_additional_image_sizes[$image_size]['width'];
        $img_height = $_wp_additional_image_sizes[$image_size]['height'];

    }

    if (intval($img_width) > 0 && intval($img_height) > 0) {
        // Only change:
        return 'https://via.placeholder.com/' . esc_attr($img_width) . 'x' . esc_attr($img_height) . '?text=' . urlencode($img_text) . '';
    }

    return '';
}