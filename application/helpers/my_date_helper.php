<?php
/**
 * Date Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Tisa Pathumwan
 * 
 * 
 * 
 * 
 * 
 */

// ------------------------------------------------------------------------
function clear_date_letter($str){
	$str = preg_replace('/\//', '', $str);
	$str = preg_replace('/-/', '', $str);
	$str = preg_replace('/ /', '', $str);
	$str = preg_replace('/:/', '', $str);
	return $str;
}

function get_time_pattern($strTime,$symbolTime){
	$timeFormat = "";
	$strTime = clear_date_letter($strTime);
	if(strlen($strTime) == 6){
		$hour = substr($strTime,0,2);
		$minute = substr($strTime,2,2);
		$second = substr($strTime,4,2);
		$timeFormat = "$hour$symbolTime$minute$symbolTime$second";
	}
	return $timeFormat;
}

function get_datetime_pattern($pattern, $strDate, $initVal=""){
	$MONTH_TH_SHORT = array(1=>"ม.ค.",2=>"ก.พ.",3=>"มี.ค.",4=>"เม.ย.",5=>"พ.ค.",6=>"มิ.ย.",7=>"ก.ค.",8=>"ส.ค.",9=>"ก.ย.",10=>"ต.ค.",11=>"พ.ย.",12=>"ธ.ค.");
	$MONTH_TH_LONG = array(1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฎาคม",8=>"สิงหาคม",9=>"กันยายน",10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม");
	$MONTH_EN_SHORT = array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec");
	$MONTH_EN_LONG = array(1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December");

	$year = ""; $month = ""; $date = ""; $time = ""; $result = "";
	
	$strDate = get_datetime_number($strDate,"ymdhis");	
	if(strlen($strDate) == 14){
		preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{6})/", $strDate, $regs);
		$year = $regs[1]; $month = $regs[2]; $date = $regs[3]; $time = get_time_pattern($regs[4],":");
	}else if(strlen($strDate) == 8){
		preg_match("/([0-9]{4})([0-9]{2})([0-9]{2})/", $strDate, $regs);
		$year = $regs[1]; $month = $regs[2]; $date = $regs[3];
	}else{
		return $initVal;
	}
	
	if(is_blank($time)){
		$time = "00:00:00";
	}
		
	//echo "Year = ".$year."<br>";
	//echo "Month = ".$month."<br>";
	//echo "Date = ".$date."<br>";
	//echo "Time = ".$time."<br>";

	if($pattern == "") $pattern = "dmy_EN_SHORT";
	
	switch($pattern)
	{
		case "db_date_format" : $result = $year."-".$month."-".$date; break; 
		case "db_datetime_format" : $result = $year."-".$month."-".$date." ".$time; break; 
		case "dmy_dot" : $result = $date.".".$month.".".$year; break; 
		case "dmyhis_dot" : $result = $date.".".$month.".".$year." ".$time; break; 
			
		case "dmy_EN_SHORT" : $result = $date." ".$MONTH_EN_SHORT[number_format($month)]." ".$year; break; 
		case "dmy_EN_LONG" : $result = $date." ".$MONTH_EN_LONG[number_format($month)]." ".$year; break; 
		case "dmy_TH_SHORT" : $result = $date." ".$MONTH_TH_SHORT[number_format($month)]." ".($year+543); break; 
		case "dmy_TH_SHORT_NOZEROPAD" : $result = ($date+0)." ".$MONTH_TH_SHORT[number_format($month)]." ".($year+543); break; 
		case "dmy_TH_SHORT_2DIGITYEAR" : $result = ($date+0)." ".$MONTH_TH_SHORT[number_format($month)]." ".substr(($year+543),-2); break; 
		case "dmy_TH_LONG" : $result = $date." ".$MONTH_TH_LONG[number_format($month)]." ".($year+543); break; 

		case "my_EN_SHORT" : $result = $MONTH_EN_SHORT[number_format($month)]." ".$year; break; 
		case "my_EN_LONG" : $result = $MONTH_EN_LONG[number_format($month)]." ".$year; break; 
		case "my_TH_SHORT" : $result = $MONTH_TH_SHORT[number_format($month)]." ".($year+543); break; 
		case "my_TH_SHORT_NOZEROPAD" : $result = $MONTH_TH_SHORT[number_format($month)]." ".($year+543); break; 
		case "my_TH_LONG" : $result = $MONTH_TH_LONG[number_format($month)]." ".($year+543); break; 

		case "dmyhis_EN_SHORT" : $result = $date." ".$MONTH_EN_SHORT[number_format($month)]." ".$year." ".$time; break; 
		case "dmyhis_EN_LONG" : $result = $date." ".$MONTH_EN_LONG[number_format($month)]." ".$year." ".$time; break; 
		case "dmyhis_TH_SHORT" : $result = $date." ".$MONTH_TH_SHORT[number_format($month)]." ".($year+543)." ".$time; break; 
		case "dmyhis_TH_SHORT_NOZEROPAD" : $result = ($date+0)." ".$MONTH_TH_SHORT[number_format($month)]." ".($year+543)." ".$time; break; 
		case "dmyhis_TH_LONG" : $result = $date." ".$MONTH_TH_LONG[number_format($month)]." ".($year+543)." ".$time; break; 
		
		case "dmyhis_FULL_TH_SHORT" : $result = get_th_num($date)." ".$MONTH_TH_LONG[number_format($month)]." พ.ศ. ".get_th_num($year+543)." ".$time; break; 

		default : $result = date( $pattern, strtotime($strDate) );

	}
	
	return $result;
}

