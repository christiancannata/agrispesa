<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook\OfferManagement;

defined( 'ABSPATH' ) || exit;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Exception;
use Firebase\JWT\SignatureInvalidException;
use WooCommerce\Facebook\FBSignedData\FBPublicKey;
use WooCommerce\Facebook\FBSignedData\PublicKeyStorageHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;



/**
 * Utils class for decoding JWTs sent from Meta. Includes retry logic where we re-retrieve keys in case of mismatch
 * between signature and key due to key rotation.
 */
class RequestVerification {
	const KEY_NAME_FIELD = 'key_name';

	/**
	 * Attempts to decode the JWT using stored public keys. We have multiple retries in this flow.
	 * We will attempt to use both current and next public keys, and if those don't work we will retrieve public keys
	 * and retry refreshed current and next keys
	 *
	 * @param string $jwt
	 * @return array
	 * @throws Exception Throws any non-swallowed exception during JWT decoding.
	 */
	public static function decode_jwt_with_retries( string $jwt ): array {
		// Extract the project header to query for refreshed keys and validate the key name.
		$b64_body   = explode( '.', $jwt )[1];
		$body_array = json_decode( JWT::urlsafeB64Decode( $b64_body ), true );
		$key_name   = $body_array[ self::KEY_NAME_FIELD ];

		$public_key = PublicKeyStorageHelper::get_current_public_key();

		$params = self::decode_jwt_retryable( $jwt, $key_name, $public_key, true );
		if ( null !== $params ) {
			return $params;
		}

		$public_key = PublicKeyStorageHelper::get_next_public_key();
		$params     = self::decode_jwt_retryable( $jwt, $key_name, $public_key, true );
		if ( null !== $params ) {
			return $params;
		}

		PublicKeyStorageHelper::request_and_store_public_key( facebook_for_woocommerce(), $key_name );
		$public_key = PublicKeyStorageHelper::get_current_public_key();
		$params     = self::decode_jwt_retryable( $jwt, $key_name, $public_key, true );
		if ( null !== $params ) {
			return $params;
		}

		// This is the last attempt, so the params result is no longer nullable (We no longer swallow exceptions)
		$public_key = PublicKeyStorageHelper::get_next_public_key();
		$params     = self::decode_jwt_retryable( $jwt, $key_name, $public_key, false );

		if ( null === $params ) {
			throw new Exception( 'Params could not be decoded' );
		}

		// json_encode -> json_decode converts nested stdClass objects into nested arrays
		return json_decode( wp_json_encode( $params ), true );
	}

	/**
	 * Swallows exceptions that could result from an invalid stored key so that we can retry with an alternate key.
	 *
	 * @param string           $jwt
	 * @param string           $jwt_key_name The key name that was provided in the JWT data. Checked against the stored key name
	 * @param null|FBPublicKey $fb_public_key
	 * @param bool             $should_swallow_retryable_exception Set to true to swallow exceptions if we plan to re-try
	 * @return array|null
	 * @throws Exception|SignatureInvalidException|UnexpectedValueException Thrown if not swallowed.
	 */
	private static function decode_jwt_retryable( string $jwt, string $jwt_key_name, ?FBPublicKey $fb_public_key, bool $should_swallow_retryable_exception ): ?array {
		if ( null === $fb_public_key ) {
			$ex = new Exception( 'No public key stored.' );
			self::swallow_or_throw_exception( $ex, $should_swallow_retryable_exception );
			return null;
		}

		if ( $jwt_key_name !== $fb_public_key->get_project() ) {
			$ex = new Exception( 'Stored key does not match request key name.' );
			self::swallow_or_throw_exception( $ex, $should_swallow_retryable_exception );
			return null;
		}

		try {
			return self::decode_jwt_with_public_key( $jwt, $fb_public_key );
		} catch ( UnexpectedValueException $ex ) {
			if ( $ex->getMessage() !== 'Incorrect key for this algorithm' ) {
				throw $ex;
			}
			self::swallow_or_throw_exception( $ex, $should_swallow_retryable_exception );
		} catch ( SignatureInvalidException $ex ) {
			self::swallow_or_throw_exception( $ex, $should_swallow_retryable_exception );
		}
		return null;
	}

	private static function decode_jwt_with_public_key( string $jwt, FBPublicKey $fb_public_key ): array {
		$jwt_key  = new Key( $fb_public_key->get_key(), $fb_public_key->get_algorithm() );
		$jwt_data = JWT::decode( $jwt, $jwt_key );
		return json_decode( wp_json_encode( $jwt_data ), true );
	}

	/**
	 * Helper to swallow exceptions or throw.
	 *
	 * @param Exception $ex Exception to swallow or throw
	 * @param bool      $should_swallow_exception If true, don't throw the excep
	 * @throws Exception Throws if no public key was stored.
	 */
	private static function swallow_or_throw_exception( Exception $ex, $should_swallow_exception ): void {
		if ( $should_swallow_exception ) {
			return;
		} else {
			throw $ex;
		}
	}
}
