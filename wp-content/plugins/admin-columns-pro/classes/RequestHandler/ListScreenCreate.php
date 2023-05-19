<?php

namespace ACP\RequestHandler;

use AC\Capabilities;
use AC\ListScreenFactoryInterface;
use AC\ListScreenRepository\Storage;
use AC\Message;
use AC\Message\Notice;
use AC\Request;
use AC\Storage\ListScreenOrder;
use AC\Type\ListScreenId;
use ACP\Nonce;
use ACP\RequestHandler;

class ListScreenCreate implements RequestHandler {

	public const PARAM_ACTION = 'action';
	public const PARAM_CREATE_LIST = 'create-layout';
	public const PARAM_DELETE_LIST = 'delete-layout';

	private $storage;

	private $order;

	private $list_screen_factory;

	public function __construct(
		Storage $storage,
		ListScreenOrder $order,
		ListScreenFactoryInterface $list_screen_factory
	) {
		$this->storage = $storage;
		$this->order = $order;
		$this->list_screen_factory = $list_screen_factory;
	}

	public function handle( Request $request ) {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( ! ( new Nonce\LayoutNonce() )->verify( $request ) ) {
			return;
		}

		$list_id = (string) $request->get( 'list_id' );
		$list_key = (string) $request->get( 'list_key' );
		$title = trim( $request->get( 'title' ) );
		$clone = '1' === $request->get( 'clone_current' );

		$list_id = ListScreenId::is_valid_id( $list_id )
			? new ListScreenId( $list_id )
			: null;

		if ( empty( $title ) ) {
			$notice = new Notice( __( 'Name can not be empty.', 'codepress-admin-columns' ) );
			$notice->set_type( Message::ERROR )->register();

			return;
		}

		if ( ! $this->list_screen_factory->can_create( $list_key ) ) {
			return;
		}

		$settings = [
			'list_id' => ListScreenId::generate()->get_id(),
			'title'   => $title,
		];

		// Copy settings
		if ( $clone && $list_id && $this->storage->exists( $list_id ) ) {
			$clone_list_screen = $this->storage->find( $list_id );

			if ( $clone_list_screen ) {
				$settings['columns'] = $clone_list_screen->get_settings();
				$settings['preferences'] = $clone_list_screen->get_settings();
			}
		}

		$list_screen = $this->list_screen_factory->create( $list_key, $settings );

		$this->storage->save( $list_screen );
		$this->order->add( $list_screen->get_key(), $list_screen->get_layout_id() );

		wp_redirect( $list_screen->get_edit_link() );
		exit;

	}

}