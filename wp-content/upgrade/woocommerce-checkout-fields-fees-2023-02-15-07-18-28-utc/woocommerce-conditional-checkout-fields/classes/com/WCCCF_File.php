<?php 
class WCCCF_File
{
	var $to_remove_from_file_name = array(".php", "../");
	public function __construct()
	{
		add_action( 'wp_ajax_wcccf_file_chunk_upload', array( &$this, 'ajax_manage_file_chunk_upload' ));
		add_action( 'wp_ajax_nopriv_wcccf_file_chunk_upload', array( &$this, 'ajax_manage_file_chunk_upload' ));
		
		add_action( 'wp_ajax_wcccf_delete_tmp_uploaded_file', array( &$this, 'delete_tmp_uploaded_file' ));
		add_action( 'wp_ajax_nopriv_wcccf_delete_tmp_uploaded_file', array( &$this, 'delete_tmp_uploaded_file' ));
		
		add_action( 'wp_ajax_wcccf_delete_uploaded_file', array( &$this, 'delete_uploaded_file' ));
		
		add_action('init', array( &$this, 'delete_unused_tmp_files' ));
	}
	function ajax_manage_file_chunk_upload()
	{
		global $wcccf_session_model ;
		
		$buffer = 5242880; //1048576; //1mb
		$target_path = $this->get_temp_dir_path();
		$tmp_name = $_FILES['wcccf_file_chunk']['tmp_name'];
		$size = $_FILES['wcccf_file_chunk']['size'];
		$current_chunk_num = $_POST['wcccf_current_chunk_num'];
		$file_name = str_replace($this->to_remove_from_file_name, "",$_POST['wcccf_file_name']);
		$tmp_file_name = $_POST['wcccf_current_upload_session_id']."_".$file_name;
		$upload_field_name = str_replace($this->to_remove_from_file_name, "", $_POST['wcccf_upload_field_name']);
		$wcccf_is_last_chunk = $_POST['wcccf_is_last_chunk'] == 'true' ? true : false;
	

		$com = fopen($target_path.$tmp_file_name, "ab");
		$in = fopen($tmp_name, "rb");
			if ( $in ) 
				while ( $buff = fread( $in, $buffer ) ) 
				   fwrite($com, $buff);
				 
			fclose($in);
		fclose($com);
		
		wp_die();
	}
	function delete_tmp_uploaded_file()
	{
		$file_to_delete = isset($_POST['file_to_delete']) ? $_POST['file_to_delete'] : null;
		$target_path = $this->get_temp_dir_path();
		
		if(isset($file_to_delete))
		{
			try{
				@unlink($target_path.$file_to_delete);
			}catch(Exception $e){};
		}
		wp_die();
	}
	function delete_uploaded_file()
	{
		global $wcccf_order_model;
		
		$file_to_delete = isset($_POST['file_to_delete']) ? $_POST['file_to_delete'] : null;
		$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : null;
		$meta_key = isset($_POST['meta_key']) ? $_POST['meta_key'] : null;
		$target_path = $this->get_temp_dir_path($order_id);
		
		if(isset($file_to_delete))
		{
			try{
				@unlink($target_path.$file_to_delete);
				$wcccf_order_model->delete_meta($order_id, $meta_key);
				
			}catch(Exception $e){};
		}
		wp_die();
	}
	function delete_unused_tmp_files()
	{
		$files = glob($this->get_temp_dir_path()."*");
		$now   = time();

		if(is_array($files) && !empty($files))
			foreach ($files as $file) 
				if (is_file($file)) 
				  if (basename ($file) != "index.html" && $now - filemtime($file) >= 60 * 60 /* * 24 * 2 */) //1 hpur
				  {
					  try{
							@unlink($file);
						}catch(Exception $e){};
				  }
			
		  
	}
	private function get_temp_dir_path($order_id = null, $baseurl = false)
	{
		$upload_dir = wp_upload_dir();
		
		$temp_dir = !$baseurl ? $upload_dir['basedir']. '/wcccf/' : $upload_dir['baseurl']. '/wcccf/';
		$temp_dir .= isset($order_id) && $order_id !=0 ? $order_id.'/': 'tmp/';
		
		if(!$baseurl)
		{
			if (!file_exists($temp_dir)) 
					mkdir($temp_dir, 0775, true);
			
			if( !file_exists ($temp_dir.'index.html'))
				$this->create_empty_file ($temp_dir.'index.html');
		}
		return $temp_dir;
	}
	private function create_empty_file($path)
	{
		$file = fopen($path, 'w'); 
		fclose($file); 
	}
	private function create_js_and_css_custom_folders()
	{
		if( !file_exists (WP_CONTENT_DIR.'/wcccf_custom_code/index.html'))
		{
			@mkdir(WP_CONTENT_DIR.'/wcccf_custom_code', 0755, true);
			$this->create_empty_file (WP_CONTENT_DIR.'/wcccf_custom_code/index.html');
		}
			
		if( !file_exists (WP_CONTENT_DIR.'/wcccf_custom_code/js/index.html'))
		{
			@mkdir(WP_CONTENT_DIR.'/wcccf_custom_code/js', 0755, true);
			$this->create_empty_file (WP_CONTENT_DIR.'/wcccf_custom_code/js/index.html');
		}
			
		if( !file_exists (WP_CONTENT_DIR.'/wcccf_custom_code/css/index.html'))
		{
			@mkdir(WP_CONTENT_DIR.'/wcccf_custom_code/css', 0755, true);
			$this->create_empty_file (WP_CONTENT_DIR.'/wcccf_custom_code/css/index.html');
		}
	}
	public function delete_all_order_files($order_id)
	{
		$file_folder = $this->get_temp_dir_path($order_id);
		
		$it = new RecursiveDirectoryIterator($file_folder, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it,
					 RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($file_folder);
	}
	public function process_files_on_order_save($order_id, $files_data)
	{
		global $wcccf_order_model;
		$this->process_files_after_checkout($order_id, $files_data);
		
		//Order meta 
		foreach($files_data as $id => $file_data) //$files_data[{cid}]
		{
			 if(!isset($file_data['file_name']) || $file_data['file_name'] == "")
				 continue;
		
			$unique_file_name = $file_data['file_name_tmp_prefix']."_".$file_data['file_name'];
			$meta_key = "_".$file_data['form_type']."_wcccf_id_".$id; //billing_wcccf_id_F2k4Lud072maLgS
			$wcccf_order_model->save_meta($order_id, $meta_key, $unique_file_name);
		}
	}
	public function process_files_after_checkout($order_id, $files_data)
	{
		/* 
		 ["F2k4Lud072maLgS"]=>
		  array(3) {
			["file_name"]=>
			string(10) "300dpi.jpg"
			["file_name_tmp_prefix"]=>
			string(7) "1687924"
			["form_type"]=>
			string(7) "billing"
		  }
		}
		*/
		//$file_info = array();
		foreach($files_data as $id => $file_data) //$files_data[{cid}]
		{
			 if(!isset($file_data['file_name']) || $file_data['file_name'] == "")
				 continue;
			 
			$upload_dir = wp_upload_dir();
			$upload_complete_dir = $upload_dir['basedir']. '/wcccf/'.$order_id.'/';
			if (!file_exists($upload_complete_dir)) 
					mkdir($upload_complete_dir, 0775, true);
				
			$tmp_file_folder = $this->get_temp_dir_path();
			$unique_file_name = $file_data['file_name_tmp_prefix']."_".$file_data['file_name'];
			$original_file_name = $file_data['file_name'];
			rename($tmp_file_folder.$unique_file_name, $upload_complete_dir.$unique_file_name);


			if( !file_exists ($upload_dir['basedir'].'/wcccf/index.html'))
				$this->create_empty_file ($upload_dir['basedir'].'/wcccf/index.html');
				

			if( !file_exists ($upload_dir['basedir'].'/wcccf/'.$order_id.'/index.html'))
				$this->create_empty_file ($upload_dir['basedir'].'/wcccf/'.$order_id.'/index.html');

			

		}
		//return $file_info;
	}
	public function get_order_file_folder($order_id)
	{
		return $this->get_temp_dir_path($order_id, true);
	}
	private function generate_unique_file_name($dir, $name, $ext = "")
	{
		return rand(0,1000000)."_".$name.$ext;
	}
	public function get_file_download_link($file_name, $order, $id="")
	{
		$html_id = $id != "" ? ' id="'.$id.'" ' : '';
		return '<a '.$html_id.' class="button" href="'.$this->get_order_file_folder($order->get_id()).$file_name.'" target="_blank" download>'.esc_html__('Download', 'woocommerce-conditional-checkout-fields').'</a>';
	}
	
	public function get_css_and_js_files()
	{
		$result = array('css'=>array(), 'js' => array());
		
		
		//setup
		$this->create_js_and_css_custom_folders();
			
		//js 
		foreach (glob(WP_CONTENT_DIR.'/wcccf_custom_code/js/*.js') as $file) 
		{
		  $result['js'][] = home_url()."/wp-content/wcccf_custom_code/js/".basename($file);
		}
		//css
		foreach (glob(WP_CONTENT_DIR.'/wcccf_custom_code/css/*.css') as $file) 
		{
		  $result['css'][] = home_url()."/wp-content/wcccf_custom_code/css/".basename($file);
		}
		return $result;
	}
}
?>