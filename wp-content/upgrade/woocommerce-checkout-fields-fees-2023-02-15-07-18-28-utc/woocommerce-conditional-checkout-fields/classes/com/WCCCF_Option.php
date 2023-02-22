<?php 
class WCCCF_Option
{
	var $cache = null;
	function __construct()
	{
		
	}
	function save_options($options_data)
	{
		//wcccf_var_dump($options_data);
		update_option( 'wcccf_option', $options_data);
	}
	function get_options()
	{
		if(isset($this->cache))
			return $this->cache;
		
		$options = get_option('wcccf_option'); //false if not existing
		$this->cache = $options;
		return $options;
	}
}
?>