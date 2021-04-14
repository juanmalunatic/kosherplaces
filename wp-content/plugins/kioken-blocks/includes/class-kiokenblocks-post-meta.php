<?php
/**
 * Register post meta.
 *
 * @package   @@pkg.title
 * @author    @@pkg.author
 * @link      @@pkg.author_uri
 * @license   @@pkg.license
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * KiokenBlocks_Post_Meta Class
 *
 * @since 1.6.0
 */
class KiokenBlocks_Post_Meta {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'init', array( $this, 'register_meta' ) );
	}

	/**
	 * Register meta.
	 */
	public function register_meta() {
		register_meta(
			'post', '_kiokenblocks_attr', array(
				'show_in_rest'  => true,
				'single'        => true,
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_meta(
			'post', '_kiokenblocks_dimensions', array(
				'show_in_rest'  => true,
				'single'        => true,
				'auth_callback' => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

}

return new KiokenBlocks_Post_Meta();
