<?php
	// $Id: teach_config.php 9179 2017-12-01 14:09:00Z smallduh $
	//系統設定檔
	include_once "../../include/config.php";
	//函式庫
	include_once "../../include/sfs_case_PLlib.php";
	
	include_once "../../include/sfs_case_dataarray.php";
	//新增按鈕名稱
	$newBtn = " 新增資料 ";
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	//刪除按鈕名稱
	$delBtn = " 確定刪除 ";
	//確定新增按鈕名稱
	$postBtn = " 確定新增 ";
	//新增時啟用流水號功能
	$is_IDauto = 1 ; // 0 為取消	

	//搜尋代號
	$srchID = " 搜尋代號 ";
	//搜尋姓名
	$srchName = " 搜尋姓名 ";

	
	//左選單設定顯示筆數
	$gridRow_num = 16;
	//左選單底色設定
	$gridBgcolor="#DDDDDC";
	//左選單男生顯示顏色
	$gridBoy_color = "blue";
	//左選單女生顯示顏色
	$gridGirl_color = "#FF6633";
	//照片寬度
	$img_width = 120;	
	
	//目錄內程式
	$teach_menu_p = array("teach_list.php"=>"基本資料","teach_post.php"=>"任職資料","teach_connect.php"=>"網路資料","mteacher.php"=>"匯入教師資料","teach_list_photo.php"=>"在職教師一覽表","teach_list_subject.php"=>"任教一覽表","teach_appraisal.php"=>"提敘考核");
	
	//設定上傳圖片路徑
	$img_path = "photo/teacher";
	
	/* 上傳檔案暫存目錄 */
	$path_str = "temp/teacher";
	set_upload_path($path_str);
	$temp_path = $UPLOAD_PATH.$path_str;

	/*薪額類別*/
	$salary_kind_array=array("teacher"=>"教育人員","worker"=>"工友","skilled-worker"=>"技工","nurse-4"=>"士(生)級","nurse-3"=>"師(三)級","nurse-2"=>"師(二)級","nurse-1"=>"師(一)級","official-1"=>"委任1職等","official-2"=>"委任2職等","official-3"=>"委任3職等","official-4"=>"委任4職等","official-5"=>"委任5職等","official-6"=>"薦任6職等","official-7"=>"薦任7職等","official-8"=>"薦任8職等","official-9"=>"薦任9職等","official-10"=>"簡任10","official-11"=>"簡任11職等","official-12"=>"簡任12職等","official-13"=>"簡任13職等","official-14"=>"簡任14職等","other"=>"未銓敘幹事");

	/* 薪額陣列 */
	$salary_array['teacher']=array(90,100,110,120,130,140,150,160,170,180,190,200,210,220,230,245,260,275,290,310,330,350,370,390,410,430,450,475,500,525,550,575,600,625,650,680,710,740,770);
	$salary_array['worker']=array(90,95,100,105,110,115,120,125,130,135,140,145,150,155,160,165,170);
	$salary_array['skilled-worker']=array(120,125,130,135,140,145,150,155,160,165,170);
	$salary_array['official-1']=array(160,170,180,190,200,210,220,230,240,250,260,270,280);
	$salary_array['official-2']=array(230,240,250,260,270,280,290,300,310,320,330);
	$salary_array['official-3']=array(280,290,300,310,320,330,340,350,360,370,385,400,415);
	$salary_array['official-4']=array(300,310,320,330,340,350,360,370,385,400,415,430,445);
	$salary_array['official-5']=array(330,340,350,360,370,385,400,415,430,445,460,475,490,505,520);
	$salary_array['official-6']=array(385,400,415,430,445,460,475,490,505,520,535);
	$salary_array['official-7']=array(415,430,445,460,475,490,505,520,535,550,590);
	$salary_array['official-8']=array(445,460,475,490,505,520,535,550,590,610,630);
	$salary_array['official-9']=array(490,505,520,535,550,590,610,630,650,670,690,710);
	$salary_array['official-10']=array(590,610,630,650,670,690,710,730,750,780);
	$salary_array['official-11']=array(610,630,650,670,690,710,730,750,780,790);
	$salary_array['official-12']=array(650,670,690,710,730,750,780,790,800);
	$salary_array['official-13']=array(710,730,750,780,790,800);
	$salary_array['official-14']=array(800);
	$salary_array['nurse-1']=array(590,610,630,650,670,690,710,730,750,780,790);
	$salary_array['nurse-2']=array(445,460,475,490,505,520,535,550,590,610,630,650,670,690,710);
	$salary_array['nurse-3']=array(385,400,415,430,445,460,475,490,505,520,535,550,590);
	$salary_array['nurse-4']=array(280,290,300,310,320,330,340,350,360,370,385,400,415,430,445,460,475,490,505,520,535);
	$salary_array['other']=array(90,100,110,120,130,140,150,160,170,180,190,200,210,220,230,245,260,275,290,310,330,350,370,390,410,430,450,475,500,525,550,575,600,625,650,680,710,740,770);

	/*專業加給*/
	//教師   校長一律以最高 31320 , 其餘為在薪額(key)以下, 以該值為加給數
	$spec_array['teacher']=array("230"=>20130,"330"=>23160,"450"=>26290,"770"=>31320);
	//公務人員
	$spec_array['official']=array("1"=>17710,"2"=>17770,"3"=>17830,"4"=>18060,"5"=>18910,"6"=>20790,"7"=>21710,"8"=>24700,"9"=>25770,"10"=>29960,"11"=>32650,"12"=>36690,"13"=>37840,"14"=>40630);
	for ($i=1;$i<=14;$i++) {
		$key="official-".$i;
		$spec_array[$key]=$spec_array['official'];
	}

	//工友
	$spec_array['worker']=array("1"=>15100);
	$spec_array['skilled-worker']=array("1"=>15390);
	//
	$spec_array['other']=$spec_array['official'];
	//護理師
	$spec_array['nurse-1']=array("1"=>33195);
	$spec_array['nurse-2']=array("1"=>25250);
	$spec_array['nurse-3']=array("1"=>21220);
	$spec_array['nurse-4']=array("1"=>18920);

	/* 地域加給 */
    $area_allowance_array=array("無"=>0,"東台加給"=>0,"偏遠一級"=>10,"偏遠二級"=>20,"偏遠三級"=>30,"高山一級"=>10,"高山二級"=>10,"高山三級"=>20,"高山四級"=>30,"離島一級"=>10,"離島二級"=>20,"離島三級"=>30);

  //取得模組設定
  $m_arr = get_sfs_module_set();
  extract($m_arr, EXTR_OVERWRITE);
  

	//取得流水號
	function get_sfs_id() {
		global $DEFAULT_TEA_ID_TITLE, $DEFAULT_TEA_ID_NUMS,$CONN;
		$sqlstr = "select max(teach_id) as mm from teacher_base ";
		if ($DEFAULT_TEA_ID_TITLE)
			$sqlstr .= " where teach_id like '$DEFAULT_TEA_ID_TITLE%'";
		$result = $CONN->Execute($sqlstr) or die ($sqlstr);
		
		$num = 1;
		for ($i=0;$i<strlen($DEFAULT_TEA_ID_NUMS);$i++)
			$num *= 10;
		
		if ($result->fields[0] == '' ) {//第一筆			
			$temp = $num+ intval($DEFAULT_TEA_ID_NUMS);
		}
		else {
			$temp = substr($result->fields[0],strlen($DEFAULT_TEA_ID_TITLE));
			$temp = $num + intval($temp)+1;		
		}
		$temp =  $DEFAULT_TEA_ID_TITLE.substr($temp,1);	
		return $temp;	
	}


	
?>
