<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to enable wallet, set min and max value for recharging wallet
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    Wallet_System_For_Woocommerce
 * @subpackage Wallet_System_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wsfw_wps_wsfw_obj;

if ( isset( $_POST['import_wallets'] ) && ! empty( $_POST['import_wallets'] ) ) {
	unset( $_POST['import_wallets'] );
	if ( ! empty( $_FILES['import_wallet_for_users'] ) ) {
		$image_name      = ( isset( $_FILES['import_wallet_for_users']['name'] ) ) ? sanitize_text_field( wp_unslash( $_FILES['import_wallet_for_users']['name'] ) ) : '';
		$image_size      = ( isset( $_FILES['import_wallet_for_users']['size'] ) ) ? sanitize_text_field( wp_unslash( $_FILES['import_wallet_for_users']['size'] ) ) : '';
		$image_file_type = strtolower( pathinfo( $image_name, PATHINFO_EXTENSION ) );
		// Allow certain file formats.
		if ( 'csv' !== $image_file_type ) {
			$wps_wsfw_error_text = esc_html__( 'Sorry, only CSV file is allowed.', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
		} else {
			$file_temp    = ( isset( $_FILES['import_wallet_for_users']['tmp_name'] ) ) ? sanitize_text_field( wp_unslash( $_FILES['import_wallet_for_users']['tmp_name'] ) ) : '';
			$file         = fopen( $file_temp, 'r' );
			$users_wallet = array();
			$first_row    = fgetcsv( $file );
			$user_id      = $first_row[0];
			$balance      = $first_row[1];
			if ( 'User Id' != $user_id || 'Wallet Balance' != $balance ) {
				$wps_wsfw_error_text = esc_html__( 'You have not selected correct file(fields are not matching)', 'wallet-system-for-woocommerce' );
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
			} else {
				$updated_users = 0;
				$number_of_users = 0;
				while ( ! feof( $file ) ) {
					$user_data   = fgetcsv( $file );
					if ( is_array( $user_data ) ) {
						$user_id = $user_data[0];
						$balance = $user_data[1];
					}
					if ( 'User Id' === $user_id && 'Wallet Balance' === $balance ) {
						continue;
					} else {
						$user = get_user_by( 'id', $user_id );
						if ( $user ) {

							$current_balance = get_user_meta( $user_id, 'wps_wallet', true );
							$current_balance = ( ! empty( $current_balance ) ) ? $current_balance : 0;
							if ( $current_balance < $balance ) {
								$net_balance = $balance - $current_balance;
								$transaction_type = esc_html__( 'Wallet credited during importing wallet', 'wallet-system-for-woocommerce' );
								$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . wc_price( $net_balance );
							} elseif ( $current_balance == $balance ) {
								$net_balance      = 0;
								$transaction_type = esc_html__( 'No money is added/deducted from wallet', 'wallet-system-for-woocommerce' );
							} else {
								$net_balance = $current_balance - $balance;
								$transaction_type = esc_html__( 'Wallet debited during importing wallet', 'wallet-system-for-woocommerce' );
								$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . wc_price( $net_balance ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
							}
							$updated_wallet = update_user_meta( $user_id, 'wps_wallet', $balance );

							if ( $updated_wallet ) {
								$updated_users++;
								$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
								if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
									$user       = get_user_by( 'id', $user_id );
									$name       = $user->first_name . ' ' . $user->last_name;
									$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
									$mail_text .= $mail_message;
									$to         = $user->user_email;
									$from       = get_option( 'admin_email' );
									$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
									$headers    = 'MIME-Version: 1.0' . "\r\n";
									$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
									$headers   .= 'From: ' . $from . "\r\n" .
										'Reply-To: ' . $to . "\r\n";

									$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
								}
							}

							$transaction_data = array(
								'user_id'          => $user_id,
								'amount'           => $net_balance,
								'currency'         => get_woocommerce_currency(),
								'payment_method'   => esc_html__( 'Through importing Wallet', 'wallet-system-for-woocommerce' ),
								'transaction_type' => $transaction_type,
								'order_id'         => '',
								'note'             => '',

							);
							$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
							$result                 = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

							$number_of_users++;
						}
					}
				}
				$wps_wsfw_error_text = esc_html__( 'Updated wallet of ', 'wallet-system-for-woocommerce' ) . $updated_users . esc_html__( ' users out of ', 'wallet-system-for-woocommerce' ) . $number_of_users;
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'success' );
			}

			fclose( $file );

		}
	} else {
		$wps_wsfw_error_text = esc_html__( 'Please select any CSV file', 'wallet-system-for-woocommerce' );
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
	}
}

