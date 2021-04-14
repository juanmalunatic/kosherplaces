<?php

/**
 * Enqueue admin CSS/JS and edit width functions
 *
 * @since   1.0.0
 * @package Kioken Blocks
 */
// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Kioken_Init.
 *
 * @package Kioken Blocks
 */
class Kioken_Init
{
    /**
     * Member Variable
     *
     * @var instance
     */
    private static  $instance ;
    /**
     *  Initiator
     */
    public static function get_instance()
    {
        if ( !isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        //Hook: Shared Scripts load for both admin and front end
        add_action( 'wp_loaded', array( $this, 'kioken_register_scripts_both_ends' ) );
        // Hook: editor assets.
        add_action( 'enqueue_block_editor_assets', array( $this, 'kioken_gutenberg_editor_assets' ) );
        // Init premium version.
        if ( function_exists( 'kkb_fs' ) && kkb_fs()->is__premium_only() ) {
            add_action( 'init', array( $this, 'kioken_init_hook__premium_only' ) );
        }
        add_action( 'body_class', array( $this, 'browser_body_classes' ) );
        //editor width hooks
        add_action( 'init', array( $this, 'kio_blocks_init_post_meta' ) );
        add_action( 'admin_head-post.php', array( $this, 'kioken_blocks_admin_editor_width' ), 100 );
        add_action( 'admin_head-post-new.php', array( $this, 'kioken_blocks_admin_editor_width' ), 100 );
    }
    
    function kioken_register_scripts_both_ends()
    {
        //jarallax
        wp_register_script(
            'jarallax',
            KK_BLOCKS_URL . 'dist/vendor/jarallax/dist/jarallax.min.js',
            array(),
            '1.12.0',
            true
        );
        wp_register_script(
            'resize-observer-polyfill',
            KK_BLOCKS_URL . 'dist/vendor/resize-observer-polyfill/ResizeObserver.global.min.js',
            array(),
            '1.5.0',
            true
        );
        wp_register_script(
            'jarallax-video',
            KK_BLOCKS_URL . 'dist/vendor/jarallax/dist/jarallax-video.min.js',
            array( 'jarallax' ),
            '1.10.7',
            true
        );
    }
    
    function kioken_gutenberg_editor_assets()
    {
        //jarallax
        wp_enqueue_script( 'jarallax' );
        wp_enqueue_script( 'resize-observer-polyfill' );
        wp_enqueue_script( 'jarallax-video' );
        // Scripts.
        wp_enqueue_script(
            'kioken-blocks-js',
            KK_BLOCKS_URL . 'dist/blocks.build.js',
            array(
            'wp-blocks',
            'wp-i18n',
            'jarallax',
            'jarallax-video',
            'resize-observer-polyfill',
            'wp-element',
            'wp-editor',
            'wp-api',
            'wp-edit-post'
        ),
            KK_BLOCKS_VERSION,
            true
        );
        
        if ( current_user_can( apply_filters( 'kioken_blocks_admin_role', 'manage_options' ) ) ) {
            $userrole = 'admin';
        } else {
            
            if ( current_user_can( apply_filters( 'kioken_blocks_editor_role', 'delete_others_pages' ) ) ) {
                $userrole = 'editor';
            } else {
                
                if ( current_user_can( apply_filters( 'kioken_blocks_author_role', 'publish_posts' ) ) ) {
                    $userrole = 'author';
                } else {
                    
                    if ( current_user_can( apply_filters( 'kioken_blocks_contributor_role', 'edit_posts' ) ) ) {
                        $userrole = 'contributor';
                    } else {
                        $userrole = 'none';
                    }
                
                }
            
            }
        
        }
        
        wp_localize_script( 'kioken-blocks-js', 'kioken_blocks_params', array(
            'config'        => get_option( 'kt_blocks_config_blocks' ),
            'configuration' => get_option( 'kioken_blocks_config_blocks' ),
            'settings'      => get_option( 'kioken_blocks_settings_blocks' ),
            'config_zengin' => ( defined( 'KK_BLOCKS_ZENGIN' ) || 'Bavarian' == wp_get_theme() ? true : false ),
            'userrole'      => $userrole,
            'post_types'    => $this->kioken_get_post_types(),
            'all_taxonomy'  => $this->kioken_get_related_taxonomy(),
            'image_sizes'   => $this->kioken_get_image_sizes(),
        ) );
        // Styles.
        wp_enqueue_style(
            'kioken-blocks-style-css',
            KK_BLOCKS_URL . 'dist/blocks.style.build.css',
            array(),
            KK_BLOCKS_VERSION
        );
        wp_enqueue_style(
            'kioken-blocks-editor-css',
            KK_BLOCKS_URL . 'dist/blocks.editor.build.css',
            array( 'wp-edit-blocks' ),
            KK_BLOCKS_VERSION
        );
        if ( get_option( 'kiokenblocks_editor_enhanced', false ) ) {
            wp_enqueue_style(
                'kioken-blocks-limited-margins-css',
                KK_BLOCKS_URL . 'dist/editor-overrides.css',
                array( 'wp-edit-blocks' ),
                KK_BLOCKS_VERSION
            );
        }
    }
    
    /**
     * Post Types & Taxonomies Queries
     */
    public static function kioken_get_post_types()
    {
        $post_types = get_post_types( array(
            'public'       => true,
            'show_in_rest' => true,
        ), 'objects' );
        $options = array();
        foreach ( $post_types as $post_type ) {
            if ( 'product' == $post_type->name || 'attachment' == $post_type->name ) {
                continue;
            }
            $options[] = array(
                'value' => $post_type->name,
                'label' => $post_type->label,
            );
        }
        return apply_filters( 'kioken_loop_post_types', $options );
    }
    
    public static function kioken_get_related_taxonomy()
    {
        $post_types = self::kioken_get_post_types();
        $return_array = array();
        foreach ( $post_types as $key => $value ) {
            $post_type = $value['value'];
            $taxonomies = get_object_taxonomies( $post_type, 'objects' );
            $data = array();
            foreach ( $taxonomies as $tax_slug => $tax ) {
                if ( !$tax->public || !$tax->show_ui ) {
                    continue;
                }
                $data[$tax_slug] = $tax;
                $terms = get_terms( $tax_slug );
                $related_tax = array();
                
                if ( !empty($terms) ) {
                    foreach ( $terms as $t_index => $t_obj ) {
                        $related_tax[] = array(
                            'id'   => $t_obj->term_id,
                            'name' => $t_obj->name,
                        );
                    }
                    $return_array[$post_type]['terms'][$tax_slug] = $related_tax;
                }
            
            }
            $return_array[$post_type]['taxonomy'] = $data;
        }
        return apply_filters( 'kioken_post_loop_taxonomies', $return_array );
    }
    
    /**
     * Get size information for all currently-registered image sizes.
     *
     * @global $_wp_additional_image_sizes
     * @uses   get_intermediate_image_sizes()
     * @link   https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
     * @since  1.9.0
     * @return array $sizes Data for all currently-registered image sizes.
     */
    public static function kioken_get_image_sizes()
    {
        global  $_wp_additional_image_sizes ;
        $sizes = get_intermediate_image_sizes();
        $image_sizes = array();
        $image_sizes[] = array(
            'value' => 'full',
            'label' => esc_html__( 'Full', 'kioken-blocks' ),
        );
        foreach ( $sizes as $size ) {
            
            if ( in_array( $size, array(
                'thumbnail',
                'medium',
                'medium_large',
                'large'
            ), true ) ) {
                $image_sizes[] = array(
                    'value' => $size,
                    'label' => ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
                );
            } else {
                $image_sizes[] = array(
                    'value' => $size,
                    'label' => sprintf(
                    '%1$s (%2$sx%3$s)',
                    ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
                    $_wp_additional_image_sizes[$size]['width'],
                    $_wp_additional_image_sizes[$size]['height']
                ),
                );
            }
        
        }
        $image_sizes = apply_filters( 'kioken_post_featured_image_sizes', $image_sizes );
        return $image_sizes;
    }
    
    /**
     * Browser Classes for Body
     */
    public static function browser_body_classes( $classes )
    {
        // the list of WordPress global browser checks
        // https://codex.wordpress.org/Global_Variables#Browser_Detection_Booleans
        $browsers = [
            'is_iphone',
            'is_chrome',
            'is_safari',
            'is_NS4',
            'is_opera',
            'is_gecko',
            'is_lynx',
            'is_IE',
            'is_edge'
        ];
        // check the globals to see if the browser is in there and return a string with the match
        $classes[] = join( ' ', array_filter( $browsers, function ( $browser ) {
            return $GLOBALS[$browser];
        } ) );
        return $classes;
    }
    
    /**
     * Register Meta for blocks width
     */
    public static function kio_blocks_init_post_meta()
    {
        register_post_meta( '', 'kk_blocks_editor_width', array(
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ) );
    }
    
    /**
     * Add inline css editor width
     */
    public static function kioken_blocks_admin_editor_width()
    {
        echo  '<style type="text/css" id="kt-block-editor-width">' ;
        echo  'body.gutenberg-editor-page.kt-editor-width-fullwidth .editor-post-title__block,
			body.gutenberg-editor-page.kt-editor-width-fullwidth .editor-default-block-appender,
			body.gutenberg-editor-page.kt-editor-width-fullwidth .editor-block-list__block,
			body.block-editor-page.kt-editor-width-fullwidth .wp-block {
				max-width: none;
			}' ;
        echo  'body.gutenberg-editor-page .editor-block-list__layout .editor-block-list__block[data-align=wide],
			body.block-editor-page .editor-block-list__layout .wp-block[data-align=wide] {
				width: auto;
				max-width: 1200px;
			}' ;
        echo  'body.gutenberg-editor-page .editor-block-list__layout .editor-block-list__block[data-align=full],
			body.block-editor-page .editor-block-list__layout .wp-block[data-align=full] {
				max-width: none;
			}' ;
        echo  '</style>' ;
        echo  "<script> var kt_blocks_sidebar_size = 1000; var kt_blocks_nosidebar_size = 1000; var kt_blocks_default_size = 1000;</script>" ;
    }

}
Kioken_Init::get_instance();