<?php

/*

@name			    Working Hours Widget

*/

class Kioken_Widget_working_Hours extends WP_Widget {

	public function __construct() {
		$this->defaults = array(
					'widget_title' => esc_html__( 'Working hours', 'bavarian' ),
					'widget_content' => '',
					'monday'       => esc_html__( 'Closed', 'bavarian' ),
					'tuesday'      => esc_html__( '10 AM - 9 PM', 'bavarian' ),
					'wednesday'    => esc_html__( '10 AM - 9 PM', 'bavarian' ),
					'thursday'     => esc_html__( '10 AM - 9 PM', 'bavarian' ),
					'friday'       => esc_html__( '10 AM - 9 PM', 'bavarian' ),
					'saturday'     => esc_html__( '10 AM - 9 PM', 'bavarian' ),
					'sunday'       => esc_html__( '10 AM - 9 PM', 'bavarian' ),
				);

    parent::__construct(
			'widget_working_hours_kioken',
			esc_html__( 'Kioken - Working Hours', 'bavarian' ),
			array(
				'classname'   => 'widget_working_hours',
        'description'        => esc_html__( 'Display the working hours of your establishment.', 'bavarian' ),
        'customizer_support' => true,
			)
		);

	}

	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		// Vars
		$widget_title   = apply_filters( 'widget_title', $instance['widget_title'] );
		$widget_content = do_shortcode( $instance['widget_content'] );

		$monday    = $instance['monday'];
		$tuesday   = $instance['tuesday'];
		$wednesday = $instance['wednesday'];
		$thursday  = $instance['thursday'];
		$friday    = $instance['friday'];
		$saturday  = $instance['saturday'];
		$sunday    = $instance['sunday'];

		echo $args['before_widget'];

		if ( $widget_title ) {
			echo $args['before_title'] . esc_attr( $widget_title ) . $args['after_title'];
		}

		if ( $widget_content ) {
			?>

			<div class="widget-content">
				<?php echo wpautop( esc_attr( $widget_content ) ); ?>
			</div><!-- END widget-content -->

			<?php
		}
		?>

		<div itemscope itemtype="http://schema.org/Restaurant">

