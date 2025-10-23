<?php


namespace wpie;

defined( 'ABSPATH' ) || exit;

class Security {

        public static function verify_request( $action = "" ) {

                $error_data = [ 'status' => "error" ];

                if ( !self::verify_nonce() ) {

                        $error_data [ 'message' ] = __( 'Session Expired. Please refresh page.', 'wp-import-export-lite' );

                        echo json_encode( $error_data );

                        die();
                }

                if ( empty( trim( $action ) ) || !current_user_can( $action ) ) {

                        $error_data [ 'message' ] = __( 'Permission denied!', 'wp-import-export-lite' );

                        echo json_encode( $error_data );

                        die();
                }
        }

        public static function verify_nonce() {

                $nonce = isset( $_REQUEST[ 'wpieSecurity' ] ) ? wpie_sanitize_field( $_REQUEST[ 'wpieSecurity' ] ) : "";

                if ( empty( trim( $nonce ) ) ) {
                        return false;
                }

                return \wp_verify_nonce( $nonce, "wpie-security" ) === 1;
        }

}
