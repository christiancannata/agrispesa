<?php
class WCCCF_Wpml
{
	var $current_lang;
	var $before_admin_lang;
	public function __construct()
	{
		add_action('plugins_loaded', array(&$this,'init_ajax_language'));
	}
	public function init_ajax_language()
	{
		if(!isset($_POST['wcccf_wpml_language']) || !$this->wpml_is_active())
			return;
		
		global $sitepress;
		load_plugin_textdomain('woocommerce-conditional-checkout-fields', false, WCFAS_PLUGIN_LANG_PATH );
		$sitepress->switch_lang($_POST['wcccf_wpml_language'], true);
	}
	public function wpml_is_active()
	{
		return class_exists('SitePress');
	}
	public function switch_to_admin_default_lang()
	{
		if(!$this->wpml_is_active())
			return; 
		
		global $sitepress_settings,$sitepress,$locale;			
		$this->before_admin_lang = ICL_LANGUAGE_CODE;
		$sitepress->switch_lang($sitepress_settings['admin_default_language']);		
		$locale = $sitepress_settings['admin_default_language']."_".strtoupper($sitepress_settings['admin_default_language']); 
		load_plugin_textdomain('woocommerce-conditional-checkout-fields', false, WCFAS_PLUGIN_LANG_PATH);
	}
	public function restore_from_admin_default_lang()
	{
		if(!$this->wpml_is_active())
			return;
		
		global $sitepress,$locale;
		$sitepress->switch_lang($this->before_admin_lang);
		$locale = $this->before_admin_lang."_".strtoupper($this->before_admin_lang);
		load_plugin_textdomain('woocommerce-conditional-checkout-fields', false, WCFAS_PLUGIN_LANG_PATH);
	}
	public function get_all_translation_ids_for_multiple_ids($post_ids, $post_type = "product")
	{
		if(!is_array($post_ids) || !class_exists('SitePress'))
			return false;
		
		$translations = array();
		foreach($post_ids as $post_id)
		{
			$results = $this->get_all_translation_ids($post_id, $post_type);
			if($results != false)
				foreach((array)$results as $result)
					$translations[] = $result;
		}
		
		return $translations;
	}
	public function get_all_translation_ids($post_id, $post_type = "product")
	{
		if(!class_exists('SitePress'))
			return false;
		
		global $sitepress, $wpdb;
		$translations = array();
		$translations_result = array();
		
		//if($post_type == "product")
		{
			$trid = $sitepress->get_element_trid($post_id, 'post_'.$post_type);
			$translations = $sitepress->get_element_translations($trid, $post_type);
			//wcccf_var_dump($translations);
			foreach($translations as $language_code => $item)
			{
				if($language_code != $sitepress->get_default_language())
					$translations_result[] = $item->element_id;
			}
			//wcccf_var_dump($translations_result);
		}
		
		return !empty($translations_result) ? $translations_result:false;
	}
	public function get_original_ids($items_array, $post_type = "product")
	{
		if(!class_exists('SitePress'))
			return false;
		
		global $sitepress;
		$original_ids = array();
		foreach($items_array as $item)	
		{
			$item_id = is_object($item) && method_exists($item,'get_id') ? $item->get_id() : $item->id;
			//$item_type = is_object($item) && method_exists($item,'get_type') ? $item->get_type() : $item->type;

			if(function_exists('icl_object_id'))
				$item_translated_id = icl_object_id($item_id, $post_type, true, $sitepress->get_default_language());
			else
				$item_translated_id = apply_filters( 'wpml_object_id', $item_id, $post_type, true, $sitepress->get_default_language() );
			
			if(!in_array($item_translated_id, $original_ids))
				array_push($original_ids, $item_translated_id);
		}
			
		return $original_ids;
	}
	public function get_original_id($item_id, $post_type = "product", $return_original = true)
	{
		if(!class_exists('SitePress'))
			return false;
		
		global $sitepress;
		if(function_exists('icl_object_id'))
			$item_translated_id = icl_object_id($item_id, $post_type, $return_original, $sitepress->get_default_language());
		else
			$item_translated_id = apply_filters( 'wpml_object_id', $item_id, $post_type, $return_original, $sitepress->get_default_language() );
		
		return $item_translated_id;
	}
	public function is_item_a_translation($item_id, $post_type = "product")
	{
		if(!$this->is_active())
			return false;
		
		$result = $this->get_original_id($item_id, $post_type);
		if($item_id != $result)
			return true;
		
		if($post_type == "product_variation")
			$_icl_lang_duplicate_of = get_post_meta( $item_id, '_wcml_duplicate_of_variation', true ); 
		else
			$_icl_lang_duplicate_of = get_post_meta( $item_id, '_icl_lang_duplicate_of', true );
		
		return $_icl_lang_duplicate_of != false ? true : false;
	}
	public function remove_translated_id($items_array, $post_type = "product", $default_language = false)
	{
		if(!class_exists('SitePress'))
			return false;
		global $sitepress;
		$current_language = ICL_LANGUAGE_CODE;
		if($default_language)
			$current_language = $sitepress->get_default_language();
		$filtered_items_list = array();
		foreach($items_array as $item)	
		{
			$item_id = is_object($item) && method_exists($item,'get_id') ? $item->get_id() : $item->id;

			//If in the selected language the $id is the same of the language, is not a transaltion so can be kept
			if(function_exists('icl_object_id'))
				$item_translated_id = icl_object_id($item_id, $post_type, false,$current_language);
			else
				$item_translated_id = apply_filters( 'wpml_object_id', $item_id, $post_type, false, $current_language );
			
			if($item_id == $item_translated_id)
				array_push($filtered_items_list, $item);
		}
			
		return $filtered_items_list ;
	}
	