if ( isset( $_POST['confirm_updatewallet'] ) && ! empty( $_POST['confirm_updatewallet'] ) ) {
	unset( $_POST['confirm_updatewallet'] );
	$update = true;



	if ( empty( $_POST['wsfw_wallet_amount_for_users'] ) ) {
		$wps_wsfw_error_text = esc_html__( 'Please enter any amount', 'wallet-system-for-woocommerce' );
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( empty( $_POST['wsfw_wallet_action_for_users'] ) ) {
		$wps_wsfw_error_text = esc_html__( 'Please select any action', 'wallet-system-for-woocommerce' );
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
		$update = false;
	}
	if ( $update ) {
		$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
		$updated_amount         = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_amount_for_users'] ) );
		$wallet_action          = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_action_for_users'] ) );
		update_option( 'wsfw_wallet_amount_for_users', $updated_amount );
		update_option( 'wsfw_wallet_action_for_users', $wallet_action );
		$wallet_amount = get_option( 'wsfw_wallet_amount_for_users', '' );
		$wallet_option = get_option( 'wsfw_wallet_action_for_users', '' );
		$user_check_box_ids_array_list = array();
		$user_check_box_ids_array = ! empty( $_POST['user_check_box_ids'] ) ? map_deep( wp_unslash( $_POST['user_check_box_ids'] ), 'sanitize_text_field' ) : '';

		if ( ! empty( $user_check_box_ids_array ) ) {
			$user_check_box_ids_array = $user_check_box_ids_array . trim( ',' );
			$user_check_box_ids_array_list = ( explode( ',', $user_check_box_ids_array ) );
		} else {
			$user_check_box_ids_array_list = '';
		}

		if ( isset( $wallet_amount ) && ! empty( $wallet_amount ) ) {

			$users = get_users();
			$updated_users   = 0;
			$number_of_users = 0;

			if ( ! empty( $user_check_box_ids_array_list ) ) {

				foreach ( $user_check_box_ids_array_list as $user ) {
					$user_id = $user;
					if ( empty( $user ) ) {
						continue;
					}
					$wallet  = get_user_meta( $user_id, 'wps_wallet', true );
					$wallet  = ( ! empty( $wallet ) ) ? $wallet : 0;
					if ( 'credit' === $wallet_option ) {
						$wallet          += $wallet_amount;
						$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', $wallet );
						if ( isset( $_POST['wsfw_wallet_transaction_details_for_users'] ) && ! empty( $_POST['wsfw_wallet_transaction_details_for_users'] ) ) {
							$transaction_type = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_transaction_details_for_users'] ) );
						} else {
							$transaction_type = __( 'Credited by admin', 'wallet-system-for-woocommerce' );
						}

						$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount );
					} elseif ( 'debit' === $wallet_option ) {

						$previous_wallet_amount = $wallet;
						if ( $wallet < $wallet_amount ) {
							$previous_wallet_amount = $wallet;
						} else {
							$wallet -= $wallet_amount;
						}

						$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', abs( $wallet ) );

						if ( isset( $_POST['wsfw_wallet_transaction_details_for_users'] ) && ! empty( $_POST['wsfw_wallet_transaction_details_for_users'] ) ) {
							if ( $previous_wallet_amount < $wallet_amount ) {
								$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
							} else {
								$transaction_type = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_transaction_details_for_users'] ) );
							}
						} else {
							if ( $previous_wallet_amount < $wallet_amount ) {
								$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
							} else {
								$transaction_type = __( 'Debited by admin', 'wallet-system-for-woocommerce' );
							}
						}
						$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
					}

					if ( $updated_wallet ) {
						$updated_users++;
					}

					$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user       = get_user_by( 'id', $user_id );
						$name       = $user->first_name . ' ' . $user->last_name;
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
						$mail_text .= $mail_message;
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
					}

					$transaction_data = array(
						'user_id'          => $user_id,
						'amount'           => $updated_amount,
						'currency'         => get_woocommerce_currency(),
						'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
						'transaction_type' => $transaction_type,
						'order_id'         => '',
						'note'             => '',

					);

					$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

					$number_of_users++;
				}
			} else {

				foreach ( $users as $user ) {
					$user_id = $user->ID;


					$wallet  = get_user_meta( $user_id, 'wps_wallet', true );
					$wallet  = ( ! empty( $wallet ) ) ? $wallet : 0;
					if ( 'credit' === $wallet_option ) {
						$wallet          += $wallet_amount;
						$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', $wallet );
						if ( isset( $_POST['wsfw_wallet_transaction_details_for_users'] ) && ! empty( $_POST['wsfw_wallet_transaction_details_for_users'] ) ) {
							$transaction_type = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_transaction_details_for_users'] ) );
						} else {
							$transaction_type = __( 'Credited by admin', 'wallet-system-for-woocommerce' );
						}

						$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount );
					} elseif ( 'debit' === $wallet_option ) {
						$previous_wallet_amount = $wallet;
						if ( $wallet < $wallet_amount ) {
							$previous_wallet_amount = $wallet;
						} else {
							$wallet -= $wallet_amount;
						}

						$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', abs( $wallet ) );

						if ( isset( $_POST['wsfw_wallet_transaction_details_for_users'] ) && ! empty( $_POST['wsfw_wallet_transaction_details_for_users'] ) ) {
							if ( $previous_wallet_amount < $wallet_amount ) {
								$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
							} else {
								$transaction_type = sanitize_text_field( wp_unslash( $_POST['wsfw_wallet_transaction_details_for_users'] ) );
							}
						} else {
							if ( $previous_wallet_amount < $wallet_amount ) {
								$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
							} else {
								$transaction_type = __( 'Debited by admin', 'wallet-system-for-woocommerce' );
							}
						}
						$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
					}

					if ( $updated_wallet ) {
						$updated_users++;
					}

					$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
					if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
						$user       = get_user_by( 'id', $user_id );
						$name       = $user->first_name . ' ' . $user->last_name;
						$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
						$mail_text .= $mail_message;
						$to         = $user->user_email;
						$from       = get_option( 'admin_email' );
						$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
						$headers    = 'MIME-Version: 1.0' . "\r\n";
						$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
						$headers   .= 'From: ' . $from . "\r\n" .
						'Reply-To: ' . $to . "\r\n";

						$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
					}
					$transaction_data = array(
						'user_id'          => $user_id,
						'amount'           => $updated_amount,
						'currency'         => get_woocommerce_currency(),
						'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
						'transaction_type' => $transaction_type,
						'order_id'         => '',
						'note'             => '',
					);
					$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );

					$number_of_users++;
				}
			}
		}
	}

	if ( $result ) {
		$wps_wsfw_error_text = esc_html__( 'Updated wallet of ', 'wallet-system-for-woocommerce' ) . $updated_users . esc_html__( ' users out of ', 'wallet-system-for-woocommerce' ) . $number_of_users;
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'success' );
	} else {
		$wps_wsfw_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $wps_wsfw_error_text, 'error' );
	}
}

