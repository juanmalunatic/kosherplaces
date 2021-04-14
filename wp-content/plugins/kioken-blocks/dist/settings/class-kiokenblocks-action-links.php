<?php
/**
 * Add links to CoBlocks on the plugins admin page.
 *
 * @package CoBlocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates a link.
 */
class KiokenBlocks_Action_Links {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Plugin row meta links
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata.
	 * @param string $plugin_file Path to the plugin file.
	 * @return array $input
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file ) {

		// Check if this is defined.
		if ( ! defined( 'KK_BLOCKS_PLUGIN_BASE' ) ) {
			define( 'KK_BLOCKS_PLUGIN_BASE', null );
		}

		if ( KK_BLOCKS_PLUGIN_BASE === $plugin_file ) {
			$row_meta = [
				'review' => '<a href="' . esc_url( KK_BLOCKS_REVIEW_URL ) . '" aria-label="' . esc_attr( __( 'Review Kioken Blocks on WordPress.org', 'kioken-blocks' ) ) . '" target="_blank">' . __( 'Rate Kioken Blocks &nbsp;★★★★★', 'kioken-blocks' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}
}

new KiokenBlocks_Action_Links();