	public function get_main_language_ids($items_array, $post_type = "product")
	{
		if(!class_exists('SitePress'))
			return $items_array;
		
		global $sitepress;
		$filtered_items_list = array();
		
		foreach($items_array as $item)	
		{
			if(is_object($item))
				$item_id = method_exists($item,'get_id') ? $item->get_id() : $item->id;
			else 
				$item_id = $item;
			
			if(function_exists('icl_object_id'))
				$item_translated_id = icl_object_id($item_id , $post_type, false, $sitepress->get_default_language());
			else
				$item_translated_id = apply_filters( 'wpml_object_id', $item_id , $post_type, false, $sitepress->get_default_language() );
			
			array_push($filtered_items_list, $item_translated_id); 
		}
		
		return $filtered_items_list ;
	}
	public function get_main_language_id($id_to_get_original, $post_type = "product")
	{
		if(!class_exists('SitePress'))
			return $id_to_get_original;
		
		global $sitepress;
		
		if(function_exists('icl_object_id'))
				$id_to_get_original = icl_object_id($id_to_get_original, $post_type, true, $sitepress->get_default_language());
			else
				$id_to_get_original = apply_filters( 'wpml_object_id',$id_to_get_original, $post_type, true, $sitepress->get_default_language() );
			
		return $id_to_get_original;
	}
	
	public function switch_to_default_language()
	{
		if(!$this->wpml_is_active())
			return;
		global $sitepress;
		$this->curr_lang = ICL_LANGUAGE_CODE ;
		$sitepress->switch_lang($sitepress->get_default_language());
	
	}
	public function switch_to_current_language()
	{
		if(!$this->wpml_is_active())
			return;
		
		global $sitepress;
		$sitepress->switch_lang($this->curr_lang);
	}
	public function get_current_language()
	{
		if(!class_exists('SitePress'))
			return get_locale();
		
		return ICL_LANGUAGE_CODE."_".strtoupper(ICL_LANGUAGE_CODE);
	}
	
	function translate_single_string($id, $text, $prefix = "wcccf_label_"  )
	{
		if(!class_exists('SitePress'))
			return $text;
		
		$result = apply_filters( 'wpml_translate_single_string', $text, 'woocommerce-conditional-checkout-fields', 'wcccf_'.$prefix.$id, ICL_LANGUAGE_CODE  );
			
		return $result != "" ? $result : $text;
	}
	function register_single_string($id, $text, $prefix = "wcccf_label_" )
	{
		do_action( 'wpml_register_single_string', 'woocommerce-conditional-checkout-fields', 'wcccf_'.$prefix.$id, $text);
	}
	public function get_default_locale()
	{
		global $sitepress;
													//en_US
		return !$this->wpml_is_active() ? get_locale(): $sitepress->get_locale($sitepress->get_default_language());
	}
	public function get_current_locale()
	{
		global $sitepress;
													//en_US
		return !$this->wpml_is_active() ? get_locale() : $sitepress->get_locale(ICL_LANGUAGE_CODE);
	}
	public function get_langauges_list()
	{
		/* 
		Array
		(
		 [0] => Array
		  (
		   ["code"]=>
			string(2) "en"
			["id"]=>
			string(1) "1"
			["native_name"]=>
			string(7) "English"
			["major"]=>
			string(1) "1"
			["active"]=>
			string(1) "1"
			["default_locale"]=>
			string(5) "en_US"
			["encode_url"]=>
			string(1) "0"
			["tag"]=>
			string(2) "en"
			["missing"]=>
			int(0)
			["translated_name"]=>
			string(7) "English"
			["url"]=>
			string(44) "https://site.com/demo/my-account/"
			["country_flag_url"]=>
			string(95) "https://site.com/demo/wp-content/plugins/sitepress-multilingual-cms/res/flags/en.png"
			["language_code"]=>
			string(2) "en"
		  )
		 */
		 $langs = apply_filters( 'wpml_active_languages', NULL, 'skip_missing=0orderby=id&order=desc' );
		 return !$this->wpml_is_active() || empty($langs) ? array(0 => array("default_locale" => $this->get_default_locale(), "country_flag_url" => "none")) : $langs;
	}	
}
?>