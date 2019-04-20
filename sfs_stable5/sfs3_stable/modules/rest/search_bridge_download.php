<?php
// $Id: output_xml.php 8928 2016-07-20 18:11:45Z smallduh $

include_once "xml_function.php";

//取得模組參數的類別設定
$m_arr = get_module_setup('toxml');
extract($m_arr,EXTR_OVERWRITE);

require "../toxml/class.php";

//只取最新一筆 , 依 move_date 排序
$sql_select = "select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
$recordSet=$CONN->Execute($sql_select) or die($sql_select);

if ($recordSet->RecordCount()>0) {
	$row=$recordSet->fetchRow();
	//是否過期
	if (strtotime(date("Y-m-d"))>strtotime($row['download_deadline']." 23:59:59")) {
        $data = array();
        $SERVICE['result'] = -1;
        $SERVICE['message'] = "下載期限已過!";
    } elseif ($row['download_times']>=$row['download_limit']) {
        $data = array();
        $SERVICE['result'] = -1;
        $SERVICE['message'] = "已超過下載次數限制! (".$row['download_limit']."次)";
    } else {
		$SERVICE['result']=1;
        $row['from_school_name']=$SCHOOL_BASE['sch_cname'];
        $SERVICE['resource_student']=$row;   //下載學生資訊
		$student_sn=$row['student_sn'];
        //下載次數加1
        $download_times=$row['download_times']+1;
        $CONN->Execute("update `stud_move` set download_times='$download_times' where move_id='{$row['move_id']}'");
	}
} else {
	$data=array();
	$SERVICE['result']=-1;
	$SERVICE['message']="查無此學生轉出記錄!";
}



//如果確定輸出XML檔案

if ($SERVICE['result']==1) {

	$stud_arr[$student_sn]=$student_sn;

            $xml_obj=new sfsxmlfile();
            $xml_obj->student_sn=$stud_arr;

	 	    $xml_obj->output();

            //學校代碼 $school_edu_id
            $smarty->assign("school_edu_id",$SCHOOL_BASE['sch_id']);
            //學籍資料
            $smarty->assign("data_arr",$xml_obj->out_arr);
            //性別陣列
            $smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
            //身份別陣列 (備註暫不產生)
            $smarty->assign("stud_kind_arr",stud_kind());
            //證照類別陣列
            $smarty->assign("id_kind_arr",stud_country_kind());
            //學生班級性質陣列
            $smarty->assign("class_kind_arr",stud_class_kind());

            //學生特殊班類別陣列
            $smarty->assign("spe_kind_arr",stud_spe_kind());
            //學生特殊班上課性質陣列
            $smarty->assign("spe_class_id_arr",stud_spe_class_id());
            //學生特殊班班別陣列
            $smarty->assign("spe_class_kind_arr",stud_spe_class_kind());
            //國中小判定 SFS 4.0 必須修正
            $smarty->assign("jhores",$IS_JHORES);
            //入學資格陣列
            $smarty->assign("preschool_status_arr",stud_preschool_status());

            //畢修業陣列
            $smarty->assign("grad_kind_arr",grad_kind());

            //存歿陣列
            $smarty->assign("is_live_arr",is_live());
            //與父關係陣列
            $smarty->assign("f_rela_arr",fath_relation());
            //與母關係陣列
            $smarty->assign("m_rela_arr",moth_relation());
            //與監護人關係陣列
            $smarty->assign("g_rela_arr",guardian_relation());
            //學歷陣列
            $smarty->assign("edu_kind_arr",edu_kind());
            //兄弟姐妹陣列
            $smarty->assign("bs_calling_kind_arr",bs_calling_kind());

            //生涯輔導考慮因素陣列
            $factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
            foreach($factor_items as $item=>$title){
                $factors[$item]=SFS_TEXT($title);
            }
            $smarty->assign("factors",$factors);

            //抓取各學期應出席日數
            $query="select * from seme_course_date order by seme_year_seme,class_year";
            $res=$CONN->Execute($query);
            while(!$res->EOF) {
                $current_seme_year_seme=$res->fields[seme_year_seme];
                $row_data=$res->FetchRow();
                $seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
            }
            $smarty->assign("seme_course_date_arr",$seme_course_date_arr);

            //將smarty輸出的資料先cache住
            ob_start();
            $smarty->display("student_3_0.tpl");
            $xmls=ob_get_contents();
            ob_end_clean();
            //ob_clean();
            //將空值以null取代
            $xmls=str_replace("><",">null<",$xmls);
            $xmls=str_replace("> <",">null<",$xmls);

            $data=$xmls;

}

?>