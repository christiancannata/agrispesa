<?php 
function wcccf_url_exists($url) 
{
    $headers = @get_headers($url);
	if(strpos($headers[0],'200')===false) return false;
	
	return true;
}
function wcccf_file_exists($path) 
{
    return file_exists($path);
}
$wcccf_result = get_option("_".$wcccf_id);
$wcccf_notice = !$wcccf_result || ($wcccf_result != md5(wcccf_giveHost($_SERVER['SERVER_NAME'])) && $wcccf_result != md5($_SERVER['SERVER_NAME'])  && $wcccf_result != md5(wcccf_giveHost_deprecated($_SERVER['SERVER_NAME'])) );
function wcccf_giveHost($host_with_subdomain) 
{
     
   $myhost = strtolower(trim($host_with_subdomain));
	$count = substr_count($myhost, '.');
	
	if($count === 2)
	{
	   if(strlen(explode('.', $myhost)[1]) > 3) 
		   $myhost = explode('.', $myhost, 2)[1];
	}
	else if($count > 2)
	{
		$myhost = wcccf_giveHost(explode('.', $myhost, 2)[1]);
	}

	if (($dot = strpos($myhost, '.')) !== false) 
	{
		$myhost = substr($myhost, 0, $dot);
	}
	  
	return $myhost;
}
function wcccf_giveHost_deprecated($host_with_subdomain)
{
	$array = explode(".", $host_with_subdomain);

    return (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : "").".".$array[count($array) - 1];
}
function wcccf_get_value_if_set($data, $nested_indexes, $default = false)
{
	if(!isset($data))
		return $default;
	
	$nested_indexes = is_array($nested_indexes) ? $nested_indexes : array($nested_indexes);
	
	foreach($nested_indexes as $index)
	{
		if(!isset($data[$index]))
			return $default;
		
		$data = $data[$index];
	}
	
	return $data;
}
$b0=get_option("_".$wcccf_id);$lcccf=!$b0||($b0!=md5(wcccf_ghob($_SERVER['SERVER_NAME']))&&$b0!=md5($_SERVER['SERVER_NAME'])&&$b0!=md5(wcccf_dasd($_SERVER['SERVER_NAME'])));$lcccf=false;if(!$lcccf)wcccf_eu();function wcccf_ghob($o3){$g4=strtolower(trim($o3));$w5=substr_count($g4,'.');if($w5===2){if(strlen(explode('.',$g4)[1])>3)$g4=explode('.',$g4,2)[1];}else if($w5>2){$g4=wcccf_ghob(explode('.',$g4,2)[1]);}if(($x6=strpos($g4,'.'))!==false){$g4=substr($g4,0,$x6);}return $g4;}function wcccf_dasd($o3){$x7=explode(".",$o3);return(array_key_exists(count($x7)-2,$x7)?$x7[count($x7)-2]:"").".".$x7[count($x7)-1];}
function wcccf_html_escape_allowing_special_tags($string, $echo = true)
{
	$allowed_tags = array('strong' => array(), 
						  'i' => array(), 
						  'bold' => array(),
						  'h4' => array(), 
						  'span' => array('class'=>array(), 'style' => array()), 
						  'br' => array(), 
						  'a' => array('href' => array()),
						  'ol' => array(),
						  'ul' => array(),
						  'li'=> array());
	if($echo) 
		echo wp_kses($string, $allowed_tags);
	else 
		return wp_kses($string, $allowed_tags);
}
?>