<?php
require "config.php";

sfs_check();
$type = $_POST['type'];
$obj = new ajax_input_sight_noti($type);

class ajax_input_sight_noti {

	function ajax_input_sight_noti($type) {

		switch ($type) {
			// 更新處置代號
			case  'update_sight_noti':
				$this->update_sight_noti();
				break;
		}

	}

	// 更新視力狀態
	function update_sight_noti()
	{
		global $CONN;

		$id = $_POST['id'];
		$arr = explode("_",$id);
		$col = strip_tags($arr[0]);
		$side = strip_tags($arr[1]);
		$student_sn = (int)$arr[2];
		$year_seme = (int)$_POST['year_seme'];
		$year = (int)substr($year_seme,0,-1);
		$semester = (int)substr($year_seme,-1);
		$value = ($_POST['value']=='true')?1:0;
		// 近視與遠視互斥
		if ($col == 'My' && $value)
			$ss = ', Hy=0';
		else if ($col == 'Hy' && $value)
			$ss = ', My=0';
		else
			$ss ='';
		$query = "UPDATE health_sight SET $col=$value $ss WHERE
		year=$year AND semester=$semester AND student_sn=$student_sn AND side='$side'";
		echo $query;
		$CONN->Execute($query) or die($query) ;

	}

}