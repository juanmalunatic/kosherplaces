<?php

/**
 * Block render for server-side.
 *
 * This method will pe passed to the render_callback parameter and it will output
 * the server side output of the block.
 *
 * @return mixed|string
 */

 // Exit if accessed directly.
 if ( ! defined( 'ABSPATH' ) ) {
 	exit;
 }

function kioken_render_kineticposts_block( $attributes ) {

	$query_args = array(
		'posts_per_page'      => ( isset( $attributes['postsToShow'] ) ) ? $attributes['postsToShow'] : 6,
		'post_status'         => 'publish',
		'post_type'           => ( isset( $attributes['postType'] ) ) ? $attributes['postType'] : 'post',
		'order'               => ( isset( $attributes['order'] ) ) ? $attributes['order'] : 'desc',
		'orderby'             => ( isset( $attributes['orderBy'] ) ) ? $attributes['orderBy'] : 'date',
		'ignore_sticky_posts' => 1,
	);

	if ( isset( $attributes['categories'] ) && '' != $attributes['categories'] ) {
		$query_args['tax_query'][] = array(
			'taxonomy' => ( isset( $attributes['taxonomyType'] ) ) ? $attributes['taxonomyType'] : 'category',
			'field'    => 'id',
			'terms'    => $attributes['categories'],
			'operator' => 'IN',
		);
	}

	$query_args = apply_filters( "kioken_post_query_args", $query_args );

	// $recent_posts = wp_get_recent_posts( $query_args );
	$recent_posts = new WP_Query( $query_args );

	$list_items_markup 		= '';
	$swiper_class			= '';
	$titleTag			    = isset( $attributes['titleTag'] ) ? 'h' . $attributes['titleTag'] : 'h4';


	if ( isset($attributes['pLayout']) && $attributes['pLayout']=="carousel" ) {
		$list_items_markup .= '<div class="swiper-container"><div class="swiper-wrapper">';
		$swiper_class				= 'swiper-slide ';
	}

	$post_row_class				= '';
	// Zoom Effect
	if ( isset( $attributes['imgZoomEffect'] ) ) {
		$post_row_class				.= 'imgzoom-' . $attributes['imgZoomEffect'] . ' ';
	}
	// Vertical alignment of the post row
	if ( isset($attributes['pLayout']) && $attributes['pLayout']=="list" ) {
		$post_row_class				.= $attributes['vAlign']=="center" ? 'kt_flex_aligncenter ' : '';
	}

	// Animated Content
	if ( isset($attributes['postDesign']) && 'imagebox' === $attributes['postDesign'] && isset($attributes['animator']) && $attributes['animator'] ) {
		$post_row_class				.= ' contents-animated' . ' behave-'. $attributes['animBehavior'];
		$post_row_class				.= isset( $attributes['flip3d'] ) && $attributes['flip3d'] ? ' flip3d' : '';
	}

    /*=============================================>>>>>
    = The LOOP =
    ===============================================>>>>>*/
    if ( $recent_posts->have_posts() ) {

        while ( $recent_posts->have_posts() ) {

            $recent_posts->the_post();
        	// foreach ( $recent_posts as $post ) { //no longer used - converted to while()

    		$id = get_the_ID();
    		$thesize = isset( $attributes['imgSize'] ) ? $attributes['imgSize'] : 'large';
    		$thumbnail_check = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $thesize );
    		$thumbnail_check ? $thumbnail = $thumbnail_check : $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'medium' );
    		$category = get_the_category( $id );

    		$has_thumb = has_post_thumbnail($id) ? true : false;

            $thumb_tag = '';

            if ( ! empty($thumbnail) ) {
                $thumb_tag = get_the_post_thumbnail( $id, $thesize, array( 'alt' => esc_html( get_the_title( $id ) ) ) );
            } else {
                $thumb_tag = '<img src="https://kk-elements.s3.eu-central-1.amazonaws.com/images/empty2.svg" alt="" />';
            }

    		if ( isset( $attributes['postDesign'] ) && 'imagebox' == $attributes['postDesign'] ) {

    			if ( ! defined( 'KK_BLOCKS_ZENGIN' ) ) {
    				return;
    			}



    			/*=============================================>>>>>
    			= Image Box Post Designs =
    			===============================================>>>>>*/
    			// if ( $has_thumb ) {
    				$list_items_markup .= '<article class="'.$swiper_class.'grid-post grid-' . $attributes['columns'] . '"><div class="grid-post-row '.$post_row_class.'">';

    				//Featured Image
    				if ( isset( $attributes['displayFeaturedImage'] ) && $attributes['displayFeaturedImage'] ) {

    						$list_items_markup .= '<div class="grid-image-area"><div class="post-image">
    							<a class="kb_posa_full" href="' . esc_url( get_the_permalink( $id ) ) . '"></a>';

    							$list_items_markup .= '<div class="kt_d_flex kt_flex_alignbottom kb_posa_full info-container">';
    								$list_items_markup .= '<div class="kb-inner">';
    								//Date
    								if ( ( isset( $attributes['displayDate'] ) && $attributes['displayDate'] ) ) {
    									$list_items_markup .= sprintf(
    										'<p><time datetime="%1$s"> %2$s </time></p>',
    										esc_attr( get_the_date( 'c', $id ) ),
    										esc_html( get_the_date( 'j F, Y', $id ) )
    									);
    								}
    								//Title
    								$list_items_markup .= sprintf(
    									'<h'. $titleTag .' class="grid-content-title">%1$s</h'. $titleTag .'>',
    									esc_html( get_the_title( $id ) )
    								);

    								//Arrow and Line
    								$list_items_markup .= '<svg class="next-arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 39 12"><line class="top" x1="23" y1="-0.5" x2="29.5" y2="6.5"></line><line class="bottom" x1="23" y1="12.5" x2="29.5" y2="5.5"></line></svg>';
    								$list_items_markup .= '<span class="line"></span>';

    								$list_items_markup .= '</div><!-- end inner -->';
    							$list_items_markup .= '</div><!-- end info container -->';
    							$list_items_markup .= '<div class="kb_posa_full post-overlay"></div>';

    						// $list_items_markup .= get_the_post_thumbnail( $id, $thesize, array( 'alt' => esc_html( get_the_title( $id ) ) ) );
    						$list_items_markup .= $thumb_tag;
    				}
    				$list_items_markup .= '
    					</div></div>
    				</div></article>';
    			// }

    		} else {
    			/*=============================================>>>>>
    			= Default Post Designs =
    			===============================================>>>>>*/
    			$list_items_markup .= '<article class="'.$swiper_class.'grid-post grid-' . $attributes['columns'] . '"><div class="grid-post-row '.$post_row_class.'">';

    			//Featured Image
    			if ( isset( $attributes['displayFeaturedImage'] ) && $attributes['displayFeaturedImage'] ) {


    					$list_items_markup .= sprintf(
    						'<div class="grid-image-area"><div class="post-image">
    							<a href="%1$s">',
    						esc_url( get_the_permalink( $id ) )
    					);

    					// $list_items_markup .= get_the_post_thumbnail($id, $thesize, array('alt' => esc_html( get_the_title( $id ) ) ) );
    					$list_items_markup .= $thumb_tag;
    					$list_items_markup .= '</a></div></div>';

    			}

    			//Content Area Start
    			$list_items_markup .= '<div class="grid-content-area' . ( $thumbnail && $attributes['displayFeaturedImage'] ? '' : ' full' ) . '">';

    			$list_items_markup .= '<div class="title-meta-wrap">';
    				//Category
    				if ( $attributes['displayCategory']===true && isset($category[0]) ) {
    					$list_items_markup .= sprintf(
    						'<div class="grid-content-category"><a href="%1$s">%2$s</a></div>',
    						esc_url( get_category_link( $category[0]->term_id ) ),
    						esc_html( $category[0]->cat_name )
    					);
    				}

    				//Title
    				$list_items_markup .= sprintf(
                        '<'. $titleTag .' class="grid-content-title"><a href="%1$s">%2$s</a></'. $titleTag .'>',

    					esc_url( get_the_permalink( $id ) ),
    					esc_html( get_the_title( $id ) )
    				);

    				//Date & Author
    				if ( ( isset( $attributes['displayDate'] ) && $attributes['displayDate'] ) || ( isset( $attributes['displayAuthor'] ) && $attributes['displayAuthor'] ) ) {
    					$list_items_markup .= '<p class="grid-content-meta">';

    					if ( ( isset( $attributes['displayDate'] ) && $attributes['displayDate'] ) ) {

    						$list_items_markup .= sprintf(
    							'<time datetime="%1$s">%2$s %3$s </time>',
    							esc_attr( get_the_date( 'c', $id ) ),
    							__( 'on', 'textdomain' ),
    							esc_html( get_the_date( 'j F, Y', $id ) )
    						);

    					}

    					if ( ( isset( $attributes['displayAuthor'] ) && $attributes['displayAuthor'] ) ) {

    						$list_items_markup .= sprintf(
    							'%1$s <a href="%2$s">%3$s</a>',
    							__( 'by', 'textdomain' ),
    							get_author_posts_url( get_post_field( 'post_author', $id ) ),
    							get_the_author_meta( 'display_name', get_post_field( 'post_author', $id ) )
    						);

    					}

    					$list_items_markup .= '</p>';
    				}

    			$list_items_markup .= '</div>'; //title-meta-wrap closure

    			if ( ( isset( $attributes['excerptLength'] ) && $attributes['excerptLength'] > 0 ) && ( isset( $attributes['displayPostExcerpt'] ) && $attributes['displayPostExcerpt'] ) ) {
    				$list_items_markup .= sprintf(
    					'<p class="grid-content-excerpt">%1$s</p>',
    					get_excerpt_by_id( $id, $attributes['excerptLength'] )
    				);
    			}

    			$list_items_markup .= '</div></div></article>';

    		}

        } //end while

        wp_reset_postdata();

    } // endif


	//carousel closure tags
	if ( isset($attributes['pLayout']) && $attributes['pLayout']=="carousel" ) {
		$list_items_markup .= '</div><!-- swiper wrapper closure -->';
		$list_items_markup .= '</div><!-- swiper container closure -->';
		$list_items_markup .= '<div class="swp_btn_wrap">
			<button class="swp-go-prev"><svg width="28px" height="16px" viewBox="0 0 28 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-664.000000, -1899.000000)" fill="currentColor"><g transform="translate(63.000000, 1479.000000)"><g transform="translate(601.000000, 420.000000)"><path d="M19.4374537,0.250047088 C19.6041202,0.0833805005 19.7812028,4.76837158e-05 19.9687024,4.76837158e-05 C20.1562019,4.76837158e-05 20.3332845,0.0833805005 20.4999511,0.250047088 L27.7499338,7.5000298 C27.9166004,7.6250295 27.9999332,7.79169609 27.9999332,8.00002861 C27.9999332,8.20836113 27.9166004,8.37502772 27.7499338,8.50002742 L20.4999511,15.7500101 C20.3332845,15.9166767 20.1562019,16.0000095 19.9687024,16.0000095 C19.7812028,16.0000095 19.6041202,15.9166767 19.4374537,15.7500101 L18.9999547,15.3125112 C18.8332881,15.1875115 18.7499553,15.0208449 18.7499553,14.8125124 C18.7499553,14.6041798 18.8332881,14.4166803 18.9999547,14.2500137 L24.2499422,9.06252608 L0.749998212,9.06252608 C0.541665693,9.06252608 0.364582146,8.98960927 0.218749478,8.8437766 C0.0729168107,8.69794393 0,8.52086038 0,8.31252787 L0,7.68752936 C0,7.47919684 0.0729168107,7.30211329 0.218749478,7.15628062 C0.364582146,7.01044795 0.541665693,6.93753114 0.749998212,6.93753114 L24.2499422,6.93753114 L18.9999547,1.75004351 C18.8332881,1.58337692 18.7499553,1.39587737 18.7499553,1.18754485 C18.7499553,0.979212334 18.8332881,0.812545747 18.9999547,0.687546045 L19.4374537,0.250047088 Z" transform="translate(13.999967, 8.000029) scale(-1, 1) translate(-13.999967, -8.000029) "></path></g></g></g></g></svg></button>
			<button class="swp-go-next"><svg width="28px" height="16px" viewBox="0 0 28 16" version="1.1" xmlns="http://www.w3.org/2000/svg"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-732.000000, -1899.000000)" fill="currentColor"><g transform="translate(63.000000, 1479.000000)"><g transform="translate(601.000000, 412.000000)"><path d="M87.4374537,8.25004709 C87.6041202,8.0833805 87.7812028,8.00004768 87.9687024,8.00004768 C88.1562019,8.00004768 88.3332845,8.0833805 88.4999511,8.25004709 L95.7499338,15.5000298 C95.9166004,15.6250295 95.9999332,15.7916961 95.9999332,16.0000286 C95.9999332,16.2083611 95.9166004,16.3750277 95.7499338,16.5000274 L88.4999511,23.7500101 C88.3332845,23.9166767 88.1562019,24.0000095 87.9687024,24.0000095 C87.7812028,24.0000095 87.6041202,23.9166767 87.4374537,23.7500101 L86.9999547,23.3125112 C86.8332881,23.1875115 86.7499553,23.0208449 86.7499553,22.8125124 C86.7499553,22.6041798 86.8332881,22.4166803 86.9999547,22.2500137 L92.2499422,17.0625261 L68.7499982,17.0625261 C68.5416657,17.0625261 68.3645821,16.9896093 68.2187495,16.8437766 C68.0729168,16.6979439 68,16.5208604 68,16.3125279 L68,15.6875294 C68,15.4791968 68.0729168,15.3021133 68.2187495,15.1562806 C68.3645821,15.010448 68.5416657,14.9375311 68.7499982,14.9375311 L92.2499422,14.9375311 L86.9999547,9.75004351 C86.8332881,9.58337692 86.7499553,9.39587737 86.7499553,9.18754485 C86.7499553,8.97921233 86.8332881,8.81254575 86.9999547,8.68754604 L87.4374537,8.25004709 Z"></path></g></g></g></g></svg></button>
		</div>';
	}

	$class 				= 'wp-block-kioken-kinetic-posts';
	if ( isset( $attributes['className'] ) ) {
		$class .= ' ' . $attributes['className'];
	}
	$printID 			= isset($attributes['uniqueID']) ? 'id="kt-kinetic-posts_'.$attributes['uniqueID'].'"' : null;
	$sliderParams = '';
	$animatorParams = '';

	if ( isset($attributes['pLayout']) || isset($attributes['metaOrder']) || isset($attributes['align']) || isset($attributes['displayFeaturedImage']) ) {
		$class .= ' is-'.$attributes['pLayout'];
		$class .= isset( $attributes['postPadding'][0] ) && $attributes['postPadding'][0] > 0 ? ' has-post-padding' : '';
		$class .= isset( $attributes['hasKBAnimator'] ) && $attributes['hasKBAnimator'] ? ' has-animator' : '';
		$class .= ' '.$attributes['metaOrder'];
		$class .= ' align'.$attributes['align'];
		$class .= isset( $attributes['columns'] ) && 'masonry' === $attributes['pLayout'] ? ' cols-' . $attributes['columns'] . ' ' : '';
		//carousel classes
		$class .= ( $attributes['pLayout']=='carousel' && $attributes['arrowPos']=='center' ) ? ' arrows-center' : '' ;
		$class .= ( $attributes['pLayout']=='carousel' && $attributes['arrowStyle']=='dark' ) ? ' arrows-dark' : '' ;
		$class .= ( $attributes['pLayout']=='carousel' && $attributes['arrowStyle']=='dark-rounded' ) ? ' arrows-dark-rounded' : '' ;
		$class .= ( $attributes['pLayout']=='carousel' && $attributes['arrowStyle']=='dark-pill' ) ? ' arrows-dark-pill' : '' ;
		$class .= ( $attributes['pLayout']=='carousel' && $attributes['fadeDeactive'] ) ? ' fade-deactive' : '' ;
		//post design classes
		$class .= ( $attributes['displayFeaturedImage'] && $attributes['pLayout']=='list' && $attributes['postDesign']=='stacked' ) ? ' design-is-stacked' : '' ;
		$class .= ( $attributes['displayFeaturedImage'] && $attributes['postDesign']=='stacked-center' ) ? ' design-is-stacked-center' : '' ;
		$class .= ( $attributes['displayFeaturedImage'] && $attributes['postDesign']=='stacked-combo' ) ? ' design-is-stacked-combo' : '' ;
		$class .= ( $attributes['displayFeaturedImage'] && $attributes['postDesign']=='imagebox' ) ? ' design-is-imagebox' : '' ;
	}

	//slider parameters
	if ( isset($attributes['pLayout']) && $attributes['pLayout']=="carousel" ) {
		$sliderParams .='data-speed="'.$attributes['sSpeed'].'" ';
		$sliderParams .='data-perview="'.$attributes['sPerView'].'" ';
		$sliderParams .='data-centerslides="'.($attributes['centerSlides'] ? 'true' : 'false').'" ';
		$sliderParams .='data-spacebtw="'.$attributes['sGap'].'" ';
		$sliderParams .='data-loop="'.($attributes['sLoop'] ? 'true' : 'false').'" ';
		$sliderParams .='data-freemode="'.($attributes['freeSlider'] ? 'true' : 'false').'" ';
		$sliderParams .='data-autoheight="'.($attributes['sAutoHeight'] ? 'true' : 'false').'" ';
	}

	//animator parameters
	if ( isset( $attributes['hasKBAnimator'] ) ) {
		$animatorParams .= 'data-object-id="'.$attributes['uniqueID'].'" ';
		$animatorParams .= isset ( $attributes['trfFrom'] ) ? 'data-anim-from="'.$attributes['trfFrom'].'" ' : '';
		$animatorParams .= 'data-anim-easing="'.$attributes['kbAnimEasing'].'" ';
		$animatorParams .= 'data-anim-effect="'.$attributes['kbAnimEffect'].'" ';
		$animatorParams .= isset ( $attributes['kbAnimRewind'] ) ? 'data-anim-rewind="'.$attributes['kbAnimRewind'].'" ' : '';
		$animatorParams .= isset ( $attributes['kbAnimStagger'] ) && $attributes['kbAnimStagger'] == 1 ? 'data-anim-stagger="'.$attributes['kbAnimStagger'].'" ' : '';
		$animatorParams .= isset ( $attributes['kbAnimStagger'] ) && $attributes['kbAnimStagger'] == 1 ? 'data-anim-stagger-delay="'.$attributes['kbAnimStaggerDelay'].'" ' : '';
		$animatorParams .= 'data-anim-duration="'.$attributes['kbAnimDuration'].'" ';
		$animatorParams .= 'data-anim-distance="'.$attributes['trfDistance'].'" ';
		$animatorParams .= 'data-anim-delay="'.$attributes['trfDelay'].'" ';
		$animatorParams .= 'data-anim-scale-x="'.$attributes['trfScaleX'].'" ';
		$animatorParams .= 'data-anim-scale-y="'.$attributes['trfScaleY'].'" ';
		$animatorParams .= 'data-anim-rotate-x="'.$attributes['trfRotateX'].'" ';
		$animatorParams .= 'data-anim-rotate-y="'.$attributes['trfRotateY'].'" ';
		$animatorParams .= 'data-anim-skew-x="'.$attributes['trfSkewX'].'" ';
		$animatorParams .= 'data-anim-skew-y="'.$attributes['trfSkewY'].'" ';
	}

	$block_content = sprintf(
		'<div %1$s '.$sliderParams.' '.$animatorParams.' class="%2$s">%3$s</div>',
		$printID,
		esc_attr( $class ),
		$list_items_markup
	);

	return $block_content;
}

