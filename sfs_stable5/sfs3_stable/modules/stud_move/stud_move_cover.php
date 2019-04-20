<?php
	// $Id: stud_move_cover.php 7712 2013-10-23 13:31:11Z smallduh $
	//新增一個 zipfile
	$ttt=new easyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');

	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->add_file("meta.xml");
	$data=$ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");
	$sql="select * from school_base";
	$rs=$CONN->Execute($sql);
	$temp_sty["school_name"]=$rs->fields['sch_cname'];
	$temp_sty["sel_year"]=Num2CNum($sel_year);
	$temp_sty["sel_seme"]=Num2CNum($sel_seme);
	$today=explode("-",date("Y-m-d",mktime(date("m"),date("d"),date("Y"))));
	$temp_sty["year"]=Num2CNum(intval($today[0]-1911));
	$temp_sty["month"]=Num2CNum(intval($today[1]));
	$temp_sty["day"]=Num2CNum(intval($today[2]));
	$replace_data=$ttt->change_temp($temp_sty,$data);
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$oo_path.sxw");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	exit;
?>
