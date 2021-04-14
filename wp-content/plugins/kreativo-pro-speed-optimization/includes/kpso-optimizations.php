<?php

$kpso_disabled_pages = get_option('kpso_disabled_pages');
$current_url = home_url($_SERVER['REQUEST_URI']);

$kpso_woo_optimization = get_option('kpso_woo_optimization');


// Exclude on Pages
if (kpso_is_keyword_included($current_url, $kpso_disabled_pages))
{
	return;
}


//Optimize Woo by Disabling Woo Scripts on Non-Woo Pages
if($kpso_woo_optimization === "yes")
{
	
	function kpso_remove_woo_styles_scripts()
	{
        if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
                return;
        }
        remove_action('wp_enqueue_scripts', [WC_Frontend_Scripts::class, 'load_scripts']);
        remove_action('wp_print_scripts', [WC_Frontend_Scripts::class, 'localize_printed_scripts'], 5);
        remove_action('wp_print_footer_scripts', [WC_Frontend_Scripts::class, 'localize_printed_scripts'], 5);
	}
	add_action( 'template_redirect', 'kpso_remove_woo_styles_scripts', 999 );

	function kpso_disable_woo_block_styles()
	{
		if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() )
		{
			return;
		}
		
		wp_dequeue_style( 'wc-block-vendors-style' );
		wp_dequeue_style( 'wc-block-style' );
	}
	add_action( 'wp_enqueue_scripts', 'kpso_disable_woo_block_styles' );
	
}