/**
 * Get post excerpt
 *
 * @return string
 */
function get_excerpt_by_id( $post_id, $excerpt_length = 200 ) {

    $inserted_excerpt = get_the_excerpt( $post_id );

    if ( $inserted_excerpt ) {
        $the_excerpt = $inserted_excerpt;
        $the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) );
    	$the_excerpt = substr( $the_excerpt, 0, $excerpt_length ) . '…';
    } else {
        $the_post = get_post( $post_id );
    	$the_excerpt = $the_post->post_content;
    	$the_excerpt = strip_tags( strip_shortcodes( $the_excerpt ) );
    	$the_excerpt = substr( $the_excerpt, 0, $excerpt_length ) . '…';
    }

	return $the_excerpt;
}

/**
 * Registers the block on server.
 */
function kioken_register_kineticposts_block() {

	// Return early if this function does not exist.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	register_block_type(
		'kioken/kinetic-posts', array(

			'attributes'      => array(
				'grid'    => array( 'type' => 'boolean', 'default' => false, ),
				'uniqueID'  => array( 'type' => 'string', 'default' => '', ),
				'align'  => array( 'type' => 'string', 'default' => 'center', ),
				'vAlign'  => array( 'type' => 'string', 'default' => 'top', ),
				'pLayout'  => array( 'type' => 'string', 'default' => 'list', ),
				'columns'              => array( 'type' => 'number', 'default' => '3', ),
				'categories'           => array( 'type' => 'string', ),
				'postsToShow'          => array( 'type'    => 'number', 'default' => '3', ),
				'order'                => array( 'type'    => 'string', 'default' => 'desc', ),
				'orderBy'              => array( 'type'    => 'string', 'default' => 'date', ),
				'postType'              => array( 'type'    => 'string', 'default' => 'post', ),
				'taxonomyType'              => array( 'type'    => 'string', 'default' => 'category', ),
				'displayFeaturedImage' => array( 'type'    => 'boolean', 'default' => true, ),
				'displayCategory'      => array( 'type'    => 'boolean', 'default' => 'true', ),
				'displayDate'          => array( 'type'    => 'boolean', 'default' => true, ),
				'displayAuthor'        => array( 'type'    => 'boolean', 'default' => true, ),
				'displayPostExcerpt'        => array( 'type'    => 'boolean', 'default' => true, ),
				'excerptLength'        => array( 'type'    => 'number', 'default' => '200', ),
				'mediaMaxH'        => array( 'type'    => 'number', 'default' => '', ),
				'forceMediaHeight' => array( 'type'    => 'boolean', 'default' => false, ),
				'metaOrder' => array( 'type'    => 'string', 'default' => 'order-cat-title-meta', ),
				'imgSize' => array( 'type'    => 'string', 'default' => 'large', ),

				'sGap' => array( 'type'    => 'number', 'default' => '30', ),
				'sPerView' => array( 'type'    => 'number', 'default' => '1', ),
				'sLoop' => array( 'type'    => 'boolean', 'default' => false, ),
				'sAutoHeight' => array( 'type'    => 'boolean', 'default' => false, ),
				'centerSlides' => array( 'type'    => 'boolean', 'default' => false, ),
				'freeSlider' => array( 'type'    => 'boolean', 'default' => false, ),
				'sAutoPlay' => array( 'type'    => 'number', 'default' => '0', ),
				'sSpeed' => array( 'type'    => 'number', 'default' => '.5', ),
				'arrowPos' => array( 'type'    => 'string', 'default' => 'bottom', ),
				'arrowStyle' => array( 'type'    => 'string', 'default' => 'light', ),
				'fadeDeactive' => array( 'type'    => 'boolean', 'default' => false, ),

				'animator' => array( 'type' => 'boolean', 'default' => false, ),
				'flip3d' => array( 'type'    => 'boolean', 'default' => false, ),
				'animBehavior' => array( 'type' => 'string', 'default' => 'onhover', ),

				'textColor' => array( 'type' => 'string', 'default' => '#ffffff', ),
				'textColorHover' => array( 'type' => 'string', 'default' => '#ffffff', ),
				'infoInnerBGColor' => array( 'type' => 'string', 'default' => 'transparent', ),
				'infoInnerHoverBGColor' => array( 'type' => 'string', 'default' => 'transparent', ),
				'imgZoomEffect' => array( 'type' => 'string', 'default' => 'default', ),

				'imgBorderRad' => array( 'type'    => 'number', 'default' => '0', ),
				'postBGColor' => array( 'type'    => 'string', 'default' => 'transparent', ),
				'postPad' => array( 'type'    => 'number', 'default' => '0', ),
				'postDesign' => array( 'type'    => 'string', 'default' => 'default', ),
				'contentBorderRad' => array( 'type'    => 'number', 'default' => '0', ),
				'postContentMaxW' => array( 'type'    => 'number', 'default' => '80', ),
				'titleFontSize' => array( 'type'    => 'number', 'default' => '24', ),
				'titleTag' => array( 'type'    => 'number', 'default' => '4', ),

				'postPadding' => array( 'type'    => 'array', 'items' => [ 'type' => 'number', ], 'default' => array(0, 0, 0, 0) , ),
				'postPaddingControl' => array( 'type'    => 'string', 'default' => 'linked', ),

				//Animator parameters
				'trfFrom' => array( 'type'    => 'string', 'default' => 'vertical', ),
				'kbAnimEasing' => array( 'type'    => 'string', 'default' => 'easeInOut', ),
				'kbAnimEffect' => array( 'type'    => 'string', 'default' => 'Cubic', ),
				'kbAnimRewind' => array( 'type'    => 'boolean', 'default' => false, ),
				'kbAnimStagger' => array( 'type'    => 'boolean', 'default' => false, ),
				'kbAnimStaggerDelay' => array( 'type'    => 'number', 'default' => 100, ),
				'kbAnimDuration' => array( 'type'    => 'number', 'default' => 1, ),
				'trfDistance' => array( 'type'    => 'number', 'default' => 50, ),
				'trfDelay' => array( 'type'    => 'number', 'default' => 0, ),
				'trfScaleX' => array( 'type'    => 'number', 'default' => 1, ),
				'trfScaleY' => array( 'type'    => 'number', 'default' => 1, ),
				'trfRotateX' => array( 'type'    => 'number', 'default' => 0, ),
				'trfRotateY' => array( 'type'    => 'number', 'default' => 0, ),
				'trfSkewX' => array( 'type'    => 'number', 'default' => 0, ),
				'trfSkewY' => array( 'type'    => 'number', 'default' => 0, ),
			),
			'render_callback' => 'kioken_render_kineticposts_block',
		)
	);
}

add_action( 'init', 'kioken_register_kineticposts_block' );