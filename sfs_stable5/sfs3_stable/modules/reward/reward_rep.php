<?php

// $Id: reward_rep.php 8680 2015-12-25 02:57:21Z qfon $

//載入設定檔
include "config.php";
include "../../include/sfs_core_globals.php";
include "../../include/sfs_oo_zip2.php";

$oo_path=$_GET[oo_path];
//新增一個 zipfile 實例
$ttt = new EasyZip;
$ttt->setPath($oo_path);
$ttt->addDir('META-INF');
$ttt->addfile("settings.xml");
$ttt->addfile("styles.xml");
$ttt->addfile("meta.xml");

$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

// 加入換頁 tag
$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="sfs_break_page" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-after="page"/></style:style>',$data);
//拆解 content.xml
$arr1 = explode("<office:body>",$data);
//檔頭
$con_head = $arr1[0]."<office:body>";
$arr2 = explode("</office:body>",$arr1[1]);
//資料內容
$con_body = $arr2[0];
//檔尾
$con_foot = "</office:body>".$arr2[1];

$sql="select * from school_base";
$rs=$CONN->Execute($sql);
$temp_arr["school_name"] = $rs->fields['sch_cname'];
$temp_arr["sch_addr"] = $rs->fields['sch_addr'];
$temp_arr["sch_post_num"] = $rs->fields['sch_post_num'];
$temp_arr["school_tel"]= $rs->fields['sch_phone'];
$sql="select * from school_room where room_id='3'";
$rs=$CONN->Execute($sql);
$temp_arr["room_name"] = $rs->fields['room_name'];

$query = "select * from reward where reward_id='".intval($_GET['reward_id'])."'";
$result = $CONN->Execute($query) or die ($query);
//取出獎懲資料
$rew_date=$result->fields["reward_date"];
$rew_date=(substr($rew_date,0,4)-1911).".".substr($rew_date,5,2).".".substr($rew_date,8,2);
$rew_ndate=$result->fields["reward_cancel_date"];
$rew_ndate=(substr($rew_ndate,0,4)-1911).".".substr($rew_ndate,5,2).".".substr($rew_ndate,8,2);
$temp_arr["rew_date"]= $rew_date;
$temp_arr["rew_ori"]= $result->fields["reward_reason"];
$temp_arr["rew_acc"]= $result->fields["reward_base"];
$rew_kind= $result->fields["reward_kind"];
$temp_arr["rew_kind"]=$reward_arr[$rew_kind];
$temp_arr["date"] =(date("Y")-1911).".".date("m").".".date("d");
$temp_arr["cancel_date"]=$rew_ndate;

$reward_id=intval($reward_id);
if ($result->fields[dep_id]=="0") {
	$query="select a.*,b.stud_name,b.curr_class_num,b.student_sn,b.stud_addr_2,b.addr_zip,c.guardian_name from reward a ,stud_base b ,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.dep_id='$reward_id'";
} else {
	$query = "select a.*,b.stud_name,b.curr_class_num,b.student_sn,b.stud_addr_2,b.addr_zip,c.guardian_name from reward a ,stud_base b ,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.reward_id='$reward_id'";
}
$result = $CONN->Execute($query) or die ($query);
$i=1;
$nums=$result->RecordCount();
$replace_data="";
while (!$result->EOF) {
	$temp_arr["stud_id"]=$result->fields["stud_id"];
	$curr_class_num=$result->fields["curr_class_num"];
	$year_name=substr($curr_class_num,0,-4);
	$temp_arr["year_name"]=$class_name_kind_1[$year_name-$IS_JHORES];
	$temp_arr["class_num"]=substr($curr_class_num,-4,-2);
	$temp_arr["site_num"]=substr($curr_class_num,-2);
	$temp_arr["class_id"]=$result->fields["stud_id"];
	$temp_arr["stud_name"]=$result->fields["stud_name"];
	$temp_arr["guardian_name"]=$result->fields['guardian_name'];
	$temp_arr["stud_addr"]=$result->fields["stud_addr_2"];
	$temp_arr["addr_zip"]=$result->fields["addr_zip"];
	$replace_data .= $ttt->change_temp($temp_arr,$con_body);
	//換頁處理
	if ($i<$nums)	$replace_data .='<text:p text:style-name="sfs_break_page"/>';
	$i++;
	$result->MoveNext();
}
//$replace_data = $ttt->change_temp2(array("break_text"=>"<text:line-break/>"),$replace_data);	
$replace_data = $con_head.$replace_data.$con_foot;

// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
$ttt->add_file($replace_data,"content.xml");

//產生 zip 檔
$sss = & $ttt->file();

//以串流方式送出 sxw

$fl="reward_".$oo_path."_".$reward_id;
header("Content-disposition: attachment; filename=$fl.sxw");
header("Content-type: application/vnd.sun.xml.writer");
//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
header("Expires: 0");

echo $sss;
?>