function get_datetime_number($strDate="",$format="dmy",$initVal=""){
	$year = ""; $month = ""; $date = ""; $time = "";
	//echo "orginal = ".$strDate."<br>";
	$strDate = preg_replace("/\//","-",$strDate);
	$tmp = explode(" ",$strDate);
	$tmpDate = explode("-",$tmp[0]);
	$strDate = "";
	foreach($tmpDate as $item){
		if(strlen($item) == 1){
			$strDate .= "0";
		}
		$strDate .= $item;
	}
	if(count($tmp) > 1) $strDate .= $tmp[1];
	
	$strDate = clear_date_letter($strDate);
	//echo "new = ".$strDate."<br>";
	
	if(strlen($strDate) == 14){
		preg_match("/([0-9]{1,2})([0-9]{1,2})([0-9]{4})([0-9]{6})/", $strDate, $regs);
		if($regs[3] < 1500){
			preg_match("/([0-9]{4})([0-9]{1,2})([0-9]{1,2})([0-9]{6})/", $strDate, $regs);
			$year = $regs[1]; $month = $regs[2]; $date = $regs[3]; $time = $regs[4];
		}else{
			$year = $regs[3]; $month = $regs[2]; $date = $regs[1]; $time = $regs[4];
		}
	}else if(strlen($strDate) == 8){
		preg_match("/([0-9]{1,2})([0-9]{1,2})([0-9]{4})/", $strDate, $regs);
		if($regs[3] < 1500){
			preg_match("/([0-9]{4})([0-9]{1,2})([0-9]{1,2})/", $strDate, $regs);
			$year = $regs[1]; $month = $regs[2]; $date = $regs[3];
		}else{
			$year = $regs[3]; $month = $regs[2]; $date = $regs[1];
		}
	}else if(strlen($strDate) == 6){
		$time = $strDate;
	}
	
	//echo "Year = ".$year."<br>";
	//echo "Month = ".$month."<br>";
	//echo "Date = ".$date."<br>";
	//echo "Time = ".$time."<br>";
	
	$result = $initVal;
	if(checkdate((int) $month, (int) $date, (int) $year)){
		switch($format)
		{
			case "dmy" : $result = $date.$month.$year; break;
			case "dmyhis" : $result = $date.$month.$year.$time; break;
			case "ymd" : $result = $year.$month.$date; break;
			case "ymdhis" : $result = $year.$month.$date.$time; break;
			case "his" : $result = $time; break;
			case "d" : $result = $date; break;
			case "m" : $result = $month; break;
			case "Y" : $result = $year; break;
			//case "dmy_EN_SHORT" : $result = $date." ".$MONTH_EN_SHORT[number_format($month)]." ".$year; break; 
			//case "dmy_EN_LONG" : $result = $date." ".$MONTH_EN_LONG[number_format($month)]." ".$year; break; 

		}
	}
	//echo "<br>Result = ".$result."<br>";
	return $result;
}

function get_db_now($format="%Y-%m-%d %H:%i:%s"){
	$time = time();
	return mdate($format, $time); 
}

function get_diff_date($strDate1,$strDate2,$unit="date"){
	$diff_date = 0;
	if($unit == "sec"){
		$unit_value = 1; 
	}else if($unit == "min"){
		$unit_value = 60;
	}else if($unit == "hour"){
		$unit_value = 60 * 60; // 1 Hour = 60*60
	}else{
		$unit_value = 60 * 60 * 24; // 1 day = 60*60*24
	}
	// echo "strDate1 = $strDate1 . strDate2 = $strDate2 <BR />";
	$strDate1 = ($unit != "date") ? get_datetime_number($strDate1,"ymdhis","") : get_datetime_number($strDate1,"ymd","");
	$strDate2 = ($unit != "date") ? get_datetime_number($strDate2,"ymdhis","") : get_datetime_number($strDate2,"ymd","");
	// echo "strDate1 = $strDate1 . strDate2 = $strDate2 <BR />";
	if($strDate1 != "" && $strDate2 != ""){
		$diff_date = ( strtotime($strDate1) - strtotime($strDate2) ) / ( $unit_value );
	}
	// echo "$strDate1 - $strDate2 = ". $diff_date."<BR>";
	return $diff_date;
}

