<?php
/**
 * Build Welcome Page With settings.
 *
 * @package Kioken Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Build Welcome Page class
 *
 * @category class
 */
class Kioken_Blocks_Settings {

	public static $settings = array();

	/**
	 * Instance of this class
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Instance Control
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class Constructor.
	 */
	public function __construct() {
		// only load if admin.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_menu' ));
			// add_filter( 'plugin_action_links_kioken-blocks/kioken-blocks.php', array( $this, 'add_settings_link' ) );
		}
		add_action( 'wp_ajax_kioken_blocks_activate_deactivate', array( $this, 'ajax_blocks_activate_deactivate' ), 10, 0 );
		add_action( 'wp_ajax_kioken_blocks_save_config', array( $this, 'ajax_blocks_save_config' ), 10, 0 );
		add_action( 'wp_ajax_kioken_post_default_width', array( $this, 'ajax_default_editor_width' ), 10, 0 );
		add_action( 'enqueue_block_editor_assets', array( $this, 'deregister_blocks' ) );
		add_action( 'admin_init', array( $this, 'activation_redirect' ) );
		add_action( 'admin_init', array( $this, 'load_settings' ) );
		add_action( 'admin_init', array( $this, 'load_api_settings' ) );
		// add_filter( 'admin_body_class', array( $this, 'kioken_editor_body_classes' )  );
		add_filter( 'admin_body_class', array( $this, 'kioken_editor_default_bodyclass' )  );
	}
	/**
	 * Redirect to the settings page on activation
	 */
	public function activation_redirect( $plugin ) {

			if ( '../init.php' !== $plugin ) {

				return;

			}

			$nonce          = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
			$activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_VALIDATE_BOOLEAN );

			if ( ! $nonce ) {

				return;

			}

			if ( defined( 'WP_CLI' ) && WP_CLI ) {

				WP_CLI::log(
					WP_CLI::colorize(
						'%b' . sprintf( '🎉 %s %s', __( 'Get started with Kioken Blocks here:', 'kioken-blocks' ), admin_url( 'admin.php?page=kioken_blocks' ) ) . '%n'
					)
				);

				return;

			}

			if ( $activate_multi ) {

				return;

			}

			wp_safe_redirect( admin_url( 'admin.php?page=kioken_blocks' ) );

			die();

	}

	public static function get_data_options( $key ) {
		if ( ! isset( self::$settings[$key] ) ) {
			self::$settings[$key] = get_option( $key, array() );
		}
		return self::$settings[$key];
	}
	/**
	 * Deregister Blocks.
	 */
	public function deregister_blocks() {
		// Scripts.
		$editor_widths = get_option( 'kk_blocks_editor_width', array() );
		if ( isset( $editor_widths['enable_editor_width'] ) && 'false' === $editor_widths['enable_editor_width'] ) {
			// $plugins = array('kioken-editor-width');
			$plugins = array();
		} else {
			$plugins = array();
		}
		wp_enqueue_script( 'kioken-blocks-deregister-js', KK_BLOCKS_URL . 'dist/settings/blocks-deregister.js', array( 'wp-blocks' ), KK_BLOCKS_VERSION, true );
		wp_localize_script( 'kioken-blocks-deregister-js', 'kt_deregister_params', array(
			'deregister' => get_option( 'kt_blocks_unregistered_blocks' ),
			'dergisterplugins' => $plugins,
		) );
	}
	/**
	 * Add option page menu
	 */
	public function add_menu() {
		$menu_icon = 'data:image/svg+xml;base64,' . base64_encode('<svg width="24px" class="kib_logo" height="23px" viewBox="0 0 24 23" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M23.7064516,7.85406928 L23.7098367,7.86427999 C24.4443983,10.587136 23.7132217,13.5720668 22.315185,16.0328479 C20.6463421,18.959918 18.13123,21.390067 14.8510905,22.2886095 C10.646825,23.4526304 6.98755689,21.9959024 3.90713693,19.1403073 C-0.0974090147,15.4270124 -1.66469961,9.8009113 2.30261049,5.35244536 C3.90914069,3.64464276 5.84234724,2.28087947 7.98615457,1.34303996 C9.41283277,0.679981872 10.9364729,0.252457038 12.498462,0.0769119342 C14.2316214,-0.106880843 15.7955269,0.00203339524 17.383128,0.808679475 C18.7120273,1.47963923 19.931179,2.35079219 20.9983901,3.39198907 C22.2796118,4.61078764 23.2144136,6.15106315 23.7064516,7.85406928 Z M11.0472515,12.1260401 C11.0570052,12.1260401 11.0661373,12.1305193 11.0716838,12.138024 L13.6080828,15.5699051 C13.857632,15.9075583 14.2685082,16.1090909 14.7073521,16.1090909 L16.2805879,16.1090909 C16.5265719,16.1090909 16.7659283,16.0345077 16.9627697,15.8965237 C17.4654989,15.5441151 17.5676184,14.8772284 17.1908598,14.4069906 L14.8477735,11.482551 C14.4740487,11.0160996 14.4946103,10.3699525 14.8973339,9.92511857 L16.9429544,7.66560056 C17.0975454,7.49484494 17.1823837,7.27834044 17.1823837,7.05458482 C17.1823837,6.53240295 16.7298226,6.10909091 16.1715601,6.10909091 L14.6608349,6.10909091 C14.2392758,6.10909091 13.8424313,6.2951465 13.590161,6.61106296 L11.0565721,9.78385937 C11.0507062,9.79120529 11.0414784,9.79553159 11.031676,9.79553159 C11.023074,9.79553159 11.0161006,9.78900893 11.0161006,9.78096282 L11.0161006,7.35909091 C11.0161006,6.66873497 10.4177875,6.10909091 9.67973079,6.10909091 L8.75455167,6.10909091 C8.01649498,6.10909091 7.9453655,6.66873497 7.9453655,7.35909091 L7.9453655,14.8590909 C7.9453655,15.5494468 8.01649498,16.1090909 8.75455167,16.1090909 L9.6953062,16.1090909 C10.4333629,16.1090909 11.031676,15.5494468 11.031676,14.8590909 L11.031676,12.1406088 C11.031676,12.1325627 11.0386494,12.1260401 11.0472515,12.1260401 Z"></path></svg>');

		$page = add_menu_page( __( 'Kioken Blocks -  Gutenberg Page Builder Utility', 'kioken-blocks' ), __( 'Kioken Blocks' ), 'edit_pages', 'kioken_blocks', array( $this, 'config_page' ), $menu_icon );
		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
	}
	/**
	 * Loads admin style sheets and scripts
	 */
	public function scripts() {
		$texts = array(
			'close' => __( 'Close', 'kioken-blocks' ),
			'save' => __( 'Save', 'kioken-blocks' ),
			'updating' => __( 'Updating', 'kioken-blocks' ),
			'updated' => __( 'Updated', 'kioken-blocks' ),
			'config' => __( 'Global Settings for', 'kioken-blocks' ),
			'settings' => __( 'Default Settings', 'kioken-blocks' ),
		);
		$settings = array(
			'kioken/spacer' => array(
				'spacerHeight' => array(
					'type' => 'number',
					'default' => 60,
					'options' => array(
						'min' => 6,
						'max' => 600,
						'step' => 1,
					),
					'name' => __( 'Spacer Height (px)', 'kioken-blocks' ),
				),
				'dividerEnable' => array(
					'type' => 'boolean',
					'default' => true,
					'name' => __( 'Enable Divider', 'kioken-blocks' ),
				),
				'dividerStyle' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'solid',
							'name' =>  __( 'Solid', 'kioken-blocks' ),
						),
						array(
							'value' => 'dashed',
							'name' =>  __( 'Dashed', 'kioken-blocks' ),
						),
						array(
							'value' => 'dotted',
							'name' =>  __( 'Dotted', 'kioken-blocks' ),
						),
					),
					'default' => 'solid',
					'name' => __( 'Divider Style', 'kioken-blocks' ),
				),
				'dividerOpacity' => array(
					'type' => 'number',
					'default' => 100,
					'options' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
					'name' => __( 'Divider Opacity (0-100)', 'kioken-blocks' ),
				),
				'dividerColor' => array(
					'type' => 'color',
					'default' => '#eee',
					'name' => __( 'Divider Color', 'kioken-blocks' ),
				),
				'dividerWidth' => array(
					'type' => 'number',
					'default' => 80,
					'options' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
					'name' => __( 'Divider Width (%)', 'kioken-blocks' ),
				),
				'dividerHeight' => array(
					'type' => 'number',
					'default' => 1,
					'options' => array(
						'min' => 0,
						'max' => 40,
						'step' => 1,
					),
					'name' => __( 'Divider Height (px)', 'kioken-blocks' ),
				),
			),
			'kioken/rowlayout' => array(
				'columnGutter' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'default',
							'name' =>  __( 'Default: 30px', 'kioken-blocks' ),
						),
						array(
							'value' => 'none',
							'name' =>  __( 'No Gutter', 'kioken-blocks' ),
						),
						array(
							'value' => 'skinny',
							'name' =>  __( 'Skinny: 10px', 'kioken-blocks' ),
						),
						array(
							'value' => 'narrow',
							'name' =>  __( 'Narrow: 20px', 'kioken-blocks' ),
						),
						array(
							'value' => 'wide',
							'name' =>  __( 'Wide: 40px', 'kioken-blocks' ),
						),
						array(
							'value' => 'wider',
							'name' =>  __( 'Wider: 60px', 'kioken-blocks' ),
						),
						array(
							'value' => 'widest',
							'name' =>  __( 'Widest: 80px', 'kioken-blocks' ),
						),
					),
					'default' => 'default',
					'name' => __( 'Column Gutter', 'kioken-blocks' ),
				),
				'htmlTag' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'div',
							'name' =>  __( 'div', 'kioken-blocks' ),
						),
						array(
							'value' => 'section',
							'name' =>  __( 'section', 'kioken-blocks' ),
						),
						array(
							'value' => 'article',
							'name' =>  __( 'article', 'kioken-blocks' ),
						),
						array(
							'value' => 'main',
							'name' =>  __( 'main', 'kioken-blocks' ),
						),
						array(
							'value' => 'aside',
							'name' =>  __( 'aside', 'kioken-blocks' ),
						),
					),
					'default' => 'div',
					'name' => __( 'Container HTML tag', 'kioken-blocks' ),
				),
				'maxWidth' => array(
					'type' => 'number',
					'default' => '1200',
					'options' => array(
						'min' => 400,
						'max' => 2000,
						'step' => 1,
					),
					'name' => __( 'Container Content Max Width (px)', 'kioken-blocks' ),
				),
				'topPadding' => array(
					'type' => 'number',
					'default' => 25,
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Top Padding (px)', 'kioken-blocks' ),
				),
				'bottomPadding' => array(
					'type' => 'number',
					'default' => 25,
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Bottom Padding (px)', 'kioken-blocks' ),
				),
				'leftPadding' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Left Padding (px)', 'kioken-blocks' ),
				),
				'rightPadding' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Right Padding (px)', 'kioken-blocks' ),
				),
				/*'topPaddingM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Top Padding (px)', 'kioken-blocks' ),
				),
				'bottomPaddingM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Bottom Padding (px)', 'kioken-blocks' ),
				),
				'leftPaddingM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Left Padding (px)', 'kioken-blocks' ),
				),
				'rightPaddingM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Right Padding (px)', 'kioken-blocks' ),
				),*/
				'topMargin' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Top Margin (px)', 'kioken-blocks' ),
				),
				'bottomMargin' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'Bottom Margin (px)', 'kioken-blocks' ),
				),
				/*'topMarginM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Top Margin (px)', 'kioken-blocks' ),
				),
				'bottomMarginM' => array(
					'type' => 'number',
					'default' => '',
					'options' => array(
						'min' => 0,
						'max' => 500,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Bottom Margin (px)', 'kioken-blocks' ),
				),*/
			),
			'kioken/advancedbtn' => array(
				'btns' => array(
					'type' => 'array',
					'name' => __( 'Button Settings', 'kioken-blocks' ),
					'default' => array(
						array(
							'size' => 18,
							'paddingBT' => '',
							'paddingLR' => '',
							'color' => '#555555',
							'background' => 'transparent',
							'border' => '#555555',
							'borderRadius' => 3,
							'borderWidth' => 2,
							'colorHover' => '#ffffff',
							'backgroundHover' => '#444444',
							'borderHover' => '#444444',
						),
					),
					'options' => array(
						'size' => array(
							'type' => 'number',
							'default' => 18,
							'options' => array(
								'min' => 10,
								'max' => 100,
								'step' => 1,
							),
							'name' => __( 'Font Size (px)', 'kioken-blocks' ),
						),
						'paddingBT' => array(
							'type' => 'number',
							'default' => '',
							'options' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
							),
							'name' => __( 'Padding Top and Bottom (px)', 'kioken-blocks' ),
						),
						'paddingLR' => array(
							'type' => 'number',
							'default' => '',
							'options' => array(
								'min' => 0,
								'max' => 100,
								'step' => 1,
							),
							'name' => __( 'Padding Left and Right (px)', 'kioken-blocks' ),
						),
						'color' => array(
							'type' => 'color',
							'default' => '#555555',
							'name' => __( 'Text Color', 'kioken-blocks' ),
						),
						'background' => array(
							'type' => 'color',
							'default' => 'transparent',
							'name' => __( 'Background Color', 'kioken-blocks' ),
						),
						'border' => array(
							'type' => 'color',
							'default' => '#555555',
							'name' => __( 'Border Color', 'kioken-blocks' ),
						),
						'borderRadius' => array(
							'type' => 'number',
							'default' => 3,
							'options' => array(
								'min' => 0,
								'max' => 50,
								'step' => 1,
							),
							'name' => __( 'Border Radius (px)', 'kioken-blocks' ),
						),
						'borderWidth' => array(
							'type' => 'number',
							'default' => 2,
							'options' => array(
								'min' => 0,
								'max' => 20,
								'step' => 1,
							),
							'name' => __( 'Border Width (px)', 'kioken-blocks' ),
						),
						'colorHover' => array(
							'type' => 'color',
							'default' => '#ffffff',
							'name' => __( 'HOVER - Text Color', 'kioken-blocks' ),
						),
						'backgroundHover' => array(
							'type' => 'color',
							'default' => '#444444',
							'name' => __( 'HOVER - Background Color', 'kioken-blocks' ),
						),
						'borderHover' => array(
							'type' => 'color',
							'default' => '#444444',
							'name' => __( 'HOVER - Border Color', 'kioken-blocks' ),
						),
					),
				),
			),
			'kioken/tabs' => array(
				'layout' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'tabs',
							'name' =>  __( 'Standard Tabs', 'kioken-blocks' ),
						),
						array(
							'value' => 'vtabs',
							'name' =>  __( 'Vertical Tabs', 'kioken-blocks' ),
						),
					),
					'default' => 'tabs',
					'name' => __( 'Tab Layout', 'kioken-blocks' ),
				),
				'tabletLayout' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'tabs',
							'name' =>  __( 'Standard Tabs', 'kioken-blocks' ),
						),
						array(
							'value' => 'vtabs',
							'name' =>  __( 'Vertical Tabs', 'kioken-blocks' ),
						),
						array(
							'value' => 'accordion',
							'name' =>  __( 'Accordion', 'kioken-blocks' ),
						),
					),
					'default' => '',
					'name' => __( 'TABLET - Tab Layout', 'kioken-blocks' ),
				),
				'mobileLayout' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'tabs',
							'name' =>  __( 'Standard Tabs', 'kioken-blocks' ),
						),
						array(
							'value' => 'vtabs',
							'name' =>  __( 'Vertical Tabs', 'kioken-blocks' ),
						),
						array(
							'value' => 'accordion',
							'name' =>  __( 'Accordion', 'kioken-blocks' ),
						),
					),
					'default' => '',
					'name' => __( 'MOBILE - Tab Layout', 'kioken-blocks' ),
				),
				'contentBgColor' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'Content Background Color', 'kioken-blocks' ),
				),
				'contentBorderColor' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'Content Border Color', 'kioken-blocks' ),
				),
				'contentBorder' => array(
					'type' => 'numberArray',
					'options' => array(
						'min' => 0,
						'max' => 20,
						'step' => 1,
					),
					'default' => array( 1,1,1,1 ),
					'name' => __( 'Content Border Width', 'kioken-blocks' ),
				),
				'innerPadding' => array(
					'type' => 'numberArray',
					'options' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
					'default' => array( 20,20,20,20 ),
					'name' => __( 'Content Padding', 'kioken-blocks' ),
				),
				'tabAlignment' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'left',
							'name' =>  __( 'Left', 'kioken-blocks' ),
						),
						array(
							'value' => 'center',
							'name' =>  __( 'Center', 'kioken-blocks' ),
						),
						array(
							'value' => 'right',
							'name' =>  __( 'Right', 'kioken-blocks' ),
						),
					),
					'default' => 'left',
					'name' => __( 'Tab Alignment', 'kioken-blocks' ),
				),
				'titleColor' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'Title Text Color', 'kioken-blocks' ),
				),
				'titleColorHover' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'HOVER - Title Text Color', 'kioken-blocks' ),
				),
				'titleColorActive' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'ACTIVE - Title Text Color', 'kioken-blocks' ),
				),
				'titleBg' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'Title Background Color', 'kioken-blocks' ),
				),
				'titleBgHover' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'HOVER - Title Background Color', 'kioken-blocks' ),
				),
				'titleBgActive' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'ACTIVE - Title Background Color', 'kioken-blocks' ),
				),
				'titleBorder' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'Title Border Color', 'kioken-blocks' ),
				),
				'titleBorderHover' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'HOVER - Title Border Color', 'kioken-blocks' ),
				),
				'titleBorderActive' => array(
					'type' => 'color',
					'default' => '',
					'name' => __( 'ACTIVE - Title Border Color', 'kioken-blocks' ),
				),
				'titleBorderWidth' => array(
					'type' => 'numberArray',
					'options' => array(
						'min' => 0,
						'max' => 20,
						'step' => 1,
					),
					'default' => '',
					'name' => __( 'Title Border Width', 'kioken-blocks' ),
				),
				'titleBorderRadius' => array(
					'type' => 'numberArray',
					'options' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
					'default' => '',
					'name' => __( 'Title Border Radius', 'kioken-blocks' ),
				),
				'titlePadding' => array(
					'type' => 'numberArray',
					'options' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
					'default' => '',
					'name' => __( 'Title Padding', 'kioken-blocks' ),
				),
				'titleMargin' => array(
					'type' => 'numberArray',
					'default' => '',
					'options' => array(
						'min' => -25,
						'max' => 25,
						'step' => 1,
					),
					'name' => __( 'Title Margin', 'kioken-blocks' ),
				),
				'sizeType' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'px',
							'name' =>  __( 'px', 'kioken-blocks' ),
						),
						array(
							'value' => 'em',
							'name' =>  __( 'em', 'kioken-blocks' ),
						),
					),
					'default' => 'px',
					'name' => __( 'Font Size Units', 'kioken-blocks' ),
				),
				'size' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'sizeType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'Title Font Size', 'kioken-blocks' ),
				),
				'tabSize' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'sizeType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'TABLET - Title Font Size', 'kioken-blocks' ),
				),
				'mobileSize' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'sizeType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Title Font Size', 'kioken-blocks' ),
				),
				'lineType' => array(
					'type' => 'select',
					'options' => array(
						array(
							'value' => 'px',
							'name' =>  __( 'px', 'kioken-blocks' ),
						),
						array(
							'value' => 'em',
							'name' =>  __( 'em', 'kioken-blocks' ),
						),
					),
					'default' => 'px',
					'name' => __( 'Line Height Size Units', 'kioken-blocks' ),
				),
				'lineHeight' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'lineType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'Title Font Line Height (px)', 'kioken-blocks' ),
				),
				'tabLineHeight' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'lineType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'TABLET - Title Font Line Height', 'kioken-blocks' ),
				),
				'mobileLineHeight' => array(
					'type' => 'typoNumber',
					'default' => '',
					'units' => 'lineType',
					'options' => array(
						'min' => 5,
						'max' => 200,
						'step' => 1,
					),
					'name' => __( 'MOBILE - Title Font Line Height', 'kioken-blocks' ),
				),
			),
		);
		wp_enqueue_style( 'kioken-blocks-admin-css', KK_BLOCKS_URL . '/dist/settings/styles.css', array( 'wp-jquery-ui-dialog', 'wp-color-picker' ), KK_BLOCKS_VERSION, 'all' );

		wp_localize_script( 'kioken-blocks-admin-js', 'kt_blocks_params', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'wpnonce' => wp_create_nonce( 'kioken-blocks-manage' ),
			'blockConfig' => get_option( 'kt_blocks_config_blocks' ),
			'blockConfigSettings' => $settings,
			'texts' => $texts,
		) );
	}

	/**
	 * Register settings
	 */
	public function load_api_settings() {
		register_setting( 'kioken_blocks_config_blocks', 'kioken_blocks_config_blocks', array(
				'type'              => 'string',
				'description'       => __( 'Configure Kioken Block Defaults', 'kioken-blocks' ),
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'default'           => '',
			)
		);
		// register_setting( 'kiokenblocks_editor_enhanced', 'kiokenblocks_editor_enhanced', array(
		// 		'type'              => 'boolean',
		// 		'description'       => __( 'Enable Experimental Limited Margins for better WYSIWYG experience?', 'kioken-blocks' ),
		// 		'sanitize_callback' => 'rest_sanitize_boolean',
		// 		'show_in_rest'      => true,
		// 		'default'           => false,
		// 	)
		// );
		register_setting( 'kiokenblocks_prepping_fonts', 'kiokenblocks_prepping_fonts', array(
				'type'              => 'boolean',
				'description'       => __( 'Enable preloading of Google Fonts in front end?', 'kioken-blocks' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => true,
			)
		);
		register_setting( 'kiokenblocks_pro_notices', 'kiokenblocks_pro_notices', array(
				'type'              => 'boolean',
				'description'       => __( 'Show Pro Notices?', 'kioken-blocks' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'show_in_rest'      => true,
				'default'           => true,
			)
		);
		register_setting( 'kioken_blocks_settings_blocks', 'kioken_blocks_settings_blocks', array(
			'type'              => 'string',
			'description'       => __( 'Configure Kioken Block Settings View', 'kioken-blocks' ),
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'default'           => '',
		)

	);
}
	/**
	 * Register settings
	 */
	public function load_settings() {

		register_setting( 'kk_blocks_editor_width', 'kk_blocks_editor_width', array( $this, 'validate_options' ) );

	}



	/**
	 * Add classes to editor body
	 */
	// public function kioken_editor_body_classes( $classes ) {
	// 	if ( ! get_option( 'kiokenblocks_editor_enhanced', false ) ) {
	// 				return;
	// 			}
	//
	// 	$screen = get_current_screen();
	//
	// 	if ( 'post' == $screen->base ) {
	// 				$classes .= ' kioken-limited-margins';
	// 			}
	//
	// 	return $classes;
	// }

	public function kioken_editor_default_bodyclass( $classes ) {

		$screen = get_current_screen();

		if ( 'post' == $screen->base ) {
				$classes .= ' kioken-editor';
		}

		return $classes;
	}

	/**
	 * Outputs Sidebar number field
	 */
	public function enabled_editor_width_callback() {
		$data = self::get_data_options( 'kk_blocks_editor_width' );
		$default_enabled = ( isset( $data['enable_editor_width'] ) ? $data['enable_editor_width'] : 'true' );
		echo '<p>' . __( 'Allows for changes to the editor width on per page/post basis with preset defaults.', 'kioken-blocks' ) . '<p>';
		echo '<select class="kt-blocks-enable-editor-width kt-editor-width-defaults-select" name="kk_blocks_editor_width[enable_editor_width]">';
			echo '<option value="false" ' . ( $default_enabled === 'false' ? 'selected' : '' ) . '>' . __( 'False', 'kioken-blocks' ) . '</option>';
			echo '<option value="true" ' . ( $default_enabled === 'true' ? 'selected' : '' ) . '>' . __( 'True', 'kioken-blocks' ) . '</option>';
		echo '</select>';
	}

	/**
	 * Outputs Sidebar number field
	 */
	public function sidebar_callback() {
		$data = self::get_data_options( 'kk_blocks_editor_width' );
		echo "<input id='kt-sidebar-max' name='kk_blocks_editor_width[sidebar]' size='25' type='number' value='" . ( isset( $data['sidebar'] ) ? $data['sidebar'] : '750' ) . "' />";
		echo '<span class="kt-sub-input-description">' . __( 'px', 'kioken-blocks' ) . '</span>';
	}
	/**
	 * Outputs no sidebar number field
	 */
	public function nosidebar_callback() {
		$data = self::get_data_options( 'kk_blocks_editor_width' );
		echo "<input id='kt-sidebar-max' name='kk_blocks_editor_width[nosidebar]' size='25' type='number' value='" . ( isset( $data['nosidebar'] ) ? $data['nosidebar'] : '1140' ) . "' />";
		echo '<span class="kt-sub-input-description">' . __( 'px', 'kioken-blocks' ) . '</span>';
	}

	/**
	 * Outputs post default select feild
	 */
	public function post_default_callback() {
		$data = self::get_data_options( 'kk_blocks_editor_width' );
		$default_post_type = ( isset( $data['post_default'] ) ? $data['post_default'] : 'sidebar' );
		echo '<select class="kt-blocks-posts-defaults kt-editor-width-defaults-select" name="kk_blocks_editor_width[post_default]">';
			echo '<option value="sidebar" ' . ( $default_post_type === 'sidebar' ? 'selected' : '' ) . '>' . __( 'Small', 'kioken-blocks' ) . '</option>';
			echo '<option value="nosidebar" ' . ( $default_post_type === 'nosidebar' ? 'selected' : '' ) . '>' . __( 'Large', 'kioken-blocks' ) . '</option>';
			echo '<option value="fullwidth" ' . ( $default_post_type === 'fullwidth' ? 'selected' : '' ) . '>' . __( 'Fullwidth', 'kioken-blocks' ) . '</option>';
		echo '</select>';
	}
	/**
	 * Outputs post default select feild
	 */
	public function page_default_callback() {
		$data = self::get_data_options( 'kk_blocks_editor_width' );
		$default_page_type = ( isset( $data['page_default'] ) ? $data['page_default'] : 'sidebar' );
		echo '<select class="kt-blocks-posts-defaults kt-editor-width-defaults-select" name="kk_blocks_editor_width[page_default]">';
			echo '<option value="sidebar" ' . ( $default_page_type === 'sidebar' ? 'selected' : '' ) . '>' . __( 'Small', 'kioken-blocks' ) . '</option>';
			echo '<option value="nosidebar" ' . ( $default_page_type === 'nosidebar' ? 'selected' : '' ) . '>' . __( 'Large', 'kioken-blocks' ) . '</option>';
			echo '<option value="fullwidth" ' . ( $default_page_type === 'fullwidth' ? 'selected' : '' ) . '>' . __( 'Fullwidth', 'kioken-blocks' ) . '</option>';
		echo '</select>';
	}
	public function maxwidths_callback() {
		global $content_width;
		echo '<h5 class="kt-main-subtitle">' . __( 'Assign Editor Template Max Widths', 'kioken-blocks' ) . '</h5>';
		echo '<div class="kt-main-description-notice">' . __( 'Note: The current active themes "$content_width" is set to:', 'kioken-blocks' ) . ' ' . $content_width . 'px</div>';
	}
	/**
	 * Sanitizes and validates all input and output for Dashboard
	 */
	public function validate_options( $input ) {
		return $input;
	}
	/**
	 * Loads config page
	 */
	public function config_page() {
		?>
			<div class="wrap kb_plugin_welcome">
				<h2 class="notices"></h2>
				<div class="kb-options-section notice">
					<div class="kb-options-section-header">
						<div class="kb-header-left">
                <h2 class="kb-options-section-title">
									<?php esc_attr_e('Welcome to Kioken Blocks! - Version ', 'kioken-blocks'); echo esc_attr( KK_BLOCKS_VERSION ); ?>
									<?php if ( defined('KK_BLOCKS_PRO') ): ?>
											<span class="kb-badge">PRO</span>
									<?php endif; ?>
								</h2>
                <h3 class="kb-options-section-subtitle"><?php esc_attr_e('Gutenberg Page Builder Utility', 'kioken-blocks') ?></h3>
            </div>
						<div class="kb-header-right">
							<a href="<?php echo esc_url('https://kiokengutenberg.com/?utm_source=kb-dashboard'); ?>" title="<?php echo esc_attr_e('Kioken Blocks Website', 'kioken-blocks'); ?>" target="_blank">
                <img src="https://kiokengutenberg.com/wp-content/uploads/2019/06/logo-kb.png" alt="logo" />
							</a>
            </div>
					</div><!-- end of section-header -->

					<div class="kb-row kb-cols-2">
						<div>
							<?php if ( ! is_plugin_active('gutenslider/eedee-gutenslider.php') ) : ?>
								<div class="notify-gutenslider">
									<h2><?php esc_html_e('Enjoying Kioken Blocks? Then You will love GutenSlider!', 'kioken-blocks'); ?></h2>
									<p><?php esc_html_e('Gutenslider is an image and video slider plugin for WordPress that adds a simple to use block to your WordPress editor. You can add any content on top that you want! Gutenslider is faster and slicker than any other slider around.', 'kioken-blocks'); ?></p>
									<p><a href="https://wordpress.org/plugins/gutenslider/" target="_blank" rel="noreferrer" class="button button-large button-primary"><?php esc_attr_e('Get GutenSlider'); ?></a></p>
								</div>
							<?php endif; ?>
							<h4 class="kb-options-section-title">
			            <?php esc_attr_e('Code Name: Sonic Boom - What\'s New in ', 'kioken-blocks'); echo esc_attr( KK_BLOCKS_VERSION ); ?>

			        </h4>
							<p class="fun-fact"><?php esc_attr_e('Fun fact: Sonic Boom is guile\'s special fireball move in Street Fighter game.'); ?> </p>
							<ul class="kb-options-features">
                  <li>- <?php esc_attr_e('New: Vertical Text Extension! Rotate paragraphs and headings for vertical text effect!'); ?></li>
									<li>- <?php esc_attr_e('New: Kioken Elements are now accessible from anywhere in the editor, without having to create a container row block first.'); ?></li>
									<li><a href="admin.php?page=kioken_blocks-pricing" class="">- <?php esc_attr_e('New: Additional animation styles and templates for Kioken Blocks Pro!'); ?></a></li>
                  <li>- <?php esc_attr_e('Update: Styling fixes and improvements for better compatibility with popular themes.'); ?></li>
                  <li>- <?php esc_attr_e('Update: Block editor UI improvements'); ?></li>
                  <li>- <?php esc_attr_e('Update: Kinetic Posts block custom padding options.'); ?></li>
									<li>- <?php esc_attr_e('Update: Changes with Maximum width option in Override Spacings extension is now also properly reflected in the editor'); ?></li>
                  <li>- <?php esc_attr_e('Fix: Kinetic Posts block not hiding excerpts even though it\'s set hidden in the editor.'); ?></li>

              </ul>

							<h4 class="kb-options-section-title">
									<?php esc_attr_e('Learn how to Master Kioken Blocks', 'kioken-blocks') ?>
									<img src="<?php echo  KK_BLOCKS_URL . 'dist/images/fire.png'; ?>" srcset="<?php echo  KK_BLOCKS_URL . 'dist/images/fire.png'; ?> 1x, <?php echo  KK_BLOCKS_URL . 'dist/images/fire.png'; ?> 2x" alt="<?php echo esc_attr('Features'); ?>">
							</h4>
							<div class="kb-options-buttons">
                  <a href="https://support.kiokentheme.com/docs/kioken-blocks/" target="_blank" class="button button-large"><?php esc_attr_e('Documentation'); ?></a>
                  <a href="https://kiokengutenberg.com/category/tutorials/?utm_source=kb-dashboard" target="_blank" class="button button-large"><?php esc_attr_e('Kioken Blocks Tutorials'); ?></a>
                  <a href="https://wordpress.org/support/plugin/kioken-blocks/" target="_blank" class="button button-large button-primary"><?php esc_attr_e('Support'); ?></a>
              </div>

							<div class="kb-socials">
								<h4 class="kb-options-section-title">
										<?php esc_attr_e('Don\'t Miss a Thing! Follow Us:', 'kioken-blocks') ?>
								</h4>
								<a href="https://twitter.com/kiokentheme" title="Kioken Theme Twitter Account" target="_blank" class="bt-kb-tw button button-large">
									<span class="dashicons dashicons-twitter"></span>
								</a>
								<a href="https://www.facebook.com/themekioken/" target="_blank" title="Kioken Theme Facebook Account" class="bt-kb-fb button button-large">
									<span class="dashicons dashicons-facebook-alt"></span>
								</a>
								<a href="https://www.youtube.com/channel/UC7XgiDYcF8RIpZtdcHBx96w" target="_blank" title="Kioken Theme Youtube Channel" class="bt-kb-yt button button-large">
									<span class="dashicons dashicons-video-alt3"></span>
								</a>
							<!-- <ul class="kb-options-features">
                  <li>- <?php esc_attr_e('Predefined block templates'); ?></li>
                  <li>- <?php esc_attr_e('Modern layout packs'); ?></li>
                  <li>- <?php esc_attr_e('Advanced layouts with the Row Layout'); ?></li>
                  <li>- <?php esc_attr_e('Row video background & blend mode'); ?></li>
                  <li>- <?php esc_attr_e('Custom post types query capable Kinetic Posts'); ?></li>
                  <li>- <?php esc_attr_e('Shape dividers'); ?></li>
                  <li>- <?php esc_attr_e('Device specific responsive controls'); ?></li>
                  <li>- <?php esc_attr_e('Google fonts for all core Gutenberg and Kioken Blocks'); ?></li>
                  <li>- <?php esc_attr_e('Classic & gradient color and background'); ?></li>
                  <li>- <?php esc_attr_e('Multi-purpose Kinetic Wrapper Block'); ?></li>
                  <li>- <?php esc_attr_e('Extensive block options'); ?></li>
                  <li>- <?php esc_attr_e('Thousands of solid and line icons'); ?></li>
                  <li>- <?php esc_attr_e('Slider Layouts for Posts and Testimonials'); ?></li>
              </ul> -->
							</div>
						</div><!-- end first col -->
						<div>
							<div class="kb-embed-responsive">
                  <iframe class="kb-embed-responsive-item" src="https://www.youtube.com/embed/Mi4oixuQ__Q" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
              </div>
						</div><!-- end second col -->
					</div><!-- end of section-header -->


				</div><!-- end main section -->

			</div>
		<?php
	}
	/**
	 * Get array of Kioken Blocks.
	 */
	public function blocks_array() {
		$blocks = array(
			'kioken/rowlayout'   => array(
				'slug'  => 'kioken/rowlayout',
				'name'  => __( 'Container Row', 'kioken-blocks' ),
				'desc'  => __( 'Create rows with nested blocks either in columns or as a container. Give style to your rows with background, overlay, padding, etc.', 'kioken-blocks' ),
				'image' => KK_BLOCKS_URL . 'dist/settings/img/rowlayout.jpg',
			),
			'kioken/icon'        => array(
				'slug'  => 'kioken/icon',
				'name'  => __( 'Icon', 'kioken-blocks' ),
				'desc'  => __( 'Choose from over 1500+ SVG Icons to add into your page and style the size, colors, background, border, etc.', 'kioken-blocks' ),
				'image' => KK_BLOCKS_URL . 'dist/settings/img/icon.jpg',
			),
			'kioken/advancedbtn' => array(
				'slug'  => 'kioken/advancedbtn',
				'name'  => __( 'Advanced Button', 'kioken-blocks' ),
				'desc'  => __( 'Create an advanced button or a row of buttons. Style each one including hover controls plus you can use an icon and display them side by side', 'kioken-blocks' ),
				'image' => KK_BLOCKS_URL . 'dist/settings/img/btn.jpg',
			),
			// 'kioken/spacer'      => array(
			// 	'slug'  => 'kioken/spacer',
			// 	'name'  => __( 'Spacer/Divider', 'kioken-blocks' ),
			// 	'desc'  => __( 'Easily create a divder and determine the space around it or just create some space in your content.', 'kioken-blocks' ),
			// 	'image' => KK_BLOCKS_URL . 'dist/settings/img/spacer.jpg',
			// ),
			// 'kioken/advancedheading'      => array(
			// 	'slug'  => 'kioken/advancedheading',
			// 	'name'  => __( 'Advanced Heading', 'kioken-blocks' ),
			// 	'desc'  => __( 'Transform your headings to Advanced Headings and customize the font family (even google fonts), color, and size.', 'kioken-blocks' ),
			// 	'image' => KK_BLOCKS_URL . 'dist/settings/img/heading.jpg',
			// ),
			// 'kioken/tabs'      => array(
			// 	'slug'  => 'kioken/tabs',
			// 	'name'  => __( 'Tabs', 'kioken-blocks' ),
			// 	'desc'  => __( 'Create custom vertical or horizontal tabs with advanced styling controls. Each tab content is an empty canvas able to contain any other blocks.', 'kioken-blocks' ),
			// 	'image' => KK_BLOCKS_URL . 'dist/settings/img/tabs.jpg',
			// ),
		);
		return apply_filters( 'kioken_blocks_enable_disable_array', $blocks );
	}
	/**
	 * Ajax Save blocks Config
	 */
	public function ajax_blocks_save_config() {
		if ( ! check_ajax_referer( 'kioken-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'kioken-blocks' ) );
		}
		if ( ! isset( $_POST['kt_block'] ) && ! isset( $_POST['config'] ) ) {
			return wp_send_json_error( __( 'Missing Content', 'kioken-blocks' ) );
		}
		// Get settings.
		$current_settings = get_option( 'kt_blocks_config_blocks' );
		$new_settings = $_POST['config'];
		if ( ! is_array( $new_settings ) ) {
			return wp_send_json_error(  __( 'Nothing to Save', 'kioken-blocks' ) );
		}
		foreach ( $new_settings as $block_key => $settings ) {
			foreach ( $settings as $attribute_key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $array_attribute_index => $array_value ) {
						if ( is_array( $array_value ) ) {
							foreach ( $array_value as $array_attribute_key => $array_attribute_value ) {
								$array_attribute_value = sanitize_text_field( $array_attribute_value );
								if ( is_numeric( $array_attribute_value ) ) {
									$array_attribute_value = floatval( $array_attribute_value );
								}
								if ( 'true' === $array_attribute_value ) {
									$array_attribute_value = true;
								}
								if ( 'false' === $array_attribute_value ) {
									$array_attribute_value = false;
								}
								$new_settings[ $block_key ][ $attribute_key ][ $array_attribute_index ][ $array_attribute_key ] = $array_attribute_value;
							}
						} else {
							$array_value = sanitize_text_field( $array_value );
							if ( is_numeric( $array_value ) ) {
								$array_value = floatval( $array_value );
							}
							$new_settings[ $block_key ][ $attribute_key ][ $array_attribute_index ] = $array_value;
						}
					}
				} else {
					$value = sanitize_text_field( $value );
					if ( is_numeric( $value ) ) {
						$value = floatval( $value );
					}
					if ( 'true' === $value ) {
						$value = true;
					}
					if ( 'false' === $value ) {
						$value = false;
					}
					$new_settings[ $block_key ][ $attribute_key ] = $value;
				}
			}
		}
		$block = sanitize_text_field( wp_unslash( $_POST['kt_block'] ) );

		if ( ! is_array( $current_settings ) ) {
			$current_settings = array();
		}
		$current_settings[ $block ] = $new_settings[ $block ];
		update_option( 'kt_blocks_config_blocks', $current_settings );
		return wp_send_json_success();
	}
	/**
	 * Ajax activate/deactivate blocks
	 */
	public function ajax_blocks_activate_deactivate() {
		if ( ! check_ajax_referer( 'kioken-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['kt_block'] ) ) {
			return wp_send_json_error();
		}
		// Get variables.
		$unregistered_blocks = get_option( 'kt_blocks_unregistered_blocks' );
		$block = sanitize_text_field( wp_unslash( $_POST['kt_block'] ) );

		if ( ! is_array( $unregistered_blocks ) ) {
			$unregistered_blocks = array();
		}

		// If current block is in the array - remove it.
		if ( in_array( $block, $unregistered_blocks ) ) {
			$index = array_search( $block, $unregistered_blocks );
			if ( false !== $index ) {
				unset( $unregistered_blocks[ $index ] );
			}
			// if current block is not in the array - add it.
		} else {
			array_push( $unregistered_blocks, $block );
		}

		update_option( 'kt_blocks_unregistered_blocks', $unregistered_blocks );
		return wp_send_json_success();
	}
	/**
	 * Ajax default posts width
	 */
	public function ajax_default_editor_width() {
		if ( ! check_ajax_referer( 'kioken-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['post_default'] ) ) {
			return wp_send_json_error();
		}
		// Get variables.
		$editor_widths = get_option( 'kk_blocks_editor_width' );
		$default = sanitize_text_field( wp_unslash( $_POST['post_default'] ) );

		if ( ! is_array( $editor_widths ) ) {
			$editor_widths = array();
		}
		$editor_widths['post_default'] = $default;

		update_option( 'kk_blocks_editor_width', $editor_widths );
		return wp_send_json_success();
	}
	/**
	 * Add settings link
	 *
	 * @param array $links plugin activate/deactivate links array.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=kioken_blocks' ) . '">' . __( 'Settings', 'kioken-blocks' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

}
Kioken_Blocks_Settings::get_instance();
