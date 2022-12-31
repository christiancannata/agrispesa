<?php
class STM_DataAccessWidget extends WP_Widget
{
    private static $instance = null;
    
    public function __construct() {

        parent::__construct(
            'stm_gdpr_widget',
            __('GDPR Compliance Widget', 'gdpr-compliance-cookie-consent'),
            array('description' => __('Sample widget based on WPBeginner Tutorial', 'gdpr-compliance-cookie-consent'))
        );

        add_action('widgets_init', function() {
			register_widget('STM_DataAccessWidget');
		});
    }
         
    public function widget( $args, $instance ) {

		?>
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="stm-gpdr-form">

		    <?php if ( ! empty( $instance['title'] ) ) : ?>
				<h2 class="widget-title">
					<?php echo esc_html( $instance['title'] ); ?>
				</h2>
		    <?php endif; ?>
 
		    <?php if ( ! empty( $instance['description'] ) ) : ?>
				<div class="stm-gdpr-description">
					<?php echo wp_kses_post( $instance['description'] ); ?>
				</div>
		    <?php endif; ?>

				<p>
					<label for="stm_gpdr"><?php esc_html_e('Email address (*)', 'gdpr-compliance-cookie-consent'); ?></label>
					<input type="email" id="stm_gpdr_data_email" name="stm_gpdr_data_email" required/>
				</p>

				<p>
					<input id="stm_gpdr_type_export" type="radio" name="stm_gpdr_data_type" value="export_personal_data" checked="checked" required/> 
					<label for="stm_gpdr_type_export"><?php esc_html_e('Export Personal Data', 'gdpr-compliance-cookie-consent'); ?></label>
					<br />
					<input id="stm_gpdr_type_remove" type="radio" name="stm_gpdr_data_type" value="remove_personal_data" required /> 
					<label for="stm_gpdr_type_remove"><?php esc_html_e('Erase Personal Data', 'gdpr-compliance-cookie-consent'); ?></label>
				</p>

				<p>
					<input type="submit" value="<?php esc_attr_e('Send request', 'gdpr-compliance-cookie-consent'); ?>" />
				</p>
			</form>
		<?php
    }
                 
    public function form( $instance ) {

        
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__('', 'gdpr-compliance-cookie-consent');
        $description = ! empty( $instance['description'] ) ? $instance['description'] : esc_html__('', 'gdpr-compliance-cookie-consent');

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Widget title:', 'gdpr-compliance-cookie-consent'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e('Widget description:', 'gdpr-compliance-cookie-consent'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $description ); ?></textarea>
        </p>
        <?php

    }
             
    public function update( $new_instance, $old_instance ) {

        $instance = array();
        
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['description'] = ( !empty( $new_instance['description'] ) ) ? $new_instance['description'] : '';
        
        return $instance;
    }

    public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

STM_DataAccessWidget::getInstance();