<?php
// $Id: graduate_score.php 8051 2014-05-29 00:40:22Z kwcmath $

include "config.php";
include "../../include/sfs_case_score.php";

//�{��
sfs_check();

$ss_link=array("�y��"=>"language","�ƾ�"=>"math","�ͬ�"=>"life","�۵M�P�ͬ����"=>"nature","���|"=>"social","���N�P�H��"=>"art","���d�P��|"=>"health","��X����"=>"complex","��`�ͬ����{"=>"nor");
$link_ss=array("language"=>"�y��","math"=>"�ƾ�","life"=>"�ͬ�","nature"=>"�۵M�P�ͬ����","social"=>"���|","art"=>"���N�P�H��","health"=>"���d�P��|","complex"=>"��X����","nor"=>"��`�ͬ����{");

$specific=array();

if ($IS_JHORES==0) $f_seme=12; else $f_seme=6;

if($_POST["step"]=="" OR $_POST[stud_class]==""){
	head("���~�ͦ��Z�պ�");
	
echo <<<HERE
<script>
function tagall(status) {
  var i =0;
  while (i<document.area.elements.length)  {
    if(document.area.elements[i].id!='') {
		var text=document.area.elements[i].id;
		if(text.indexOf('area_')==0) document.area.elements[i].disabled=status;
    }
    i++;
  }
}
</script>
HERE;
	
	//���o�Юv�ҤW�~�šB�Z��  ���B�n�אּ���˵����S���޲z�v�]�w
	$session_tea_sn = $_SESSION['session_tea_sn'];
	$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
	$result =  $CONN->Execute($query) or user_error("Ū�����ѡI<br>$query",256) ;
	$row = $result->FetchRow() ;
	$class_num = $row["class_num"];

	$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];
	if($class_num<>"" OR checkid($SCRIPT_FILENAME,1)) { 
	
	
	//���ͭn�p�⪺���ﶵ
	$area="<form name=\"area\" method=\"post\" action=\"$_SERVER[PHP_SELF]\"><input type='hidden' name='step' value='@'>
		<table border=1 cellpadding=10 cellspacing=0 style='border-collapse: collapse' bordercolor='#111111' width='100%'>
		<tr><td bgcolor='#CFCFAA'  width=250>�B�J</td><td width=300 bgcolor='#CFCFAA'>�ѼƳ]�w</td><td rowspan=5  bgcolor='#AACFCF'>
		<pre><font color=#2288FF><img src='./images/edit.gif'> �Ҳջ���:<a href='mailto:infodaes@seed.net.tw'> (by infodaes  2005/05/13)</a>
		
    ���ҲիY��[���U��]-[���~�ͤɾǸ��]
��[���~���Z]�ק�ӨӡA�]�Ҷq�ɮv�ϥΪ�
�K�Q�ʡA�G�N��W�ߥX�ӡC
    ���Ҳճ]�p���ت��A�Y�t�X�Ǯղ��~�ɡA
