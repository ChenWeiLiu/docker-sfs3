<?php

// $Id: reward_one.php 7062 2013-01-08 15:37:05Z smallduh $

//���o�]�w��
include_once "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();


//�q�X����
head("��ǥͭӤH���g�n�O");

	//�����\���
$tool_bar=&make_menu($student_menu_p);
echo $tool_bar;

//�ثe�Ǵ�
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());

//���o�Ҧ��Ǵ�
$seme_list=get_class_seme();

//�ثe��w�Ǵ�
$work_year_seme=$_POST['work_year_seme'];
if ($work_year_seme=='') $work_year_seme = $c_curr_seme;
$move_year_seme = intval(substr($work_year_seme,0,-1)).substr($work_year_seme,-1,1);

//�R���@��
if ($_POST['act']=='del_reward') {
  //���o stud_id
  $query="select stud_id from stud_base where student_sn='".$_POST['selected_student']."'";
  $res=mysql_query($query);
  list($stud_id)=mysql_fetch_row($res);
  //���o���g�Ǵ�
  $query="select reward_year_seme from reward where reward_id='".$_POST['option1']."'";
  $res=mysql_query($query);
  list($reward_year_seme)=mysql_fetch_row($res);
  
  $query="delete from reward where reward_id='".$_POST['option1']."'";
  mysql_query($query);
  
	cal_rew(substr($reward_year_seme,0,strlen($reward_year_seme)-1),substr($reward_year_seme,-1),$stud_id); 

} // end if del_reward

//�s�W�@��
if ($_POST['act']=='add_reward') {
	//�B�z���
	if ($_POST['temp_reward_date']) {
		$dd=explode("-",$_POST['temp_reward_date']);
		if ($dd[0]<1911) $dd[0]+=1911;
		$_POST['temp_reward_date']=implode("-",$dd);
	}
	
  $query="select stud_id from stud_base where student_sn='".$_POST['selected_student']."'";
  $res=mysql_query($query);
  list($stud_id)=mysql_fetch_row($res);

  if ($stud_id=='') {
    echo "�䤣��������Ǹ�!";
    exit();
  }
  
		$sel_year=sprintf('%d',substr($_POST['reward_year_seme'],0,strlen($_POST['reward_year_seme'])-1));
		$sel_seme=substr($_POST['reward_year_seme'],-1);

 $reward_year_seme=$sel_year.$sel_seme; //�o�ͮɪ��Ǵ� ,�p��100, �e�����[0
 $reward_kind=$_POST['reward_kind']; //���g����
 $reward_reason=$_POST['reward_reason']."(�L�ռ��g)"; //���g�z��
 $reward_base=$_POST['reward_base']; //���g�̾�
 $reward_date=$_POST['temp_reward_date']; //���g���
 
 		$student_sn=$_POST['selected_student'];
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=date("Y-m-j");
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','$stud_id','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn')";
		
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$stud_id); 

 $_POST['act']='';

}