function get_th_num($str){
	$DAY_NUM_TH= array(0=>"๐",1=>"๑",2=>"๒",3=>"๓",4=>"๔",5=>"๕",6=>"๖",7=>"๗",8=>"๘",9=>"๙");
	$result = "";
	$str =number_format(clear_date_letter($str),'','','');
	$str_arr = str_split($str);
		foreach($str_arr as $item){
			$result .= $DAY_NUM_TH[number_format($item)];
		}
	return $result;
}

function get_month_name($strMonth="",$format="",$initVal=""){
	$result = $initVal;
	
	$MONTH_TH_SHORT = array(1=>"ม.ค",2=>"ก.พ",3=>"มี.ค",4=>"เม.ย",5=>"พ.ค",6=>"มิ.ย",7=>"ก.ค",8=>"ส.ค",9=>"ก.ย",10=>"ต.ค",11=>"พ.ย",12=>"ธ.ค");
	$MONTH_TH_LONG = array(1=>"มกราคม",2=>"กุมภาพันธ์",3=>"มีนาคม",4=>"เมษายน",5=>"พฤษภาคม",6=>"มิถุนายน",7=>"กรกฎาคม",8=>"สิงหาคม",9=>"กันยายน",10=>"ตุลาคม",11=>"พฤศจิกายน",12=>"ธันวาคม");
	$MONTH_EN_SHORT = array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec");
	$MONTH_EN_LONG = array(1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"Auguest",9=>"September",10=>"October",11=>"November",12=>"December");
	
	$month = number_format($strMonth);	
	if($month <= 0 || $month > 12 ) return $initVal;

	//echo "Month = ".$month."<br>";
	
	if($format == "") $format = "EN_SHORT";
	
	switch($format)
	{
		case "EN_SHORT" : $result = $MONTH_EN_SHORT[number_format($month)]; break; 
		case "EN_LONG" : $result = $MONTH_EN_LONG[number_format($month)]; break; 
		case "TH_SHORT" : $result = $MONTH_TH_SHORT[number_format($month)]; break; 
		case "TH_LONG" : $result = $MONTH_TH_LONG[number_format($month)]; break; 
	}
	
	return $result;
}

function get_pretty_date($strDate, $pattern="dmyhis_EN_SHORT", $initVal=""){  
	$today = date("YmdHis");
	// echo "strDate = $strDate , today = $today <BR />";
	$integer =  get_diff_date($strDate,$today,"sec");
	// echo "integer = $integer <BR />";

	$return = get_datetime_pattern($pattern ,$strDate, $initVal);
	$minutes = '';
	$hours = '';
	$days = '';
	$weeks = '';
	$years = '';

	//Case time in future
	if($integer >0){
		return $return;
	}

	$seconds=abs($integer);
	// echo "seconds = $seconds <BR />";

	$minutes=floor($seconds/60);
	$hours=floor($minutes/60);
	$days=floor($hours/24);
	$weeks=floor($days/7);
	$years=floor($weeks/7);

	// echo "weeks = $weeks , days = $days , hours = $hours , minutes = $minutes , seconds = $seconds <BR />";
	if($years > 0 || $weeks > 0 || $days > 1){
		if (get_datetime_pattern("H:i:s", $strDate, "") == "00:00:00") {
			$return = get_datetime_pattern("dmy_EN_SHORT" ,$strDate, $initVal);
			return $return;
		}
		else {
			return $return;
		}
		
	}
	if($years == 0 && $weeks == 0 && $days == "1"){
		if (get_datetime_pattern("H:i:s", $strDate, "") == "00:00:00") {
			return "Yesterday";
		}
		else {
			return "Yesterday at ".get_datetime_pattern("H:i:s", $strDate, "");
		}
	}

	if($years == 0 && $weeks == 0 && $days == 0){
		if($hours > 0){
			return "$hours hr".( ($hours>1)?"s":"" );
		}else if($minutes > 0){
			return "$minutes min".( ($minutes>1)?"s":"" );
		}else if($seconds >= 0){
			// return "$seconds sec".( ($seconds>1)?"s":"" );
			return "Just now";
		}
	}
	return $return;
}

function convert_date_range_to_array( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
	$dates = array();
	$current = strtotime( $first );
	$last = strtotime( $last );

	while( $current <= $last ) {

		$dates[] = date( $format, $current );
		$current = strtotime( $step, $current );
	}
	return $dates;
}

?>