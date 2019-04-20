<?php
// $Id: act_data_function.php 5310 2009-01-10 07:57:56Z hami $

function change_addr($addr,$mode=0) {
	//¿¤¥«
	$temp_str = split_str($addr,"¿¤",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"¥«",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

    //¶mÂí	
	$temp_str = split_str($addr,"¶m",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"Âí",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"¥«",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"°Ï",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//§ø¨½
	$temp_str = split_str($addr,"§ø",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"¨½",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//¾F
	$temp_str = split_str($addr,"¾F",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//¸ô
	$temp_str = split_str($addr,"¸ô",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"µó",1);
	
	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//¬q
	$temp_str = split_str($addr,"¬q",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//«Ñ
	$temp_str = split_str($addr,"«Ñ",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//§Ë
	$temp_str = split_str($addr,"§Ë",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//¸¹
	$temp_str = split_str($addr,"¸¹",$mode);
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];
	
	//¼Ó
	$temp_str = split_str($addr,"¼Ó",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//¼Ó¤§
	if ($addr != "") {
		if ($mode)
			$temp_str = $addr;
		else
			$temp_str = substr(chop($addr),2);
	} else
		$temp_str ="";
		
	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
      	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}

?>