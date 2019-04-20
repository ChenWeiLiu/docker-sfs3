<?php

/**
* 身分模擬物件
* $Id: simu_class.php 8511 2015-09-02 01:05:33Z smallduh $
*/
if (!$CONN)
	exit;

include_once "../../include/sfs_case_dataarray.php";
class simu_class{
	var $smarty = null;
	var $adodb = null;
	var $t_path = '';

	/**
	* 建構式
	*/
	function simu_class(){
		global $CONN,$smarty;

		$this->adodb = &$CONN;
		$this->smarty = &$smarty;
		$this->t_path = dirname(__FILE__);
		$this->smarty->assign('post_office_p',room_kind());
		$this->smarty->assign('class_name', class_base());
		$this->smarty->assign('self',$this);

	}
	/*
	* 程式處理
	*/

	function process(){
		if (!empty($_POST['teach_id'])){
			$this->login_chk_teacher($_POST['teach_id']);
			exit;
		}
		head('身分模擬');
		echo $this->smarty->fetch($this->t_path."/templates/list.tpl");
		foot();
	}

	/**
	* 取得教師列表
	*/
	function get_teacher_name($sort,$mode){
		// 取得不可模擬 ID
		$m_arr = get_sfs_module_set();
		$limit_id = '';
		if (!empty($m_arr['limit_id'])){
			$temp_arr = explode(",",$m_arr['limit_id']);
			if ($m_arr['limit_kind']=='y')
				$limit_id = ' AND a.teach_id  IN(';
			else
				$limit_id = ' AND a.teach_id NOT IN(';
			foreach($temp_arr as $val)
				$limit_id .= "'$val',";
			$limit_id = substr($limit_id,0,-1).")";
		}
		if ($mode=="all") {
			$wherestr = " order by";
			switch($sort) {
				case "post" :
				$wherestr .= " b.post_office, b.post_kind,";
				break;
				case "title" :
				$wherestr .= " d.teach_title_id,";
				break;
				case "name" :
				$wherestr .= " a.name,";
				break;
				default :
				
				break;
			}
			$wherestr .= " d.rank, b.class_num";
		} else if (!empty($_POST['name'])){
			$wherestr = " and a.name like '%{$_POST['name']}%'";
		}
		else{
			return false;
		}
		$query="
		SELECT a.teacher_sn,a.teach_id,a.name, b.post_kind, b.post_office,d.title_name ,b.class_num,d.rank FROM teacher_base a , teacher_post b, teacher_title d WHERE a.teacher_sn = b.teacher_sn AND b.teach_title_id = d.teach_title_id  AND a.teach_condition = 0 ".$limit_id . $wherestr ;

		$res = $this->adodb->Execute($query) or die($query);
		return $res->getRows();
	}

	/**
	* 模擬教師帳號
	*/
	function login_chk_teacher($log_id){
		global $SFS_PATH_HTML;
	
		$sql_select = " select teacher_sn,name,login_pass from teacher_base where teach_condition = 0 and teach_id='$log_id' and teach_id<>''";
		$recordSet = $this->adodb->Execute($sql_select) or trigger_error("資料連結錯誤：" , E_USER_ERROR);
        
		while(list($teacher_sn, $name, $login_pass) = $recordSet -> FetchRow()){
			//先清除必要變數
			$session_log_id="";
			$session_tea_name="";
			$session_tea_sn="";
			$session_prob_open="";
			$session_who="";
			$session_login_chk="";
			$session_prob = get_session_prot();
			/***
			//啟用SESSION
			session_start(); 
			session_register("session_log_id"); 
			session_register("session_tea_name");
			session_register("session_tea_sn");
			session_register("session_prob_open");
			session_register("session_who");
			session_register("session_log_pass");
			session_register("session_login_chk");
			session_register($session_prob);
			***/
			$_SESSION['session_log_id'] = $log_id;
			$_SESSION['session_tea_sn'] = $teacher_sn;
			$_SESSION['session_tea_name'] = $name;
			$_SESSION['session_who'] = "教師";
			$_SESSION['session_log_pass'] = $login_pass;			
			
			$_SESSION[$session_prob] = get_prob_power($teacher_sn,"教師");
			
			// 記錄使用者狀態
			$query = "insert into pro_user_state (teacher_sn,pu_state,pu_time,pu_ip) values($teacher_sn,1,now(),'{$_SERVER['REMOTE_ADDR']}')";
			$this->adodb->Execute($query) or user_error("新增失敗！<br>$query",256);
			header("Location: $SFS_PATH_HTML"."index.php");

		}
	}


}

?>