//�[��ӥͥثe�w�ɵn���g������
if ($_POST['act_join']=='join' || $_POST['act_joinp']=='joinp') {

    list($stud_id)=mysql_fetch_row(mysql_query("select stud_id from stud_base where student_sn='".$_POST['selected_student']."'"));

    if ($stud_id=='') {
        echo "�䤣��������Ǹ�!";
        exit();
    }

if ($_POST['act_join']=='join')$joinx=$_POST['join'];
if ($_POST['act_joinp']=='joinp')$joinx=$_POST['joinp'];

for($k=0;$k<count($joinx);$k++)
{	
if ($_POST['act_join']=='join') $query="select * from reward where student_sn='$joinx[$k]' Limit 1";
if ($_POST['act_joinp']=='joinp') $query="select * from reward_exchange where sn='$joinx[$k]' Limit 1";
$res4=$CONN->Execute($query);
while (!$res4->EOF) 
{

$sel_year=reward_date_year($res4->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǧ~
$sel_seme=reward_date_seme($res4->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǵ�

$reward_year_seme=$sel_year.$sel_seme; //�o�ͮɪ��Ǵ� ,�p��100, �e�����[0
 
 if ($_POST['act_joinp']=='joinp')
 {
	 $reward_numbers=$res4->fields['reward_numbers']; //���g����
	 if ($reward_numbers==1)$reward_numbers="�@";
	 if ($reward_numbers==2)$reward_numbers="�G";
	 if ($reward_numbers==3)$reward_numbers="�T";
	 
	 $reward_kind=$res4->fields['reward_kind'].$reward_numbers."��";
 }	 
 else
 {
	$reward_kind=$res4->fields['reward_kind']; 
 }

 //�N���g�������ܦ��Ʀr�N�X, ���Ƭ���, �t�Ƭ��g
 foreach ($reward_arr as $i=>$v) {
     if ($v==$reward_kind) {
         $reward_kind=$i;
         break;
     }
 }

/*
 for($i=0;$i<count($reward_arr);$i++)
 {   
  if ($reward_kind==$reward_arr[$i]){
      $reward_kind=$i;
      break;
  }
 }
 */
 if ($_POST['act_join']=='join')$reward_reason=$res4->fields['reward_reason']; //���g�z�� 
 if ($_POST['act_joinp']=='joinp')$reward_reason=$res4->fields['reward_reason']."(�L�ռ��g)"; //���g�z��
 $reward_base=$res4->fields['reward_base']; //���g�̾�
 $reward_date=$res4->fields['reward_date']; //���g���
 
 		$student_sn=$_POST['selected_student'];
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=$reward_date;
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','$stud_id','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn')";
		
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$stud_id); 

 
 $res4->MoveNext();
}
	
}


 $_POST['act_join']="";
 $_POST['act_joinp']="";


}




	//���g���
	$sel1 = new drop_select(); //������O
	$sel1->s_name = "reward_kind"; //���W��	
	$sel1->id = $reward_kind; //�w�]�ﶵ	
	$sel1->arr = $reward_arr; //���e�}�C		
	$sel1->top_option = "-- ��ܼ��g --";
	$reward_select=$sel1->get_select();

	// ����禡
	if ($reward_date=="") $reward_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	$seldate=new date_class("myform");
	$seldate->demo="";

	//���g���
	$date_input=$seldate->date_add("reward_date",$reward_date);

  //�w�I�諸�ǥ� student_sn
  $selected_student=$_POST['selected_student'];

  if ($reward_base=='') $reward_base='�ǥͼ��g���ɹ�I��k';

?>

<form method="post" name="myform" act="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">	
		�������J���Ǵ��G
	<select name="work_year_seme" onchange="document.myform.submit();">
  <?php
		foreach($seme_list as $key=>$value) {
	?>		
	 <option value="<?php echo $key;?>" <?php if ($key==$work_year_seme) echo " selected";?>><?php echo $value;?></option>
	 <?php
	 }
	 ?>
	</select><br>
	
<?php
  //�w��Ǵ��C�X�ǥ�
  if ($work_year_seme!='') {
  	$check_student=0;
  	//���o�ӾǴ���J�ǥͲM��
		$sql="SELECT a.*,b.stud_id,b.stud_name,b.stud_sex,b.stud_study_year FROM stud_move a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.move_kind in (2,3,14) AND move_year_seme='$move_year_seme' ORDER BY move_date DESC";
		$recordSet=$CONN->Execute($sql) or user_error("Ū��stud_move�Bstud_base��ƥ��ѡI<br>$sql",256);
		$col=3; //�]�w�C�@�C��ܴX�H
		$studentdata="����ܱ��ɵn���ǥ͡G<table>";
		while(!$recordSet->EOF) {
			$currentrow=$recordSet->currentrow()+1;
			if($currentrow % $col==1) $studentdata.="<tr>";
			$student_sn=$recordSet->fields['student_sn'];
			$stud_id=$recordSet->fields['stud_id'];
			$stud_name=$recordSet->fields['stud_name'];
			$stud_move_date=$recordSet->fields['move_date'];
			if($recordSet->fields['stud_sex']=='1') $color='#CCFFCC'; else  $color='#FFCCCC';
			if($student_sn==$selected_student) {
				$color='#FFFFAA';
				$stud_study_year=$recordSet->fields['stud_study_year'];
				$selected_student_id=$stud_id;
			}
	    
	    if ($student_sn==$selected_student) {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' checked onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			  $check_student=1;
			} else {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			}
			$studentdata.="<td bgcolor='$color' align='center'> $student_radio </td>";

			if( $currentrow % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
			$recordSet->movenext();
	  } // end while
			$studentdata.='</table><hr>';
		
    echo $studentdata;
    
    //�Y�w�I��ǥ�, �C�X�ӥͪ���Ƥηs�W���
    if ($check_student) {
    ?>
    <table>
    	<tr class='title_sbody2'>
				<td>���g�ƹꤧ�Ǧ~��/�Ǵ�</td>
				<td align='left' bgcolor='white'>
					<?php
				 $year_seme_select = "<select name='reward_year_seme'>\n";
				foreach($seme_list as $k=>$v) {
				if ($reward_year_seme==$k)
	      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
	      	else
	      		$year_seme_select.="<option value='$k'>$v</option>\n";
				}
				$year_seme_select.= "</select>"; 
				
				echo $year_seme_select;

					?>
				</td>
			</tr>

    	<tr class='title_sbody2'>
				<td>���g���O</td>
				<td align='left' bgcolor='white'><?php echo $reward_select;?></td>
			</tr>
			<tr class='title_sbody2'>
				<td>���g�ƥ�</td>
				<td align='left' bgcolor='white'><input type='text' name='reward_reason' value='<?php echo $reward_reason;?>' size='30' maxlength='30'></td>
			</tr>
			<tr class='title_sbody2'>
				<td>���g�̾�</td>
				<td align='left' bgcolor='white'><input type='text' name='reward_base' value='<?php echo $reward_base;?>' size='30' maxlength='30'></td>
			</tr>
			<tr class='title_sbody2'>
				<td>���g���</td>
				<td align='left' bgcolor='white'><?php echo $date_input;?>
			</tr>
	</table>
	<input type="button" value="�s�W�@��" onclick="check_data();"><br><font size=2 color=red>���`�N�G<br>1.�аȥ��T�{���g�ƹ�o�ͪ��ɶ��A�P�ӥͦb�Ǥ��Ǵ��O�_�k�X�A�H�K�y�����g�`��έp���~�����D�C<br>2.���V��J���~�A�N�ӵ��O���R�����s��J�Y�i�C<br>3.�t�αN�۰ʩ���g�ƥѥ[���u(�L�ռ��g)�v�r�ˡC</font>
  <hr>
    <?php
      //�C�X�ӥͩҦ�����
      
      //�H�ǥͬy�����B�z���

		
		$sn=$_POST['selected_student'];
		
		//�C�X
		?>
<table bgcolor=yellow width=100%><tr><td align="center">�ӥͥثe�w�ɵn���g����</td></tr></table>		
<center><table width=100%>
<tr class="title_sbody2">
<td align="center">�Ǧ~</td>
<td align="center">�Ǵ�</td>
<td align="center">���g�ƥ�</td>
<td align="center" width="70">���g���O</td>
<td align="center">���g�̾�</td>
<td align="center" width="80">���g�ͮĤ��</td>
<td align="center" width="80">�P�L���</td>
</tr>
<?php
$query="select * from reward where student_sn='$sn' order by reward_div,reward_date desc";
$res=$CONN->Execute($query);
while (!$res->EOF) 
{
$sel_year=substr($res->fields['reward_year_seme'],0,strlen($res->fields['reward_year_seme'])-1);
$sel_seme=substr($res->fields['reward_year_seme'],-1);
 
?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res->fields['reward_reason'];?></td>
				<td align="center"><?php echo $reward_arr[$res->fields['reward_kind']];?></td>
				<td align="left"><?php echo $res->fields['reward_base'];?></td>
				<td align="center"><?php echo $res->fields['reward_date'];?></td>
				<td align="center"><?php if ($res->fields['reward_kind']>0) { echo "---"; } else { if ($res->fields['reward_cancel_date']=="0000-00-00") { echo "���P�L"; } else { echo $res->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
				  <input type="image" src="images/del.png"  alt="�R��" onclick="if (confirm('�z�P�w�n:\n�R���@��<?php echo $res->fields['reward_date'];?>���O��?')) { document.myform.option1.value='<?php echo $res->fields['reward_id'];?>'; document.myform.act.value='del_reward';document.myform.submit(); }">
				</td>
		</tr>
		<?php
   $res->MoveNext();
} // end while
		?>
</table></center><p>

<?php

echo "<table border=1 width=100%><tr>";
echo "<td valign=top width=50%>";	
echo "<table bgcolor=yellow width=100%><tr><td align='center'>�����Ҹ��ۦP���ǥͼ��g����</td></tr></table>";		
	
//�Ѭy�����M�����Ҥ��O�_�ӥͥѸӮ���X�S��^
$res0=$CONN->Execute("select DISTINCT b.student_sn as ssn ,b.stud_person_id as spid ,b.stud_name as sname from stud_base a,stud_base b where a.student_sn='$sn' and a.stud_person_id=b.stud_person_id and a.student_sn<>b.student_sn");
while(!$res0->EOF) 
{
$ssn=$res0->fields['ssn'];
$spid=$res0->fields['spid'];
$sname=$res0->fields['sname'];

echo "�Ǹ�:".$ssn."������:".$spid." �m�W:".$sname."<br>"; 

?>
<table width=100%>
<tr class="title_sbody2">
<td align="center">�Ǧ~</td>
<td align="center">�Ǵ�</td>
<td align="center">���g�ƥ�</td>
<td align="center" width="70">���g���O</td>
<td align="center">���g�̾�</td>
<td align="center" width="80">���g�ͮĤ��</td>
<td align="center" width="80">�P�L���</td>

</tr>
<?php
 
$query="select * from reward where student_sn='$ssn' order by reward_div,reward_date desc";
$res2=$CONN->Execute($query);
while (!$res2->EOF) 
{
$sel_year=reward_date_year($res2->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǧ~
$sel_seme=reward_date_seme($res2->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǵ�

?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res2->fields['reward_reason'];?></td>
				<td align="center"><?php echo $reward_arr[$res2->fields['reward_kind']];?></td>
				<td align="left"><?php echo $res2->fields['reward_base'];?></td>
				<td align="center"><?php echo $res2->fields['reward_date'];?></td>
				<td align="center"><?php if ($res2->fields['reward_kind']>0) { echo "---"; } else { if ($res2->fields['reward_cancel_date']=="0000-00-00") { echo "���P�L"; } else { echo $res2->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
                  <input type="checkbox" name="join[]" value="<?php echo $res2->fields['student_sn'];?>">				
				</td>
		</tr>
		<?php
   $res2->MoveNext();
} // end while
		?>
</table>



<?php
   
	$res0->MoveNext();
}
?>
<input type="hidden" name="act_join" value="<?php echo $_POST['act_join'];?>">
<center><input type="submit" value="�[��ӥͥثe���g������" onclick="check_join();"></center>

<?php
 echo "</td>";
 
 echo "<td valign=top width=50%><table bgcolor=yellow width=100%><tr><td align='center'>XML�פJ���������g����</td></tr></table>";
 ?>
 
<table width=100%>
<tr class="title_sbody2">
<td align="center">�Ǧ~</td>
<td align="center">�Ǵ�</td>
<td align="center">���g�ƥ�</td>
<td align="center" width="70">���g���O</td>
<td align="center">���g����</td>
<td align="center">���g�̾�</td>
<td align="center" width="80">���g�ͮĤ��</td>
<td align="center" width="80">�P�L���</td>
<td align="left" width="80"><input type="checkbox" value='����' onclick='chkall("myform",this)'>����</td>

</tr>
<?php
 
$query="select * from reward_exchange where student_sn='$sn' order by reward_date desc";
$res3=$CONN->Execute($query);
while (!$res3->EOF) 
{
	
$sel_year=reward_date_year($res3->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǧ~
$sel_seme=reward_date_seme($res3->fields['reward_date']);//�Ѽ��g�����ɶ����X�Ǵ�


?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res3->fields['reward_reason'];?></td>
				<td align="center"><?php echo $res3->fields['reward_kind'];?></td>
				<td align="center"><?php echo $res3->fields['reward_numbers'];?></td>
				<td align="left"><?php echo $res3->fields['reward_base'];?></td>
				<td align="center"><?php echo $res3->fields['reward_date'];?></td>
				<td align="center"><?php if ($res3->fields['reward_kind']>0) { echo "---"; } else { if ($res3->fields['reward_cancel_date']=="0000-00-00") { echo "���P�L"; } else { echo $res3->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
				  <input type="checkbox" name="joinp[]" value="<?php echo $res3->fields['sn'];?>">
				</td>
		</tr>
		<?php
        $res3->MoveNext();
} // end while
		?>
</table>
<input type="hidden" name="act_joinp" value="<?php echo $_POST['act_joinp'];?>">
<center><input type="submit" value="�[��ӥͥثe���g������" onclick="check_joinp();"></center>

 <?php
 echo "</td></tr></table>";	
  
	} // end if selected_student
    
    
 } // end if
?>
	
	

</form>



<?php
foot();

//�Ѥ�����o�Ǧ~
function reward_date_year($date="",$sp="-"){
	global $SFS_SEME1,$CONN;
	$query = "select year from school_day where  day<='$date' and day_kind='start' order by day desc limit 1";
	$res = $CONN->Execute($query);
	return $res->fields[year];
}
//�Ѥ�����o�Ǵ�
function reward_date_seme($date="",$sp="-"){
	global $SFS_SEME1,$CONN;
	$query = "select seme from school_day where  day<='$date' and day_kind='start' order by day desc limit 1";
	$res = $CONN->Execute($query);
	return $res->fields[seme];
}


?>
<Script Language="JavaScript">
function check_data() {
 if (document.myform.reward_kind.value=='') { 
 	 alert('����ܼ��g���O!');
 	 return false;
 	}
 if (document.myform.reward_reason.value=='') { 
 	 alert('����J���g�ƥ�!');
 	 document.myform.reward_reason.focus(); 	 
 	 return false;
 	}
 if (document.myform.reward_base.value=='') { 
 	 alert('����J���g�̾�!');
 	 document.myform.reward_base.focus(); 	 
 	 return false;
 	}
 if (document.myform.temp_reward_date.value=='') { 
 	 alert('����J���g���!');
 	 document.myform.temp_reward_date.focus(); 	 
 	 return false;
 	}
  document.myform.act.value='add_reward';
  document.myform.submit();
}

function check_join() {
	
if (confirm('�z�P�w�n�[��ثe�w�ɵn���g������?')) 
{ 
  document.myform.act_join.value='join';
  
}
else
{
  document.myform.act_join.value='';

}
document.myform.submit();
}

function check_joinp() {

if (confirm('�z�P�w�n�[��ثe�w�ɵn���g������?')) 
{ 
  document.myform.act_joinp.value='joinp';
  
}
else
{
	document.myform.act_joinp.value='';
}

  document.myform.submit();
}

function chkall(input1,input2)
{
    var objForm = document.forms[input1];
    var objLen = objForm.length;
    for (var iCount = 0; iCount < objLen; iCount++)
    {
        if (input2.checked == true)
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = true;
            }
        }
        else
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = false;
            }
        }
    }
}

</Script>



