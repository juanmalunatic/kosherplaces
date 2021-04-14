<?php
/**
 * Homey functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Homey
 * @since Homey 1.0.0
 * @author Waqas Riaz
 */

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
global $wp_version;
/**
*	---------------------------------------------------------------
*	Define constants
*	---------------------------------------------------------------
*/
define( 'HOMEY_THEME_NAME', 'Homey' );
define( 'HOMEY_THEME_SLUG', 'homey' );
define( 'HOMEY_THEME_VERSION', '1.6.4' );
define( 'HOMEY_CSS_DIR_URI', get_template_directory_uri() . '/css/' );
define( 'HOMEY_JS_DIR_URI', get_template_directory_uri() . '/js/' );
/**
*	----------------------------------------------------------------------------------
*	Set up theme default and register various supported features.
*	----------------------------------------------------------------------------------
*/
if ( ! function_exists( 'homey_setup' ) ) {
	function homey_setup() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		//Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		//Add support for post thumbnails.
		add_theme_support( 'post-thumbnails' );

		add_image_size( 'homey-listing-thumb', 450, 300, true );
		add_image_size( 'homey-gallery-thumb', 250, 250, true );
		add_image_size( 'homey-gallery', 1140, 760, true );
		add_image_size( 'homey-gallery-thumb2', 120,80, true );
		add_image_size( 'homey-variable-slider', 0, 500, true );

		add_image_size( 'homey_thumb_555_360', 555, 360, true );
		add_image_size( 'homey_thumb_555_262', 555, 262, true );
		add_image_size( 'homey_thumb_360_360', 360, 360, true );
		add_image_size( 'homey_thumb_360_120', 360, 120, true );
		

		/**
		*	Register nav menus. 
		*/
		register_nav_menus(
			array(
				'main-menu' => esc_html__( 'Main Menu', 'homey' ),
				'top-menu' => esc_html__( 'Top Menu', 'homey' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array(

		) );

		homey_update_guests_meta();

		//remove gallery style css
		add_filter( 'use_default_gallery_style', '__return_false' );
		
	}

	add_action( 'after_setup_theme', 'homey_setup' );
}

/**
 *	-----------------------------------------------------------------
 *	Make the theme available for translation.
 *	-----------------------------------------------------------------
 */
load_theme_textdomain( 'homey', get_template_directory() . '/languages' );

/**
 *	-------------------------------------------------------------------------
 *	Set up the content width value based on the theme's design.
 *	-------------------------------------------------------------------------
 */
if( !function_exists('homey_content_width') ) {
	function homey_content_width()
	{
		$GLOBALS['content_width'] = apply_filters('homey_content_width', 1170);
	}

	add_action('after_setup_theme', 'homey_content_width', 0);
}

function homey_update_guests_meta() {
	global $wpdb;


	if( !get_option('homey_guests_meta', false) ) {

		$prefix = $wpdb->prefix;

		$delete_query = 'delete from '.$prefix.'postmeta where meta_key = "homey_total_guests_plus_additional_guests"';
		
		$qry = 'INSERT INTO '.$prefix.'postmeta ( post_id, meta_key, meta_value) 
		select  p1.ID ,  "homey_total_guests_plus_additional_guests" , (select sum(pm2.meta_value) as sleepsTotal from '.$prefix.'postmeta pm2
		 	where pm2.post_id = p1.ID
		 	and pm2.meta_key in ("homey_guests", "homey_num_additional_guests")) 
		from '.$prefix.'posts p1
		where p1.post_type = "listing"';


		$wpdb->query($delete_query);
		$wpdb->query($qry);

		update_option('homey_guests_meta', true);
	}
}


/**
 *	-------------------------------------------------------------------
 *	Visual Composer
 *	-------------------------------------------------------------------
 */
if (class_exists('Vc_Manager') && class_exists('Homey') ) {

	if( !function_exists('homey_include_composer') ) {
		function homey_include_composer()
		{
			require_once(get_template_directory() . '/framework/vc_extend.php');
		}

		add_action('init', 'homey_include_composer', 9999);
	}

}

if(!function_exists('homey_or_custom_posts')) {
	function homey_or_custom_posts($query) {
	  if($query->is_admin) {
	  	$post_type = $query->get('post_type');

	    if ( $post_type == 'homey_reservation' || $post_type == 'homey_review' || $post_type == 'homey_invoice') {

	    	$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : '';

	    	if(empty($orderby)) {
		      	$query->set('orderby', 'date');
		      	$query->set('order', 'DESC');
		      }
	    }
	  }
	  return $query;
	}
	add_filter('pre_get_posts', 'homey_or_custom_posts');
}


/**
 *	-----------------------------------------------------------------------------------------
 *	Enqueue scripts and styles.
 *	-----------------------------------------------------------------------------------------
 */
require_once( get_template_directory() . '/inc/register-scripts.php' );


/**
 *	-----------------------------------------------------------------------------------------
 *	Include files
 *	-----------------------------------------------------------------------------------------
 */
require_once( get_template_directory() . '/framework/functions/helper.php' );
require_once( get_template_directory() . '/framework/functions/wallet.php' );
require_once( get_template_directory() . '/framework/functions/profile.php' );
require_once( get_template_directory() . '/framework/functions/price.php' );
require_once( get_template_directory() . '/framework/functions/listings.php' );
require_once( get_template_directory() . '/framework/functions/reservation.php' );
require_once( get_template_directory() . '/framework/functions/reservation-hourly.php' );
require_once( get_template_directory() . '/framework/functions/calendar.php' );
require_once( get_template_directory() . '/framework/functions/calendar-hourly.php' );
require_once( get_template_directory() . '/framework/functions/review.php' );
require_once( get_template_directory() . '/framework/functions/search.php' );
require_once( get_template_directory() . '/framework/functions/messages.php' );
require_once( get_template_directory() . '/framework/functions/cron.php' );
require_once( get_template_directory() . '/framework/functions/icalendar.php' );
require_once( get_template_directory() . '/framework/functions/v13-db.php' );
require_once( get_template_directory() . '/framework/ics-parser/class.iCalReader.php' );
require_once( get_template_directory() . '/template-parts/header/favicons.php' );

require_once( get_template_directory() . '/framework/thumbnails/better-jpgs.php');


if ( class_exists( 'WooCommerce', false ) ) {
	require_once( get_template_directory() . '/framework/functions/woocommerce.php' );
}

/**
 *	-----------------------------------------------------------------------------------------
 *	Localizations
 *	-----------------------------------------------------------------------------------------
 */
require_once(get_theme_file_path('localization.php'));

/**
 *	-----------------------------------------------------------------------------------------
 *	Include hooks and filters
 *	-----------------------------------------------------------------------------------------
 */
require_once( get_template_directory() . '/framework/homey-hooks.php' );


/**
 *	-----------------------------------------------------------------------------------------
 *	Styling
 *	-----------------------------------------------------------------------------------------
 */
if ( class_exists( 'ReduxFramework' ) ) {
	require_once( get_template_directory() . '/inc/styling-options.php' );
}
require_once( get_template_directory() . '/framework/functions/demo-importer.php' );


/**
 *	-----------------------------------------------------------------------------------------
 *	TMG plugin activation
 *	-----------------------------------------------------------------------------------------
 */
	require_once( get_template_directory() . '/framework/class-tgm-plugin-activation.php' );
	require_once( get_template_directory() . '/framework/register-plugins.php' );


/**
 *	---------------------------------------------------------------------------------------
 *	Meta Boxes
 *	---------------------------------------------------------------------------------------
 */
require_once(get_template_directory() . '/framework/metaboxes/homey-meta-boxes.php');
require_once(get_template_directory() . '/framework/metaboxes/listing-state-meta.php');
require_once(get_template_directory() . '/framework/metaboxes/listing-cities-meta.php');
require_once(get_template_directory() . '/framework/metaboxes/listing-area-meta.php');
require_once( get_template_directory() . '/framework/metaboxes/listing-type-meta.php' );


/**
 *	---------------------------------------------------------------------------------------
 *	Options Admin Panel
 *	---------------------------------------------------------------------------------------
 */
require_once( get_template_directory() . '/framework/options/remove-tracking-class.php' ); // Remove tracking
require_once( get_template_directory() . '/framework/options/homey-options.php' );
require_once( get_template_directory() . '/framework/options/homey-option.php' );



/*-----------------------------------------------------------------------------------*/
/*	Register blog sidebar, footer and custom sidebar
/*-----------------------------------------------------------------------------------*/
if( !function_exists('homey_widgets_init') ) {
	add_action('widgets_init', 'homey_widgets_init');
	function homey_widgets_init()
	{
		register_sidebar(array(
			'name' => esc_html__('Default Sidebar', 'homey'),
			'id' => 'default-sidebar',
			'description' => esc_html__('Widgets in this area will be shown in the default sidebar.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		register_sidebar(array(
			'name' => esc_html__('Page Sidebar', 'homey'),
			'id' => 'page-sidebar',
			'description' => esc_html__('Widgets in this area will be shown in the page sidebar.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		register_sidebar(array(
			'name' => esc_html__('Listings Sidebar', 'homey'),
			'id' => 'listing-sidebar',
			'description' => esc_html__('Widgets in this area will be shown in listings sidebar.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Blog Sidebar', 'homey'),
			'id' => 'blog-sidebar',
			'description' => esc_html__('Widgets in this area will be shown in the blog sidebar.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Single Listing', 'homey'),
			'id' => 'single-listing',
			'description' => esc_html__('Widgets in this area will be shown in the single listing sidebar.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Custom Sidebar 1', 'homey'),
			'id' => 'custom-sidebar-1',
			'description' => esc_html__('This sidebar can be assigned to any page when add/edit page.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Custom Sidebar 2', 'homey'),
			'id' => 'custom-sidebar-2',
			'description' => esc_html__('This sidebar can be assigned to any page when add/edit page.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Custom Sidebar 3', 'homey'),
			'id' => 'custom-sidebar-3',
			'description' => esc_html__('This sidebar can be assigned to any page when add/edit page.', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));

		register_sidebar(array(
			'name' => esc_html__('Footer Area 1', 'homey'),
			'id' => 'footer-sidebar-1',
			'description' => esc_html__('Widgets in this area will be show in footer column one', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		register_sidebar(array(
			'name' => esc_html__('Footer Area 2', 'homey'),
			'id' => 'footer-sidebar-2',
			'description' => esc_html__('Widgets in this area will be show in footer column two', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		register_sidebar(array(
			'name' => esc_html__('Footer Area 3', 'homey'),
			'id' => 'footer-sidebar-3',
			'description' => esc_html__('Widgets in this area will be show in footer column three', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		register_sidebar(array(
			'name' => esc_html__('Footer Area 4', 'homey'),
			'id' => 'footer-sidebar-4',
			'description' => esc_html__('Widgets in this area will be show in footer column four', 'homey'),
			'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<div class="widget-top"><h3 class="widget-title">',
			'after_title' => '</h3></div>',
		));
		
	}
}

if ( !current_user_can('administrator') && !is_admin() ) {
	add_filter('show_admin_bar', '__return_false');
}

if ( !function_exists( 'homey_block_users' ) ) :

	add_action( 'init', 'homey_block_users' );

	function homey_block_users() {
		$users_admin_access = homey_option('users_admin_access');

		if( is_user_logged_in() ) {
			if ($users_admin_access != 0) {
				if (is_admin() && !current_user_can('administrator') && isset( $_GET['action'] ) != 'delete' && !(defined('DOING_AJAX') && DOING_AJAX)) {
					wp_die(esc_html("You don't have permission to access this page.", "homey"));
					exit;
				}
			}
		}
	}

endif;

function homey_stop_image_remove_while_listing_delete() {
	if(isset($_GET['image_delete']) && $_GET['image_delete'] != '') {
		update_option('homey_not_delete_for_demo', $_GET['image_delete']);
	}
}
homey_stop_image_remove_while_listing_delete();


//Delete property attachments when delete property
add_action( 'before_delete_post', 'homey_delete_property_attachments' );
if( !function_exists('homey_delete_property_attachments') ) {
	function homey_delete_property_attachments($postid)
	{
		
		// We check if the global post type isn't ours and just return
		global $post_type;

		if ($post_type == 'homey_review') {
			$review_listing_id = get_post_meta($postid, 'reservation_listing_id', true); 
			homey_adjust_listing_rating_on_delete($review_listing_id, $postid); 
		}

		if(get_option('homey_not_delete_for_demo') == 1) {
			return;
		}
		if ($post_type == 'listing') {
			$media = get_children(array(
				'post_parent' => $postid,
				'post_type' => 'attachment'
			));
			if (!empty($media)) {
				foreach ($media as $file) {
					// pick what you want to do
					//unlink(get_attached_file($file->ID));
					wp_delete_attachment($file->ID);
				}
			}
			$attachment_ids = get_post_meta($postid, 'homey_listing_images', false);
			if (!empty($attachment_ids)) {
				foreach ($attachment_ids as $id) {
					wp_delete_attachment($id);
				}
			}
		}
		return;
	}
}

function homey_delete_property_attachments_frontend($postid) {
		
		// We check if the global post type isn't ours and just return
		global $post_type;


		if(get_option('homey_not_delete_for_demo') == 1) {
			return;
		}
		$media = get_children(array(
			'post_parent' => $postid,
			'post_type' => 'attachment'
		));
		if (!empty($media)) {
			foreach ($media as $file) {
				// pick what you want to do
				//unlink(get_attached_file($file->ID));
				wp_delete_attachment($file->ID);
			}
		}
		$attachment_ids = get_post_meta($postid, 'homey_listing_images', false);
		if (!empty($attachment_ids)) {
			foreach ($attachment_ids as $id) {
				wp_delete_attachment($id);
			}
		}
		return;
	}


function homey_pre_get_posts($query) {

    if( is_admin() ) 
        return;

    if( is_search() && $query->is_main_query() ) {
        $query->set('post_type', 'post');
    } 

}

add_action( 'pre_get_posts', 'homey_pre_get_posts' );

/*
 * For Meta Tags
 * */

//Adding the Open Graph in the Language Attributes
function add_opengraph_doctype( $output ) {
    return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'add_opengraph_doctype');

//Lets add Open Graph Meta Info

function insert_fb_in_head() {
    global $post;
    if ( !is_singular()) //if it is not a post or a page
        return;
    echo '<meta property="og:title" content="' . get_the_title() . '"/>';
    echo '<meta property="og:type" content="article"/>';
    echo '<meta property="og:url" content="' . get_permalink() . '"/>';
    echo '<meta property="og:site_name" content="'.get_bloginfo( '', 'string' ).'"/>';
    if(!has_post_thumbnail( $post->ID )) { //the post does not have featured image, use a default image
        $default_image="http://example.com/image.jpg"; //replace this with a default image on your server or an image in your media library
        echo '<meta property="og:image" content="' . $default_image . '"/>';
    }
    else{
        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>';
    }
    echo "
";
}
add_action( 'wp_head', 'insert_fb_in_head', 5 );

add_action('wp_head', 'show_template');
function show_template() {
    global $template;
    if($_SERVER['HTTP_HOST'] == "localhost"){
        echo ' current template: '.basename($template);
    }
}

//extending search for CPT listing
add_filter( 'posts_join', 'extending_listing_admin_search_join' );
function extending_listing_admin_search_join ( $join ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "listing".
    if ( is_admin() && 'edit.php' === $pagenow && 'listing' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'extending_listing_search_where' );
function extending_listing_search_where( $where ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "listing".
    if ( is_admin() && 'edit.php' === $pagenow && 'listing' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->posts . ".ID LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where );
    }
    return $where;
}

function update_homey_membership_plan($post_ID, $post_after, $post_before){
   if($post_after->post_type == 'hm_homey_memberships'){
       delete_option($post_ID.'_'.homey_option('paypal_client_id'));// to delete plan for paypal
       delete_option($post_ID.'_'.homey_option('stripe_publishable_key'));//to delete plan for stripe
   }
}

add_action( 'post_updated', 'update_homey_membership_plan', 10, 3 );

function homey_listing_image_dimension($file)
{

    $img = getimagesize($file['tmp_name']);
    $dimensions = explode('x', homey_option('upload_image_min_dimensions'));
    $width = isset($dimensions[0]) ? (int)$dimensions[0] : 1200;
    $heigth = isset($dimensions[1]) ? (int)$dimensions[1] : 640;
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );
    $minimum = array('width' => $width, 'height' => $heigth);
    $width = $img[0];
    $height = $img[1];

    if ($width < $minimum['width'] || $height < $minimum['height']){
         return -1;
    }

    return 1;
}
?>