<?php


namespace Palasthotel\WordPress\UseMemcached;


class Tools {
	const SLUG = "use_memcached";

	/**
	 * Tools constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_action( 'admin_init', array($this, 'save_settings') );
	}

	/**
	 * get url to tools page
	 * @return string
	 */
	public function getUrl(){
		return admin_url("admin.php?page=use-memcached");
	}

	function save_settings(){

		if(!current_user_can("manage_options")) return;

		if(
			isset($_POST['use_memcached_disable_toggle'])
			&&
			$_POST['use_memcached_disable_toggle'] == "yes"
		){
			$this->plugin->memcache->toggleEnabled();
			wp_redirect(add_query_arg("use-memcached-flush", "do-flush",$this->getUrl()));
		}
		if(isset($_GET["use-memcached-flush"]) && $_GET["use-memcached-flush"] == "do-flush"){
			$this->plugin->memcache->flush();
			wp_redirect($this->getUrl());
		}
	}
}