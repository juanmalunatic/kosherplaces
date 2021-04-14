<?php
/**
 * Load google fonts.
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
 * Load google fonts for our blocks.
 *
 * @since 1.6.0
 */
class KiokenBlocks_Font_Loader {


	/**
	 * This plugin's instance.
	 *
	 * @var KiokenBlocks_Font_Loader
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new KiokenBlocks_Font_Loader();
		}
	}

	/**
	 * The Plugin version.
	 *
	 * @var string $_version
	 */
	private $_version;


	/**
	 * The Constructor.
	 */
	private function __construct() {
		$this->_version = KK_BLOCKS_VERSION;

		add_action( 'wp_enqueue_scripts', array( $this, 'prep_gfonts_for_editor' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'fonts_loader' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'fonts_loader' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'prep_gfonts_for_editor' ) );
	}

	public function prep_gfonts_for_editor() {
		//preloading fonts that were used in kioken elements
		$prep_fonts = array(
			'family' => 'Dosis:200,300,400,500,600,700,800%7CRubik:300,400,500,700,900%7CWork+Sans:100,300,400,500,600,700,800,900%7CPoppins:100,200,300,400,500,600,700,800,900%7CCormorand+Garamond:300,400,500,600,700%7CIBM+Plex+Serif:100,200,300,400,500,600,700%7CIBM+Plex+Sans:100,200,300,400,500,600,700%7COswald:200,300,400,500,600,700%7CRoboto:100,300,400,500,700,900',
			'subset' => 'latin',
		);
		wp_register_style( 'kioken-prep-google_fonts', add_query_arg( $prep_fonts, "//fonts.googleapis.com/css" ), array(), null );
		if ( get_option( 'kiokenblocks_prepping_fonts', false ) ) {
			wp_enqueue_style( 'kioken-prep-google_fonts' );
		}

	}

	/**
	 * Load fonts.
	 *
	 * @access public
	 */
	public function fonts_loader() {
		global $post;

		if ( $post && isset( $post->ID ) ) {

			$fonts = get_post_meta( $post->ID, '_kiokenblocks_attr', true );

			if ( ! empty( $fonts ) ) {

				$fonts = array_unique( explode( ',', $fonts ) );

				$system = array(
					'Arial',
					'Tahoma',
					'Verdana',
					'Helvetica',
					'Times New Roman',
					'Trebuchet MS',
					'Georgia',
				);

				$gfonts = '';

				$gfonts_attr = ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';

				foreach ( $fonts as $font ) {
					if ( ! in_array( $font, $system, true ) && ! empty( $font ) ) {
						$gfonts .= str_replace( ' ', '+', trim( $font ) ) . $gfonts_attr . '|';
					}
				}

				if ( ! empty( $gfonts ) ) {
					$query_args = array(
						'family' => $gfonts,
					);

					wp_register_style(
						'kiokenblocks-google-fonts',
						add_query_arg( $query_args, '//fonts.googleapis.com/css' ),
						array(),
						$this->_version
					);

					wp_enqueue_style( 'kiokenblocks-google-fonts' );
					$this->prep_gfonts_for_editor();
				}
				// else {
				// 	delete_post_meta( $post->ID, '_kiokenblocks_attr' );
				// 	wp_dequeue_style( 'kiokenblocks-google-fonts' );
				// }
				// Reset.
				$gfonts = '';
			}


		}
	}
}

KiokenBlocks_Font_Loader::register();
