<?php

/**
 * Plugin Name: Gutenberg Page Builder - Kioken Blocks
 * Plugin URI: https://kiokengutenberg.com/
 * Description: Gutenberg Page Builder Utility. The next best thing after Gutenberg.
 * Author: Kioken Theme
 * Author URI: https://kiokengutenberg.com/
 * Version: 1.3.9
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package KIOKEN
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
define( 'KK_DEBUG', false );

if ( function_exists( 'kkb_fs' ) ) {
    kkb_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( 'kkb_fs' ) && 'Bavarian' != wp_get_theme() ) {
        // Create a helper function for easy SDK access.
        function kkb_fs()
        {
            global  $kkb_fs ;
            
            if ( !isset( $kkb_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $kkb_fs = fs_dynamic_init( array(
                    'id'             => '4106',
                    'slug'           => 'kioken-blocks',
                    'premium_slug'   => 'kioken-blocks-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_9864027322b64f06cba01418876b6',
                    'is_premium'     => false,
                    'premium_suffix' => '(Pro)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'       => 'kioken_blocks',
                    'first-path' => 'admin.php?page=kioken_blocks',
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $kkb_fs;
        }
        
        // Init Freemius.
        kkb_fs();
        // Signal that SDK was initiated.
        do_action( 'kkb_fs_loaded' );
    }
    
    define( 'KK_BLOCKS_PATH', realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR );
    define( 'KK_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
    define( 'KK_BLOCKS_VERSION', '1.3.9' );
    define( 'KK_BLOCKS_PLUGIN_BASE', plugin_basename( __FILE__ ) );
    define( 'KK_BLOCKS_REVIEW_URL', 'https://wordpress.org/support/plugin/kioken-blocks/reviews/?filter=5' );
    /**
     * Load Plugin
     */
    function kioken_blocks_init()
    {
        
        if ( true === KK_DEBUG ) {
            define( 'KK_BLOCKS_ASSET_SUFFIX', null );
        } else {
            define( 'KK_BLOCKS_ASSET_SUFFIX', '-min' );
        }
        
        require_once KK_BLOCKS_PATH . 'dist/init.php';
        require_once KK_BLOCKS_PATH . 'widgets/widgets.php';
        //includes
        require_once KK_BLOCKS_PATH . 'includes/class-kioken-blocks-frontend.php';
        require_once KK_BLOCKS_PATH . 'includes/class-kioken-body-classes.php';
        require_once KK_BLOCKS_PATH . 'includes/class-kiokenblocks-font-loader.php';
        require_once KK_BLOCKS_PATH . 'includes/class-kiokenblocks-post-meta.php';
        require_once KK_BLOCKS_PATH . 'includes/class-kioken-google-map.php';
        require_once KK_BLOCKS_PATH . 'includes/class-kiokenblocks-rest.php';
        require_once KK_BLOCKS_PATH . 'includes/get-dynamic-blocks.php';
        //settings
        require_once KK_BLOCKS_PATH . 'dist/settings/class-kioken-blocks-settings.php';
        if ( is_admin() || defined( 'WP_CLI' ) && WP_CLI ) {
            require_once KK_BLOCKS_PATH . 'dist/settings/class-kiokenblocks-action-links.php';
        }
    }
    
    add_action( 'plugins_loaded', 'kioken_blocks_init' );
}
