<?php

// $Id: dis_stud.php 8328 2015-02-25 06:44:47Z brucelyc $

// --系統設定檔
include "select_data_config.php";

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
	$_POST[year_seme]=$year_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

//判斷家長
$parent_col="guardian_name";
if($_POST['parent']==2) $parent_col="fath_name";
if($_POST['parent']==3) $parent_col="moth_name";

//認證
sfs_check();

chk_tbl();

$year_name=intval($_POST['year_name']);
if ($year_name) {
	//匯出免試中投區高中職
	if ($_POST['ct_out']) {
		require_once "../../include/sfs_case_excel.php";
		$sp_map_arr=array(1=>1,2=>2,3=>4,4=>4,5=>4,6=>4,7=>4,8=>4,9=>4,"A"=>4,"B"=>4,"C"=>3);
		$sex_arr=array(1=>"男",2=>"女");
		$x=new sfs_xls();
		$x->setUTF8();
		$x->setBorderStyle(1);
		$x->addSheet("Student");
		$x->filename="Student.xls";
		//欄位中文名稱
		$x->setRowText(array("1.班級","2.座號","3.學生姓名","4.身分證號","5.性別","6.出生年","7.出生月","8.出生日","9.畢業學年度","10.學生身分","11.低收失業","12.家長姓名","13.電話","14.郵遞區號","15.地址","16.手機"));
		$query="select a.*,b.* from stud_seme_dis a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' order by a.seme_class,a.seme_num";
		$res=$CONN->Execute($query);
		$data_arr=array();
		while(!$res->EOF) {
			$row_arr=array();
			$row_arr[]=substr($res->fields['seme_class'],-2,2); //班級
			$row_arr[]=sprintf("%02d",$res->fields['seme_num']); //座號
			$row_arr[]=$res->fields['stud_name']; //學生姓名
			$row_arr[]=$res->fields['stud_person_id']; //身分證號
			$row_arr[]=$sex_arr[$res->fields['stud_sex']]; //性別
			$d_arr=explode("-",$res->fields[stud_birthday]);
			$row_arr[]=intval($d_arr[0]-1911); //出生年
			$row_arr[]=$d_arr[1]; //出生月
			$row_arr[]=$d_arr[2]; //出生日
			$row_arr[]=$sel_year; //畢業學年度
			$sp_kind=$sp_map_arr[$res->fields['sp_kind']];
			if ($sp_kind=="") $sp_kind=0;
			if ($res->fields['su']) $sp_kind=9; //判斷直升生
			$row_arr[]=$sp_kind; //學生身分
			$low=0;
			if ($res->fields['lowincome']) $low=1;
			if ($res->fields['unemployed']) $low=2;
			if ($res->fields['midincome']) $low=3;
			$row_arr[]=$low; //低收失業
			$row_arr[]=$res->fields['parent']; //家長姓名
			$row_arr[]=$res->fields['tel']; //電話
			$row_arr[]=$res->fields['addr_zip']; //郵遞區號
			$row_arr[]=$res->fields['addr']; //地址
			$row_arr[]=$res->fields['cell']; //手機
			$data_arr[]=$row_arr;
			$res->MoveNext();
		}
		$x->items=$data_arr;
		$x->writeSheet();
		$x->process();
		exit;
	}

	$_POST['sel']=intval($_POST['sel']);
	$sel=$_POST['sel'];
	if ($sel) {
		//儲存考區碼
		if ($_POST['act']=="area") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="update stud_seme_dis set area1='".sprintf("%02d",intval($_POST['area']))."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}

		//儲存分發區碼
		if ($_POST['act']=="area2") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="update stud_seme_dis set area2='".sprintf("%02d",intval($_POST['area2']))."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}

		//儲存家長
		if ($_POST['act']=="par") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="select * from stud_domicile where student_sn='$sn'";
				$res=$CONN->Execute($query);
				$query="update stud_seme_dis set parent='".addslashes(substr($res->fields[$parent_col],0,20))."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}
		
		//儲存電話
		if ($_POST['act']=="tel") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="select * from stud_base where student_sn='$sn'";
				$res=$CONN->Execute($query);
				$query="update stud_seme_dis set tel='".substr($res->fields['stud_tel_'.intval($_POST['phone'])],0,10)."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}
		
		//儲存住址
		if ($_POST['act']=="addr") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="select * from stud_base where student_sn='$sn'";
				$res=$CONN->Execute($query);
				$query="update stud_seme_dis set addr='".addslashes(substr($res->fields["stud_addr_".intval($_POST['address'])],0,80))."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}
		
		//儲存郵遞區號
		if ($_POST['act']=="zip") {
			foreach($_POST['sn'] as $sn=>$d) {
				$query="update stud_seme_dis set zip='".$_POST['zip'][$sn]."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res=$CONN->Execute($query);
			}
		}

		$query="select a.*,a.stud_kind as sk,b.* from stud_seme_dis a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$sel' order by a.seme_num";
		$res=$CONN->Execute($query);
		$temp_arr=array();
		while(!$res->EOF) {
			$sn=$res->fields['student_sn'];
			$temp_arr[$sn]['stud_name']=$res->fields['stud_name'];
			$temp_arr[$sn]['stud_sex']=$res->fields['stud_sex'];
			$temp_arr[$sn]['stud_site']=$res->fields['seme_num'];
			$temp_arr[$sn]['stud_parent']=$res->fields['parent'];
			$temp_arr[$sn]['stud_addr']=$res->fields['addr'];
			$temp_arr[$sn]['stud_tel']=$res->fields['tel'];
			$temp_arr[$sn]['stud_cell']=$res->fields['cell'];
			$temp_arr[$sn]['addr_zip']=$res->fields['zip'];
			$temp_arr[$sn]['area1']=$res->fields['area1'];
			$temp_arr[$sn]['area2']=$res->fields['area2'];
			$temp_arr[$sn]['stud_kind']=$res->fields['sk'];
			$temp_arr[$sn]['hand_kind']=$res->fields['hand_kind'];
			$temp_arr[$sn]['sp_kind']=$res->fields['sp_kind'];
			$temp_arr[$sn]['lowincome']=$res->fields['lowincome'];
			$temp_arr[$sn]['midincome']=$res->fields['midincome'];
			$temp_arr[$sn]['unemployed']=$res->fields['unemployed'];
			$temp_arr[$sn]['cal']=$res->fields['cal'];
			$temp_arr[$sn]['enable0']=$res->fields['enable0'];
			$temp_arr[$sn]['enable1']=$res->fields['enable1'];
			$temp_arr[$sn]['enable2']=$res->fields['enable2'];
			$res->MoveNext();
		}
	} else {
		//處理封存
		if ($_POST['lock']) {
			$query="update stud_seme_dis set cal='2' where seme_year_seme='$seme_year_seme' and cal='1'";
			$res=$CONN->Execute($query);
		}

		//處理解除封存
		if ($_POST['unlock']) {
			$query="update stud_seme_dis set cal='1' where seme_year_seme='$seme_year_seme' and cal='2'";
			$res=$CONN->Execute($query);
		}

		//判斷是否已封存
		$query="select count(*) as nums from stud_seme_dis where seme_year_seme='$seme_year_seme' and cal='2'";
		$res=$CONN->Execute($query);
		$isLock=($res->fields['nums']>0)?1:0;
		$smarty->assign("isLock",$isLock);

		//處理同步化
		if ($_POST['sync']) {
			//判斷族語認證欄位
			$type9="";
			$query="select * from stud_subkind_ref where type_id='9'";
			$res=$CONN->Execute($query);
			if ($res->fields['memo_title']=="族語認證") $type9="memo";
			if ($res->fields['note_title']=="族語認證") $type9="note";

			//判斷境外科技人才子女代碼
			$type71="";
			$query="select * from sfs_text where t_kind='stud_kind' and t_name='境外科技人才子女'";
			$res=$CONN->Execute($query);
			$type71=$res->fields['d_id'];

			//身分別陣列
			$sk_arr=array(1=>9,2=>12,3=>51,4=>6,5=>7,6=>52,7=>$type71);
			$hk_arr=array(1=>44,2=>41,3=>42,4=>43,5=>46,6=>200,7=>200,8=>48,9=>200,"A"=>45,"B"=>49);
			$splus_arr=array('無'=>1,'有'=>2, '未滿一學期'=>3, '未滿一學年'=>4, '未滿二學年'=>5, '未滿三學年'=>6);
			$sym_arr=array(0=>0,1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>'A',11=>'B',12=>'C');
			$all_sn=array();
			$query="select a.*,b.* from stud_seme a left join stud_base b on a.student_sn=b.student_sn where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%' and b.stud_study_cond in ($cal_str)";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				$sn=$res->fields['student_sn'];
				$all_sn[]=$sn;
				//判斷學生身分別
				//考生身分
				$sk=0;
				$sp_kind=0;
				$stud_kind=$res->fields['stud_kind'];
				reset($sk_arr);
				foreach($sk_arr as $k => $v) {
					if (strstr($stud_kind,",$v,")) $sk=$k;
					if ($sk) {
						if ($sk==1) {
							$query2="select * from stud_subkind where type_id='9' and student_sn='$sn'";
							$res2=$CONN->Execute($query2);
							$sp_kind=$splus_arr[$res2->fields[$type9]];
						} elseif ($sk==2 || $sk==7) {
							$query2="select * from stud_subkind where type_id in ('12','$type71') and student_sn='$sn'";
							$res2=$CONN->Execute($query2);
							$sp_kind=($res2->fields['clan'])?($splus_arr[$res2->fields['clan']]+(($res2->fields['type_id']=="12")?4:0)):"";
						} elseif ($sk==3) {
							$sp_kind=11;							
						}
						break;
					}
				}
				//身心障礙
				if (strstr($stud_kind,",1,")) $sp_kind=12;
				//身心障礙(細項)
				$hk=0;
				reset($hk_arr);
				foreach($hk_arr as $k => $v) {
					if (strstr($stud_kind,",$v,")) $hk=$k;
					if ($hk) {
						$sp_kind=12;
						break;
					}
				}
				//低收入戶
				$lowincome=(strstr($stud_kind,",3,"))?1:0;
				//中低收入戶
				$midincome=(strstr($stud_kind,",$type61,"))?1:0;
				//失業勞工子女
				$unemployed=(strstr($stud_kind,",53,"))?1:0;
				$query2="select * from stud_seme_dis where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				$res2=$CONN->Execute($query2);
				if ($res2->RecordCount()>0) {
					$area_str=($_POST['syn']['area'])?",area1='".sprintf("%02d",intval($_POST['area']))."'":"";
					$area2_str=($_POST['syn']['area2'])?",area2='".sprintf("%02d",intval($_POST['area2']))."'":"";
					$phone_str=($_POST['syn']['phone'])?",tel='".substr($res->fields["stud_tel_".intval($_POST['phone'])],0,10)."'":"";
					$addr_str=($_POST['syn']['address'])?",addr='".addslashes(substr($res->fields["stud_addr_".intval($_POST['address'])],0,80))."'":"";
					$query2="update stud_seme_dis set seme_class='".$res->fields['seme_class']."',seme_num='".$res->fields['seme_num']."',cell='".substr($res->fields['stud_tel_3'],0,10)."',zip='".substr($res->fields['addr_zip'],0,3)."',stud_kind='$sk',hand_kind='$hk',lowincome='$lowincome',unemployed='$unemployed',sp_kind='".$sym_arr[$sp_kind]."',midincome='$midincome' $area_str $area2_str $phone_str $addr_str where seme_year_seme='$seme_year_seme' and student_sn='$sn'";
				} else {
					$area_col=($_POST['syn']['area'])?",area1":"";
					$area2_col=($_POST['syn']['area2'])?",area2":"";
					$phone_col=($_POST['syn']['phone'])?",tel":"";
					$addr_col=($_POST['syn']['address'])?",addr":"";
					$area_str=($_POST['syn']['area'])?",'".sprintf("%02d",intval($_POST['area']))."'":"";
					$area2_str=($_POST['syn']['area2'])?",'".sprintf("%02d",intval($_POST['area2']))."'":"";
					$phone_str=($_POST['syn']['phone'])?",'".substr($res->fields["stud_tel_".intval($_POST['phone'])],0,10)."'":"";
					$addr_str=($_POST['syn']['address'])?",'".addslashes(substr($res->fields["stud_addr_".intval($_POST['address'])],0,80))."'":"";
					$query2="insert into stud_seme_dis (seme_year_seme,student_sn,seme_class,seme_num,cell,zip,stud_kind,hand_kind,lowincome,unemployed,sp_kind,midincome $area_col $area2_col $phone_col $addr_col) values ('$seme_year_seme','$sn','".$res->fields['seme_class']."','".$res->fields['seme_num']."','".substr($res->fields['stud_tel_3'],0,10)."','".substr($res->fields['addr_zip'],0,3)."','$sk','$hk','$lowincome','$unemployed','".$sym_arr[$sp_kind]."','$midincome' $area_str $area2_str $phone_str $addr_str)";
				}
				$res2=$CONN->Execute($query2);
				$res->MoveNext();
			}
			$all_sn_str="'".implode("','",$all_sn)."'";

			//同步家長資料
			if ($_POST['syn']['parent']) {
				$query="select * from stud_domicile where student_sn in ($all_sn_str)";
				$res=$CONN->Execute($query);
				while(!$res->EOF) {
					$sn=$res->fields['student_sn'];
					$CONN->Execute("update stud_seme_dis set parent='".addslashes(substr($res->fields[$parent_col],0,20))."' where seme_year_seme='$seme_year_seme' and student_sn='$sn'");
					$res->MoveNext();
				}
			}

			//刪除不在籍學生
			if (!$isLock) {
				$query="delete from stud_seme_dis where seme_year_seme='$seme_year_seme' and student_sn not in ($all_sn_str)";
				$res=$CONN->Execute($query);
			}

			//儲存是否參與計算
			$query="update stud_seme_dis set cal='".(1-$isLock)."' where seme_year_seme='$seme_year_seme' and cal is NULL";
			$res=$CONN->Execute($query);
		}

		//檢查資料
		$query="select a.seme_class,count(a.student_sn) as nums from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ($cal_str) group by a.seme_class";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$temp_arr[$res->fields['seme_class']]=$res->fields['nums'];
			$res->MoveNext();
		}
		$query="select seme_class,count(*) as nums from stud_seme_dis where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%' group by seme_class";
		$res=$CONN->Execute($query);
		$temp2_arr=array();
		while(!$res->EOF) {
			$temp2_arr[$res->fields['seme_class']]=$res->fields['nums'];
			$res->MoveNext();
		}
		$query="select seme_class,count(*) as nums from stud_seme_dis where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%' and cal>'0' group by seme_class";
		$res=$CONN->Execute($query);
		$temp3_arr=array();
		while(!$res->EOF) {
			$temp3_arr[$res->fields['seme_class']]=$res->fields['nums'];
			$res->MoveNext();
		}
		$smarty->assign("rowdata3",$temp3_arr);
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","免試學生資料管理"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$year_name));
$smarty->assign("seme_year_seme",$seme_year_seme);
$smarty->assign("type61",$type61);
$smarty->assign("rowdata",$temp_arr);
$smarty->assign("rowdata2",$temp2_arr);
$smarty->assign("today",date("Y-m-d"));
if ($sel) {
	$smarty->assign("menu2","處理欄位： <input type=\"radio\" name=\"act\" value=\"area\" id=\"act_1\">考$area_sel"."<input type=\"radio\" name=\"act\" value=\"area2\" id=\"act_2\">分發區：".menu_sel($area2_arr,"area2",$_POST['area2'])."<input type=\"radio\" name=\"act\" value=\"par\" id=\"act_3\">家長：".menu_sel($parent_arr,"parent",$_POST['parent'])."<input type=\"radio\" name=\"act\" value=\"tel\" id=\"act_4\">電話：".menu_sel($phone_arr,"phone",$_POST['phone'])."<input type=\"radio\" name=\"act\" value=\"addr\"  id=\"act_5\">住址：".menu_sel($address_arr,"address",$_POST['address'])."<input type=\"radio\" name=\"act\" value=\"zip\" id=\"act_6\">郵遞區號");
} else {
	$smarty->assign("menu2","同步欄位： <input type=\"checkbox\" name=\"syn[area]\">考$area_sel"."<input type=\"checkbox\" name=\"syn[area2]\">分發區：".menu_sel($area2_arr,"area2",$_POST['area2'])."<input type=\"checkbox\" name=\"syn[parent]\">家長：".menu_sel($parent_arr,"parent",$_POST['parent'])."<input type=\"checkbox\" name=\"syn[phone]\">電話：".menu_sel($phone_arr,"phone",$_POST['phone'])."<input type=\"checkbox\" name=\"syn[address]\">住址：".menu_sel($address_arr,"address",$_POST['address']));
}
$smarty->display("stud_basic_test_dis_stud.tpl");
?>
