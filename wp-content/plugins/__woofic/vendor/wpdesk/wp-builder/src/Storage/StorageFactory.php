<?php

namespace WPDesk\PluginBuilder\Storage;

class StorageFactory {

	/**
	 * @return PluginStorage
	 */
	public function create_storage() {
		return new WordpressFilterStorage();
	}
}

