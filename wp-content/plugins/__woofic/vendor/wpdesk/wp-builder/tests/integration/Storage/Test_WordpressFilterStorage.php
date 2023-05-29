<?php

use WPDesk\Plugin\Flow\Initialization\Simple\SimpleStrategy;
use WPDesk\PluginBuilder\Storage\Exception\ClassNotExists;
use WPDesk\PluginBuilder\Storage\WordpressFilterStorage;

class Test_WordpressFilterStorage extends WP_UnitTestCase {
	public function test_can_read_and_write() {
		$storage = new WordpressFilterStorage();

		$storage->add_to_storage( Test_WordpressFilterStorage::class, $this );
		$this->assertInstanceOf( Test_WordpressFilterStorage::class,
			$storage->get_from_storage( Test_WordpressFilterStorage::class ),
			'Returned instance should be the same as saved' );
	}

	public function test_empty_storage_is_empty() {
		$storage = new WordpressFilterStorage();

		$this->expectException( ClassNotExists::class );
		$storage->get_from_storage( Test_WordpressFilterStorage::class );
	}
}
