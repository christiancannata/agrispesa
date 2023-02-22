<?php 
class WCCCF_Code
{
	var $code_data = null;
	public function __construct()
	{
	}
	public function save_code($data_to_save)
	{
		update_option('wcccf_code_data', $data_to_save);
		$this->code_data = null;
	}
	public function get_code($code_type = 'javascript')
	{
		$result = !isset($this->code_data) ? get_option('wcccf_code_data') : $this->code_data;
		$this->code_data = $result;
		
		$index = 0;
		
		
		return isset($result) && is_array($result) && isset($result[$code_type]) ? stripslashes($result[$code_type]) : "";
	}
}
?>