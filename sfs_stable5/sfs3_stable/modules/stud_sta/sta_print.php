<?php

include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔

if ($_POST['form_act'] == 'prt') {
    $template_file = dirname(__file__) . "/templates/sta_to_print.htm";
} else if ($_POST['form_act'] == 'prteng') {
    $template_file = dirname(__file__) . "/templates/sta_to_print_eng.htm";
}

//建立物件
$obj = new stud_sta($CONN, $smarty);

//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("stud_sta模組");之前
$obj->process();
$obj->display($template_file);

//物件class
class stud_sta {
    
    var $CONN; //adodb物件
    var $smarty; //smarty物件
    var $set; //模組設定
    var $sch; //學校設定
    var $seme;

    //建構函式
    function stud_sta($CONN, $smarty) {
        $this->CONN = &$CONN;
        $this->smarty = &$smarty;
    }

    //初始化
    function init() {		
        $this->set = get_sfs_module_set("stud_sta");
        $this->sch = get_school_base();
        $this->need_teacher = $_POST[need_teacher] ? "導師：__________________" : "";

    }

    //程序
    function process() {
        if ($_POST[stu] == '' && $_GET[prove_id] == '')
            die("無資料");
        if ($_GET[prove_id] != '') {
            $this->stu[] = $this->one($_GET[prove_id]);
            return;
        }
        if ($_POST[stu] != '' && $_POST[form_act] == 'prt') {
            foreach ($_POST[stu] as $id => $null) {
                $this->stu[] = $this->one($id);
            }
//			echo "<pre>";print_r($_POST);//echo $SQL;
        }

        if ($_POST[stu] != '' && $_POST[form_act] == 'prteng') {
            foreach ($_POST[stu] as $id => $null) {
                $this->stu[] = $this->one($id);
            }
//			echo "<pre>";print_r($_POST);//echo $SQL;
        }
    }

    //顯示
    function display($tpl) {
        $this->smarty->assign("this", $this);
        $this->smarty->display($tpl);
    }

    //擷取資料
    function one($id) {
//		$curr_seme = curr_year().curr_seme();
        global $UPLOAD_PATH, $UPLOAD_URL;
        if ($id == '')
            return;
        $SQL = "select prove_id,student_sn,prove_year_seme,prove_date from stud_sta  where prove_id='$id' ";
        $rs = &$this->CONN->Execute($SQL) or die($SQL);
        if ($rs and $ro = $rs->FetchNextObject(false)) {
            $stu1 = get_object_vars($ro);
        }
        $y_seme = $stu1[prove_year_seme]; //951
        $y_seme2 = sprintf("%04d", $stu1[prove_year_seme]); //0951
        $stu1[seme] = substr($y_seme, -1); //取學期
        $SQL = "select a.stud_name,a.stud_name_eng,a.stud_id,a.stud_birthday,b.seme_class,a.stud_study_year from stud_base a ,stud_seme b  where a.student_sn='{$stu1[student_sn]}' and a.student_sn=b.student_sn and b.seme_year_seme='$y_seme2' ";
        $rs = &$this->CONN->Execute($SQL) or die($SQL);

        if ($rs and $ro = $rs->FetchNextObject(false)) {
            $stu2 = get_object_vars($ro);
            if ($_POST['need_photo']) {
                $myphoto = $UPLOAD_PATH . "photo/student/" . $ro->stud_study_year . "/" . $ro->stud_id;
                $myphotoUrl = $UPLOAD_URL . "photo/student/" . $ro->stud_study_year . "/" . $ro->stud_id;
                if (file_exists($myphoto))
                    $stu2[photo] = "<img src='$myphotoUrl' height=200 align='left' border=1  hspace=10 vspace=10>";
                else
                    $stu2[photo] = "";
            }
            else
                $stu2[photo] = "";
        }
        $stu2["seme_class2"] = substr($stu2[seme_class], 0, 1); //取學期
        if ($stu2["seme_class2"] > 6)
            $stu2["seme_class2"] = $stu2["seme_class2"] - 6; //國中
        $stu = array_merge($stu1, $stu2);

//		echo "<pre>";print_r($stu);echo $SQL;
        return $stu;
//		echo "<pre>";print_r($this->stu);//echo $SQL;
    }

    function CD($d, $type) {
        $d = split("-", $d);
        if ($type == 'Y')
            return $d[0] - 1911;
        if ($type == 'm')
            return $d[1] + 0;
        if ($type == 'd')
            return $d[2] + 0;
    }

    function CD2($d, $type) {
        $d = split("-", $d);
        if ($type == 'Y')
            return $d[0];
        if ($type == 'm') {
            switch ($d[1]) {
                case 1:
                    return 'January';
                case 2:
                    return 'February';
                case 3:
                    return 'March';
                case 4:
                    return 'April';
                case 5:
                    return 'May';
                case 6:
                    return 'June';
                case 7:
                    return 'July';
                case 8:
                    return 'August';
                case 9:
                    return 'September';
                case 10:
                    return 'October';
                case 11:
                    return 'November';
                case 12:
                    return 'December';
            }
        }
        if ($type == 'd') {
            switch ($d[2]) {
                case 1:
                    return 'st';
                case 2:
                    return 'nd';
                case 3:
                    return 'rd';
                default:
                    return 'th';
            }
        }
        if ($type == 's') {
            switch ($d[0]) {
                case 1:
                    return 'first';
                case 2:
                    return 'second';
                case 3:
                    return 'third';
                case 4:
                    return 'forth';
                case 5:
                    return 'fifth';
                case 6:
                    return 'six';
            }
        }
    }

}

?>