do_action( 'user_restriction_saving' );

if ( isset( $_POST['update_wallet'] ) && ! empty( $_POST['update_wallet'] ) ) {
	$nonce = ( isset( $_POST['user_update_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['user_update_nonce'] ) ) : '';
	if ( wp_verify_nonce( $nonce ) ) {
		unset( $_POST['update_wallet'] );
		$update = true;
		if ( empty( $_POST['wps_wallet-edit-popup-input'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'Please enter any amount', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( $_POST['wps_wallet-edit-popup-input'] < 0 ) {
			$msfw_wpg_error_text = esc_html__( 'Please enter amount in positive value.', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( empty( $_POST['action_type'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'Please select any action', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( empty( $_POST['user_id'] ) ) {
			$msfw_wpg_error_text = esc_html__( 'User Id is not given', 'wallet-system-for-woocommerce' );
			$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			$update = false;
		}
		if ( $update ) {

			$wallet_payment_gateway = new Wallet_System_For_Woocommerce();
			$updated_amount         = sanitize_text_field( wp_unslash( $_POST['wps_wallet-edit-popup-input'] ) );
			$wallet_action          = sanitize_text_field( wp_unslash( $_POST['action_type'] ) );
			$user_id                = sanitize_text_field( wp_unslash( $_POST['user_id'] ) );
			$wallet                 = get_user_meta( $user_id, 'wps_wallet', true );
			$wallet                 = ( ! empty( $wallet ) ) ? $wallet : 0;
			if ( 'credit' === $wallet_action ) {
				$wallet          += $updated_amount;
				$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', $wallet );
				if ( isset( $_POST['wps_wallet-edit-popup-transaction-detail'] ) && ! empty( $_POST['wps_wallet-edit-popup-transaction-detail'] ) ) {
					$transaction_type = sanitize_text_field( wp_unslash( $_POST['wps_wallet-edit-popup-transaction-detail'] ) );
				} else {
					$transaction_type = __( 'Credited by admin', 'wallet-system-for-woocommerce' );
				}
				$mail_message     = __( 'Merchant has credited your wallet by ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount );
			} elseif ( 'debit' === $wallet_action ) {
				$previous_wallet_amount = $wallet;
				if ( $wallet < $updated_amount ) {
					$previous_wallet_amount = $wallet;
				} else {
					$wallet -= $updated_amount;
				}
				$updated_wallet   = update_user_meta( $user_id, 'wps_wallet', abs( $wallet ) );

				if ( isset( $_POST['wps_wallet-edit-popup-transaction-detail'] ) && ! empty( $_POST['wps_wallet-edit-popup-transaction-detail'] ) ) {
					if ( $previous_wallet_amount < $updated_amount ) {
						$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
					} else {
						$transaction_type = sanitize_text_field( wp_unslash( $_POST['wps_wallet-edit-popup-transaction-detail'] ) );
					}
				} else {
					if ( $previous_wallet_amount < $updated_amount ) {
						$transaction_type = __( 'unable to debit ', 'wallet-system-for-woocommerce' ) . __( ' amount due to Insufficient Balance ie. ', 'wallet-system-for-woocommerce' ) . wc_price( $wallet );
					} else {
						$transaction_type = __( 'Debited by admin', 'wallet-system-for-woocommerce' );
					}
				}

				$mail_message     = __( 'Merchant has deducted ', 'wallet-system-for-woocommerce' ) . wc_price( $updated_amount ) . __( ' from your wallet.', 'wallet-system-for-woocommerce' );
			}

			$send_email_enable = get_option( 'wps_wsfw_enable_email_notification_for_wallet_update', '' );
			if ( isset( $send_email_enable ) && 'on' === $send_email_enable ) {
				$user       = get_user_by( 'id', $user_id );
				$name       = $user->first_name . ' ' . $user->last_name;
				$mail_text  = esc_html__( 'Hello ', 'wallet-system-for-woocommerce' ) . esc_html( $name ) . __( ',<br/>', 'wallet-system-for-woocommerce' );
				$mail_text .= $mail_message;
				$to         = $user->user_email;
				$from       = get_option( 'admin_email' );
				$subject    = __( 'Wallet updating notification', 'wallet-system-for-woocommerce' );
				$headers    = 'MIME-Version: 1.0' . "\r\n";
				$headers   .= 'Content-Type: text/html;  charset=UTF-8' . "\r\n";
				$headers   .= 'From: ' . $from . "\r\n" .
					'Reply-To: ' . $to . "\r\n";

				$wallet_payment_gateway->send_mail_on_wallet_updation( $to, $subject, $mail_text, $headers );
			}

			$transaction_data = array(
				'user_id'          => $user_id,
				'amount'           => $updated_amount,
				'currency'         => get_woocommerce_currency(),
				'payment_method'   => esc_html__( 'Manually By Admin', 'wallet-system-for-woocommerce' ),
				'transaction_type' => $transaction_type,
				'order_id'         => '',
				'note'             => '',

			);

			$result = $wallet_payment_gateway->insert_transaction_data_in_table( $transaction_data );
			if ( $result ) {
				$msfw_wpg_error_text = esc_html__( 'Updated wallet of user', 'wallet-system-for-woocommerce' );
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'success' );
			} else {
				$msfw_wpg_error_text = esc_html__( 'There is an error in database', 'wallet-system-for-woocommerce' );
				$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( $msfw_wpg_error_text, 'error' );
			}
		}
	} else {
		$wsfw_wps_wsfw_obj->wps_wsfw_plug_admin_notice( esc_html__( 'Failed security check', 'wallet-system-for-woocommerce' ), 'error' );
	}
}

$wsfw_wallet_topup_settings = apply_filters( 'wsfw_wallet_settings_array', array() );
$wsfw_update_wallet         = apply_filters( 'wsfw_update_wallet_array', array() );
$wsfw_import_settings       = apply_filters( 'wsfw_import_wallet_array', array() );


?>
<div class="wps-wpg-gen-section-form-container">
	<div class="wpg-secion-wrap">
		<h3><?php esc_html_e( 'Credit/Debit amount from user\'s wallet', 'wallet-system-for-woocommerce' ); ?></h3>
	</div>
	<div class="wps-wpg-gen-section-form-wrapper">
		<form action="" method="POST" class="wps-wpg-gen-section-form" id="form_update_wallet"> 
			<div class="wpg-secion-wrap">
				<h3><?php esc_html_e( 'Edit wallet of all users at once', 'wallet-system-for-woocommerce' ); ?></h3>
				<?php
				$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_update_wallet );
				echo esc_html( $wsfw_general_html );
				?>
			</div>
			<div class="wps_wallet-update--popupwrap">
				<div class="wps_wallet-update-popup">
					<div id="wps_all_users" style="display:none">
					<h3><?php esc_html_e( 'Are you sure to update wallet of all users?', 'wallet-system-for-woocommerce' ); ?></h3>
					</div>
					<div id="wps_all_selected_users" style="display:none">
					<h3><?php esc_html_e( 'Are you sure to update wallet of selected user?', 'wallet-system-for-woocommerce' ); ?></h3>
					</div>
					<div class="wps_wallet-update-popup-btn">
						<input type="submit" class="wps-btn wps-btn__filled" name="confirm_updatewallet" id="confirm_updatewallet" value="<?php esc_html_e( 'Yes, I\'m Sure', 'wallet-system-for-woocommerce' ); ?>" >
						<a href="javascript:void(0);" id="cancel_walletupdate" ><?php esc_html_e( 'Not now', 'wallet-system-for-woocommerce' ); ?></a>
					</div>
					</div>
			</div>
		</form>
		
		<button class="mdc-ripple-upgraded" id="export_user_wallet" > <img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/down-arrow.png" title="Download CSV file" >
		</button>

		<form action="" method="POST" class="wps-wpg-gen-section-form" enctype="multipart/form-data">
			<div class="wpg-secion-wrap">
				<h3><?php esc_html_e( 'Import wallets for user', 'wallet-system-for-woocommerce' ); ?></h3>
				<?php
				$wsfw_general_html = $wsfw_wps_wsfw_obj->wps_wsfw_plug_generate_html( $wsfw_import_settings );
				echo esc_html( $wsfw_general_html );
				?>
			</div>
		</form>
	</div>
</div>

<div class="wps-wpg-gen-section-table-wrap">
	<h4><?php esc_html_e( 'Wallet User', 'wallet-system-for-woocommerce' ); ?></h4>

<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Extending Wp_List_Table class to create segment table.
 */
class Wallet_User_Table extends WP_List_Table {

	/**
	 * Prepare the items for the table to process.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$per_page     = 10;
		$columns      = $this->get_columns();
		$data         = $this->table_data();
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$data                  = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}

	/**
	 * This function is used to get columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'id'       => esc_html__( 'ID', 'wallet-system-for-woocommerce' ),
			'name'     => esc_html__( 'Name', 'wallet-system-for-woocommerce' ),
			'email'    => esc_html__( 'Email', 'wallet-system-for-woocommerce' ),
			'role'     => esc_html__( 'Role', 'wallet-system-for-woocommerce' ),
			'amount'   => esc_html__( 'Amount', 'wallet-system-for-woocommerce' ),
			'action'   => esc_html__( 'Actions', 'wallet-system-for-woocommerce' ),
			'res_user' => esc_html__( 'Restrict User', 'wallet-system-for-woocommerce' ),
		);
		return $columns;
	}



	/**
	 * This function is used to filter product.
	 *
	 * @return array
	 */
	public function table_data() {
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key'     => 'wps_wallet',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'wps_wallet',
				'compare' => 'NOT EXISTS',
			),
		);
		$wps_paged_no           = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : 0;
		$pagination_no          = 0;

		if ( 1 == $wps_paged_no ) {
			$pagination_no = 2;
		} else {
			$pagination_no = $wps_paged_no + 1;
		}

		$pagination_no = $pagination_no * 10;
		if ( 10 == $pagination_no ) {
			$pagination_no = 11;
		}
		$args['number'] = $pagination_no;
		if ( isset( $_REQUEST['s'] ) ) {
			$wps_request_search = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
			$args['search']     = '*' . $wps_request_search . '*';
		}
		$user_data = new WP_User_Query( $args );
		$user_data = $user_data->get_results();

		if ( ! empty( $user_data ) ) {
			foreach ( $user_data as $all_user ) {
				$user               = get_user_by( 'id', $all_user->ID );
				$x      = array(
					'id'       => $this->wsfw_get_id( $user ),
					'name'     => $this->wsfw_get_name( $user ),
					'email'    => $this->wsfw_get_email( $user ),
					'role'     => $this->wsfw_get_role( $user ),
					'amount'   => $this->wsfw_get_amount( $user ),
					'action'   => $this->wsfw_get_action( $user ),
					'res_user' => $this->wsfw_get_res_user( $user ),
				);
				$data[] = $x;
			}
		}
		return $data;
	}

	/**
	 * This function is used to show checkbox.
	 *
	 * @param int $item item id.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" onclick="set_checked_value(this)" id="wps_wallet_ids[]" name="wps_wallet_ids[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * This function is used to show columns.
	 *
	 * @param string $item item.
	 * @param string $column_name column name.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return sprintf( $item[ $column_name ], true );
	}

	/**
	 * Show user id.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_id( $user ) {
		return $user->ID;
	}

	/**
	 * This function is used to show user name.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_name( $user ) {
		return $user->display_name;
	}

	/**
	 * This function is used to show user email.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_email( $user ) {
		return $user->user_email;
	}

	/**
	 * This functions is used to show user role.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_role( $user ) {
		return ! empty( $user->roles[0] ) ? $user->roles[0] : '-';
	}

	/**
	 * This function ia used to show user wallet amount.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_amount( $user ) {
		$wallet_bal = get_user_meta( $user->ID, 'wps_wallet', true );
		$wallet_bal = ! empty( $wallet_bal ) ? $wallet_bal : 0;
		$wallet_bal = wc_price( $wallet_bal, array( 'currency' => get_woocommerce_currency() ) );
		return $wallet_bal;
	}

	/**
	 * This function is to edit user wallet and show transactions.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_action( $user ) {
		$wallet_bal = get_user_meta( $user->ID, 'wps_wallet', true );
		$wallet_bal = ! empty( $wallet_bal ) ? $wallet_bal : 0;
		$data  = '';
		$data .= '<span>';
		$data .= '<a class="edit_wallet" user-amount="' . esc_attr( $wallet_bal ) . '"  data-userid="' . esc_attr( $user->ID ) . '" href="" title="Edit Wallet" >';
		$data .= '<img src="' . esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/edit.svg"></a>';
		$data .= '<a href="' . esc_url( admin_url( 'admin.php?page=wallet_system_for_woocommerce_menu' ) . '&wsfw_tab=wps-user-wallet-transactions&id=' . $user->ID ) . '" title="View Transactions" >';
		$data .= '<img src="' . esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ) . 'admin/image/eye.svg"></a>';
		$data .= '</span>';
		return $data;
	}

	/**
	 * This function is used to restrict user.
	 *
	 * @param object $user user.
	 * @return string
	 */
	public function wsfw_get_res_user( $user ) {
		$is_user_restricted = get_user_meta( $user->ID, 'user_restriction_for_wallet', true );
		$html               = '<div class="wps-form-group__control"> <div> <div class="mdc-switch mdc-switch--checked"> <div class="mdc-switch__track"></div> <div class="mdc-switch__thumb-underlay mdc-ripple-upgraded mdc-ripple-upgraded--unbounded" style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:1.71429; --mdc-ripple-left:10px; --mdc-ripple-top:10px;"> <div class="mdc-switch__thumb"></div> ';
		$html              .= '<input name="wsfw_restrict_user_' . esc_html( $user->ID ) . '" user_id="' . esc_html( $user->ID ) . '" type="checkbox" id="wsfw_restrict_user_' . esc_html( $user->ID ) . '" value="on" class="mdc-switch__native-control wsfw-radio-switch-class wsfw_restrict_user" role="switch" ';

		if ( 'restricted' == $is_user_restricted ) {
			$html .= 'aria-checked="true"';
		} else {
			$html .= 'aria-checked="false"';
		}
		if ( 'restricted' == $is_user_restricted ) {
			$html .= 'checked="checked"';
		} else {
			$html .= '';
		}
		$html .= '> </div> </div> </div> </div>';
		$html = apply_filters( 'wsfw_wallet_user_restriction_after', $html, $user );
		return $html;
	}

}
?>
<form method="post">
	<?php
		$wallet_user_table = new Wallet_User_Table();
		$wallet_user_table->prepare_items();
		$wallet_user_table->search_box( __( 'Search', 'wallet-system-for-woocommerce' ), 'search_id' );
		$wallet_user_table->display();
	?>
</form>
</div>

<div class="wps_wallet-edit--popupwrap">
	<div class="wps_wallet-edit-popup">
		<p><span id="close_wallet_form"><img src="<?php echo esc_url( WALLET_SYSTEM_FOR_WOOCOMMERCE_DIR_URL ); ?>admin/image/cancel.svg"></span></p>
		<form method="post">
			<div class="wps_wallet-edit-popup-content">
				<div class="wps_wallet-edit-popup-amount">
					<div class="wps_wallet-edit-popup-label">
						<label for="wps_wallet-edit-popup-input" class="wps_wallet-edit-popup-input">
							<?php echo esc_html__( 'Select Amount (', 'wallet-system-for-woocommerce' ) . esc_html( get_woocommerce_currency_symbol() ) . '):'; ?>
						</label>
					</div>
					<div class="wps_wallet-edit-popup-control">
						<input type="number" name="wps_wallet-edit-popup-input" step="0.01" id="wps_wallet-edit-popup-input"  class="wps_wallet-edit-popup-fill">
						<p class="error"></p>
					</div>
				</div>
				<div class="wps_wallet-edit-popup-amount">
					<div class="wps_wallet-edit-popup-label">
					<label for="wps_wallet-edit-popup-input" class="wps_wallet-edit-popup-input">
							<?php echo esc_html__( 'Transaction Detail:', 'wallet-system-for-woocommerce' ); ?>
						</label>
					</div>
					<div class="wps_wallet-edit-popup-control">
						<input type="text" name="wps_wallet-edit-popup-transaction-detail" id="wps_wallet-edit-popup-transaction-detail"  class="wps_wallet-edit-popup-fill">
					
					</div>
				</div>
				<div class="wps_wallet-edit-popup-amount">
					<div class="wps_wallet-edit-popup-label">
						<label for="wps_wallet-edit-popup-card" class="wps_wallet-edit-popup-card"><?php esc_html_e( 'Select Action:', 'wallet-system-for-woocommerce' ); ?></label>
					</div>
					<div class="wps_wallet-edit-popup-control">
						<div class="wps-form-select-card">
							<input type="radio" id="debit" name="action_type" value="debit">
							<label for="debit"><?php esc_html_e( 'Debit Wallet', 'wallet-system-for-woocommerce' ); ?></label>
						</div>
						<div class="wps-form-select-card">
							<input type="radio" id="credit" name="action_type" value="credit">
							<label for="credit"><?php esc_html_e( 'Credit Wallet', 'wallet-system-for-woocommerce' ); ?></label>
						</div>
					</div>
				</div>
			</div>
			<div class="wps_wallet-edit-popup-btn">
				<input type="hidden" id="user_update_nonce" name="user_update_nonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>" />
				
				<input type="button" id="wps_wallet_submit_val" name="update_wallet" class="wps-btn wps-btn__filled" value="<?php esc_html_e( 'Update Wallet', 'wallet-system-for-woocommerce' ); ?>">
				<input type="submit" style="display:none" id="wps_wallet_submit_val_submit" name="update_wallet" class="wps-btn wps-btn__filled" value="<?php esc_html_e( 'Update Wallet', 'wallet-system-for-woocommerce' ); ?>">
			
			</div>
		</form>
	</div>
</div>
<?php do_action( 'wsfw_wallet_restrict_user_pro_after' ); ?>