�U�Z�ũ����|���Ħ�u�ʪ��ǲ߻��ξǴ���
�[�v�覡�ӭp��Z�žǥͬY�ǯS���ݭn�Ʃw��
�Z���Ǫ����Z�A�H��K�@�����w���Ǫ��β��~
�����������ѦҡC

���ѼƳ]�w:�Ц�[�Ҳ��v���޲z]�]�w�Ҳ��ܼ�
���޲z�v�]�w:�D�Z�žɮv�ϥζ����޲z�v
��2011-09-05 �W�[���[�v�ϥέ�ӽҵ{�]�w�ﶵ

���ϥΨB�J:
  1.��w�n�p�⪺[�Z��]
  2.�]�w[���[�v]
  3.�]�w�Ǵ��[�v
  4.�]�w�W���C�ǤH��:
  5.��[�����p���C��]��ܵ��G
	</font></pre><center><input type='submit' value='�����p���C��' name='go' style='font-family: �з���; font-size: 14 pt' onClick='return checkok();'></center></td>";

	
	//�ؼЯZ��
	if($class_num) $stud_class=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,0,1),substr($class_num,-2));
	$class_list=get_class_select(curr_year(),curr_seme(),"","stud_class","",$stud_class);
	if( !checkid($SCRIPT_FILENAME,1) AND $class_num) {
		$class_list="<input type='hidden' name='stud_class' value='$stud_class'>".str_replace("'stud_class'","'stud_class_xxx' disabled",$class_list);
	}
	if(checkid($SCRIPT_FILENAME,1)) $slect_all_grade="�@<input type='checkbox' name='all_classes'>�Ӧ~�Ť@�֭p��"; else $slect_all_grade="";
	$area.="</tr><tr bgcolor='#CCAAFF'><td><img src='./images/icon.gif'>Step 1: ��w�n�p�⪺�Z��:</td><td>$class_list $slect_all_grade</td></tr>
		<tr bgcolor='#CCFFAA'><td><img src='./images/icon.gif'>Step 2: ��쪺�[�v��:<br><br><font color='blue'><center><input type='checkbox' name='original_rate' checked onclick='tagall(this.checked);'>�ϥέ�ҵ{�]�w�[�v</center></td><td>";

	//print_r($semesters);
	$range_select=$range_select?$range_select:50;
	
	foreach($ss_link as $key=>$value) {	
		$area.="$key:<select name='$value' id='area_$value' disabled>";
		for($i=0;$i<=$range_select;$i++) { $area.="<option".(($m_arr[$value]==$i)?" selected":"").">$i</option>";}
		$area.="</select><BR>";
	}

	$weight="";
	for($i=1;$i<=$f_seme;$i++) {
		$weight.=floor(($i+1)/2).(($i % 2)?"�W":"�U").":<select name='$i'>"; 
		for($k=0;$k<=$range_select;$k++)  $weight.="<option".(($semesters[$i-1]==$k)?" selected":"").">$k</option>";
		$weight.="</select>".(($i % 2)?"�@�@":"<BR>");
	}
	
	$rank_count="<input type='text'  size=5 name='rank_count' value='".$m_arr['rank_count']."'>�@�@�@<input type='checkbox' name='show_detail'>��ܦU�ͦU��즨�Z";
	$area.="<tr bgcolor='#FFCCAA'><td><img src='./images/icon.gif'>Step 3:�U�Ǵ����[�v��:<BR></td><td>$weight</td></tr>";
	$area.="<tr bgcolor='#FFACCA'><td><img src='./images/icon.gif'>Step 4:�W���C�ǤH��:<BR></td><td>$rank_count</td></tr>";
	$area.="</table></form>";
	echo $area;
	} else { echo "<h2><center><BR><BR><font color=#FF0000>�z�å��Q���v�ϥΦ��Ҳ�(�D�ɮv�μҲպ޲z��)</font></center></h2>"; } 
	
	foot();

} ELSE {	
	set_time_limit(0);  //�קK�Z�żƹL�h�Ӳ���timeout���D	
	
	//���o���[�v�Ʀ� $specific �}�C
	foreach($ss_link as $key=>$value){
		$specific[$value]=$_POST[$value];
		$total_rate+=$specific[$value];
	}
	//���o�B�z���Z��
	$stud_class=($_POST[stud_class]);
	$class_data=explode('_',$stud_class);
        $class_id=$class_data[2]*100+$class_data[3];
        
        $SQL_filter=$_POST[all_classes]?(" like '".substr($class_id,0,1)."%'"):"='$class_id'";
        $show_detail=$_POST[show_detail];
        
	//print_r($specific);  print_r($stud_class);
	//echo "<BR>����`�[�v��:$total_rate<BR>";
	
	//���o�Ǵ���
	for($i=0;$i<$f_seme;$i++) $seme_weight[$i]=$_POST[$i+1];
	
	//echo "<BR>###############";
	//print_r($seme_weight);
	
	//�]�w�u��p�⥻�Ǧ~��
	$sel_year = curr_year(); //�ثe�Ǧ~
	$sel_seme = curr_seme(); //�ثe�Ǵ�
	$year_seme=$sel_year."_".$sel_seme;


	if ($_POST["step"]=="@") {
		$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
		//$seme_class=$_POST[year_name].sprintf("%02d",$_POST[me]);
		//$query="select a.*,b.stud_name from stud_seme a,stud_base b where a.student_sn=b.student_sn AND a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_id' and b.stud_study_cond in ('0','15') order by a.seme_num";
		$query="select a.*,b.stud_name,b.curr_class_num from stud_seme a,stud_base b where a.student_sn=b.student_sn AND a.seme_year_seme='$seme_year_seme' and a.seme_class $SQL_filter and b.stud_study_cond=0 order by b.curr_class_num";
		
//echo $query."<BR>";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$sn[]=$res->fields[student_sn];
			$curr_class_num[]=$_POST[all_classes]?$res->fields[curr_class_num]:$res->fields[seme_num];
			$stud_name[]=$res->fields[stud_name];
			$stud_id[]=$res->fields[stud_id];
			$res->MoveNext();
		}
		
		//print_r($sn);
		//print_r($stud_name);
		//print_r($stud_id);
		
		//exit;
	
		$rank_count=$_POST['rank_count'];
				
		$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";  //���o�J�Ǧ~
		$res=$CONN->Execute($query);
		$stud_study_year=$res->fields[0];
		if ($IS_JHORES==0)
			$f_year=5;
		else
			$f_year=2;
			for ($i=0;$i<=$f_year;$i++) {
				for ($j=1;$j<=2;$j++) {
					$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
					$show_year[]=$stud_study_year+$i;
					$show_seme[]=$j;
				}
		}
		$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]));
		$fin_nor_score=cal_fin_nor_score($sn,$semes);

		$sm=&get_all_setup("",$sel_year,$sel_seme,$_POST[year_name]);
		$rule=explode("\n",$sm[rule]);
		while(list($s,$v)=each($fin_score)) {
			$fin_score[$s][avg][str]=score2str($fin_score[$s][avg][score],"",$rule);
		}


		$final_score=array();
	
		
		//��즨�Z�[�J��`�ͬ����{���Z(������줧�@)
		foreach($fin_nor_score as $student_sn=>$nor_score)
		{
				$fin_score[$student_sn]['nor']=$fin_nor_score[$student_sn];
		}
	
		//�ץ���p�⪺�[�v
		if($_POST[original_rate]) {	  //�u�p��Ǵ����p����
			for($i=0;$i<=count($sn)+1;$i++){
				$final_score[$sn[$i]]=0;
				foreach($specific as $key=>$value) {
					$fin_score[$sn[$i]][$key]['avg']['score']=0;
					$fin_score[$sn[$i]][$key]['avg']['rate']=0;
					//�p��Ǵ����
					for($j=0;$j<=count($semes);$j++) {
						$fin_score[$sn[$i]][$key][$semes[$j]][rate]=$fin_score[$sn[$i]][$key][$semes[$j]][rate]*$seme_weight[$j];
						$fin_score[$sn[$i]][$key]['avg']['score']+=$fin_score[$sn[$i]][$key][$semes[$j]][score]*$fin_score[$sn[$i]][$key][$semes[$j]][rate];
						$fin_score[$sn[$i]][$key]['avg']['rate']+=$fin_score[$sn[$i]][$key][$semes[$j]][rate];
					}
					$final_score[$sn[$i]]+=$fin_score[$sn[$i]][$key]['avg']['score'];
				}	
			}
		} else {   //�̳]�w�p��
			for($i=0;$i<=count($sn)+1;$i++){
				$fin_score[$sn[$i]]['avg']['score']=0;
				foreach($specific as $key=>$value) {
					$fin_score[$sn[$i]][$key]['avg']['score']=0;
					//�p��Ǵ����
					for($j=0;$j<=count($semes);$j++) {
						$fin_score[$sn[$i]][$key]['avg']['score']+=$fin_score[$sn[$i]][$key][$semes[$j]][score]*$seme_weight[$j];
					}
					//�p������
					$fin_score[$sn[$i]]['avg']['score']+=$fin_score[$sn[$i]][$key]['avg']['score']*$value;
				}
				$final_score[$sn[$i]]=$fin_score[$sn[$i]]['avg']['score'];
			}
		}
		//�w��̫ᵲ�G���Ƨ�
		arsort($final_score);
	
		//�g�J�W��
		$rank=0;
		$rank_list="<table border=1 cellpadding=0 cellspacing=0 bordercolor=#5555AA style='border-collapse: collapse' width=100%><tr bgcolor=#FFCAAC><td align='center'>�Ƨ�</td><td align='center'>�Z�Ůy��</td><td align='center'>�m�W</td><td align='center'>�[�v�`��</td><td align='center'>�ơ@�@�@�@��</td></tr>";
		foreach($final_score as $key=>$value) {
		if($key){
			$rank+=1;
			$fin_score[$key]['avg']['rank']=$rank;
			if($rank<=$rank_count AND $rank<=count($sn)) {
			$rank_list.="<tr><td align='center'>$rank</td><td align='center'>".$curr_class_num[array_search($key,$sn)]."</td><td align='center'>".$stud_name[array_search($key,$sn)]."</td><td align='center'>".$final_score[$key]."</td><td></td></tr>"; }
		}
		}
		$rank_list.="</table>";
	}
	
	//��ܳ���
	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
	$smarty->assign("module_name","���~���Z�պ�"); 
	$smarty->assign("SFS_MENU",$menu_p); 
	$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
	$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
	$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
	$smarty->assign("show_year",$show_year);
	$smarty->assign("show_seme",$show_seme);
	$smarty->assign("semes",$semes);
	$smarty->assign("stud_id",$stud_id);
	$smarty->assign("student_sn",$sn);
	$smarty->assign("curr_class_num",$curr_class_num);
	$smarty->assign("stud_name",$stud_name);
	$smarty->assign("stud_num",count($stud_id));
	$smarty->assign("fin_score",$fin_score);
	$smarty->assign("final_score",$final_score);
	$smarty->assign("fin_nor_score",$fin_nor_score);
	$smarty->assign("ss_link",$ss_link);
	$smarty->assign("link_ss",$link_ss);
	$smarty->assign("ss_num",count($ss_link));
	$smarty->assign("rule",$rule_all);
	$smarty->assign("jos",$IS_JHORES);
	$smarty->assign("class_base",class_base($seme_year_seme));
	$smarty->assign("seme_class",$_POST[year_name].sprintf("%02d",$_POST[me]));
	$smarty->assign("seme_weight",$seme_weight);
	$smarty->assign("specific",$specific);
	$smarty->assign("rank_list",$rank_list);
	$smarty->assign("show_detail",$show_detail);
	
	
	//if ($_POST[friendly_print]) {
	//	$smarty->display("stud_grad_grad_score_print.tpl");
	//} else {
		$smarty->display("graduate_score.tpl");
	//}
	
}
?>