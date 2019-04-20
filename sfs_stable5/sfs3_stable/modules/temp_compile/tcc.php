<?php
//$Id: tcc.php 7712 2013-10-23 13:31:11Z smallduh $
/*引入學務系統設定檔*/
require "config.php";

sfs_check();

$stud_study_year=date("Y")-1911;
$class_year_b=($IS_JHORES==0)?1:7;

if ($_POST[out]) {
	$sex_arr=array("1"=>"1","2"=>"0");
	if ($_POST[kind]==1) {
		$sure_study=1;
		$score_str="cc desc,";
		$kind_str="";
		$sex_comp="and stud_sex='".$_POST[sex]."'";
	} else {
		$sure_study=2;
		$score_str="";
		$kind_str="meno,";
		$sex_str=($_POST[sex]==1)?"stud_sex,":"stud_sex desc,";
		$meno_str="and meno='".$_POST[spec_kind]."'";
	}
	//先取出各次未輸入成績學生資料
	for ($i=1;$i<=3;$i++) {
		$query="select temp_id from new_stud where temp_score$i='-100' and stud_study_year='$stud_study_year' and class_year='$class_year_b'";
		$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$ids="";
		while (!$res->EOF) {
			$ids.="'".$res->fields[temp_id]."',";
			$res->MoveNext();
		}
		if ($ids) $ids=substr($ids,0,-1);
		$temp_score_id[$i]=$ids;
		$query="update new_stud set temp_score$i='0' where temp_score$i='-100' and stud_study_year='$stud_study_year' and class_year='$class_year_b'";
		$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	}
	$Str="臨時編號,班級,座號,姓      名,性別,總分,新班,新座號,編班序號,備註\n";
	$query="select *,(temp_score1+temp_score2+temp_score3) as cc from new_stud where stud_study_year='$stud_study_year' and sure_study='$sure_study' $sex_comp $meno_str order by $kind_str $sex_str $score_str temp_id";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$Str.=$res->fields[temp_id].",".substr($res->fields[temp_class],1).",".$res->fields[temp_site].",".$res->fields[stud_name].",".$sex_arr[$res->fields[stud_sex]].",".$res->fields[cc].",,,,".$res->fields[meno]."\n";
		$res->MoveNext();
	}
	$filename="編班資料-".$sure_study.".csv";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $Str;
	exit;	
}elseif ($_FILES[upload_file][name]!="") {
	head("新生編班");
	print_menu($menu_p);
	//檔案上傳
	$file_name=$_FILES['upload_file']['name'];
	$lastname=substr($file_name,(strpos($file_name,".")+1),3);
	$path_str="temp/temp_compile/";
	set_upload_path($path_str);
	$fname=$UPLOAD_PATH.$path_str.$file_name;
	if ($_FILES['upload_file']['size'] >0 && $file_name != "" &&  ($lastname == "CSV" || $lastname == "csv")){
		copy($_FILES['upload_file']['tmp_name'],$fname);
	}
	$fd=fopen($fname,"r");
	$i=0;
	while($tt=sfs_fgetcsv($fd,2000,",")){
		if ($tt[0]!="" && $tt[6]!="" && $tt[7]!="") {
			$query="select * from new_stud where temp_id='".$tt[0]."' and stud_study_year='$stud_study_year' and class_year='$class_year_b'";
			$res=$CONN->Execute($query);
			$stud_name=$res->fields[stud_name];
			if ($res->fields[newstud_sn]!="") {
				$query="update new_stud set class_year='$class_year_b',class_sort='".$tt[6]."',class_site='".$tt[7]."' where newstud_sn='".$res->fields[newstud_sn]."'";
				$CONN->Execute($query);
				echo $tt[0]."--".$stud_name."==>".$tt[6]."班".$tt[7]."號 匯入成功！<br>";
				$i++;
			}
		}
	}
	fclose($fd);
	unlink($fname);
	echo "共成功匯入 $i 筆資料";
	foot();
} else {
	head("新生編班");
	print_menu($menu_p);
	if ($_POST[kind]=="") $_POST[kind]="1";
	$kind_chk[$_POST[kind]]="selected";
	if ($_POST[sex]=="") $_POST[sex]="1";
	$sex_chk[$_POST[sex]]="selected";
	if ($_POST[kind]==1) {
		$sex_sel='<option value="1" '.$sex_chk[1].'>男生<option value="2" '.$sex_chk[2].'>女生';
	} else {
		$sex_sel='<option value="1" '.$sex_chk[1].'>男生在前<option value="2" '.$sex_chk[2].'>女生在前';
		$spec_sel='<select name="spec_kind">';
		$query="select distinct meno from new_stud  where stud_study_year='$stud_study_year' and sure_study='2' order by meno";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$meno=$res->fields[meno];
			if ($meno=="") continue;
			$spec_sel.='<option value="'.$meno.'">'.$meno;
			$res->MoveNext();
		}
		$spec_sel.='</select>';
	}
	echo '	
	<table border=0 width=100% style="font-size:12pt;" cellspacing="1" cellpadding=3  bgcolor=#9EBCDD>
	<form action="'.$_SERVER[PHP_SELF].'" method="post" target="_blank">
	<tr bgcolor="white">
	<td width=50% valign=top nowrap><h3>匯出'.(date("Y")-1911).'學年度統一編班資料</h3>
	<select name="kind" OnChange="this.form.submit();"><option value="1" '.$kind_chk[1].'>一般班<option value="2" '.$kind_chk[2].'>特殊班</select>
	<select name="sex" OnChange="this.form.submit();">'.$sex_sel.'</select> '.$spec_sel.'
	<input type="submit" name="out" value="匯出資料"><br>
	<td width=50% valign=top>
	<ol>
	<li>要使資料能依一般生、特殊生匯出，必須在「<a href="newstud_manage.php">管理新生</a>」→「<a href="newstud_manage.php?work=2&class_year_b='.($IS_JHORES+1).'">標記是否就讀本校</a>」標記成<font color=red>「就讀」</font>和<font color=red>「特殊班」</font>。</li>
	<li>要使特殊班能夠匯出，必須在特殊班類別輸入「資源班」、「資優班」等等班別資料。</li>
	<li>特殊班匯出時的排序為<font color=red>姓別、臨時編號。</font></li>
	</ol>
	</tr>
	</form>
	<form action="'.$_SERVER[PHP_SELF].'" enctype="multipart/form-data" method="post">
	<tr bgcolor="white">
	<td width=50% valign=top nowrap><h3>匯入'.(date("Y")-1911).'學年度統一編班資料</h3>
	上傳檔案：<input type="file" name="upload_file"> <input type="submit" value="上傳">
	<td width=50% valign=top>
	<ol>
	<li>要使用本匯入功能必須是以本模組匯出編班資料後，由統一編班程式得到電子檔，匯入資料才不會發生問題。</li>
	</ol>
	</tr>
	</form>
	</table>';
	foot();
}

?>
