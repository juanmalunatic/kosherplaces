<?php

// Replace '*' next to required fields
// wp-content/themes/homey/framework/functions/helper.php

function homey_req( $field ) {
    $required_fields = homey_option('add_listing_required_fields');
    if( $required_fields[$field] != 0 ) {
        return "<span class='add-listing-required'>*</span>";
    }
    return '';
}