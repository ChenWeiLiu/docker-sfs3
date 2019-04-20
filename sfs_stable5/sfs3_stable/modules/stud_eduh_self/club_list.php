<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $


//檢查是否開放社團模組
if ($m_arr["club_enable"]!="1"){
   echo "目前不開放社團活動模組！";
   exit;
}


//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

	//取得學期社團設定
  $SETUP=get_club_setup($year_seme);

	$school_kind_name[100]="跨年";
	$class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
	$class_year_array[100]="100";
	?>
			<?php
			//依年級列出社團一覽表 , 檢查 club_class值
      foreach ($class_year_array as $K=>$class_year_name) {
			  $query="select * from stud_club_base where year_seme='$c_curr_seme' and club_class='$K' order by club_name";
			  $result=mysql_query($query);
			  //該年級有社團再列出
			  if (mysql_num_rows($result)) {
			?>
 			<table border="0" style="border-collapse:collapse" bordercolor="#000000" width="100%">
			<tr>
			   <td valign="top" style="color:#800000">
			   	<?php echo $school_kind_name[$K];?>級社團
			  </td>
			</tr>
			<tr>
				<td>
									<?php
             	      	list_class_club_choice_detail($c_curr_seme,$K,0,0); //列出年級社團選課明細
                	?>
        </td>      	
      </tr>
      </table>
			<?php
			  } // end if
			} // end foreach
			?>

