<?php
//$Id$
require "config.php";

sfs_check();
$type = $_POST['type'];
$obj = new ajax_input_sight_status($type);


class ajax_input_sight_status
{

	function ajax_input_sight_status($type)
	{
		switch ($type) {
			// 更新處置代號
			case  'update_health_sight_manage_id':
				$this->update_health_sight_manage_id();
				break;
			// 更新就診醫療院所
			case 'update_health_sight_hospital':
				$this->update_health_sight_hospital();
		}
	}

	//更新就診醫療院所

	function update_health_sight_hospital()
	{
		global $CONN;

		$arr = explode("-", $_POST['id']);
		$year = (int) substr($arr[0], 0, -1);
		$semester = (int) substr($arr[0], -1);
		$student_sn = (int) $arr[1];
		if ($val = $_POST['val']) {
			$val = strip_tags($_POST['val']);
			$val = iconv("utf8" , "big5", $val);
		}
		$query = "UPDATE health_sight SET hospital='$val' WHERE year=$year AND semester=$semester AND student_sn=$student_sn";
		$res = $CONN->Execute($query) or die($query);
		//echo $query;
		echo $res;
	}

	// 更新處置代號
	function update_health_sight_manage_id()
	{
		global $CONN;

		$yearSeme = $_POST['year_seme'];
		$year = (int) substr($yearSeme,0,-1);
		$semester = (int) substr($yearSeme,-1);
		$student_sn = (int) $_POST['student_sn'];
		$side = $_POST['side'];
		$diag= strip_tags($_POST['diag']);
		if ($diag<>'') {
			$id ='N';
			$diagSql = " , diag='".iconv("utf8","big5",$diag)."' ";
		}
		else {
			$id =$_POST['id'];
			$diagSql = '';
		}
		$query = "UPDATE health_sight SET manage_id = '$id' $diagSql WHERE year=$year
		AND semester=$semester  AND student_sn=$student_sn";
		$CONN->Execute($query) or die($query);
		if ($diag<>'') {
			//header("Content-type: text/html; charset=big5");
			echo $diag;
		}
		else
		echo $id;
	}

}



