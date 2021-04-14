<?php

class Kioken_Widget_Social_Links extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * List of supported socials
	 *
	 * @var array
	 */
	protected $socials;

	/**
	 * Constructor
	 */
	function __construct() {

	if ( ! function_exists( 'kiokenblocks_icon_url' ) ) :
	function kiokenblocks_icon_url($the_icon = 'search') {

			$icon = '<svg class="feather"><use xlink:href="'.KK_BLOCKS_URL.'widgets/img/brandlogos.svg#'.$the_icon.'"></use></svg>';

			return $icon;
	}

	endif;

		$socials = array(
			'facebook'    => esc_html__( 'Facebook', 'bavarian' ),
			'twitter'     => esc_html__( 'Twitter', 'bavarian' ),
			// 'google-plus' => esc_html__( 'Google Plus', 'bavarian' ),
			// 'pinterest'   => esc_html__( 'Pinterest', 'bavarian' ),
			// 'flickr'      => esc_html__( 'Flickr', 'bavarian' ),
			'instagram'   => esc_html__( 'Instagram', 'bavarian' ),
      'youtube'     => esc_html__( 'Youtube', 'bavarian' ),
      // 'dribbble'    => esc_html__( 'Dribbble', 'bavarian' ),
      'linkedin'    => esc_html__( 'Linkedin', 'bavarian' ),
			'github'      => esc_html__( 'Github', 'bavarian' ),

		);

		$this->socials = apply_filters( 'bavarian_social_media', $socials );
		$this->default = array(
			'title' => '',
		);
		foreach ( $this->socials as $k => $v ) {
			$this->default["{$k}_title"] = $v;
			$this->default["{$k}_url"]   = '';
		}

		parent::__construct(
			'kioken_social_links_widget',
			esc_html__( 'Kioken - Social Links', 'bavarian' ),
			array(
				'classname'   => 'social-links-widget social-links',
				'description' => esc_html__( 'Display links to social media networks.', 'bavarian' ),
				'customizer_support' => true,
			)/*,
			array( 'width' => 600 )*/
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		foreach ( $this->socials as $social => $label ) {
			if ( ! empty( $instance[$social . '_url'] ) ) {
				$icon = /*'youtube' == $social ? 'youtube' : */$social;

				printf(
					'<a href="%s" class="share-%s social d-inline-block text-center mr-3" rel="nofollow" title="%s" target="_blank">'.kiokenblocks_icon_url($icon).'</a>',
					esc_url( $instance[$social . '_url'] ),
					esc_attr( $social ),
					esc_attr( $instance[$social . '_title'] ),
					esc_attr( $icon )
				);
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'bavarian' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		foreach ( $this->socials as $social => $label ) {
			printf(
				'<div style="width: 280px; float: left; margin-right: 10px;">
					<label>%s</label>
					<p><input type="text" class="widefat" name="%s" placeholder="%s" value="%s"></p>
				</div>',
				$label,
				$this->get_field_name( $social . '_url' ),
				esc_html__( 'URL', 'bavarian' ),
				$instance[$social . '_url']
			);
		}
	}
}
