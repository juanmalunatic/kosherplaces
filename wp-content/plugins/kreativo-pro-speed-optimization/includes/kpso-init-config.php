<?php


// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}


// Register Settings Menu
function kpso_register_settings_menu()
{
    add_options_page('Kreativo Pro Speed Optimization', 'KP Speed Optimize', 'manage_options', 'kpso', 'kpso_settings_page');
}
add_action('admin_menu', 'kpso_register_settings_menu');


// Set Default Config on Plugin Activation
function kpso_set_default_config() {

    if (KPSO_VERSION !== get_option('KPSO_VERSION')) {
		
		$kpso_init_css_keywords = array("fonts.googleapis.com","wp-content/cache/min/", "/wp-content/cache/autoptimize/");
		$kpso_init_js_keywords = array("jquery.min.js","lazyload.min.js","wp-content/cache/min/", "/wp-content/cache/autoptimize/");
		$kpso_init_video_keywords = array("mp4", "webm", "ogv", "youtube", "vimeo");
		
		$kpso_init_key = 'ea3c4764396fdca4f0f988f445263cf1';

        if (get_option('kpso_css_include_list') === false)
            update_option('kpso_css_include_list', $kpso_init_css_keywords);
		if (get_option('kpso_js_include_list') === false)
            update_option('kpso_js_include_list', $kpso_init_js_keywords);
		if (get_option('kpso_video_include_list') === false)
            update_option('kpso_video_include_list', $kpso_init_video_keywords);

        if (get_option('kpso_disabled_pages') === false)
            update_option('kpso_disabled_pages', []);
			
		if (get_option('kpso_css_mobile_disabled') === false)
            update_option('kpso_css_mobile_disabled', "no");
		if (get_option('kpso_js_mobile_disabled') === false)
            update_option('kpso_js_mobile_disabled', "no");
			
		if (get_option('kpso_woo_optimization') === false)
            update_option('kpso_woo_optimization', "no");

        update_option('KPSO_VERSION', KPSO_VERSION);
		update_option('kpso_init_key', $kpso_init_key);
    }
}
add_action('plugins_loaded', 'kpso_set_default_config');


// Restore Default Options
function kpso_restore_default_settings() {
		
		$kpso_init_css_keywords = array("fonts.googleapis.com","wp-content/cache/min/", "/wp-content/cache/autoptimize/");
		$kpso_init_js_keywords = array("jquery.min.js","lazyload.min.js","wp-content/cache/min/", "/wp-content/cache/autoptimize/");
		$kpso_init_video_keywords = array("mp4", "webm", "ogv", "youtube", "vimeo");

		update_option('kpso_css_include_list', $kpso_init_css_keywords);
		update_option('kpso_js_include_list', $kpso_init_js_keywords);
		update_option('kpso_video_include_list', $kpso_init_video_keywords);
		update_option('kpso_disabled_pages', []);
		update_option('kpso_css_mobile_disabled', "no");
		update_option('kpso_js_mobile_disabled', "no");
		update_option('kpso_woo_optimization', "no");
		update_option('KPSO_VERSION', KPSO_VERSION);
}


//Set Transient on Plugin Activation
function kpso_admin_notice_transient()
{
    set_transient( 'kpso-admin-notice-activation', true, 5*60 );
}


//Display Message on Plugin Activation
function kpso_admin_notice_activation()
{
    if( get_transient('kpso-admin-notice-activation') ){
        ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using <strong>Kreativo Pro Speed Optimization</strong> plugin!</p>
        </div>
        <?php
        delete_transient( 'kpso-admin-notice-activation' );
    }
}
add_action( 'admin_notices', 'kpso_admin_notice_activation' );


//Delete Plugin Settings Upon Plugin Deletion
function kpso_delete_settings()
{
	delete_option('kpso_css_include_list');
	delete_option('kpso_js_include_list');
	delete_option('kpso_video_include_list');
	delete_option('kpso_disabled_pages');
	delete_option('kpso_css_mobile_disabled');
	delete_option('kpso_js_mobile_disabled');
	delete_option('KPSO_VERSION');
	delete_option('kpso_init_key');
}

function kpso_license_confirm_key( $data )
{
	$kpso_init_key = get_option('kpso_init_key');
	$newdata = md5($data);
	
	if ( $newdata === $kpso_init_key )
	{
		echo '<div class="notice notice-success is-dismissible"><p>Kreativo Pro Speed Optimization License has been activated for this website!</p></div>';
		set_transient( 'kpso-key-validate-activate', true, 6*60*60 );
	}
	else
	{
		echo '<div class="notice notice-error is-dismissible"><p><span class="dashicons dashicons-no"></span> Please enter correct license key or contact Kreativo Pro team.</p></div>';
	}
}