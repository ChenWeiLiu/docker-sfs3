<?php
// $Id: chc_teacher.v2.php 8205 2014-11-14 08:01:43Z hami $
include "config.php";
sfs_check();
$ary = array();
if ($_POST[act]=='add' && $_POST[year_seme]!=''&& $_POST[grade]!='' ){
	foreach ($_POST[class_id] as $new_class=>$tea_sn) {
	if ($new_class=='' || $new_class=='0' || !$tea_sn) continue;

	$ary[$tea_sn][sn]=$tea_sn;
	$new_class1= split("_",$new_class);//切開字串
	$seme_class=($new_class1[2]+0).$new_class1[3];
	$ary[$tea_sn][seme_class]=$seme_class;
	$ary[$tea_sn][year]=$new_class1[0]+0;
	$ary[$tea_sn][semester]=$new_class1[1]+0;
	$ary[$tea_sn][c_year]=sprintf("%d",$new_class1[2]);
	$ary[$tea_sn][c_sort]=$new_class1[3]+0;
	$ary[$tea_sn][tea_name]=$_POST[tea][$new_class];
	unset($new_class1);
	}
	//是否本學期
	$now=curr_year()."_".curr_seme();
	($_POST[year_seme]==$now) ? $chk_now='Y':$chk_now='N';
	//是的話才更新teacher_post表
	if ($chk_now=='Y'){
		foreach ($ary as $new_class) {
			$ary2[]=$new_class[seme_class];
			}
		$in_ary=join(",",$ary2);
		$SQL="update teacher_post set class_num='' where  class_num in ($in_ary) ";
		$rs=$CONN->Execute($SQL) or die($SQL);
	}

	foreach ($ary as $sn => $dat) {
		//如果,是本學期才修改 teacher_post
		if ($chk_now=='Y' and $sn){
			$SQL="update teacher_post set class_num='$dat[seme_class]' where teacher_sn='$sn'";
			$rs=$CONN->Execute($SQL) or die($SQL);

			}
		$sql_update = "update school_class set teacher_1='$dat[tea_name]' where year='$dat[year]' and semester='$dat[semester]' and c_year='$dat[c_year]' and c_sort='$dat[c_sort]' and enable=1";
		$rs=$CONN->Execute($sql_update) or die($sql_update); 
		}
		$URL=$_SERVER[PHP_SELF]."?year_seme=".$_POST[year_seme]."&grade=".$_POST[grade];
	header("location:".$URL);
}


////直接指定樣本檔位置
$template_file = $SFS_PATH."/".get_store_path()."/templates/chc_teacher.v2.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

head("導師設定");
print_menu($school_menu_p);


// 2.判斷學年度

$now_year_seme=curr_year()."_".curr_seme();
($_GET[year_seme]=='') ? $year_seme=$now_year_seme:$year_seme=$_GET[year_seme];
$sel_year= split("_",$year_seme);//切開字串

	//選擇的學年度
$smarty->assign("year_seme",$year_seme);
$smarty->assign("sel_year",sel_year("year_seme",$year_seme));
($IS_JHORES==6) ? $all_grade=array(7=>"一年級",8=>"二年級",9=>"三年級","all"=>"全部"):$all_grade=array(1=>"一年級",2=>"二年級",3=>"三年級",4=>"四年級",5=>"五年級",6=>"六年級","all"=>"全部");
//傳入年級陣列
$smarty->assign("sel_grade",$all_grade);


