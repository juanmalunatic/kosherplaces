<?php
/**
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package Kioken Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to Enqueue CSS/JS of all the blocks.
 *
 * @category class
 */
class Kioken_Blocks_Frontend {

	/**
	 * Google fonts to gnqueue
	 *
	 * @var array
	 */
	public static $gfonts = array();

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
		add_action( 'enqueue_block_assets', array( $this, 'blocks_assets' ) );
		// add_action( 'wp_head', array( $this, 'kioken_frontend_style' ) );
		add_action( 'wp_head', array( $this, 'frontend_inline_css' ), 1000 );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 1001 );
	}
	/**
	 * Enqueue Gutenberg block assets
	 *
	 * @since 1.0.0
	 */
	public function kioken_frontend_style() {
		wp_enqueue_style( 'kioken-blocks-style', KK_BLOCKS_URL . 'dist/blocks.style.build.css', array(), KK_BLOCKS_VERSION );
	}
	public function blocks_assets() {
		// If in the backend, bail out.
		if ( is_admin() ) {
			return;
		}

		global $is_IE, $is_safari;


		wp_enqueue_style( 'kioken-blocks-style', KK_BLOCKS_URL . 'dist/blocks.style.build.css', array(), KK_BLOCKS_VERSION );
		wp_enqueue_script( 'jquery-ui-datepicker' );

		//swiper

		if ( ! wp_script_is('swiper-js') ) {
			wp_enqueue_script( 'swiper-js', KK_BLOCKS_URL . 'dist/vendor/swiper.min.js', array(), '4.5.0', true );
		}

		if ( $is_IE || $is_safari ) {
		//object fit js - enables object fit css property work on IE
			wp_enqueue_script( 'object-fit-images', KK_BLOCKS_URL . 'dist/vendor/ofi.min.js', array(), '3.2.4', true );
		}

		//tilt js
		if ( has_block( 'kioken/imagebox' ) || has_block( 'kioken/feature' ) ) {
			wp_enqueue_script( 'vanillatilt-js', KK_BLOCKS_URL . 'dist/vendor/vanillatilt.min.js', array('jquery'), '1.1.19', true );
		}


		//anime.js
		wp_enqueue_script( 'anime-js', KK_BLOCKS_URL . 'dist/vendor/anime.min.js', array(), '3.1.0', true );
		//scrollmagic
		wp_enqueue_script( 'scrollmagic-js', KK_BLOCKS_URL . 'dist/vendor/scrollmagic/ScrollMagic.min.js', array('anime-js'), '2.0.7', true );

		//anime scrollmagic plugin
		wp_enqueue_script( 'anime-scrollmagic-js', KK_BLOCKS_URL . 'dist/vendor/animation.anime.min.js', array('anime-js', 'scrollmagic-js'), '1.0.0', true );



		// scrollmagic debug
		// wp_enqueue_script( 'scrollmagic-js-debug', KK_BLOCKS_URL . 'dist/vendor/scrollmagic/debug.addIndicators.min.js', array(), '2.0.7', true );

		wp_enqueue_script( 'kioken-blocks-frontend-utils',
		KK_BLOCKS_URL . 'dist/js/kiokenblocks-min.js', array('jquery', 'jarallax', 'jarallax-video', 'resize-observer-polyfill', 'anime-scrollmagic-js', 'anime-js'), KK_BLOCKS_VERSION, true );

	}
	/**
	 * Hex to RGBA
	 *
	 * @param string $hex string hex code.
	 * @param number $alpha alpha number.
	 */
	public function hex2rgba( $hex, $alpha ) {
		$hex = str_replace( '#', '', $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgba = 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
		return $rgba;
	}
	public function frontend_gfonts() {
		if ( empty( self::$gfonts ) ) {
			return;
		}
		$print_google_fonts = apply_filters( 'kioken_blocks_print_google_fonts', true );
		if ( ! $print_google_fonts ) {
			return;
		}
		$link    = '';
		$subsets = array();
		foreach ( self::$gfonts as $key => $gfont_values ) {
			if ( ! empty( $link ) ) {
				$link .= '%7C'; // Append a new font to the string.
			}
			$link .= $gfont_values['fontfamily'];
			if ( ! empty( $gfont_values['fontvariants'] ) ) {
				$link .= ':';
				$link .= implode( ',', $gfont_values['fontvariants'] );
			}
			if ( ! empty( $gfont_values['fontsubsets'] ) ) {
				foreach ( $gfont_values['fontsubsets'] as $subset ) {
					if ( ! in_array( $subset, $subsets ) ) {
						array_push( $subsets, $subset );
					}
				}
			}
		}
		if ( ! empty( $subsets ) ) {
			$link .= '&amp;subset=' . implode( ',', $subsets );
		}
		wp_enqueue_style( 'kiokenblocks-inner-gfonts', '//fonts.googleapis.com/css?family=' . esc_attr( str_replace( '|', '%7C', $link ) ), array(), null );

	}
	/**
	 * Gets the parsed blocks, need to use this becuase wordpress 5 doesn't seem to include gutenberg_parse_blocks
	 */
	 public function kioken_parse_blocks( $content ) {

 		if ( ! function_exists( 'parse_blocks' ) ) {
 				return gutenberg_parse_blocks( $content );
 			} else {
 				return parse_blocks( $content );
 			}
 	}
	/**
	 * Outputs extra css for blocks.
	 */
	public function frontend_inline_css() {

		if ( function_exists( 'has_blocks' ) && has_blocks( get_the_ID() ) ) {
			global $post;
			if ( ! is_object( $post ) ) {
				return;
			}
			$blocks = $this->kioken_parse_blocks( $post->post_content );

			if ( ! is_array( $blocks ) || empty( $blocks ) ) {
				return;
			}
			$css  = '<style type="text/css" media="all" id="kioken-blocks-frontend">';


			foreach ( $blocks as $indexkey => $block ) {

				if ( ! is_object( $block ) && is_array( $block ) && isset( $block['blockName'] ) ) {
					if ( 'kioken/rowlayout' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Row/Layout.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->row_layout_array_css( $blockattr, $unique_id );
								if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
									$css .= $this->column_layout_cycle( $block['innerBlocks'], $unique_id );
								}
							}
						}
					}
					if ( 'kioken/imagebox' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_imagebox_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/visual-list' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_iconlist_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/kinetic-posts' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_posts_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/videobox' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_videobox_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/testimonials-carousel' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_testimonials_carousel_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/wrapper' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_wrapper_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/splitheading' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_splitheading_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/features' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_features_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/feature' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_single_feature_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/tabs' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_tabs_array_css( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/opentable' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID'] ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr['uniqueID'];
								$css .= $this->blocks_kioken_opentable_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/advancedbtn' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['uniqueID']  ) ) {
								// Create CSS for Advanced Button.
								$unique_id = $blockattr['uniqueID'] ;
								$css .= $this->blocks_advanced_btn_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'core/block' === $block['blockName'] ) {
						if ( isset( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
							$blockattr = $block['attrs'];
							if ( isset( $blockattr['ref']  ) ) {
								$reusable_block = get_post( $blockattr['ref'] );
								if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
									$reuse_data_block = $this->kioken_parse_blocks( $reusable_block->post_content );
									$css .= $this->blocks_cycle_through( $reuse_data_block );
								}
							}
						}
					}
					if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
						$css .= $this->blocks_cycle_through( $block['innerBlocks'] );
					}
				}
				if ( is_object( $block ) && isset( $block->blockName ) ) {
					if ( 'kioken/rowlayout' === $block->blockName ) {
						if ( isset( $block->attrs ) && is_object( $block->attrs ) ) {
							$blockattr = $block->attrs;
							if ( isset( $blockattr->uniqueID ) ) {
								// Create CSS for Row/Layout.
								$unique_id = $blockattr->uniqueID;
								$css .= $this->row_layout_css( $blockattr, $unique_id );
								if ( isset( $block->innerBlocks ) && ! empty( $block->innerBlocks ) && is_array( $block->innerBlocks ) ) {
									$css .= $this->column_layout_cycle( $block->innerBlocks , $unique_id );
								}
							}
						}
					}
					if ( 'kioken/imagebox' === $block->blockName ) {
						if ( isset( $block->attrs ) && is_object( $block->attrs ) ) {
							$blockattr = $block->attrs;
							if ( isset( $blockattr->uniqueID ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr->uniqueID;
								$css .= $this->blocks_kioken_imagebox( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/wrapper' === $block->blockName ) {
						if ( isset( $block->attrs ) && is_object( $block->attrs ) ) {
							$blockattr = $block->attrs;
							if ( isset( $blockattr->uniqueID ) ) {
								// Create CSS for Advanced Heading.
								$unique_id = $blockattr->uniqueID;
								$css .= $this->blocks_kioken_wrapper_array( $blockattr, $unique_id );
							}
						}
					}
					if ( 'kioken/advancedbtn' === $block->blockName ) {
						if ( isset( $block->attrs ) && is_object( $block->attrs ) ) {
							$blockattr = $block->attrs;
							if ( isset( $blockattr->uniqueID ) ) {
								// Create CSS for Advanced Button.
								$unique_id = $blockattr->uniqueID;
								$css .= $this->blocks_advanced_btn( $blockattr, $unique_id );
							}
						}
					}
					if ( isset( $block->innerBlocks ) && ! empty( $block->innerBlocks ) && is_array( $block->innerBlocks ) ) {
						$css .= $this->blocks_cycle_through( $block->innerBlocks );
					}
				}
			}
			$css .= '</style>';
			echo $css;
		}
	}

	/**
	 * Builds css for inner columns
	 *
	 * @param object/array $inner_blocks array of inner blocks.
	 */
	public function column_layout_cycle( $inner_blocks, $unique_id ) {
		$css = '';
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			if ( is_object( $inner_block ) ) {
				if ( isset( $inner_block->blockName ) ) {
					if ( 'kioken/column' === $inner_block->blockName ) {
						if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
							$blockattr = $inner_block->attrs;
							$csskey = $in_indexkey + 1;
							$css .= $this->column_layout_css( $blockattr, $unique_id, $csskey );
						} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
							$blockattr = $inner_block->attrs;
							$csskey = $in_indexkey + 1;
							$css .= $this->column_layout_array_css( $blockattr, $unique_id, $csskey );
						}
					}
				}
			} elseif ( is_array( $inner_block ) ) {
				if ( isset( $inner_block['blockName'] ) ) {
					if ( 'kioken/column' === $inner_block['blockName'] ) {
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
							$blockattr = $inner_block['attrs'];
							$csskey = $in_indexkey + 1;
							$css .= $this->column_layout_array_css( $blockattr, $unique_id, $csskey );
						}
					}
				}
			}
		}
		return $css;
	}
	/**
	 * Builds css for inner blocks
	 *
	 * @param array $inner_blocks array of inner blocks.
	 */
	function blocks_cycle_through( $inner_blocks ) {
		$css = '';
		foreach ( $inner_blocks as $in_indexkey => $inner_block ) {
			if ( ! is_object( $inner_block ) && is_array( $inner_block ) && isset( $inner_block['blockName'] ) ) {
				if ( 'kioken/rowlayout' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Row/Layout.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->row_layout_array_css( $blockattr, $unique_id );
							if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
								$css .= $this->column_layout_cycle( $inner_block['innerBlocks'], $unique_id );
							}
						}
					}
				}
				if ( 'kioken/imagebox' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_imagebox_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/visual-list' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_iconlist_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/kinetic-posts' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_posts_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/videobox' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_videobox_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/testimonials-carousel' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_testimonials_carousel_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/wrapper' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Wrapper
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_wrapper_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/splitheading' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_splitheading_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/features' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_features_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/feature' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_single_feature_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/tabs' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_tabs_array_css( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/opentable' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_opentable_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/advancedbtn' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Button.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_advanced_btn_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'core/block' === $inner_block['blockName'] ) {
					if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
						$blockattr = $inner_block['attrs'];
						if ( isset( $blockattr['ref']  ) ) {
							$reusable_block = get_post( $blockattr['ref'] );
							if ( $reusable_block && 'wp_block' == $reusable_block->post_type ) {
								$reuse_data_block = $this->kioken_parse_blocks( $reusable_block->post_content );
								$css .= $this->blocks_cycle_through( $reuse_data_block );
							}
						}
					}
				}
				if ( isset( $inner_block['innerBlocks'] ) && ! empty( $inner_block['innerBlocks'] ) && is_array( $inner_block['innerBlocks'] ) ) {
					$css .= $this->blocks_cycle_through( $inner_block['innerBlocks'] );
				}
			} elseif ( is_object( $inner_block ) && isset( $inner_block->blockName ) ) {
				if ( 'kioken/rowlayout' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Row/Layout.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->row_layout_css( $blockattr, $unique_id );
							if ( isset( $inner_block->innerBlocks ) && ! empty( $inner_block->innerBlocks ) && is_array( $inner_block->innerBlocks ) ) {
								$css .= $this->column_layout_cycle( $inner_block->innerBlocks , $unique_id );
							}
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Row/Layout.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->row_layout_array_css( $blockattr, $unique_id );
							if ( isset( $inner_block->innerBlocks ) && ! empty( $inner_block->innerBlocks ) && is_array( $inner_block->innerBlocks ) ) {
								$css .= $this->column_layout_cycle( $inner_block->innerBlocks , $unique_id );
							}
						}
					}
				}
				if ( 'kioken/imagebox' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_kioken_imagebox( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_imagebox_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/testimonials-carousel' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_testimonials_carousel_array( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_testimonials_carousel_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/opentable' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr->uniqueID;
							$css .= '';
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_opentable_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/splitheading' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_kioken_splitheading( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_splitheading_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/features' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_kioken_features_array( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_features_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/feature' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_kioken_splitheading( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Split Heading.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_single_feature_array( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/tabs' === $inner_block->blockName ) {
					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Tabs
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_kioken_tabs_array_css( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Tabs
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_kioken_tabs_array_css( $blockattr, $unique_id );
						}
					}
				}
				if ( 'kioken/advancedbtn' === $inner_block->blockName ) {

					if ( isset( $inner_block->attrs ) && is_object( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr->uniqueID ) ) {
							// Create CSS for Advanced Button.
							$unique_id = $blockattr->uniqueID;
							$css .= $this->blocks_advanced_btn( $blockattr, $unique_id );
						}
					} elseif ( isset( $inner_block->attrs ) && is_array( $inner_block->attrs ) ) {
						$blockattr = $inner_block->attrs;
						if ( isset( $blockattr['uniqueID'] ) ) {
							// Create CSS for Advanced Button.
							$unique_id = $blockattr['uniqueID'];
							$css .= $this->blocks_advanced_btn_array( $blockattr, $unique_id );
						}
					}
				}
				if ( isset( $inner_block->innerBlocks ) && ! empty( $inner_block->innerBlocks ) && is_array( $inner_block->innerBlocks ) ) {
					$css .= $this->blocks_cycle_through( $inner_block->innerBlocks );
				}
			}
		}
		return $css;
	}
	/**
	 * Builds CSS for Tabs block.
	 *
	 * @param object $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */

	public function kbArrayNumCheck($attr) {

		$value = isset( $attr ) ? $attr : '0';

		return $value;
	}

	/**
	 * Builds CSS for Advanced Heading block.
	 *
	 * @param object  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_kioken_imagebox( $attr, $unique_id ) {
		$css = '';
		// if ( isset( $attr->hoverOpacity ) ) {
		// 	$css .= '#kt-imagebox_' . $unique_id . ':hover img {';
		// 		$css .= 'opacity:' . $attr->hoverOpacity . ' !important;';
		// 	$css .= '}';
		// }

		return $css;
	}
	public function blocks_kioken_imagebox_array( $attr, $unique_id ) {
		$css = '';
		//img
		if ( isset( $attr['opacity'] ) ) {
			$css .= '.kt-imagebox_' . $unique_id . ' img {';
				$css .= 'opacity:' . $attr['opacity'] . ';';
			$css .= '}';
		}

		//colors for default
		if ( isset( $attr['customTextColor'] ) || isset( $attr['customBackgroundColor'] ) ) {
			$css .= '.kt-imagebox_' . $unique_id . ' {';
				isset( $attr['customTextColor'] ) ? $css .= 'color:' . $attr['customTextColor'] . ';' : '';
				isset( $attr['customBackgroundColor'] ) ? $css .= 'background-color:' . $attr['customBackgroundColor'] . ';' : '';
			$css .= '}';
		}

		//scales
		if ( isset( $attr['imgScale']) && isset( $attr['addAnim'] ) ) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition img {';
				$css .= 'transform:scale3d(' . $attr['imgScale'] . ', ' . $attr['imgScale'] . ', 1);';
			$css .= '}';
		}

		if ( (isset($attr['hoverOpacity']) && isset($attr['addAnim'])) || (isset($attr['imgScaleOnHover']) && isset($attr['addAnim'])) ) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover img {';
				isset( $attr['hoverOpacity'] ) ? $css .= 'opacity:' . $attr['hoverOpacity'] . ' !important;' : '';
				isset( $attr['imgScaleOnHover'] ) ? $css .= 'transform:scale3d(' . $attr['imgScaleOnHover'] . ', ' . $attr['imgScaleOnHover'] . ', ' . $attr['imgScaleOnHover'] . ') ;' : '';
			$css .= '}';
		}

		if ( isset( $attr['contentMaxW']) ) {
			$css .= '@media (min-width:768px) {';
				$css .= '.kt-imagebox_' . $unique_id . ' .the-content {';
					 isset( $attr['contentMaxW']) ? $css .= 'max-width:' . $attr['contentMaxW'] . '% ;' : '';
				$css .= '}';
			$css .= '}';
		}

		//colors on hover
		if ( isset($attr['addAnim']) && isset($attr['txtColorOnHover'])) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover {';
				$css .= 'color:' . $attr['txtColorOnHover'] . ' !important;';
			$css .= '}';
		}
		if ( isset($attr['addAnim']) && isset($attr['bgColorOnHover'])) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover {';
				$css .= 'background-color:' . $attr['bgColorOnHover'] . ' !important;';
			$css .= '}';
		}

		if ( isset($attr['addAnim']) && isset($attr['shadeColor'])) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition .imgwrap:before {';
				$css .= 'background-image: linear-gradient(180deg, transparent,' . $attr['shadeColor'] . ');';
			$css .= '}';
		}
		// if ( isset($attr['addAnim']) && isset($attr['shadeColorOnHover'])) {
		// 	$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover .imgwrap:before {';
		// 		$css .= 'background-image: linear-gradient(180deg, transparent,' . $attr['shadeColorOnHover'] . ');';
		// 	$css .= '}';
		// }

		if ( isset( $attr['imgWrapScaleOnHover']) && isset( $attr['addAnim'] ) ) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover .imgwrap {';
				$css .= 'transform:scale3d(' . $attr['imgWrapScaleOnHover'] . ', ' . $attr['imgWrapScaleOnHover'] . ', ' . $attr['imgWrapScaleOnHover'] . ');';
			$css .= '}';
		}

		if ( isset($attr['addAnim']) && isset($attr['descHeight'])) {
			$css .= '.kt-imagebox_' . $unique_id . '.has-transition:hover .wp-block-kioken-imagebox__masked {';
				$css .= 'max-height:' . $attr['descHeight'] . 'px;';
			$css .= '}';
		}


		if ( isset( $attr['typoHeading'] ) ) {
			$css .= '.kt-imagebox_' . $unique_id . ' .wp-block-kioken-imagebox__heading {';
			if ( isset( $attr['typoHeading'] ) && ! empty( $attr['typoHeading'] ) ) {
				$css .= 'font-family:' . $attr['typoHeading'] . ';';
			}
			if ( isset( $attr['fontWeight'] ) && ! empty( $attr['fontWeight'] ) ) {
				$css .= 'font-weight:' . $attr['fontWeight'] . ';';
			}
			if ( isset( $attr['fontStyle'] ) && ! empty( $attr['fontStyle'] ) ) {
				$css .= 'font-style:' . $attr['fontStyle'] . ';';
			}
			$css .= '}';
		}

		//google font load frontend
		if ( isset( $attr['googleFontH'] ) && $attr['googleFontH'] && ( ! isset( $attr['loadGoogleFontH'] ) || $attr['loadGoogleFontH'] == true ) && isset( $attr['typoHeading'] ) ) {
			// Check if the font has been added yet
			if ( ! array_key_exists( $attr['typoHeading'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $attr['typoHeading'],
					'fontvariants' => ( isset( $attr['fontVariantH'] ) && ! empty( $attr['fontVariantH'] ) ? array( $attr['fontVariantH'] ) : array() ),
					'fontsubsets' => ( isset( $attr['fontSubsetH'] ) && !empty( $attr['fontSubsetH'] ) ? array( $attr['fontSubsetH'] ) : array() ),
				);
				self::$gfonts[ $attr['typoHeading'] ] = $add_font;
			} else {
				if( isset( $attr['fontVariantH'] ) && ! empty( $attr['fontVariantH'] ) ) {
					if ( ! in_array( $attr['fontVariantH'], self::$gfonts[ $attr['typoHeading'] ]['fontvariants'], true ) ) {
						array_push( self::$gfonts[ $attr['typoHeading'] ]['fontvariants'], $attr['fontVariantH'] );
					}
				}
				if( isset( $attr['fontSubsetH'] ) && ! empty( $attr['fontSubsetH'] ) ) {
					if ( ! in_array( $attr['fontSubsetH'], self::$gfonts[ $attr['typoHeading'] ]['fontsubsets'], true ) ) {
						array_push( self::$gfonts[ $attr['typoHeading'] ]['fontsubsets'], $attr['fontSubsetH'] );
					}
				}
			}
		}

		return $css;
	}
	public function blocks_testimonials_carousel_array( $attr, $unique_id ) {
		$css = '';

		if ( isset( $attr['fadeGradientColor'] ) ) {
			$css .= '.kt-testimonial-carousel_' . $unique_id . '.fade-deactive .swiper-container:before, .kt-testimonial-carousel_' . $unique_id . '.fade-deactive .swiper-container:after {';
				$css .= 'background: linear-gradient(90deg, '.$attr['fadeGradientColor'].' 0%, '.$this->hex2rgba($attr['fadeGradientColor'], 0).' 100%) !important;';
			$css .= '}';
		}
		//img
		if ( isset( $attr['avatarW'] ) && isset( $attr['textAlign'] ) && 'left' === $attr['textAlign'] ) {
			$css .= '.kt-testimonial-carousel_' . $unique_id . '.alignment-left .inner_wrap .testimonial_avatar {';
				$css .= 'flex: 0 0 '.$attr['avatarW'].'px;';
			$css .= '}';
		}



		return $css;
	}
	public function blocks_kioken_splitheading_array( $attr, $unique_id ) {
		$css = '';
		if ( isset($attr['curtainBeforeColor'])) {
			$css .= '#kt-split-heading_' . $unique_id . '.has-transition .heading-line >*::before {';
				$css .= 'background-color:' . $attr['curtainBeforeColor'] . ';';
			$css .= '}';
		}
		if ( isset($attr['curtainAfterColor'])) {
			$css .= '#kt-split-heading_' . $unique_id . '.has-transition .heading-line >*::after {';
				$css .= 'background-color:' . $attr['curtainAfterColor'] . ';';
			$css .= '}';
		}

		return $css;
	}

	public function blocks_kioken_features_array( $attr, $unique_id ) {
		$css = '';

		$paddingUnit 	= isset( $attr['paddingUnit']) ? $attr['paddingUnit'] : 'px';
		$marginUnit 	= isset( $attr['marginUnit']) ? $attr['marginUnit'] : 'px';

		//padding default settings
		if ( isset( $attr['paddingTop'] ) || isset( $attr['paddingRight'] ) || isset( $attr['paddingBottom'] ) || isset( $attr['paddingLeft'] ) || isset( $attr['marginTop'] ) || isset( $attr['marginRight'] ) || isset( $attr['marginBottom'] ) || isset( $attr['marginLeft'] ) )  {

			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
					//padding values
					if ( isset( $attr['paddingTop'] ) ) {
						$css.= $attr['paddingTop']!=='' ? 'padding-top:'.$attr['paddingTop'].$paddingUnit.'; ' : '';
					}
					if ( isset( $attr['paddingRight']) ) {
						$css.= $attr['paddingRight']!=='' ? 'padding-right:'.$attr['paddingRight'].$paddingUnit.'; ' : '';
					}
					if ( isset( $attr['paddingBottom']) ) {
						$css.= $attr['paddingBottom']!=='' ? 'padding-bottom:'.$attr['paddingBottom'].$paddingUnit.'; ' : '';
					}
					if ( isset( $attr['paddingLeft']) ) {
						$css.= $attr['paddingLeft']!=='' ? 'padding-left:'.$attr['paddingLeft'].$paddingUnit.'; ' : '';
					}
					//margin values
					if ( isset( $attr['marginTop']) ) {
						$css.= $attr['marginTop']!=='' ? 'margin-top:'.$attr['marginTop'].$marginUnit.'; ' : '';
					}
					if ( isset( $attr['marginRight']) ) {
						$css.= $attr['marginRight']!=='' ? 'margin-right:'.$attr['marginRight'].$marginUnit.'; ' : '';
					}
					if ( isset( $attr['marginBottom']) ) {
						$css.= $attr['marginBottom']!=='' ? 'margin-bottom:'.$attr['marginBottom'].$marginUnit.'; ' : '';
					}
					if ( isset( $attr['marginLeft']) ) {
						$css.= $attr['marginLeft']!=='' ? 'margin-left:'.$attr['marginLeft'].$marginUnit.'; ' : '';
					}
			$css.='}';
		}

		//padding tablet settings
		if ( isset( $attr['paddingTopTablet'] ) || isset( $attr['paddingRightTablet'] ) || isset( $attr['paddingBottomTablet'] ) || isset( $attr['paddingLeftTablet'] ) || isset( $attr['marginTopTablet'] ) || isset( $attr['marginRightTablet'] ) || isset( $attr['marginBottomTablet'] ) || isset( $attr['marginLeftTablet'] ) ) {

			$css.='@media (max-width: 768px) {';

				$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
					//padding values
					if ( isset( $attr['paddingTopTablet'] ) ) {
						$css.='padding-top:'.$attr['paddingTopTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingRightTablet']) ) {
						$css.='padding-right:'.$attr['paddingRightTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingBottomTablet']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingLeftTablet']) ) {
						$css.='padding-left:'.$attr['paddingLeftTablet'].$paddingUnit.'; ';
					}

					//margin values
					if ( isset( $attr['marginTopTablet']) ) {
						$css.='margin-top:'.$attr['marginTopTablet'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginRightTablet']) ) {
						$css.='margin-right:'.$attr['marginRightTablet'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginBottomTablet']) ) {
						$css.='margin-bottom:'.$attr['marginBottomTablet'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginLeftTablet']) ) {
						$css.='margin-left:'.$attr['marginLeftTablet'].$marginUnit.'; ';
					}

					$css.='}';
			$css.='}';
		}

		//mobile settings
		if ( isset( $attr['paddingTopMobile'] ) || isset( $attr['paddingRightMobile'] ) || isset( $attr['paddingBottomMobile'] ) || isset( $attr['paddingLeftMobile'] ) || isset( $attr['marginTopMobile'] ) || isset( $attr['marginRightMobile'] ) || isset( $attr['marginBottomMobile'] ) || isset( $attr['marginLeftMobile'] ) ) {
			$css.='@media (max-width: 576px) {';
				$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
					//padding values
					if ( isset( $attr['paddingTopMobile']) ) {
						$css.='padding-top:'.$attr['paddingTopMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingRightMobile']) ) {
						$css.='padding-right:'.$attr['paddingRightMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingBottomMobile']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingLeftMobile']) ) {
						$css.='padding-left:'.$attr['paddingLeftMobile'].$paddingUnit.' !important; ';
					}

					//margin values
					if ( isset( $attr['marginTopMobile']) ) {
						$css.='margin-top:'.$attr['marginTopMobile'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginRightMobile']) ) {
						$css.='margin-right:'.$attr['marginRightMobile'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginBottomMobile']) ) {
						$css.='margin-bottom:'.$attr['marginBottomMobile'].$marginUnit.'; ';
					}
					if ( isset( $attr['marginLeftMobile']) ) {
						$css.='margin-left:'.$attr['marginLeftMobile'].$marginUnit.'; ';
					}

					$css.='}';
			$css.='}';
		}

		//shadows
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][ 0 ] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
					$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['color'] . ';';
				$css .= '}';
			}
			if ( isset( $attr['shadowHover'] ) && is_array( $attr['shadowHover'] ) && is_array( $attr['shadowHover'][ 0 ] ) ) {
				$shadow_hover = $attr['shadowHover'][ 0 ];
				$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner:hover {';
					$css .= 'box-shadow:' . $shadow_hover['hOffset'] . 'px ' . $shadow_hover['vOffset'] . 'px ' . $shadow_hover['blur'] . 'px ' . $shadow_hover['spread'] . 'px ' . $shadow_hover['color'] . ';';
				$css .= '}';
			}
		}

		//border
		if ( isset($attr['borderControl']) || isset($attr['borderWidth'][0]) ) {
			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
				$css .= isset($attr['borderWidth'][0]) ? 'border-width:'.$attr['borderWidth'][0].'px;' : '';
				$css .= isset($attr['borderControl']) && $attr['borderControl']==='individual' ? 'border-width:'.$attr['borderWidth'][0].'px '.$attr['borderWidth'][1].'px '.$attr['borderWidth'][2].'px '.$attr['borderWidth'][3].';' : '';
				$css .= isset($attr['borderWidth'][0]) ? 'border-style:solid;' : '';
				$css .= isset($attr['borderColor']) ? 'border-color:'.$attr['borderColor'].';' : '';
			$css .= '}';
		}

		//border radius
		if ( isset($attr['borderRadiusControl']) || isset($attr['borderRadius'][0]) ) {
			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
				$css .= isset($attr['borderRadius'][0]) ? 'border-radius:'.$attr['borderRadius'][0].'px;' : '';
				$css .= isset($attr['borderRadiusControl']) && $attr['borderRadiusControl']==='individual' ? 'border-radius:'.$attr['borderRadius'][0].'px '.$attr['borderRadius'][1].'px '.$attr['borderRadius'][2].'px '.$attr['borderRadius'][3].'px;' : '';
			$css .= '}';
		}

		//gradient bg
		if ( isset( $attr['backgroundType'] ) && $attr['backgroundType'] === 'gradient' && isset($attr['gradientBG']) ) {
			$gradient = $attr['gradientBG'][ 0 ];
			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner.has-gradient-bg:before {';
			$css .= 'background-image:linear-gradient('.$gradient['angle'].'deg,'.$gradient['colorFirst'].' '.$gradient['locFirst'].'%,'.$gradient['colorSecond'].' '.$gradient['locSecond'].'%) !important;';
			$css .= '}';
		}

		//background and text color
		if ( isset($attr['customBackgroundColor']) || isset($attr['customTextColor']) || isset($attr['contentMaxW']) ) {
			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner {';
				$css .= isset($attr['customBackgroundColor']) ? 'background-color:'.$attr['customBackgroundColor'].';' : '';
				$css .= isset($attr['customTextColor']) ? 'color:'.$attr['customTextColor'].';' : '';
				$css .= isset($attr['contentMaxW']) ? 'max-width:'.$attr['contentMaxW'].'px;' : '';
			$css .= '}';
		}

		//hover colors and stuff
		if ( isset($attr['hoverBGColor']) || isset($attr['hoverTextColor']) || isset($attr['hoverBorderColor']) ) {
			$css .= '.kioken-features-' . $unique_id . ' .wp-block-kioken-features__inner:hover {';
				$css .= isset($attr['hoverBGColor']) ? 'background-color:'.$attr['hoverBGColor'].';' : '';
				$css .= isset($attr['hoverTextColor']) ? 'color:'.$attr['hoverTextColor'].';' : '';
				$css .= isset($attr['hoverBorderColor']) ? 'border-color:'.$attr['hoverBorderColor'].';' : '';
			$css .= '}';
		}

		return $css;
	}
	public function blocks_kioken_single_feature_array( $attr, $unique_id ) {
		$css = '';

		$paddingUnit 	= isset( $attr['paddingUnit']) ? $attr['paddingUnit'] : 'px';
		$marginUnit 	= isset( $attr['marginUnit']) ? $attr['marginUnit'] : 'px';

		//padding default settings
		if ( isset($attr['paddingSize']) && $attr['paddingSize']==='advanced' && (isset( $attr['paddingTop'] ) || isset( $attr['paddingRight'] ) || isset( $attr['paddingBottom'] ) || isset( $attr['paddingLeft'] )) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
					//padding values
					if ( isset( $attr['paddingTop'] ) ) {
						$css.='padding-top:'.$attr['paddingTop'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingRight']) ) {
						$css.='padding-right:'.$attr['paddingRight'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingBottom']) ) {
						$css.='padding-bottom:'.$attr['paddingBottom'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingLeft']) ) {
						$css.='padding-left:'.$attr['paddingLeft'].$paddingUnit.'; ';
					}
			$css.='}';
		}

		//padding tablet settings
		if ( isset( $attr['paddingTopTablet'] ) || isset( $attr['paddingRightTablet'] ) || isset( $attr['paddingBottomTablet'] ) || isset( $attr['paddingLeftTablet'] ) ) {

			$css.='@media (max-width: 768px) {';

				$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
					//padding values
					if ( isset( $attr['paddingTopTablet'] ) ) {
						$css.='padding-top:'.$attr['paddingTopTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingRightTablet']) ) {
						$css.='padding-right:'.$attr['paddingRightTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingBottomTablet']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomTablet'].$paddingUnit.'; ';
					}
					if ( isset( $attr['paddingLeftTablet']) ) {
						$css.='padding-left:'.$attr['paddingLeftTablet'].$paddingUnit.'; ';
					}

					$css.='}';
			$css.='}';
		}

		//mobile settings
		if ( isset( $attr['paddingTopMobile'] ) || isset( $attr['paddingRightMobile'] ) || isset( $attr['paddingBottomMobile'] ) || isset( $attr['paddingLeftMobile'] ) ) {
			$css.='@media (max-width: 576px) {';
				$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
					//padding values
					if ( isset( $attr['paddingTopMobile']) ) {
						$css.='padding-top:'.$attr['paddingTopMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingRightMobile']) ) {
						$css.='padding-right:'.$attr['paddingRightMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingBottomMobile']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingLeftMobile']) ) {
						$css.='padding-left:'.$attr['paddingLeftMobile'].$paddingUnit.' !important; ';
					}

					$css.='}';
			$css.='}';
		}


		//shadows
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][ 0 ] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
					$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['color'] . ';';
				$css .= '}';
			}
			if ( isset( $attr['shadowHover'] ) && is_array( $attr['shadowHover'] ) && is_array( $attr['shadowHover'][ 0 ] ) ) {
				$shadow_hover = $attr['shadowHover'][ 0 ];
				$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover {';
					$css .= 'box-shadow:' . $shadow_hover['hOffset'] . 'px ' . $shadow_hover['vOffset'] . 'px ' . $shadow_hover['blur'] . 'px ' . $shadow_hover['spread'] . 'px ' . $shadow_hover['color'] . ';';
				$css .= '}';
			}
		}

		//border, radius, max width
		if ( isset($attr['borderControl']) || isset($attr['borderWidth'][0]) || isset($attr['contentMaxW']) || isset($attr['borderRadiusControl']) || isset($attr['borderRadius'][0]) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
				$css .= isset($attr['borderWidth'][0]) ? 'border-width:'.$attr['borderWidth'][0].'px;' : '';
				$css .= isset($attr['borderControl']) && $attr['borderControl']==='individual' ? 'border-width:'.$attr['borderWidth'][0].'px '.$attr['borderWidth'][1].'px '.$attr['borderWidth'][2].'px '.$attr['borderWidth'][3].'px;' : '';
				$css .= isset($attr['borderWidth'][0]) ? 'border-style:solid;' : '';
				$css .= isset($attr['borderColor']) ? 'border-color:'.$attr['borderColor'].';' : '';

				$css .= isset($attr['borderRadius'][0]) ? 'border-radius:'.$attr['borderRadius'][0].'px;' : '';
				$css .= isset($attr['borderRadiusControl']) && $attr['borderRadiusControl']==='individual' ? 'border-radius:'.$attr['borderRadius'][0].'px '.$attr['borderRadius'][1].'px '.$attr['borderRadius'][2].'px '.$attr['borderRadius'][3].'px;' : '';

				$css .= isset($attr['contentMaxW']) ? 'max-width:'.$attr['contentMaxW'].'px;' : '';

			$css .= '}';
		}


		//gradient bg
		if ( isset( $attr['backgroundType'] ) && $attr['backgroundType'] === 'gradient' && isset($attr['gradientBG']) ) {
			$gradient = $attr['gradientBG'][ 0 ];
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner.has-gradient-bg:before {';
			$css .= 'background-image:linear-gradient('.$gradient['angle'].'deg,'.$gradient['colorFirst'].' '.$gradient['locFirst'].'%,'.$gradient['colorSecond'].' '.$gradient['locSecond'].'%) !important;';
			$css .= '}';
		}

		//background and text color
		if ( isset($attr['customBackgroundColor']) || isset($attr['customTextColor']) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner {';
				$css .= isset($attr['customBackgroundColor']) ? 'background-color:'.$attr['customBackgroundColor'].';' : '';
				$css .= isset($attr['customTextColor']) ? 'color:'.$attr['customTextColor'].';' : '';
			$css .= '}';
		}

		//custom icon color for both normal and hover
		if ( isset($attr['iconColor']) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner .kt-svg-icon {';
				$css .= 'color:'.$attr['iconColor'].' !important;';
			$css .= '}';
		}
		if ( isset($attr['iconHoverColor']) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover .kt-svg-icon {';
				$css .= 'color:'.$attr['iconHoverColor'].' !important;';
			$css .= '}';
		}

		//custom headings color for both normal and hover
		if ( isset($attr['headingsColor']) ) {
			$css .= '	.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h1,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h2,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h3,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h4,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h5,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner h6 {';
				$css .= 'color:'.$attr['headingsColor'].' !important;';
			$css .= '}';
		}
		if ( isset($attr['headingsHoverColor']) ) {
			$css .= '	.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h1,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h2,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h3,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h4,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h5,
								.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover h6 {';
				$css .= 'color:'.$attr['headingsHoverColor'].' !important;';
			$css .= '}';
		}


		//hover colors, lift and scale and stuff
		if ( isset($attr['hoverBGColor']) || isset($attr['hoverTextColor']) || isset($attr['hoverBorderColor']) || ( isset( $attr['scaleElement'] ) && $attr['scaleElement'] === true ) || ( isset( $attr['liftElement'] ) && $attr['liftElement'] === true ) ) {
			$css .= '.kioken-feature-' . $unique_id . ' .wp-block-kioken-feature__inner:hover {';
				$css .= isset($attr['hoverBGColor']) ? 'background-color:'.$attr['hoverBGColor'].';' : '';
				$css .= isset($attr['hoverTextColor']) ? 'color:'.$attr['hoverTextColor'].';' : '';
				$css .= isset($attr['hoverBorderColor']) ? 'border-color:'.$attr['hoverBorderColor'].';' : '';

				//lift and scale
				if ( ( isset( $attr['scaleElement'] ) && $attr['scaleElement'] === true ) || ( isset( $attr['liftElement'] ) && $attr['liftElement'] === true ) ) {
					$scale = isset( $attr['hoverScaleSize'] ) ? $attr['hoverScaleSize'] : 1.1;
					$lift = isset( $attr['hoverLiftSize'] ) ? $attr['hoverLiftSize'] : 10;
					//scale only
					if ( isset( $attr['hoverScaleSize'] ) && !isset( $attr['liftElement'] ) ) {
						$css .= 'transform:scale3d('.$scale.','.$scale.','.$scale.');';
					}
					//lift only
					if ( isset( $lift ) && !isset( $attr['scaleElement'] ) ) {
						$css .= 'transform:translate3d(0,'.$lift.'px,0);';
					}
					//both
					if ( isset( $attr['liftElement'] ) && isset( $attr['scaleElement'] ) && isset( $lift ) && isset( $scale ) ) {
						$css .= 'transform:translate3d(0,'.$lift.'px,0) scale3d('.$scale.','.$scale.','.$scale.');';
					}
				}

			$css .= '}';
		}



		return $css;
	}

	public function blocks_kioken_tabs_array_css( $attr, $unique_id ) {
		$css = '';
		if ( isset($attr['buttonBGColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item {';
			$css .= 'background-color:'.$attr['buttonBGColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item {';
			$css .= 'color:'.$attr['buttonColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonBorderColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item {';
			$css .= 'border-color:'.$attr['buttonBorderColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonHoverBGColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item:hover {';
			$css .= 'background-color:'.$attr['buttonHoverBGColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonHoverColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item:hover {';
			$css .= 'color:'.$attr['buttonHoverColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonBorderHoverColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item:hover {';
			$css .= 'border-color:'.$attr['buttonBorderHoverColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonActiveBGColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item-active {';
			$css .= 'background-color:'.$attr['buttonActiveBGColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonActiveColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item-active {';
			$css .= 'color:'.$attr['buttonActiveColor'].';';
			$css .= '}';
		}
		if ( isset($attr['buttonBorderActiveColor']) && isset($attr['uniqueID']) ) {
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item-active {';
			$css .= 'border-color:'.$attr['buttonBorderActiveColor'].';';
			$css .= '}';
		}
		//content borders
		if ( isset( $attr['contentBorderWidth'] ) && is_array( $attr['contentBorderWidth'] ) ) {
			$css .= '#kioken-tabs_' . $unique_id . '.wp-block-kioken-tabs .kioken-tabs-content {';
			$css .= 'border-width:' . $attr['contentBorderWidth'][0] . 'px ' . $attr['contentBorderWidth'][1] . 'px ' . $attr['contentBorderWidth'][2] . 'px ' . $attr['contentBorderWidth'][3] . 'px !important;';
			$css .= '}';
		}

		//borders
		if ( isset( $attr['buttonBorderWidth'] ) && is_array( $attr['buttonBorderWidth'] ) ) {
			$borderW = $attr['buttonBorderWidth'];
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item {';
			if ( isset( $borderW[0] ) && !isset( $borderW[1] ) && !isset( $borderW[2] ) && !isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px;';
			} elseif( isset( $borderW[0] ) && isset( $borderW[1] ) && isset( $borderW[2] ) && isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px ' . $borderW[1] . 'px ' . $borderW[2] . 'px ' . $borderW[3] . 'px;';
			}
			$css .= '}';
		}

		if ( isset( $attr['buttonBorderRadius'] ) && is_array( $attr['buttonBorderRadius'] ) ) {
			$radius = $attr['buttonBorderRadius'];
			$css .= '#kioken-tabs_' . $unique_id . ' a.kioken-tabs-buttons-item {';
				if ( isset( $radius[0] ) && !isset( $radius[1] ) && !isset( $radius[2] ) && !isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px;';
				} elseif( isset( $radius[0] ) && isset( $radius[1] ) && isset( $radius[2] ) && isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px ' . $radius[1] . 'px ' . $radius[2] . 'px ' . $radius[3] . 'px;';
				}
			$css .= '}';
		}

		//content borders
		if ( isset( $attr['contentBorderWidth'] ) && is_array( $attr['contentBorderWidth'] ) ) {
			$borderW = $attr['contentBorderWidth'];
			$css .= '#kioken-tabs_' . $unique_id . ' .kioken-tabs-content {';
			if ( isset( $borderW[0] ) && !isset( $borderW[1] ) && !isset( $borderW[2] ) && !isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px;';
			} elseif( isset( $borderW[0] ) && isset( $borderW[1] ) && isset( $borderW[2] ) && isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px ' . $borderW[1] . 'px ' . $borderW[2] . 'px ' . $borderW[3] . 'px;';
			}
			$css .= '}';
		}

		if ( isset( $attr['contentBorderRadius'] ) && is_array( $attr['contentBorderRadius'] ) ) {
			$radius = $attr['contentBorderRadius'];
			$css .= '#kioken-tabs_' . $unique_id . ' .kioken-tabs-content {';
				if ( isset( $radius[0] ) && !isset( $radius[1] ) && !isset( $radius[2] ) && !isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px;';
				} elseif( isset( $radius[0] ) && isset( $radius[1] ) && isset( $radius[2] ) && isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px ' . $radius[1] . 'px ' . $radius[2] . 'px ' . $radius[3] . 'px;';
				}
			$css .= '}';
		}

		return $css;
	}
	public function blocks_kioken_wrapper_array( $attr, $unique_id ) {
		$css = '';

		$paddingUnit 	= isset( $attr['paddingUnit']) ? $attr['paddingUnit'] : 'px';
		$marginUnit 	= isset( $attr['marginUnit']) ? $attr['marginUnit'] : 'px';

		//gradient bg
		if ( isset( $attr['backgroundType'] ) && $attr['backgroundType'] === 'gradient' && isset($attr['gradientBG']) ) {
			$gradient = $attr['gradientBG'][ 0 ];
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
			$css .= 'background-image:linear-gradient('.$gradient['angle'].'deg,'.$gradient['colorFirst'].' '.$gradient['locFirst'].'%,'.$gradient['colorSecond'].' '.$gradient['locSecond'].'%) !important;';
			$css .= '}';
		}

		if ( isset( $attr['maxWidth'] ) ) {
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
				$css .= 'max-width:' . $attr['maxWidth'] . 'px;';
			$css .= '}';
		}

		//shadows
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][ 0 ] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
				$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['color'] . ';';
				$css .= '}';
			}
			 else {
				$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
				$css .= 'box-shadow:0px 0px 14px 0px rgba(0,0,0,0.2);';
				$css .= '}';
			}
		}

		//borders
		if ( isset( $attr['zIndex'] ) ) {
			$css.='.kt-wrapper_'.$unique_id.' { ';
				$css .='z-index:'.$attr['zIndex'].' !important; ';
			$css .= '}';
		}

		if (isset($attr['backBlendMode']) && isset($attr['backgroundImg'])) {
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic > .kt-absolute-bg.has-background-overlay:before,
			.kt-wrapper_'.$unique_id.' > .kt-inner > .kinetic > .kt-absolute-bg.has-background
			{ ';
			$css.='background-blend-mode:'.$attr['backBlendMode'].';';
			$css.='}';

			if (isset($attr['jsParallax'])) {
				$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic > .kt-absolute-bg > div[id|=jarallax-container] { ';
				$css.='mix-blend-mode:'.$attr['backBlendMode'].';';
				$css.='}';
			}

		}

		//borders
		if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) ) {
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
				if ( isset($attr['borderWidth'][0]) || isset($attr['borderWidth'][1]) || isset($attr['borderWidth'][2]) || isset($attr['borderWidth'][3])  ) {
					$css .= 'border-width:' . $attr['borderWidth'][0] . 'px ' . $attr['borderWidth'][1] . 'px ' . $attr['borderWidth'][2] . 'px ' . $attr['borderWidth'][3] . 'px;';
				} else {
					$css .= '';
				}
			$css .= '}';
		}
		if ( isset( $attr['borderRadius'] ) && is_array( $attr['borderRadius'] ) ) {
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
				if ( isset($attr['borderRadius'][0]) || isset($attr['borderRadius'][1]) || isset($attr['borderRadius'][2]) || isset($attr['borderRadius'][3])  ) {
					$css .= 'border-radius:' . $attr['borderRadius'][0] . 'px ' . $attr['borderRadius'][1] . 'px ' . $attr['borderRadius'][2] . 'px ' . $attr['borderRadius'][3] . 'px;';
				} else {
					$css .= '';
				}
			$css .= '}';
		}
		if ( isset( $attr['borderColor'] ) ) {
			$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
			$css .= 'border-color:' . $attr['borderColor'] . ';';
			$css .= '}';
		}



		//tablet settings
		if ( isset( $attr['paddingTopTablet'] ) || isset( $attr['paddingRightTablet'] ) || isset( $attr['paddingBottomTablet'] ) || isset( $attr['paddingLeftTablet'] ) || isset( $attr['marginTopTablet'] ) || isset( $attr['marginRightTablet'] ) || isset( $attr['marginBottomTablet'] ) || isset( $attr['marginLeftTablet'] ) ) {

			$css.='@media (max-width: 768px) {';

				$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
					//padding values
					if ( isset( $attr['paddingTopTablet'] ) ) {
						$css.='padding-top:'.$attr['paddingTopTablet'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingRightTablet']) ) {
						$css.='padding-right:'.$attr['paddingRightTablet'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingBottomTablet']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomTablet'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingLeftTablet']) ) {
						$css.='padding-left:'.$attr['paddingLeftTablet'].$paddingUnit.' !important; ';
					}
					//margin values
					if ( isset( $attr['marginTopTablet']) ) {
						$css.='margin-top:'.$attr['marginTopTablet'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginRightTablet']) ) {
						$css.='margin-right:'.$attr['marginRightTablet'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginBottomTablet']) ) {
						$css.='margin-bottom:'.$attr['marginBottomTablet'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginLeftTablet']) ) {
						$css.='margin-left:'.$attr['marginLeftTablet'].$marginUnit.' !important; ';
					}

					$css.='}';
			$css.='}';
		}

		//mobile settings
		if ( isset( $attr['paddingTopMobile'] ) || isset( $attr['paddingRightMobile'] ) || isset( $attr['paddingBottomMobile'] ) || isset( $attr['paddingLeftMobile'] ) || isset( $attr['marginTopMobile'] ) || isset( $attr['marginRightMobile'] ) || isset( $attr['marginBottomMobile'] ) || isset( $attr['marginLeftMobile'] ) ) {
			$css.='@media (max-width: 576px) {';
				$css.='.kt-wrapper_'.$unique_id.'  > .kt-inner > .kinetic { ';
					//padding values
					if ( isset( $attr['paddingTopMobile']) ) {
						$css.='padding-top:'.$attr['paddingTopMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingRightMobile']) ) {
						$css.='padding-right:'.$attr['paddingRightMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingBottomMobile']) ) {
						$css.='padding-bottom:'.$attr['paddingBottomMobile'].$paddingUnit.' !important; ';
					}
					if ( isset( $attr['paddingLeftMobile']) ) {
						$css.='padding-left:'.$attr['paddingLeftMobile'].$paddingUnit.' !important; ';
					}
					//margin values
					if ( isset( $attr['marginTopMobile']) ) {
						$css.='margin-top:'.$attr['marginTopMobile'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginRightMobile']) ) {
						$css.='margin-right:'.$attr['marginRightMobile'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginBottomMobile']) ) {
						$css.='margin-bottom:'.$attr['marginBottomMobile'].$marginUnit.' !important; ';
					}
					if ( isset( $attr['marginLeftMobile']) ) {
						$css.='margin-left:'.$attr['marginLeftMobile'].$marginUnit.' !important; ';
					}

					$css.='}';
			$css.='}';
		}

		return $css;
	}
	public function blocks_kioken_opentable_array( $attr, $unique_id ) {
		$css = '';
		if ( isset($attr['buttonBGColor']) || isset($attr['buttonColor'])) {
			$css .= '.kt-ot' . $unique_id . '.wp-block-kioken-opentable .otb-button-wrap .otb-submit-button {';
				isset($attr['buttonBGColor']) ? $css .= 'background-color:' . $attr['buttonBGColor'] . ';' : '';
				isset($attr['buttonColor']) ? $css .= 'color:' . $attr['buttonColor'] . ';' : '';
			$css .= '}';
		}
		if ( isset($attr['buttonBGColorHover']) || isset($attr['buttonColorHover'])) {
			$css .= '.kt-ot' . $unique_id . '.wp-block-kioken-opentable .otb-button-wrap .otb-submit-button:hover {';
				isset($attr['buttonBGColorHover']) ? $css .= 'background-color:' . $attr['buttonBGColorHover'] . ';' : '';
				isset($attr['buttonColorHover']) ? $css .= 'color:' . $attr['buttonColorHover'] . ';' : '';
			$css .= '}';
		}

		return $css;
	}
	/**
	 * Builds CSS for Visual Icon List block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	 public function blocks_kioken_iconlist_array( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr['listGap'] ) && ! empty( $attr['listGap'] ) ) {
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap {';
				$css .= 'margin-bottom:' . $attr['listGap'] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['listItemPadding'] ) && ! empty( $attr['listItemPadding'] ) ) {
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap {';
				$css .= 'padding:' . $attr['listItemPadding'] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['listLabelGap'] ) && ! empty( $attr['listLabelGap'] ) ) {
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap .kb-icon-list-single {';
				$css .= 'margin-right:' . $attr['listLabelGap'] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['listStyles'] ) && is_array( $attr['listStyles'] ) && is_array( $attr['listStyles'][ 0 ] ) ) {
			$list_styles = $attr['listStyles'][ 0 ];
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap, .kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap a {';
			if ( isset( $list_styles['color'] ) && ! empty( $list_styles['color'] ) ) {
				$css .= 'color:' . $list_styles['color'] .  ';';
			}
			if ( isset( $list_styles['size'] ) && is_array( $list_styles['size'] ) && ! empty( $list_styles['size'][0] ) ) {
				$css .= 'font-size:' . $list_styles['size'][0] . ( ! isset( $list_styles['sizeType'] ) ? 'px' : $list_styles['sizeType'] ) . ';';
			}
			if ( isset( $list_styles['lineHeight'] ) && is_array( $list_styles['lineHeight'] ) && ! empty( $list_styles['lineHeight'][0] ) ) {
				$css .= 'line-height:' . $list_styles['lineHeight'][0] . ( ! isset( $list_styles['lineType'] ) ? 'px' : $list_styles['lineType'] ) . ';';
			}
			if ( isset( $list_styles['letterSpacing'] ) && ! empty( $list_styles['letterSpacing'] ) ) {
				$css .= 'letter-spacing:' . $list_styles['letterSpacing'] .  'px;';
			}
			if ( isset( $list_styles['textTransform'] ) && ! empty( $list_styles['textTransform'] ) ) {
				$css .= 'text-transform:' . $list_styles['textTransform'] .  ';';
			}
			if ( isset( $list_styles['family'] ) && ! empty( $list_styles['family'] ) ) {
				$css .= 'font-family:' . $list_styles['family'] .  ';';
			}
			if ( isset( $list_styles['style'] ) && ! empty( $list_styles['style'] ) ) {
				$css .= 'font-style:' . $list_styles['style'] .  ';';
			}
			if ( isset( $list_styles['weight'] ) && ! empty( $list_styles['weight'] ) ) {
				$css .= 'font-weight:' . $list_styles['weight'] .  ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['listStyles'] ) && is_array( $attr['listStyles'] ) && isset( $attr['listStyles'][0] ) && is_array( $attr['listStyles'][0] ) && ( ( isset( $attr['listStyles'][0]['size'] ) && is_array( $attr['listStyles'][0]['size'] ) && isset( $attr['listStyles'][0]['size'][1] ) && ! empty( $attr['listStyles'][0]['size'][1] ) ) || ( isset( $attr['listStyles'][0]['lineHeight'] ) && is_array( $attr['listStyles'][0]['lineHeight'] ) && isset( $attr['listStyles'][0]['lineHeight'][1] ) && ! empty( $attr['listStyles'][0]['lineHeight'][1] ) ) ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap {';
			if ( isset( $attr['listStyles'][0]['size'][1] ) && ! empty( $attr['listStyles'][0]['size'][1] ) ) {
				$css .= 'font-size:' . $attr['listStyles'][0]['size'][1] . ( ! isset( $attr['listStyles'][0]['sizeType'] ) ? 'px' : $attr['listStyles'][0]['sizeType'] ) . ';';
			}
			if ( isset( $attr['listStyles'][0]['lineHeight'][1] ) && ! empty( $attr['listStyles'][0]['lineHeight'][1] ) ) {
				$css .= 'line-height:' . $attr['listStyles'][0]['lineHeight'][1] . ( ! isset( $attr['listStyles'][0]['lineType'] ) ? 'px' : $attr['listStyles'][0]['lineType'] ) . ';';
			}
			$css .= '}';
			$css .= '}';
		}
		if ( isset( $attr['listStyles'] ) && is_array( $attr['listStyles'] ) && isset( $attr['listStyles'][0] ) && is_array( $attr['listStyles'][0] ) && ( ( isset( $attr['listStyles'][0]['size'] ) && is_array( $attr['listStyles'][0]['size'] ) && isset( $attr['listStyles'][0]['size'][2] ) && ! empty( $attr['listStyles'][0]['size'][2] ) ) || ( isset( $attr['listStyles'][0]['lineHeight'] ) && is_array( $attr['listStyles'][0]['lineHeight'] ) && isset( $attr['listStyles'][0]['lineHeight'][2] ) && ! empty( $attr['listStyles'][0]['lineHeight'][2] ) ) ) ) {
			$css .= '@media (max-width: 767px) {';
			$css .= '.kb-icon-list-items' . $unique_id . ' ul.kb-icon-list .kb-icon-list-item-wrap {';
				if ( isset( $attr['listStyles'][0]['size'][2] ) && ! empty( $attr['listStyles'][0]['size'][2] ) ) {
					$css .= 'font-size:' . $attr['listStyles'][0]['size'][2] . ( ! isset( $attr['listStyles'][0]['sizeType'] ) ? 'px' : $attr['listStyles'][0]['sizeType'] ) . ';';
				}
				if ( isset( $attr['listStyles'][0]['lineHeight'][2] ) && ! empty( $attr['listStyles'][0]['lineHeight'][2] ) ) {
					$css .= 'line-height:' . $attr['listStyles'][0]['lineHeight'][2] . ( ! isset( $attr['listStyles'][0]['lineType'] ) ? 'px' : $attr['listStyles'][0]['lineType'] ) . ';';
				}
			$css .= '}';
			$css .= '}';
		}

		//fonts
		if ( isset( $attr['listStyles'] ) && is_array( $attr['listStyles'] ) && isset( $attr['listStyles'][0] ) && is_array( $attr['listStyles'][0] ) && isset( $attr['listStyles'][0]['google'] ) && $attr['listStyles'][0]['google'] && ( ! isset( $attr['listStyles'][0]['loadGoogle'] ) || true === $attr['listStyles'][0]['loadGoogle'] ) &&  isset( $attr['listStyles'][0]['family'] ) ) {
			$list_font = $attr['listStyles'][0];
			// Check if the font has been added yet
			if ( ! array_key_exists( $list_font['family'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $list_font['family'],
					'fontvariants' => ( isset( $list_font['variant'] ) && ! empty( $list_font['variant'] ) ? array( $list_font['variant'] ) : array() ),
					'fontsubsets' => ( isset( $list_font['subset'] ) && !empty( $list_font['subset'] ) ? array( $list_font['subset'] ) : array() ),
				);
				self::$gfonts[ $list_font['family'] ] = $add_font;
			} else {
				if ( ! in_array( $list_font['variant'], self::$gfonts[ $list_font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $list_font['family'] ]['fontvariants'], $list_font['variant'] );
				}
				if ( ! in_array( $list_font['subset'], self::$gfonts[ $list_font['family'] ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $list_font['family'] ]['fontsubsets'], $list_font['subset'] );
				}
			}
		}
		return $css;
	}
	/**
	 * Builds CSS for Kinetic Posts block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	 public function blocks_kioken_posts_array( $att, $unique_id ) {
		$css = '';
		$att['postDesign'] = ! isset($att['postDesign']) ? 'default' : $att['postDesign'];
		$att['postContentMaxW'] = ! isset($att['postContentMaxW']) ? '80' : $att['postContentMaxW'];

		//image border radiuses and max height assignments
		if ( isset( $att['imgBorderRad'] ) || isset( $att['mediaMaxH'] ) || isset( $att['forceMediaHeight'] ) ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . ' .grid-post-row img { ';
			if ( isset($att['imgBorderRad']) ) {
				$css .= 'border-radius: ' . $att['imgBorderRad'] . 'px;';
			}
			if ( isset($att['mediaMaxH']) && ! isset( $att['forceMediaHeight'] ) ) {
				$css .= 'max-height: ' . $att['mediaMaxH'] . 'px;';
			}
			if ( isset($att['mediaMaxH']) && isset( $att['forceMediaHeight'] ) && $att['forceMediaHeight'] ) {
				$css .= 'max-height: ' . $att['mediaMaxH'] . 'px;';
				$css .= 'height: ' . $att['mediaMaxH'] . 'px;';
			}
			$css .= '}';
		}
		if ( isset( $att['imgBorderRad'] ) ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . ' .grid-post-row .post-image { ';
				$css .= 'border-radius: ' . $att['imgBorderRad'] . 'px;';
			$css .= '}';
		}

		//post title settings
		if ( isset( $att['titleFontSize'] ) ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . ' .grid-post-row .grid-content-title { ';
				$css .= 'font-size: ' . $att['titleFontSize'] . 'px;';
			$css .= '}';
		}

		//post content settings
		if ( isset( $att['contentBorderRad'] ) || isset( $att['postContentMaxW'] ) ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . ' .grid-post-row .grid-content-area { ';
				if ( isset( $att['contentBorderRad'] ) )  {
					$css .= 'border-radius: ' . $att['contentBorderRad'] . 'px;';
				}
				if ( isset( $att['postContentMaxW'] ) && 'stacked-center' == $att['postDesign'] )  {
					$css .= 'max-width:'.$att['postContentMaxW'].'%; flex:0 0 '.$att['postContentMaxW'].'%;';
				}

			$css .= '}';
		}

		//post-overlay hover effects
		if ( isset( $att['overlayColor'] ) && 'imagebox' == $att['postDesign'] ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image .post-overlay { ';
					$css .= 'background-color: ' . $att['overlayColor'] . ';';
			$css .= '}';
		}
		if ( isset( $att['overlayHoverColor'] ) && 'imagebox' == $att['postDesign'] ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image:hover .post-overlay { ';
					$css .= 'background-color: ' . $att['overlayHoverColor'] . ';';
			$css .= '}';
		}

		//postcontent hover effects
		if ( isset( $att['textColor'] ) && 'imagebox' == $att['postDesign'] ) {
			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image .info-container { ';
					$css .= 'color: ' . $att['textColor'] . ';';
			$css .= '}';

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image .info-container .line { ';
					$css .= 'background-color: ' . $att['textColor'] . ';';
			$css .= '}';

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image .info-container svg { ';
					$css .= 'stroke: ' . $att['textColor'] . ';';
			$css .= '}';
		}

		if ( isset( $att['textColorHover'] ) && 'imagebox' == $att['postDesign'] ) {

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image:hover .info-container { ';
					$css .= 'color: ' . $att['textColorHover'] . ';';
			$css .= '}';

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image:hover .info-container .line { ';
					$css .= 'background-color: ' . $att['textColorHover'] . ';';
			$css .= '}';

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image:hover .info-container svg { ';
					$css .= 'stroke: ' . $att['textColorHover'] . ';';
			$css .= '}';

		}

		if ( isset( $att['infoInnerBGColor'] ) && 'imagebox' == $att['postDesign'] ) {

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image .info-container .kb-inner { ';
					$css .= 'background-color: ' . $att['infoInnerBGColor'] . ';';
			$css .= '}';

		}

		if ( isset( $att['infoInnerHoverBGColor'] ) && 'imagebox' == $att['postDesign'] ) {

			$css .= '#kt-kinetic-posts_' . $unique_id . '.design-is-imagebox .post-image:hover .info-container .kb-inner { ';
					$css .= 'background-color: ' . $att['infoInnerHoverBGColor'] . ';';
			$css .= '}';

		}

		//post box padding
		//background color
		if ( ( isset( $att['postPadding'] ) && isset( $att['postPadding'][0] ) ) || isset( $att['postBGColor'] ) ) {

			$css .= '#kt-kinetic-posts_' . $unique_id . ' .grid-post-row { ';

			if ( isset( $att['postPadding'] ) && isset( $att['postPadding'][0] ) ) {
				$postPadControl = isset( $att['postPaddingControl'] ) ? $att['postPaddingControl'] : 'linked';
				if ( 'individual' !== $postPadControl ) {
					$css .= 'padding:' . $this->kbArrayNumCheck( $att['postPadding'][0] ) . 'px;';
				} else {
					$css .= 'padding:' .
					$this->kbArrayNumCheck( $att['postPadding'][0] ) . 'px ' .
					$this->kbArrayNumCheck( $att['postPadding'][1] ) . 'px ' .
					$this->kbArrayNumCheck( $att['postPadding'][2] ) . 'px ' .
					$this->kbArrayNumCheck( $att['postPadding'][3] ) . 'px;' ;
				}
			}
			if ( isset( $att['postBGColor'] ) ) {
				$css .= 'background-color:' . $att['postBGColor'] . ';';
			}


			$css .= '}';
		}


		return $css;
	}
	/**
	 * Builds CSS for VideoBox block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	public function blocks_kioken_videobox_array( $attr, $unique_id ) {
		$css = '';

		//border, radius, max
		if ( isset($attr['borderRadiusControl']) || isset($attr['borderRadius'][0]) ) {
			$css .= '.kioken-videobox-' . $unique_id . ' {';

				$css .= isset($attr['borderRadius'][0]) ? 'border-radius:'.$attr['borderRadius'][0].'px;' : '';
				$css .= isset($attr['borderRadiusControl']) && $attr['borderRadiusControl']==='individual' ? 'border-radius:'.$attr['borderRadius'][0].'px '.$attr['borderRadius'][1].'px '.$attr['borderRadius'][2].'px '.$attr['borderRadius'][3].'px;' : '';

			$css .= '}';
		}

		//shadows
		if ( isset( $attr['enableBs'] ) && ! empty( $attr['enableBs'] ) && true === $attr['enableBs'] ) {
			// enableBs, bsXPos, bsYPos, bsBlur, bsSpread, bsColor, bsInset,
			// bsHoverXPos, bsHoverYPos, bsHoverBlur, bsHoverSpread, bsHoverColor, bsHoverInset,
			$bsColor = isset( $attr['bsColor'] ) ? $attr['bsColor'] : '';
			$bsHoverColor = isset( $attr['bsHoverColor'] ) ? $attr['bsHoverColor'] : '';

			$bsXPos = isset( $attr['bsXPos'] ) ? $attr['bsXPos'] : 0;
			$bsHoverXPos = isset( $attr['bsHoverXPos'] ) ? $attr['bsHoverXPos'] : 0;
			$bsYPos = isset( $attr['bsYPos'] ) ? $attr['bsYPos'] : 0;
			$bsHoverYPos = isset( $attr['bsHoverYPos'] ) ? $attr['bsHoverYPos'] : 0;
			$bsBlur = isset( $attr['bsBlur'] ) ? $attr['bsBlur'] : 0;
			$bsHoverBlur = isset( $attr['bsHoverBlur'] ) ? $attr['bsHoverBlur'] : 0;
			$bsSpread = isset( $attr['bsSpread'] ) ? $attr['bsSpread'] : 0;
			$bsHoverSpread = isset( $attr['bsHoverSpread'] ) ? $attr['bsHoverSpread'] : 0;
			$bsInset = isset( $attr['bsInset'] ) ? 'inset' : '';
			$bsHoverInset = isset( $attr['bsHoverInset'] ) ? 'inset' : '';

			$css .= '.kioken-videobox-' . $unique_id . ' {';
				$css .= 'box-shadow:' . $bsInset . ' ' . $bsXPos . 'px ' . $bsYPos . 'px ' . $bsBlur . 'px ' . $bsSpread . 'px ' . $bsColor . ';';
			$css .= '}';

			$css .= '.kioken-videobox-' . $unique_id . ':hover {';
				$css .= 'box-shadow:' . $bsHoverInset . ' ' . $bsHoverXPos . 'px ' . $bsHoverYPos . 'px ' . $bsHoverBlur . 'px ' . $bsHoverSpread . 'px ' . $bsColor . ';';
			$css .= '}';

		}


		return $css;
	}

	/**
	 * Builds CSS for Advanced Button block.
	 *
	 * @param object  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	function blocks_advanced_btn( $attr, $unique_id ) {
		$css = '';
		if ( isset( $attr->typography ) ) {
			$css .= '.kt-btns' . $unique_id . ' {';
			if ( isset( $attr->typography ) && ! empty( $attr->typography ) ) {
				$css .= 'font-family:' . $attr->typography . ';';
			}
			if ( isset( $attr->fontWeight ) && ! empty( $attr->fontWeight ) ) {
				$css .= 'font-weight:' . $attr->fontWeight . ';';
			}
			if ( isset( $attr->fontStyle ) && ! empty( $attr->fontStyle ) ) {
				$css .= 'font-style:' . $attr->fontStyle . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr->btns ) && is_array( $attr->btns ) ) {
			foreach ( $attr->btns as $btnkey => $btnvalue ) {
				if ( is_object( $btnvalue ) ) {
					$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button {';
						if ( isset( $btnvalue->color ) && ! empty( $btnvalue->color ) ) {
							$css .= 'color:' . $btnvalue->color . ';';
						}
						if ( isset(  $btnvalue->background ) && ! empty(  $btnvalue->background ) ) {
							$css .= 'background-color:' .  $btnvalue->background . ';';
						}
						if ( isset( $btnvalue->border ) && ! empty( $btnvalue->border ) ) {
							$css .= 'border-color:' . $btnvalue->border . ';';
						}
					$css .= '}';
					$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:hover, .kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:focus {';
						if ( isset( $btnvalue->colorHover ) && ! empty( $btnvalue->colorHover ) ) {
							$css .= 'color:' . $btnvalue->colorHover . ';';
						}
						if ( isset(  $btnvalue->backgroundHover ) && ! empty(  $btnvalue->backgroundHover ) ) {
							$css .= 'background-color:' .  $btnvalue->backgroundHover . ';';
						}
						if ( isset( $btnvalue->borderHover ) && ! empty( $btnvalue->borderHover ) ) {
							$css .= 'border-color:' . $btnvalue->borderHover . ';';
						}
					$css .= '}';
				}
			}
		}
		if ( isset( $attr->googleFont ) && $attr->googleFont && ( ! isset( $attr->loadGoogleFont ) || $attr->loadGoogleFont == true ) && isset( $attr->typography ) ) {
			// Check if the font has been added yet
			if ( ! array_key_exists( $attr->typography, self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $attr->typography,
					'fontvariants' => ( isset( $attr->fontVariant ) && ! empty( $attr->fontVariant ) ? array( $attr->fontVariant ) : array() ),
					'fontsubsets' => ( isset( $attr->fontSubset ) && !empty( $attr->fontSubset ) ? array( $attr->fontSubset ) : array() ),
				);
				self::$gfonts[$attr->typography] = $add_font;
			} else {
				if ( ! in_array( $attr->fontVariant, self::$gfonts[ $attr->typography ]['fontvariants'], true ) ) {
					array_push( self::$gfonts[ $attr->typography ]['fontvariants'], $attr->fontVariant );
				}
				if ( ! in_array( $attr->fontSubset, self::$gfonts[ $attr->typography ]['fontsubsets'], true ) ) {
					array_push( self::$gfonts[ $attr->typography ]['fontsubsets'], $attr->fontSubset );
				}
			}
		}
		return $css;
	}
	/**
	 * Builds CSS for Advanced Button block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	function blocks_advanced_btn_array( $attr, $unique_id ) {
		$css = '';

		if ( isset( $attr['buttonSpacing'] ) ) {
			$css .= '.kt-btns' . $unique_id . '  .kt-btn-wrap {';
				$css .= 'margin-right:' . $attr['buttonSpacing'] . 'px;';
			$css .= '}';
		}
		if ( isset( $attr['buttonsMarginTop'] ) || isset( $attr['buttonsMarginBottom'] ) ) {
			$css .= '.kt-btns' . $unique_id . '  .kt-btn-wrap {';
				$css .= isset( $attr['buttonsMarginTop'] ) ? 'margin-top:' . $attr['buttonsMarginTop'] . 'px;' : '';
				$css .= isset( $attr['buttonsMarginBottom'] ) ? 'margin-bottom:' . $attr['buttonsMarginBottom'] . 'px;' : '';
			$css .= '}';
		}

		if ( isset( $attr['typography'] ) ) {
			$css .= '.kt-btns' . $unique_id . ' {';
			if ( isset( $attr['typography'] ) && ! empty( $attr['typography'] ) ) {
				$css .= 'font-family:' . $attr['typography'] . ';';
			}
			if ( isset( $attr['fontWeight'] ) && ! empty( $attr['fontWeight'] ) ) {
				$css .= 'font-weight:' . $attr['fontWeight'] . ';';
			}
			if ( isset( $attr['fontStyle'] ) && ! empty( $attr['fontStyle'] ) ) {
				$css .= 'font-style:' . $attr['fontStyle'] . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['btns'] ) && is_array( $attr['btns'] ) ) {
			foreach ( $attr['btns'] as $btnkey => $btnvalue ) {
				if ( is_array( $btnvalue ) ) {
					$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button {';
						if ( isset( $btnvalue['color'] ) && ! empty( $btnvalue['color'] ) ) {
							$css .= 'color:' . $btnvalue['color'] . ';';
						}
						if ( isset(  $btnvalue['background'] ) && ! empty(  $btnvalue['background'] ) ) {
							$css .= 'background-color:' .  $btnvalue['background'] . ';';
						}
						if ( isset( $btnvalue['border'] ) && ! empty( $btnvalue['border'] ) ) {
							$css .= 'border-color:' . $btnvalue['border'] . ';';
						}
					$css .= '}';

					$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:hover, .kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:focus {';
						if ( isset( $btnvalue['colorHover'] ) && ! empty( $btnvalue['colorHover'] ) ) {
							$css .= 'color:' . $btnvalue['colorHover'] . ';';
						}
						if ( isset(  $btnvalue['backgroundHover'] ) && ! empty(  $btnvalue['backgroundHover'] ) ) {
							$css .= 'background-color:' .  $btnvalue['backgroundHover'] . ';';
						}
						if ( isset( $btnvalue['borderHover'] ) && ! empty( $btnvalue['borderHover'] ) ) {
							$css .= 'border-color:' . $btnvalue['borderHover'] . ';';
						}
					$css .= '}';
					//gradientBG
					if ( isset(  $btnvalue['gradBG'] ) && ! empty(  $btnvalue['gradBG'] ) ) {
						$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button.has-gradient-bg::before {';
							if ( isset($btnvalue['gradAngle']) && isset($btnvalue['gradColorFirst']) && isset($btnvalue['gradLocFirst']) && isset($btnvalue['gradColorSec']) && isset($btnvalue['gradColorSec']) ) {
								$css .= 'background-image:linear-gradient('.$btnvalue['gradAngle'].'deg, '.$btnvalue['gradColorFirst'].' '.$btnvalue['gradLocFirst'].'%, '.$btnvalue['gradColorSec'].' '.$btnvalue['gradLocSec'].'%);';
							}

							if ( isset( $btnvalue['borderWidth'] ) ) {
								$border_space = $btnvalue['borderWidth'];
								$border_space_add = $border_space * 2;
								$css .= 'top: -'.$border_space.'px; left:-'.$border_space.'px; ';
								$css .= 'width: calc(100% + '.$border_space_add.'px); height:calc(100% + '.$border_space_add.'px); ';
							}
						$css .= '}';
					}
					//shadow
					if ( isset(  $btnvalue['shadow'] ) && ! empty(  $btnvalue['shadow'] &&  ! empty($btnvalue['shadowColor']) ) ) {
						$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button {';
							$css .= 'box-shadow:'.$btnvalue['shadowHoffset'].'px '.$btnvalue['shadowVoffset'].'px '.$btnvalue['shadowBlur'].'px '.$btnvalue['shadowSpread'].'px '.$btnvalue['shadowColor'].';';
						$css .= '}';
						if ( isset(  $btnvalue['shadowHoverColor'] ) && ! empty(  $btnvalue['shadowHoverColor'] ) && isset($btnvalue['shadowHoverVoffset']) && isset($btnvalue['shadowHoverHoffset']) ) {
							$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:hover {';
								$css .= 'box-shadow:'.$btnvalue['shadowHoverHoffset'].'px '.$btnvalue['shadowHoverVoffset'].'px '.$btnvalue['shadowHoverBlur'].'px '.$btnvalue['shadowHoverSpread'].'px '.$btnvalue['shadowHoverColor'].';';
							$css .= '}';
						}
					}
					//lift
					if ( isset(  $btnvalue['lift'] ) && ! empty(  $btnvalue['lift'] ) ) {
						$css .= '.kt-btns' . $unique_id . ' .kt-btn-wrap-' . $btnkey .' .kt-button:hover {';
							$css .= 'transform:translate3d('.$btnvalue['liftHPos'].'px, '.$btnvalue['liftVPos'].'px, 0);';
						$css .= '}';
					}
				}
			}
		}

		if ( isset( $attr['googleFont'] ) && $attr['googleFont'] && ( ! isset( $attr['loadGoogleFont'] ) || $attr['loadGoogleFont'] == true ) && isset( $attr['typography'] ) ) {
			// Check if the font has been added yet
			if ( ! array_key_exists( $attr['typography'], self::$gfonts ) ) {
				$add_font = array(
					'fontfamily' => $attr['typography'],
					'fontvariants' => ( isset( $attr['fontVariant'] ) && ! empty( $attr['fontVariant'] ) ? array( $attr['fontVariant'] ) : array() ),
					'fontsubsets' => ( isset( $attr['fontSubset'] ) && !empty( $attr['fontSubset'] ) ? array( $attr['fontSubset'] ) : array() ),
				);
				self::$gfonts[ $attr['typography'] ] = $add_font;
			} else {
				if( isset( $attr['fontVariant'] ) && ! empty( $attr['fontVariant'] ) ) {
					if ( ! in_array( $attr['fontVariant'], self::$gfonts[ $attr['typography'] ]['fontvariants'], true ) ) {
						array_push( self::$gfonts[ $attr['typography'] ]['fontvariants'], $attr['fontVariant'] );
					}
				}
				if( isset( $attr['fontSubset'] ) && ! empty( $attr['fontSubset'] ) ) {
					if ( ! in_array( $attr['fontSubset'], self::$gfonts[ $attr['typography'] ]['fontsubsets'], true ) ) {
						array_push( self::$gfonts[ $attr['typography'] ]['fontsubsets'], $attr['fontSubset'] );
					}
				}
			}
		}
		return $css;
	}
	/**
	 * Builds Css for row layout block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	function row_layout_css( $attr, $unique_id ) {
		$css = '';

		$minHeightUnit 	= isset( $attr->minHeightUnit ) ? $attr->minHeightUnit : 'px';

		if ( isset( $attr->bgColor ) || isset( $attr->bgImg ) || isset( $attr->topMargin ) || isset( $attr->bottomMargin ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' {';
			if ( isset( $attr->topMargin ) ) {
				$css .= 'margin-top:' . $attr->topMargin . 'px;';
			}
			if ( isset( $attr->bottomMargin ) ) {
				$css .= 'margin-bottom:' . $attr->bottomMargin . 'px;';
			}
			if ( isset( $attr->bgColor ) ) {
				$css .= 'background-color:' . $attr->bgColor . ';';
			}
			if ( isset( $attr->bgImg ) ) {
				$css .= 'background-image:url(' . $attr->bgImg . ');';
				$css .= 'background-size:' . ( isset( $attr->bgImgSize ) ? $attr->bgImgSize : 'cover' ) . ';';
				$css .= 'background-position:' . ( isset( $attr->bgImgPosition ) ? $attr->bgImgPosition : 'center center' ) . ';';
				$css .= 'background-attachment:' . ( isset( $attr->bgImgAttachment ) ? $attr->bgImgAttachment : 'scroll' ) . ';';
				$css .= 'background-repeat:' . ( isset( $attr->bgImgRepeat ) ? $attr->bgImgRepeat : 'no-repeat' ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr->bottomSep ) && 'none' != $attr->bottomSep ) {
			if ( isset( $attr->bottomSepHeight ) || isset( $attr->bottomSepWidth ) ) {
				if ( isset( $attr->bottomSepHeight ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
						$css .= 'height:' . $attr->bottomSepHeight . 'px;';
					$css .= '}';
				}
				if ( isset( $attr->bottomSepWidth ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
						$css .= 'width:' . $attr->bottomSepWidth . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr->bottomSepHeightTablet ) || isset( $attr->bottomSepWidthTablet ) ) {
				$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
					if ( isset( $attr->bottomSepHeightTablet ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
							$css .= 'height:' . $attr->bottomSepHeightTablet . 'px;';
						$css .= '}';
					}
					if ( isset( $attr->bottomSepWidthTablet ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
							$css .= 'width:' . $attr->bottomSepWidthTablet . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
			if ( isset( $attr->bottomSepHeightMobile ) || isset( $attr->bottomSepWidthMobile ) ) {
				$css .= '@media (max-width: 767px) {';
					if ( isset( $attr->bottomSepHeightMobile ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
							$css .= 'height:' . $attr->bottomSepHeightMobile . 'px;';
						$css .= '}';
					}
					if ( isset( $attr->bottomSepWidthMobile ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
							$css .= 'width:' . $attr->bottomSepWidthMobile . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
		}
		if ( isset( $attr->topSep ) && 'none' != $attr->topSep ) {
			if ( isset( $attr->topSepHeight ) || isset( $attr->topSepWidth ) ) {
				if ( isset( $attr->topSepHeight ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
						$css .= 'height:' . $attr->topSepHeight . 'px;';
					$css .= '}';
				}
				if ( isset( $attr->topSepWidth ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
						$css .= 'width:' . $attr->topSepWidth . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr->topSepHeightTablet ) || isset( $attr->topSepWidthTablet ) ) {
				$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
					if ( isset( $attr->topSepHeightTablet ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
							$css .= 'height:' . $attr->topSepHeightTablet . 'px;';
						$css .= '}';
					}
					if ( isset( $attr->topSepWidthTablet ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
							$css .= 'width:' . $attr->topSepWidthTablet . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
			if ( isset( $attr->topSepHeightMobile ) || isset( $attr->topSepWidthMobile ) ) {
				$css .= '@media (max-width: 767px) {';
					if ( isset( $attr->topSepHeightMobile ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
							$css .= 'height:' . $attr->topSepHeightMobile . 'px;';
						$css .= '}';
					}
					if ( isset( $attr->topSepWidthMobile ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
							$css .= 'width:' . $attr->topSepWidthMobile . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
		}
		if ( isset( $attr->topPadding ) || isset( $attr->bottomPadding ) || isset( $attr->leftPadding ) || isset( $attr->rightPadding ) || isset( $attr->minHeight ) ||  isset( $attr->maxWidth ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap {';
				if ( isset( $attr->topPadding ) ) {
					$css .= 'padding-top:' . $attr->topPadding . 'px;';
				}
				if ( isset( $attr->bottomPadding ) ) {
					$css .= 'padding-bottom:' . $attr->bottomPadding . 'px;';
				}
				if ( isset( $attr->leftPadding ) ) {
					$css .= 'padding-left:' . $attr->leftPadding . 'px;';
				}
				if ( isset( $attr->rightPadding ) ) {
					$css .= 'padding-right:' . $attr->rightPadding . 'px;';
				}
				if ( isset( $attr->minHeight ) ) {
					$css .= 'min-height:' . $attr->minHeight . $minHeightUnit . '; ';
				}
				if ( isset( $attr->maxWidth ) ) {
					$css .= 'max-width:' . $attr->maxWidth . 'px; ';
					$css .= 'margin-left:auto;';
					$css .= 'margin-right:auto;';
				}
			$css .= '}';
		}
		if ( isset( $attr->overlay ) || isset( $attr->overlayBgImg ) || isset( $attr->overlaySecond ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kio-row-layout-overlay {';
				if ( isset( $attr->overlayOpacity ) ) {
					if ( $attr->overlayOpacity < 10 ) {
						$css .= 'opacity:0.0' . $attr->overlayOpacity . ';';
					} else if ( $attr->overlayOpacity >= 100 ) {
						$css .= 'opacity:1;';
					} else {
						$css .= 'opacity:0.' . $attr->overlayOpacity . ';';
					}
				}
				if ( isset( $attr->currentOverlayTab ) && 'grad' == $attr->currentOverlayTab ) {
					$type = ( isset( $attr->overlayGradType ) ? $attr->overlayGradType : 'linear');
					if ( 'radial' === $type ) {
						$angle = ( isset( $attr->overlayBgImgPosition ) ? 'at ' . $attr->overlayBgImgPosition : 'at center center' );
					} else {
						$angle = ( isset( $attr->overlayGradAngle ) ? $attr->overlayGradAngle . 'deg' : '180deg');
					}
					$loc = ( isset( $attr->overlayGradLoc ) ? $attr->overlayGradLoc : '0');
					$color = ( isset( $attr->overlay ) ? $attr->overlay : 'transparent');
					$locsecond = ( isset( $attr->overlayGradLocSecond ) ? $attr->overlayGradLocSecond : '100');
					$colorsecond = ( isset( $attr->overlaySecond ) ? $attr->overlaySecond : '#00B5E2');
					$css .= 'background-image: ' . $type . '-gradient(' . $angle. ', ' . $color . ' ' . $loc . '%, ' . $colorsecond . ' ' . $locsecond . '%);';
				} else {
					if ( isset( $attr->overlay ) ) {
						$css .= 'background-color:' . $attr->overlay . ';';
					}
					if ( isset( $attr->overlayBgImg ) ) {
						$css .= 'background-image:url(' . $attr->overlayBgImg . ');';
						$css .= 'background-size:' . ( isset( $attr->overlayBgImgSize ) ? $attr->overlayBgImgSize : 'cover' ) . ';';
						$css .= 'background-position:' . ( isset( $attr->overlayBgImgPosition ) ? $attr->overlayBgImgPosition : 'center center' ) . ';';
						$css .= 'background-attachment:' . ( isset( $attr->overlayBgImgAttachment ) ? $attr->overlayBgImgAttachment : 'scroll' ) . ';';
						$css .= 'background-repeat:' . ( isset( $attr->overlayBgImgRepeat ) ? $attr->overlayBgImgRepeat : 'no-repeat' ) . ';';
					}
				}
				if ( isset( $attr->overlayBlendMode ) ) {
					$css .= 'mix-blend-mode:' . $attr->overlayBlendMode . ';';
				}
			$css .= '}';
		}
		if ( isset( $attr->topPaddingM ) || isset( $attr->bottomPaddingM ) || isset( $attr->leftPaddingM ) || isset( $attr->rightPaddingM ) || isset( $attr->topMarginM ) || isset( $attr->bottomMarginM ) ) {
			$css .= '@media (max-width: 767px) {';
				if ( isset( $attr->topMarginM ) || isset( $attr->bottomMarginM ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' {';
						if ( isset( $attr->topMarginM ) ) {
							$css .= 'margin-top:' . $attr->topMarginM . 'px;';
						}
						if ( isset( $attr->bottomMarginM ) ) {
							$css .= 'margin-bottom:' . $attr->bottomMarginM . 'px;';
						}
					$css .= '}';
				}
				if ( isset( $attr->topPaddingM ) || isset( $attr->bottomPaddingM ) || isset( $attr->leftPaddingM ) || isset( $attr->rightPaddingM ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap {';
					if ( isset( $attr->topPaddingM ) ) {
						$css .= 'padding-top:' . $attr->topPaddingM . 'px;';
					}
					if ( isset( $attr->bottomPaddingM ) ) {
						$css .= 'padding-bottom:' . $attr->bottomPaddingM . 'px;';
					}
					if ( isset( $attr->leftPaddingM ) ) {
						$css .= 'padding-left:' . $attr->leftPaddingM . 'px;';
					}
					if ( isset( $attr->rightPaddingM ) ) {
						$css .= 'padding-right:' . $attr->rightPaddingM . 'px;';
					}
					$css .= '}';
				}

			$css .= '}';
		}
		return $css;
	}
	/**
	 * Builds Css for row layout block.
	 *
	 * @param array  $attr the blocks attr.
	 * @param string $unique_id the blocks attr ID.
	 */
	function row_layout_array_css( $attr, $unique_id ) {
		$css = '';

		$minHeightUnit 	= isset( $attr['minHeightUnit']) ? $attr['minHeightUnit'] : 'px';
		$gutterCustom 	= isset( $attr['columnGutterCustom']) ? $attr['columnGutterCustom'] : 30;

		if ( isset( $attr['backgroundType'] ) && $attr['backgroundType'] === 'gradient' && isset($attr['gradientBG']) ) {
			$gradient = $attr['gradientBG'][ 0 ];
			$css.='#kt-layout-id' . $unique_id . ' { ';
			$css .= 'background-image:linear-gradient('.$gradient['angle'].'deg,'.$gradient['colorFirst'].' '.$gradient['locFirst'].'%,'.$gradient['colorSecond'].' '.$gradient['locSecond'].'%) !important;';
			$css .= '}';
		}

		if (isset($attr['backBlendMode'])) {
			$css.='#kt-layout-id' . $unique_id . ' { ';
			$css.='background-blend-mode:'.$attr['backBlendMode'].';';
			$css.='}';
		}

		if (isset($attr['backBlendMode']) && isset($attr['jsParallax'])) {
			$css.='#kt-layout-id' . $unique_id . ' > div[id|=jarallax-container] { ';
			$css.='mix-blend-mode:'.$attr['backBlendMode'].';';
			$css.='}';
		}

		if (isset($attr['columnGutter']) && $attr['columnGutter']==='custom') {
			$css.='@media (min-width: 767px) {';
				$css.='#kt-layout-id' . $unique_id . ' > .kt-gutter-custom > .wp-block-kioken-column:not(:last-child) { ';
				$css.='margin-right:'.$gutterCustom.'px;';
				$css.='}';
			$css.='}';
		}

		//borders
		// if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) ) {
		// 	$css .= '#kt-layout-id' . $unique_id . ' {';
		// 	$css .= 'border-width:' . $attr['borderWidth'][0] . 'px ' . $attr['borderWidth'][1] . 'px ' . $attr['borderWidth'][2] . 'px ' . $attr['borderWidth'][3] . 'px;';
		// 	$css .= '}';
		// }
		if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' {';
				if ( isset($attr['borderWidth'][0]) || isset($attr['borderWidth'][1]) || isset($attr['borderWidth'][2]) || isset($attr['borderWidth'][3])  ) {
					$css .= 'border-width:' . $attr['borderWidth'][0] . 'px ' . $attr['borderWidth'][1] . 'px ' . $attr['borderWidth'][2] . 'px ' . $attr['borderWidth'][3] . 'px;';
				} else {
					$css .= '';
				}
			$css .= '}';
		}

		// if ( isset( $attr['borderRadius'] ) && is_array( $attr['borderRadius'] ) ) {
		// 	$css .= '#kt-layout-id' . $unique_id . ' {';
		// 		$css .= 'border-radius:' . $attr['borderRadius'][0] . 'px ' . $attr['borderRadius'][1] . 'px ' . $attr['borderRadius'][2] . 'px ' . $attr['borderRadius'][3] . 'px;';
		// 	$css .= '}';
		// }
		if ( isset( $attr['borderRadius'] ) && is_array( $attr['borderRadius'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' {';
				if ( isset($attr['borderRadius'][0]) || isset($attr['borderRadius'][1]) || isset($attr['borderRadius'][2]) || isset($attr['borderRadius'][3])  ) {
					$css .= 'border-radius:' . $attr['borderRadius'][0] . 'px ' . $attr['borderRadius'][1] . 'px ' . $attr['borderRadius'][2] . 'px ' . $attr['borderRadius'][3] . 'px;';
				} else {
					$css .= '';
				}
			$css .= '}';
		}
		if ( isset( $attr['borderColor'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' {';
			$css .= 'border-color:' . $attr['borderColor'] . ';';
			$css .= '}';
		}

		if ( isset( $attr['bgColor'] ) || isset( $attr['bgImg'] ) || isset( $attr['topMargin'] ) || isset( $attr['bottomMargin'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' {';
			if ( isset( $attr['topMargin'] ) ) {
				$css .= 'margin-top:' . $attr['topMargin'] . 'px;';
			}
			if ( isset( $attr['bottomMargin'] ) ) {
				$css .= 'margin-bottom:' . $attr['bottomMargin'] . 'px;';
			}
			if ( isset( $attr['bgColor'] ) ) {
				$css .= 'background-color:' . $attr['bgColor'] . ';';
			}
			if ( isset( $attr['bgImg'] ) ) {
				$css .= 'background-image:url(' . $attr['bgImg'] . ');';
				$css .= 'background-size:' . ( isset( $attr['bgImgSize'] ) ? $attr['bgImgSize'] : 'cover' ) . ';';
				$css .= 'background-position:' . ( isset( $attr['bgImgPosition'] ) ? $attr['bgImgPosition'] : 'center center' ) . ';';
				$css .= 'background-attachment:' . ( isset( $attr['bgImgAttachment'] ) ? $attr['bgImgAttachment'] : 'scroll' ) . ';';
				$css .= 'background-repeat:' . ( isset( $attr['bgImgRepeat'] ) ? $attr['bgImgRepeat'] : 'no-repeat' ) . ';';
			}
			$css .= '}';
		}
		if ( isset( $attr['textColor'] ) ) {
			$css .= '.kt-layout-id' . $unique_id . ', .kt-layout-id' . $unique_id . ' h1, .kt-layout-id' . $unique_id . ' h2, .kt-layout-id' . $unique_id . ' h3, .kt-layout-id' . $unique_id . ' h4, .kt-layout-id' . $unique_id . ' h5, .kt-layout-id' . $unique_id . ' h6 {';
				$css .= 'color:' . $attr['textColor'] . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkColor'] ) ) {
			$css .= '.kt-layout-id' . $unique_id . ' a {';
				$css .= 'color:' . $attr['linkColor'] . ';';
			$css .= '}';
		}
		if ( isset( $attr['linkHoverColor'] ) ) {
			$css .= '.kt-layout-id' . $unique_id . ' a:hover, .entry-content .kt-layout-id' . $unique_id . ' a:hover  {';
				$css .= 'color:' . $attr['linkHoverColor'] . ';';
			$css .= '}';
		}

		if ( isset( $attr['bottomSep'] ) && 'none' != $attr['bottomSep'] ) {
			if ( isset( $attr['bottomSepHeight'] ) || isset( $attr['bottomSepWidth'] ) ) {
				if ( isset( $attr['bottomSepHeight'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
						$css .= 'height:' . $attr['bottomSepHeight'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['bottomSepWidth'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
						$css .= 'width:' . $attr['bottomSepWidth'] . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr['bottomSepHeightTablet'] ) || isset( $attr['bottomSepWidthTablet'] ) ) {
				$css .= '@media (min-width: 768px) and (max-width: 1024px) {';
					if ( isset( $attr['bottomSepHeightTablet'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
							$css .= 'height:' . $attr['bottomSepHeightTablet'] . 'px;';
						$css .= '}';
					}
					if ( isset( $attr['bottomSepWidthTablet'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
							$css .= 'width:' . $attr['bottomSepWidthTablet'] . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
			if ( isset( $attr['bottomSepHeightMobile'] ) || isset( $attr['bottomSepWidthMobile'] ) ) {
				$css .= '@media (max-width: 767px) {';
					if ( isset( $attr['bottomSepHeightMobile'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep {';
							$css .= 'height:' . $attr['bottomSepHeightMobile'] . 'px;';
						$css .= '}';
					}
					if ( isset( $attr['bottomSepWidthMobile'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-bottom-sep svg {';
							$css .= 'width:' . $attr['bottomSepWidthMobile'] . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
		}
		if ( isset( $attr['topSep'] ) && 'none' != $attr['topSep'] ) {
			if ( isset( $attr['topSepHeight'] ) || isset( $attr['topSepWidth'] ) ) {
				if ( isset( $attr['topSepHeight'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
						$css .= 'height:' . $attr['topSepHeight'] . 'px;';
					$css .= '}';
				}
				if ( isset( $attr['topSepWidth'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
						$css .= 'width:' . $attr['topSepWidth'] . '%;';
					$css .= '}';
				}
			}
			if ( isset( $attr['topSepHeightTablet'] ) || isset( $attr['topSepWidthTablet'] ) ) {
				$css .= '@media (min-width: 767px) and (max-width: 1024px) {';
					if ( isset( $attr['topSepHeightTablet'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
							$css .= 'height:' . $attr['topSepHeightTablet'] . 'px;';
						$css .= '}';
					}
					if ( isset( $attr['topSepWidthTablet'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
							$css .= 'width:' . $attr['topSepWidthTablet'] . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
			if ( isset( $attr['topSepHeightMobile'] ) || isset( $attr['topSepWidthMobile'] ) ) {
				$css .= '@media (max-width: 767px) {';
					if ( isset( $attr['topSepHeightMobile'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep {';
							$css .= 'height:' . $attr['topSepHeightMobile'] . 'px;';
						$css .= '}';
					}
					if ( isset( $attr['topSepWidthMobile'] ) ) {
						$css .= '#kt-layout-id' . $unique_id . ' .kio-row-layout-top-sep svg {';
							$css .= 'width:' . $attr['topSepWidthMobile'] . '%;';
						$css .= '}';
					}
				$css .= '}';
			}
		}
		if ( isset( $attr['topPadding'] ) || isset( $attr['bottomPadding'] ) || isset( $attr['leftPadding'] ) || isset( $attr['rightPadding'] ) || isset( $attr['minHeight'] ) ||  isset( $attr['maxWidth'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap {';
				if ( isset( $attr['topPadding'] ) ) {
					$css .= 'padding-top:' . $attr['topPadding'] . 'px;';
				}
				if ( isset( $attr['bottomPadding'] ) ) {
					$css .= 'padding-bottom:' . $attr['bottomPadding'] . 'px;';
				}
				if ( isset( $attr['leftPadding'] ) ) {
					$css .= 'padding-left:' . $attr['leftPadding'] . 'px;';
				}
				if ( isset( $attr['rightPadding'] ) ) {
					$css .= 'padding-right:' . $attr['rightPadding'] . 'px;';
				}
				if ( isset( $attr['minHeight'] ) ) {
					$css .= 'min-height:' . $attr['minHeight'] . $minHeightUnit . ';';
				}
				if ( isset( $attr['maxWidth'] ) ) {
					$css .= 'max-width:' . $attr['maxWidth'] . 'px;';
					$css .= 'margin-left:auto;';
					$css .= 'margin-right:auto;';
				}
			$css .= '}';
		}
		if ( isset( $attr['overlay'] ) || isset( $attr['overlayBgImg'] ) || isset( $attr['overlaySecond'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kio-row-layout-overlay {';
				if ( isset( $attr['overlayOpacity'] ) ) {
					if ( $attr['overlayOpacity'] < 10 ) {
						$css .= 'opacity:0.0' . $attr['overlayOpacity'] . ';';
					} else if ( $attr['overlayOpacity'] >= 100 ) {
						$css .= 'opacity:1;';
					} else {
						$css .= 'opacity:0.' . $attr['overlayOpacity'] . ';';
					}
				}
				if ( isset( $attr['currentOverlayTab'] ) && 'grad' == $attr['currentOverlayTab'] ) {
					$type = ( isset( $attr['overlayGradType'] ) ? $attr['overlayGradType'] : 'linear');
					if ( 'radial' === $type ) {
						$angle = ( isset( $attr['overlayBgImgPosition'] ) ? 'at ' . $attr['overlayBgImgPosition'] : 'at center center' );
					} else {
						$angle = ( isset( $attr['overlayGradAngle'] ) ? $attr['overlayGradAngle'] . 'deg' : '180deg');
					}
					$loc = ( isset( $attr['overlayGradLoc'] ) ? $attr['overlayGradLoc'] : '0');
					$color = ( isset( $attr['overlay'] ) ? $attr['overlay'] : 'transparent');
					$locsecond = ( isset( $attr['overlayGradLocSecond'] ) ? $attr['overlayGradLocSecond'] : '100');
					$colorsecond = ( isset( $attr['overlaySecond'] ) ? $attr['overlaySecond'] : '#00B5E2');
					$css .= 'background-image: ' . $type . '-gradient(' . $angle. ', ' . $color . ' ' . $loc . '%, ' . $colorsecond . ' ' . $locsecond . '%);';
				} else {
					if ( isset( $attr['overlay'] ) ) {
						$css .= 'background-color:' . $attr['overlay'] . ';';
					}
					if ( isset( $attr['overlayBgImg'] ) ) {
						$css .= 'background-image:url(' . $attr['overlayBgImg'] . ');';
						$css .= 'background-size:' . ( isset( $attr['overlayBgImgSize'] ) ? $attr['overlayBgImgSize'] : 'cover' ) . ';';
						$css .= 'background-position:' . ( isset( $attr['overlayBgImgPosition'] ) ? $attr['overlayBgImgPosition'] : 'center center' ) . ';';
						$css .= 'background-attachment:' . ( isset( $attr['overlayBgImgAttachment'] ) ? $attr['overlayBgImgAttachment'] : 'scroll' ) . ';';
						$css .= 'background-repeat:' . ( isset( $attr['overlayBgImgRepeat'] ) ? $attr['overlayBgImgRepeat'] : 'no-repeat' ) . ';';
					}
				}
				if ( isset( $attr['overlayBlendMode'] ) ) {
					$css .= 'mix-blend-mode:' . $attr['overlayBlendMode'] . ';';
				}
			$css .= '}';
		}
		if ( isset( $attr['topPaddingM'] ) || isset( $attr['bottomPaddingM'] ) || isset( $attr['leftPaddingM'] ) || isset( $attr['rightPaddingM'] ) || isset( $attr['topMarginM'] ) || isset( $attr['bottomMarginM'] ) ) {
			$css .= '@media (max-width: 767px) {';
				if ( isset( $attr['topMarginM'] ) || isset( $attr['bottomMarginM'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' {';
						if ( isset( $attr['topMarginM'] ) ) {
							$css .= 'margin-top:' . $attr['topMarginM'] . 'px;';
						}
						if ( isset( $attr['bottomMarginM'] ) ) {
							$css .= 'margin-bottom:' . $attr['bottomMarginM'] . 'px;';
						}
					$css .= '}';
				}
				if ( isset( $attr['topPaddingM'] ) || isset( $attr['bottomPaddingM'] ) || isset( $attr['leftPaddingM'] ) || isset( $attr['rightPaddingM'] ) ) {
					$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap {';
					if ( isset( $attr['topPaddingM'] ) ) {
						$css .= 'padding-top:' . $attr['topPaddingM'] . 'px;';
					}
					if ( isset( $attr['bottomPaddingM'] ) ) {
						$css .= 'padding-bottom:' . $attr['bottomPaddingM'] . 'px;';
					}
					if ( isset( $attr['leftPaddingM'] ) ) {
						$css .= 'padding-left:' . $attr['leftPaddingM'] . 'px;';
					}
					if ( isset( $attr['rightPaddingM'] ) ) {
						$css .= 'padding-right:' . $attr['rightPaddingM'] . 'px;';
					}
					$css .= '}';
				}

			$css .= '}';
		}
		return $css;
	}
	/**
	 * Builds CSS for column layout block.
	 *
	 * @param object $attr the blocks attr.
	 * @param string $unique_id the blocks parent attr ID.
	 * @param number $column_key the blocks key.
	 */
	/*function column_layout_css( $attr, $unique_id, $column_key ) {
		$css = '';
		if (isset($attr['backBlendMode']) && isset($attr['backgroundImg']) && isset($attr['jsParallax'])) {

				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-absolute-bg > div[id|=jarallax-container] { ';
				$css.='mix-blend-mode:'.$attr['backBlendMode'].';';
				$css.='}';
		}
		if ( isset( $attr->innerPadding ) || isset( $attr->topMargin ) || isset( $attr->bottomMargin ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';

				if ( isset(  $attr->innerPadding ) && ! empty(  $attr->innerPadding ) && is_array(  $attr->innerPadding ) ) {
					$css .= 'padding:' .  $attr->innerPadding[0] . 'px ' .  $attr->innerPadding[1] . 'px ' .  $attr->innerPadding[2] . 'px ' .  $attr->innerPadding[3] . 'px ;';
				}
				if ( isset( $attr->topMargin ) ) {
					$css .= 'margin-top:' . $attr->topMargin . 'px;';
				}
				if ( isset( $attr->bottomMargin ) ) {
					$css .= 'margin-bottom:' . $attr->bottomMargin . 'px;';
				}
			$css .= '}';
		}
		if ( isset( $attr->topPaddingM ) || isset( $attr->bottomPaddingM ) || isset( $attr->leftPaddingM ) || isset( $attr->rightPaddingM ) || isset( $attr->topMarginM ) || isset( $attr->bottomMarginM ) ) {
			$css .= '@media (max-width: 767px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
				if ( isset( $attr->topPaddingM ) ) {
					$css .= 'padding-top:' . $attr->topPaddingM . 'px;';
				}
				if ( isset( $attr->bottomPaddingM ) ) {
					$css .= 'padding-bottom:' . $attr->bottomPaddingM . 'px;';
				}
				if ( isset( $attr->leftPaddingM ) ) {
					$css .= 'padding-left:' . $attr->leftPaddingM . 'px;';
				}
				if ( isset( $attr->rightPaddingM ) ) {
					$css .= 'padding-right:' . $attr->rightPaddingM . 'px;';
				}
				if ( isset( $attr->topMarginM ) ) {
					$css .= 'margin-top:' . $attr->topMarginM . 'px;';
				}
				if ( isset( $attr->bottomMarginM ) ) {
					$css .= 'margin-bottom:' . $attr->bottomMarginM . 'px;';
				}
				$css .= '}';
			$css .= '}';
		}
		return $css;
	}*/
	 /**
 	 * Builds CSS for column layout block.
 	 *
 	 * @param array  $attr the blocks attr.
 	 * @param string $unique_id the blocks parent attr ID.
 	 * @param number $column_key the blocks key.
 	 */
 	function column_layout_array_css( $attr, $unique_id, $column_key ) {
 		$css = '';

		$colMaxWidthSizeType = isset($attr['colMaxWidthSizeType']) ? $attr['colMaxWidthSizeType'] : 'px';

		if ( isset( $attr['textColorOnHover'] ) ) {
			$colorHover = $attr['textColorOnHover'];
			$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col:hover { ';
				$css .= 'color:'.$colorHover.' !important;';
			$css .= '}';
		}

		if ( isset( $attr['backgroundType'] ) && $attr['backgroundType'] === 'gradient' && isset($attr['gradientBG']) ) {
			$gradient = $attr['gradientBG'][ 0 ];
			$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col.has-gradient-bg::before { ';
				$css .= 'background-image:linear-gradient('.$gradient['angle'].'deg,'.$gradient['colorFirst'].' '.$gradient['locFirst'].'%,'.$gradient['colorSecond'].' '.$gradient['locSecond'].'%) !important;';
			$css .= '}';
		}

		//lift and scale
		if ( ( isset( $attr['scaleElement'] ) && $attr['scaleElement'] === true ) || ( isset( $attr['liftElement'] ) && $attr['liftElement'] === true ) ) {
			//scale only
			if ( isset( $attr['hoverScaleSize'] ) && !isset( $attr['liftElement'] ) ) {
				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col:hover { ';
				$css .= 'transform:scale3d('.$attr['hoverScaleSize'].','.$attr['hoverScaleSize'].','.$attr['hoverScaleSize'].');';
				$css .= '}';
			}
			//lift only
			if ( isset( $attr['hoverLiftSize'] ) && !isset( $attr['scaleElement'] ) ) {
				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col:hover { ';
				$css .= 'transform:translate3d(0,'.$attr['hoverLiftSize'].'px,0);';
				$css .= '}';
			}
			//both
			if ( isset( $attr['liftElement'] ) && isset( $attr['scaleElement'] ) && isset( $attr['hoverLiftSize'] ) && isset( $attr['hoverScaleSize'] ) ) {
				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col:hover { ';
				$css .= 'transform:translate3d(0,'.$attr['hoverLiftSize'].'px,0) scale3d('.$attr['hoverScaleSize'].','.$attr['hoverScaleSize'].','.$attr['hoverScaleSize'].');';
				$css .= '}';
			}
		}

		//shadows
		if ( isset( $attr['displayShadow'] ) && ! empty( $attr['displayShadow'] ) && true === $attr['displayShadow'] ) {
			if ( isset( $attr['shadow'] ) && is_array( $attr['shadow'] ) && is_array( $attr['shadow'][ 0 ] ) ) {
				$shadow = $attr['shadow'][ 0 ];
				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col { ';
				$css .= 'box-shadow:' . $shadow['hOffset'] . 'px ' . $shadow['vOffset'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['color'] . ';';
				$css .= '}';
			}
			if ( isset( $attr['shadowHover'] ) && is_array( $attr['shadowHover'] ) && is_array( $attr['shadowHover'][ 0 ] ) ) {
				$shadow_hover = $attr['shadowHover'][ 0 ];
				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col:hover { ';
				$css .= 'box-shadow:' . $shadow_hover['hOffset'] . 'px ' . $shadow_hover['vOffset'] . 'px ' . $shadow_hover['blur'] . 'px ' . $shadow_hover['spread'] . 'px ' . $shadow_hover['color'] . ';';
				$css .= '}';
			} else {
				// $css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col { ';
				// $css .= 'box-shadow:0px 0px 14px 0px rgba(0,0,0,0.2);';
				// $css .= '}';
			}
		}

		//column max width
		if (isset($attr['colMaxWidth']) ) {

				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col { ';
				$css.='max-width:'.$attr['colMaxWidth'].$colMaxWidthSizeType.';';
				$css.='}';
		}

		//column max width
		if (isset($attr['customColWidth']) ) {
			$css.= '@media (min-width: 767px) {';
				$css.= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' { ';
				$css.= 'flex:0 0 ' . $attr['customColWidth'] . '%;';
				$css.= '}';
			$css.= '}';
		}

		if (isset($attr['backBlendMode']) && isset($attr['backgroundImg']) && isset($attr['jsParallax'])) {

				$css.='#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-absolute-bg > div[id|=jarallax-container] { ';
				$css.='mix-blend-mode:'.$attr['backBlendMode'].';';
				$css.='}';
		}

		//borders
		if ( isset( $attr['borderWidth'] ) && is_array( $attr['borderWidth'] ) ) {
			$borderW = $attr['borderWidth'];
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
			if ( isset( $borderW[0] ) && !isset( $borderW[1] ) && !isset( $borderW[2] ) && !isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px;';
			} elseif( isset( $borderW[0] ) && isset( $borderW[1] ) && isset( $borderW[2] ) && isset( $borderW[3] ) ) {
				$css .= 'border-width:' . $borderW[0] . 'px ' . $borderW[1] . 'px ' . $borderW[2] . 'px ' . $borderW[3] . 'px;';
			}
			$css .= '}';
		}

		if ( isset( $attr['borderRadius'] ) && is_array( $attr['borderRadius'] ) ) {
			$radius = $attr['borderRadius'];
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
				if ( isset( $radius[0] ) && !isset( $radius[1] ) && !isset( $radius[2] ) && !isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px;';
				} elseif( isset( $radius[0] ) && isset( $radius[1] ) && isset( $radius[2] ) && isset( $radius[3] ) ) {
					$css .= 'border-radius:' . $radius[0] . 'px ' . $radius[1] . 'px ' . $radius[2] . 'px ' . $radius[3] . 'px;';
				}
			$css .= '}';
		}
		if ( isset( $attr['borderColor'] ) ) {
			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
			$css .= 'border-color:' . $attr['borderColor'] . ';';
			$css .= '}';
		}

		//z-index
 		if ( isset( $attr['zIndex'] ) ) {
 			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' {';
 				if ( isset( $attr['zIndex'] ) ) {
 					$css .= 'z-index:' . $attr['zIndex'] . ';';
 				}
 			$css .= '}';
 		}

		//default padding/margin values
 		if ( isset( $attr['innerPadding'] ) || isset( $attr['topMargin'] ) || isset( $attr['bottomMargin'] ) || isset( $attr['rightMargin'] ) || isset( $attr['leftMargin'] ) ) {
 			$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';

				if ( isset(  $attr['innerPadding'] ) && ! empty(  $attr['innerPadding'] ) && is_array(  $attr['innerPadding'] ) ) {
					isset($attr['innerPadding'][0]) ? $css .= 'padding:' .  $attr['innerPadding'][0] . 'px ' .  $attr['innerPadding'][1] . 'px ' .  $attr['innerPadding'][2] . 'px ' .  $attr['innerPadding'][3] . 'px ;' : '';
				}
 				/*if ( isset( $attr['topPadding'] ) ) {
 					$css .= 'padding-top:' . $attr['topPadding'] . 'px;';
 				}
 				if ( isset( $attr['bottomPadding'] ) ) {
 					$css .= 'padding-bottom:' . $attr['bottomPadding'] . 'px;';
 				}
 				if ( isset( $attr['leftPadding'] ) ) {
 					$css .= 'padding-left:' . $attr['leftPadding'] . 'px;';
 				}
 				if ( isset( $attr['rightPadding'] ) ) {
 					$css .= 'padding-right:' . $attr['rightPadding'] . 'px;';
 				}*/
 				if ( isset( $attr['topMargin'] ) ) {
 					$css .= 'margin-top:' . $attr['topMargin'] . 'px;';
 				}
 				if ( isset( $attr['bottomMargin'] ) ) {
 					$css .= 'margin-bottom:' . $attr['bottomMargin'] . 'px;';
 				}
 				if ( isset( $attr['rightMargin'] ) ) {
 					$css .= 'margin-right:' . $attr['rightMargin'] . 'px;';
 				}
 				if ( isset( $attr['leftMargin'] ) ) {
 					$css .= 'margin-left:' . $attr['leftMargin'] . 'px;';
 				}
 			$css .= '}';
 		}

		//desktop padding
		if ( isset($attr['innerPaddingLG']) && is_array($attr['innerPaddingLG']) ) {
			$css .= '@media (min-width: 1024px) and (max-width: 1024px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['innerPaddingControlLG'] ) ) {
						$css .= isset($attr['innerPaddingLG'][0]) && $attr['innerPaddingLG'][0] !=='' ? 'padding:' . $attr['innerPaddingLG'][0] . 'px;' : '';
					} else {
						$css .= 'padding:' . $attr['innerPaddingLG'][0] . 'px ' . $attr['innerPaddingLG'][1] . 'px ' . $attr['innerPaddingLG'][2] . 'px ' . $attr['innerPaddingLG'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}
		//desktop margin
		if ( isset( $attr['marginLG'] ) && is_array($attr['marginLG']) ) {
			$css .= '@media (min-width: 1024px) and (max-width: 1024px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['marginControlLG'] ) ) {
						$css .= isset($attr['marginLG'][0]) && $attr['marginLG'][0] !=='' ? 'margin:' . $attr['marginLG'][0] . 'px;' : '';
					} else {
						$css .= 'margin:' . $attr['marginLG'][0] . 'px ' . $attr['marginLG'][1] . 'px ' . $attr['marginLG'][2] . 'px ' . $attr['marginLG'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}

		//tablet padding
		if ( isset( $attr['innerPaddingMD'] ) && is_array( $attr['innerPaddingMD'] ) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1023px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['innerPaddingControlMD'] ) ) {
						$css .= isset($attr['innerPaddingMD'][0]) && $attr['innerPaddingMD'][0] !=='' ? 'padding:' . $attr['innerPaddingMD'][0] . 'px;' : '';
					} else {
						$css .= 'padding:' . $attr['innerPaddingMD'][0] . 'px ' . $attr['innerPaddingMD'][1] . 'px ' . $attr['innerPaddingMD'][2] . 'px ' . $attr['innerPaddingMD'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}
		//tablet margin
		if ( isset( $attr['marginMD'] ) && is_array($attr['marginMD']) ) {
			$css .= '@media (min-width: 767px) and (max-width: 1023px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['marginControlMD'] ) ) {
						$css .= isset($attr['marginMD'][0]) && $attr['marginMD'][0] !=='' ? 'margin:' . $attr['marginMD'][0] . 'px;' : '';
					} else {
						$css .= 'margin:' . $attr['marginMD'][0] . 'px ' . $attr['marginMD'][1] . 'px ' . $attr['marginMD'][2] . 'px ' . $attr['marginMD'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}


		//mobile padding
		if ( isset( $attr['innerPaddingSM'] ) && is_array($attr['innerPaddingSM']) ) {

			$css .= '@media (max-width: 576px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['innerPaddingControlSM'] ) ) {
						$css .= isset($attr['innerPaddingSM'][0]) && $attr['innerPaddingSM'][0] !=='' ? 'padding:' . $attr['innerPaddingSM'][0] . 'px;' : '';
					} else {
						$css .= 'padding:' . $attr['innerPaddingSM'][0] . 'px ' . $attr['innerPaddingSM'][1] . 'px ' . $attr['innerPaddingSM'][2] . 'px ' . $attr['innerPaddingSM'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}
		//mobile margin
		if ( isset( $attr['marginSM'] ) && is_array($attr['marginSM']) ) {
			$css .= '@media (max-width: 576px) {';
				$css .= '#kt-layout-id' . $unique_id . ' > .kt-row-column-wrap > .inner-column-' . $column_key . ' > .kt-inside-inner-col {';
					if (! isset( $attr['marginControlSM'] ) ) {
						$css .= isset($attr['marginSM'][0]) && $attr['marginSM'][0] !=='' ? 'margin:' . $attr['marginSM'][0] . 'px;' : '';
					} else {
						$css .= 'margin:' . $attr['marginSM'][0] . 'px ' . $attr['marginSM'][1] . 'px ' . $attr['marginSM'][2] . 'px ' . $attr['marginSM'][3] . 'px ;';
					}
				$css .= '}';
			$css .= '}';
		}

 		return $css;
 	}
}
Kioken_Blocks_Frontend::get_instance();