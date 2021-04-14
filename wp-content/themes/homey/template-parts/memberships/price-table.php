<?php
$billing_period = get_post_meta( get_the_ID(), 'hm_settings_bill_period', true );
$billing_frequency = get_post_meta( get_the_ID(), 'hm_settings_billing_frequency', true );
$listings_included = get_post_meta( get_the_ID(), 'hm_settings_listings_included', true );
$unlimited_listings = get_post_meta( get_the_ID(), 'hm_settings_unlimited_listings', true );
$featured_listings = get_post_meta( get_the_ID(), 'hm_settings_featured_listings', true );
$package_price = get_post_meta( get_the_ID(), 'hm_settings_package_price', true );
$stripe_id = get_post_meta( get_the_ID(), 'hm_settings_stripe_id', true );
$visibility = get_post_meta( get_the_ID(), 'hm_settings_visibility', true );
$images_per_listing = get_post_meta( get_the_ID(), 'hm_settings_images_per_listing', true );
$unlimited_images = get_post_meta( get_the_ID(), 'hm_settings_unlimited_images', true );
$taxes = get_post_meta( get_the_ID(), 'hm_settings_taxes', true );
$popular_featured = get_post_meta( get_the_ID(), 'hm_settings_popular_featured', true );
$custom_link = get_post_meta( get_the_ID(), 'hm_settings_custom_link', true );
$detail_link = get_post_permalink(get_the_ID());

?>
<div class="price-table-module">
    <div class="price-table-title"><?php the_title(); ?></div><!-- price-table-title -->
    <div class="price-table-price-wrap">
        <span class="price-table-currency">$</span>
        <span class="price-table-price"><?php echo $package_price; ?></span>
    </div><!-- price-table-price-wrap -->
    <div class="price-table-description">
        <ul class="list-unstyled">
            <li>
                <i class="fa fa-check" aria-hidden="true"></i> Time Period: <strong><?php echo $billing_frequency.' '.$billing_period; ?></strong>
            </li>
            <li>
                <i class="fa fa-check" aria-hidden="true"></i> Listings: <strong><?php echo $listings_included; ?></strong>
            </li>
            <li>
                <i class="fa fa-check" aria-hidden="true"></i> Featured Listings: <strong><?php echo $featured_listings; ?></strong>
            </li>
        </ul>
    </div><!-- price-table-description -->
    <div class="price-table-button">
        <a class="btn btn-primary" href="<?php echo $detail_link; ?>">Get Started</a>
    </div><!-- price-table-button -->
</div><!-- taxonomy-grids-module -->