if ($_GET[grade]!=''){
	$grade=$_GET[grade];
	$smarty->assign("now_grade",$grade);

	$SQL_NOW="select a.teach_id, a.teach_person_id, a.name, a.sex, a.birthday,a.teach_condition, a.teacher_sn, b.class_num  from teacher_base a left join   teacher_post b on  a.teacher_sn=b.teacher_sn and b.class_num!='' where teach_condition='0'  order by b.class_num desc,a.sex ,a.name ";
	$SQL_AGO="select a.teach_id, a.teach_person_id, a.name, a.sex, a.birthday,a.teach_condition, a.teacher_sn, b.class_num  from teacher_base a left join   teacher_post b on  a.teacher_sn=b.teacher_sn  and b.class_num!=''   order by b.class_num desc,a.sex ,a.name  ";

	($year_seme==$now_year_seme) ? $SQL=$SQL_NOW: $SQL=$SQL_AGO;

	$rs=$CONN->Execute($SQL) or die($SQL); 
	while ($ro=$rs->FetchNextObject(false)) {
		$tea[$ro->teach_id]=get_object_vars($ro);
		}
	$SQL_all="select  class_sn,class_id, year, semester, c_year, c_name, c_kind, c_sort, enable, teacher_1, teacher_2 from school_class where year='".$sel_year[0]."' and  semester='".$sel_year[1]."'  and enable='1' order by class_id ";
	$SQL_part="select  class_sn,class_id, year, semester, c_year, c_name, c_kind, c_sort, enable, teacher_1, teacher_2 from school_class where year='".$sel_year[0]."' and  semester='".$sel_year[1]."' and  c_year='$grade'  and enable='1' order by class_id ";
	//取部分班級或全部
	($grade=='all') ? $SQL=$SQL_all:$SQL=$SQL_part;
	$rs=$CONN->Execute($SQL) or die($SQL);
	$class_ary=$rs->GetArray();
	foreach ($class_ary as $tmp_ary){
		($IS_JHORES==6) ? $tmp_ary[cc_year]=num_tw($tmp_ary[c_year]-6):$tmp_ary[cc_year]=num_tw($tmp_ary[c_year]);
		$class_ary_1[]=$tmp_ary;
		}
	$class_ary=$class_ary_1;
	$smarty->assign("SEX",array(1=>"男",2=>"女"));
	//教師
	$smarty->assign("tea",add_to_td2($tea,4));
	//班級陣列
	$smarty->assign("class_ary",add_to_td2($class_ary,2));
}


////顯示結果
$smarty->display($template_file);
// echo "<PRE>";
//print_r($class_ary);
//..print_r($tea);

foot();

//////-------------補齊顯示用函式2---------------///////
function add_to_td2($data,$num) {
	$all=count($data);
	$loop=ceil($all/$num);
	$flag=$num-1;//幾格的key
	$all_td=($loop*$num)-1;//最大值小1
	$show=array();$i=0;
	foreach ($data as $key=>$ary ){
		(($i%$num)==$flag && $i!=0 && $i!=$all_td ) ? $ary[next_line]='yes':$ary[next_line]='';
		$show[$key]=$ary;
		$i++;
		}
	if ($i<=$all_td ){
		for ($i;$i<=$all_td;$i++){
			$key='Add_Td_'.$i;
		(($i%$num)==$flag && $i!=0 && $i!=$all_td ) ? $show[$key][next_line]='yes':$show[$key][next_line]='';
		}
	}
		return $show;
}

##################  學期下拉式選單函式  ##########################
function sel_year($name,$select_t='') {
	global $CONN ;
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$year_seme=$ro->year."_".$ro->seme;
	$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?".$name."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$select_t) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	return $str;
	}

###########################################################
##  傳出中文數字函數

function num_tw($num, $type=0) {
 $num_str[0] = "十百千";
        $num_str[1] = "拾佰仟";
        $num_type[0]='零一二三四五六七八九';
        $num_type[1]='零壹貳參肆伍陸柒捌玖';
        $num = sprintf("%d",$num);
        while ($num) {
                $num1 = substr($num,0,1);
                $num = substr($num,1);
                $target .= substr($num_type[$type], $num1*2, 2);
                if (strlen($num)>0) $target .= substr($num_str[$type],(strlen($num)-1)*2,2);
 }
 return $target;
}

?>
