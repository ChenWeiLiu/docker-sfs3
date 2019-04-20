<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

//載入社團活動模組的公用函式
include_once "../stud_club/my_functions.php";


	//取回系統控制變數
	$MSETUP =get_module_setup("stud_club");
	//學生可選填志願數
	$choice_num=$MSETUP['choice_num'];
	//預設開放選填時間
	$choice_sttime=$MSETUP['choice_sttime'];
	//預設結束選填時間
	$choice_endtime=$MSETUP['choice_endtime'];

sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}


//秀出網頁
head("社團活動 - 社團活動");

//模組選單
print_menu($menu_p);


//檢查是否開放社團模組
if ($m_arr["club_enable"]!="1"){
   echo "目前不開放社團活動模組！";
   exit;
}

$_POST['club_menu']=($_POST['club_menu']=='')?"club_list.php":$_POST['club_menu'];

?>
 <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" name="menu_form">
  <table border="0">
    <tr>
    	<td style="color:#0000FF">請選擇你要的功能：</td>
      <td><input type="radio" name="club_menu" value="club_list.php" <?php if ($_POST['club_menu']=='club_list.php') echo "checked";?> onclick='document.menu_form.submit()'>本學期社團一覽表</td>
      <td><input type="radio" name="club_menu" value="club_choice.php" <?php if ($_POST['club_menu']=='club_choice.php') echo "checked";?> onclick='document.menu_form.submit()'>社團選課</td>
      <td><input type="radio" name="club_menu" value="club_feedback.php" <?php if ($_POST['club_menu']=='club_feedback.php') echo "checked";?> onclick='document.menu_form.submit()'>填寫自我省思</td>
    </tr>
  </table>
 </form>
 
<?php

if ($_POST['club_menu']!="") {
 include_once($_POST['club_menu']);
}

?>
