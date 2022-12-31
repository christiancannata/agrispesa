<?php
namespace STM_GDPR\includes;

use STM_GDPR\includes\STM_Helpers;

class STM_DataAccess
{
	private static $instance = null;

	public function stm_gdpr_shortcode( $args ) {
		ob_start(); ?>
			<form action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" id="stm-gpdr-form">

				<p>
					<label for="stm_gpdr"><?php esc_html_e('Email address (*)', 'gdpr-compliance-cookie-consent'); ?></label>
					<input type="email" id="stm_gpdr_data_email" class="<?php echo STM_Helpers::stm_helpers_cmb_get_option(STM_GDPR_PREFIX . 'data_access', 'input-class'); ?>" name="stm_gpdr_data_email" required/>
				</p>

				<p>
					<input id="stm_gpdr_type_export" type="radio" name="stm_gpdr_data_type" value="export_personal_data" checked="checked" required/> 
					<label for="stm_gpdr_type_export"><?php esc_html_e('Export Personal Data', 'gdpr-compliance-cookie-consent'); ?></label>
					<br />
					<input id="stm_gpdr_type_remove" type="radio" name="stm_gpdr_data_type" value="remove_personal_data" required /> 
					<label for="stm_gpdr_type_remove"><?php esc_html_e('Erase Personal Data', 'gdpr-compliance-cookie-consent'); ?></label>
				</p>

				<p>
					<input type="submit" class="<?php echo STM_Helpers::cmb_get_option(STM_GDPR_PREFIX . 'data_access', 'button-class'); ?>" value="<?php esc_attr_e('Send request', 'gdpr-compliance-cookie-consent'); ?>" />
				</p>
			</form>
		<?php return ob_get_clean();
	}

	public function stm_gpdr_data_request() {

		$request_type  = sanitize_key( $_POST['stm_gpdr_data_type'] );
		$email = sanitize_email( $_POST['stm_gpdr_data_email'] );

		if ( !function_exists( 'wp_create_user_request' ) ) {
			wp_send_json_success( esc_html__('Your request can’t be processed on this website. Minimum required WordPress version is 4.9.6!', 'gdpr-compliance-cookie-consent') );
			die();
		} 
	
		if ( !empty($email) ) {

			if ( !is_email($email) ) {
				$errors[] = esc_html__('Invalid email address!', 'gdpr-compliance-cookie-consent');
			}

			if ( !in_array( $request_type, array('export_personal_data', 'remove_personal_data' ), true ) ) {
				$errors[] = esc_html__('Request type invalid, please try again!', 'gdpr-compliance-cookie-consent');
			}

		} else {
			$errors[] = esc_html__('Please fill up required fields!', 'gdpr-compliance-cookie-consent');
		}

		if ( empty( $errors ) ) {

			$new_request = wp_create_user_request( $email, $request_type );

			if ( is_wp_error( $new_request ) ) {
				wp_send_json_success( $new_request->get_error_message() );
			} elseif ( ! $new_request ) {
				wp_send_json_success( esc_html__('Unable to initiate confirmation request. Please contact the administrator.', 'gdpr-compliance-cookie-consent') );
			} else {
				$send_request = wp_send_user_request( $new_request );
				wp_send_json_success( 'success' );
			}

		} else {
			wp_send_json_success( join( '<br />', $errors ) );
		}

		die();

	}

    public static function getInstance() {

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}