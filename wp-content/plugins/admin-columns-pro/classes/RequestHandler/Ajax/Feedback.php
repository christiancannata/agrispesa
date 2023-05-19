<?php

namespace ACP\RequestHandler\Ajax;

use AC\Capabilities;
use AC\Nonce;
use AC\Plugin\Version;
use AC\Request;
use ACP\RequestAjaxHandler;

class Feedback implements RequestAjaxHandler {

	private $version;

	public function __construct( Version $version ) {
		$this->version = $version;
	}

	public function handle(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		$request = new Request();

		if ( ! ( new Nonce\Ajax() )->verify( $request ) ) {
			wp_send_json_error( __( 'Invalid request', 'codepress-admin-columns' ) );
		}

		$email = trim( $request->filter( 'email', null, FILTER_SANITIZE_EMAIL ) );

		if ( ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please insert a valid email so we can reply to your feedback.', 'codepress-admin-columns' ) );
		}

		$feedback = $request->get( 'feedback' );

		if ( empty( $feedback ) ) {
			wp_send_json_error( __( 'Your feedback form is empty.', 'codepress-admin-columns' ) );
		}

		$headers = [
			sprintf( 'From: <%s>', $email ),
			'Content-Type: text/html',
		];

		wp_mail(
			acp_support_email(),
			sprintf( 'Beta Feedback on Admin Columns Pro %s', $this->version->get_value() ),
			nl2br( $feedback ),
			$headers
		);

		wp_send_json_success( __( 'Thank you very much for your feedback!', 'codepress-admin-columns' ) );
	}

}