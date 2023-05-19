<?php

namespace ACP\Admin\Section\Partial;

use AC\Renderable;
use AC\Settings\Option\EditButton;
use AC\View;
use ACP\Settings\Option;

class LayoutTabs implements Renderable {

	/**
	 * @var EditButton
	 */
	private $option;

	public function __construct() {
		$this->option = new Option\LayoutStyle();
	}

	/**
	 * @return string
	 */
	public function render() {
		$options = [
			Option\LayoutStyle::OPTION_TABS     => _x( 'Tabs', 'table view display', 'codepress-admin-columns' ),
			Option\LayoutStyle::OPTION_DROPDOWN => _x( 'Dropdown', 'table view display', 'codepress-admin-columns' ),
		];

		$setting = new View( [
			'options' => json_encode( $options ),
			'value'   => $this->option->get() ?: Option\LayoutStyle::OPTION_DROPDOWN,
		] );
		$setting->set_template( 'admin/settings/layout-style' );

		$view = new View( [ 'setting' => $setting->render() ] );

		return $view->set_template( 'admin/settings/setting-row' )->render();
	}

}