      <ul>
        <?php if ( !empty( $monday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Monday', 'bavarian' ); ?> <?php echo esc_attr( $monday ); ?>">
            <span class="day"><?php esc_html_e( 'Mon', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $monday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $tuesday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Tuesday', 'bavarian' ); ?> <?php echo esc_attr( $tuesday ); ?>">
            <span class="day"><?php esc_html_e( 'Tue', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $tuesday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $wednesday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Wednesday', 'bavarian' ); ?> <?php echo esc_attr( $wednesday ); ?>">
            <span class="day"><?php esc_html_e( 'Wed', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $wednesday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $thursday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Thursday', 'bavarian' ); ?> <?php echo esc_attr( $thursday ); ?>">
            <span class="day"><?php esc_html_e( 'Thu', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $thursday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $friday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Friday', 'bavarian' ); ?> <?php echo esc_attr( $friday ); ?>">
            <span class="day"><?php esc_html_e( 'Fri', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $friday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $saturday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Saturday', 'bavarian' ); ?> <?php echo esc_attr( $saturday ); ?>">
            <span class="day"><?php esc_html_e( 'Sat', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $saturday ); ?></span>
          </li>
        <?php endif;?>
        <?php if ( !empty( $sunday ) ) :?>
          <li itemprop="workingHours" content="<?php esc_html_e( 'Sunday', 'bavarian' ); ?> <?php echo esc_attr( $sunday ); ?>">
            <span class="day"><?php esc_html_e( 'Sun', 'bavarian' ); ?></span>
            <span class="working"><?php echo esc_attr( $sunday ); ?></span>
          </li>
        <?php endif;?>
      </ul>
		</div>

		<?php
		echo $args['after_widget'];

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance = $new_instance;

		$instance['widget_title']   = strip_tags( $new_instance['widget_title'] );
		$instance['widget_content'] = $new_instance['widget_content'];

		$instance['monday']    = $new_instance['monday'];
		$instance['tuesday']   = $new_instance['tuesday'];
		$instance['wednesday'] = $new_instance['wednesday'];
		$instance['thursday']  = $new_instance['thursday'];
		$instance['friday']    = $new_instance['friday'];
		$instance['saturday']  = $new_instance['saturday'];
		$instance['sunday']    = $new_instance['sunday'];


		return $instance;

	}

	function form( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		$widget_title   = isset( $instance['widget_title'] ) ? esc_attr( $instance['widget_title'] ) : '';
		$widget_content = isset( $instance['widget_content'] ) ? esc_attr( $instance['widget_content'] ) : '';

		$monday    = isset( $instance['monday'] ) ? esc_attr( $instance['monday'] ) : '';
		$tuesday   = isset( $instance['tuesday'] ) ? esc_attr( $instance['tuesday'] ) : '';
		$wednesday = isset( $instance['wednesday'] ) ? esc_attr( $instance['wednesday'] ) : '';
		$thursday  = isset( $instance['thursday'] ) ? esc_attr( $instance['thursday'] ) : '';
		$friday    = isset( $instance['friday'] ) ? esc_attr( $instance['friday'] ) : '';
		$saturday  = isset( $instance['saturday'] ) ? esc_attr( $instance['saturday'] ) : '';
		$sunday    = isset( $instance['sunday'] ) ? esc_attr( $instance['sunday'] ) : '';

		?>

		<p>

			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>">
				<?php esc_html_e( 'Title:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'widget_title' ); ?>"
			       name="<?php echo $this->get_field_name( 'widget_title' ); ?>"
			       value="<?php echo esc_attr( $widget_title ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'widget_content' ); ?>">
				<?php esc_html_e( 'Content:', 'bavarian' ); ?></label>

			<textarea class="widefat" rows="3" cols="20"
			          id="<?php echo $this->get_field_id( 'widget_content' ); ?>"
			          name="<?php echo $this->get_field_name( 'widget_content' ); ?>"><?php echo( $widget_content ); ?></textarea>

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'monday' ); ?>">
				<?php esc_html_e( 'Monday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'monday' ); ?>"
			       name="<?php echo $this->get_field_name( 'monday' ); ?>"
			       value="<?php echo esc_attr( $monday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'tuesday' ); ?>">
				<?php esc_html_e( 'Tuesday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'tuesday' ); ?>"
			       name="<?php echo $this->get_field_name( 'tuesday' ); ?>"
			       value="<?php echo esc_attr( $tuesday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'wednesday' ); ?>">
				<?php esc_html_e( 'Wednesday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'wednesday' ); ?>"
			       name="<?php echo $this->get_field_name( 'wednesday' ); ?>"
			       value="<?php echo esc_attr( $wednesday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'thursday' ); ?>">
				<?php esc_html_e( 'Thursday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'thursday' ); ?>"
			       name="<?php echo $this->get_field_name( 'thursday' ); ?>"
			       value="<?php echo esc_attr( $thursday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'friday' ); ?>">
				<?php esc_html_e( 'Friday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'friday' ); ?>"
			       name="<?php echo $this->get_field_name( 'friday' ); ?>"
			       value="<?php echo esc_attr( $friday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'saturday' ); ?>">
				<?php esc_html_e( 'Saturday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'saturday' ); ?>"
			       name="<?php echo $this->get_field_name( 'saturday' ); ?>"
			       value="<?php echo esc_attr( $saturday ); ?>">

		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'sunday' ); ?>">
				<?php esc_html_e( 'Sunday:', 'bavarian' ); ?></label>

			<input type="text" class="widefat"
			       id="<?php echo $this->get_field_id( 'sunday' ); ?>"
			       name="<?php echo $this->get_field_name( 'sunday' ); ?>"
			       value="<?php echo esc_attr( $sunday ); ?>">

		</p>

		<?php

	}

}