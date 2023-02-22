<?php class WCCCF_DateTime
{
	function __construct()
	{
		
	}
	function time_is_greater_than($time1 , $time2)
	{
		if(!isset($time1) || $time1 == "")
			return false;
		
		$time2 == 'now' ? date('H:i') : $time2;
		
		if(strtotime($time1) < strtotime($time2))
			return false;
		
		return true;
	}
	function time_now()
	{
		return date('H:i');
	}
}
?>