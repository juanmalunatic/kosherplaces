<?php

class Kioken_Widget_About extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Class constructor
	 * Set up the widget
	 */
	function __construct() {
		$this->defaults = array(
			'title'              => '',
      'image_uri'          => '',
      'widget_content'     => '',
		);

		parent::__construct(
			'kioken_about_widget',
			esc_html__( 'Kioken - About', 'bavarian' ),
			array(
				'classname'   => 'widget_kioken_about',
				'description' => esc_html__( 'Display an about information with image.', 'bavarian' ),
        'customizer_support' => true,
			)
		);
	}

	/**
	 * Display widget
	 *
	 * @param array $args     Sidebar configuration
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );


    $image_size				= 'kt-img-md';
    $image_id         = $instance['image_uri'];
    $the_content      = do_shortcode( $instance['widget_content'] );




		echo $args['before_widget'];

    if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }

    if ($image_id) :
      $img_attachment 	= wp_get_attachment_image_src($image_id, $image_size);
      $final_img 				= $img_attachment[0];
      ?>
    <img class="img-fluid" src="<?php echo $final_img; ?>" alt="" />

    <?php
    endif;

    if ($the_content): ?>
    <?php echo wpautop( esc_attr( $the_content ) ); ?>
    <?php
    endif;
		echo $args['after_widget'];
	}

	/**
	 * Update widget
	 *
	 * @param array $new_instance New widget settings
	 * @param array $old_instance Old widget settings
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$new_instance['title']         = strip_tags( $new_instance['title'] );
    $new_instance['image_uri']     = strip_tags( $new_instance['image_uri'] );
    $new_instance['widget_content']= $new_instance['widget_content'] ;



		return $new_instance;
	}

	/**
	 * Display widget settings
	 *
	 * @param array $instance Widget settings
	 *
	 * @return void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );

    $widget_content = isset( $instance['widget_content'] ) ? esc_attr( $instance['widget_content'] ) : '';


    $image_size				= 'kt-img-md';
    $image_id         = $instance['image_uri'];
    $img_attachment 	= wp_get_attachment_image_src($image_id, $image_size);
    $final_img 				= $img_attachment[0];
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'bavarian' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>



    <p>
      <img class="kt_img_preview"
			     src="<?php echo esc_url( $final_img ); ?>" alt="" style="max-width:100%;">

           <label for="<?php echo esc_attr( $this->get_field_id( 'image_uri' ) ); ?>"><?php esc_html_e( 'Image', 'bavarian' ); ?></label><br>
      <input type="text" class="widefat hidden image_src"
        name="<?php echo $this->get_field_name( 'image_uri' ); ?>"
        id="<?php echo $this->get_field_id('image_uri'); ?>"
        value="<?php echo $instance['image_uri']; ?>" style="margin-top:5px;">

      <input type="button" class="button button-primary image_upl custom_media_button" id="upload-<?php echo $this->get_field_id('image_uri'); ?>" name="upload_button-<?php echo $this->get_field_name('image_uri'); ?>" value="<?php esc_html_e( 'Select Image', 'bavarian' ); ?>" style="margin-top:5px;" />
      <input type="button" class="hidden button button-secondary btn_remove" id="remove-<?php echo $this->get_field_id('image_uri'); ?>" name="remove_button-<?php echo $this->get_field_name('image_uri'); ?>" value="<?php esc_html_e( 'Remove Image', 'bavarian' ); ?>" style="margin-top:5px;" />
    </p>

    <p>

			<label for="<?php echo $this->get_field_id( 'widget_content' ); ?>">
				<?php esc_html_e( 'Content:', 'bavarian' ); ?></label>

			<textarea class="widefat" rows="3" cols="20"
			          id="<?php echo $this->get_field_id( 'widget_content' ); ?>"
			          name="<?php echo $this->get_field_name( 'widget_content' ); ?>"><?php echo( $widget_content ); ?></textarea>

		</p>

		<?php
	}
}






