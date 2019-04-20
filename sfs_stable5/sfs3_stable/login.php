<?php
// $Id: login.php 9253 2018-05-11 02:21:19Z igogo $

require "include/config.php" ; 
//取得LDAP模組相關資訊
    $query="select * from sfs_module where dirname='ldap' and islive='1'";
  	$res=$CONN->Execute($query) or die('Error! SQL='.$query);;
     if ($res->RecordCount()>0) {
  		$query="select * from ldap limit 1";
  		$res=$CONN->Execute($query); // or die('Error! SQL='.$query);  
  		if (!$res) {
  			$LDAP['enable']=0;
  			$LDAP['enable1']=0;
  		} else {
  			$LDAP=$res->fetchrow();  
  		}
     } else {
      $LDAP['enable']=0;
      $LDAP['enable1']=0;
     }

//先清除必要變數
$session_log_id="";
$session_tea_name="";
$session_tea_sn="";
$session_prob_open="";
$session_who="";
$session_login_chk="";

//avoid javascript hijack cookie
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies",1);
//啟用SESSION
session_start(); 

//若啟用 OpenID且已驗證成功
if ($OpenID_enable) {
	require_once "./include/OIDpackage/commonclass.php";
	$obj = new TC_OID_BASE();
	//session_start();
	$obj->setFinishFile("login.php");
	//進行 OPEN_ID認證, 且認證通過
	if( $obj->getResponseStatus($msg) >0) {
        $_POST['log_id'] = '';
        $_POST['log_pass'] = '';
        $_POST['log_who'] = '教師';
        $arr = $obj->getResponseArray();

        //比對 [identity] => http://xxxxxx.openid.tc.edu.tw/ 是否為正碓的 provider
        $o = explode(".", substr($arr['identity'], 7, strlen($arr['identity']) - 8), 2);
        $oid = $o[1];
        if ($oid != $OpenID_dn) {
            header("Location: login.php");    //非合法的 OpenID provider
        }

        //比對 $arr[edusid] 是否為本學校人員
        $sql="select sch_id from school_base limit 1";
        $res=$CONN->Execute($sql);
        $sch_id=$res->fields['sch_id'];
        if ($sch_id!=$arr['edusid']) {
            header("Location: login.php");   //非本校人員
        }
        //開始比對 $arr['tcguid'] 是否與 sfs 資料庫裡的身分證字號相符
  	    //把 teach_base 裡的 teach_person_id 取出
  	    $sql="select teach_id,teach_person_id,login_pass from teacher_base where teach_condition=0";
		$res=$CONN->Execute($sql);
		while ($row=$res->FetchRow()) {
		  $ID_SHA=hash('sha256',$row['teach_person_id']);
		  //比對
    	//如果驗證成功, 把 $_POST['log_id'] , $_POST['log_pass'] , $_POST['log_who'] 填入資料 ,
  		//並註記 $OpenID_login=1 , 以便 $log_pass 不要執行 pass_operate 函式
			if ($arr['tcguid']==$ID_SHA) {
		   $_POST['log_id']=$row['teach_id'];
		   $OpenID_LOG_PASS=$row['login_pass'];
		   $LDAP['enable']=0; //把 LDAP 驗證關閉
		   $OpenID_login=1;
		   //echo $_POST['log_id'];
		   break;
		  } // end if
		} // end while

	}	
} //================================================================

/*
//session_register("session_log_id"); 
//session_register("session_log_pass");
//session_register("session_tea_name");
//session_register("session_tea_sn");
//session_register("session_prob_open");
//session_register("session_who");
//session_register("session_login_chk");
*/
//如果自然人憑證登入未啟用, 導向到一般登入
if ($_REQUEST['cdc'] && !$CDCLOGIN) header("Location: ".$SFS_PATH_HTML."login.php");

//如果採自然人憑證登入, 則產生明文
if ($_REQUEST['cdc'] && $_POST['id4']=="") {
	set_encrypt();
}

if ($_POST['id4']) $_POST[log_who]="CDC";

//先除去兩端空白
$_POST['log_id']=trim($_POST['log_id']);
$_POST['log_pass']=trim($_POST['log_pass']);

//檢查密碼狀態
if (!empty($_POST['log_pass']))
	$_SESSION['session_login_chk']=pass_check($_POST['log_pass'],$_POST['log_id']);

//配合中心端版本改變,記錄學校ID
$session_prob = get_session_prot();
//session_register($session_prob);

//解決隱碼登入問題
if (strstr($_POST['log_id'],"'")) {
	$_POST['log_id']=str_replace("'", "", $_POST['log_id']);
	bad_login($_POST['log_id'],1);
}
$_POST['log_pass']=str_replace("'", "", $_POST['log_pass']);

//阻擋連續嚐試登入
if (chk_login_err()) $_POST[log_who]="";

if ($_POST['log_who']=='教師')
	//採用 OpenID登入時，驗證通過，密碼自動自資料庫取出，不需再將密碼編碼
	$log_pass = ($OpenID_login==1)?$OpenID_LOG_PASS:pass_operate($_POST['log_pass']);
	//$log_pass = pass_operate($_POST['log_pass']);
else 
   $log_pass = $_POST['log_pass'];

// 確定連線成立
if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

// 登出
if ($_GET['logout'] == "yes"){
	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	setcookie(session_name(),'',time()-3600);
        $_SESSION = array();
	header("Location: $SFS_PATH_HTML");
}
 
//若OpenID已驗證通過, 略過圖片檢查
if (!$OpenID_login) {
  //登入圖片驗証
  if (!chk_login_img($_SESSION["Login_img"],$_POST["log_pass_chk"])) $log_pass="";
  //kitten
  if ($_SESSION['CAPTCHA']['TYPE']==1 && $_SESSION["kittenCheck"]!="OK") $log_pass="";
}
switch($_POST[log_who]){
	case "教師":
	$REFERER=login_chk_teacher($_POST['log_id'],$log_pass);
	$REFERER = preg_replace('!\r|\n.*!s','',$REFERER);
	header("location: $REFERER");
	break;
	
	case "CDC":
	$REFERER=login_chk_cdc();
	$REFERER = preg_replace('!\r|\n.*!s','',$REFERER);
	header("location: $REFERER");
	break;

	case "家長":
	$REFERER=login_chk_parent($_POST['log_id'],$log_pass);
	$REFERER = preg_replace('!\r|\n.*!s','',$REFERER);
	header("location: $REFERER");
	break;
	
	case "學生":
	$REFERER=login_chk_student($_POST['log_id'],$log_pass);
	$REFERER = preg_replace('!\r|\n.*!s','',$REFERER);
	header("location: $REFERER");
	break;
	
	case "其他":
	$REFERER=login_chk_other($_POST['log_id'],$log_pass);
	$REFERER = preg_replace('!\r|\n.*!s','',$REFERER);
	header("location: $REFERER");
	break;
	
	default:
	head("密碼檢查", "", 1);
	include $THEME_FILE."_login.php";
	foot();
}

// 找出任職中符合該帳號密碼的教師
function login_chk_teacher($log_id = "", $log_pass = ""){
	global $CONN,$SFS_PATH_HTML,$session_prob,$LDAP,$OpenID_login;
	if (!get_magic_quotes_gpc()) {
              $log_id=addslashes($log_id) ;
              $log_pass=addslashes($log_pass) ;
        }

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	//若 LDAP 模組啟用, 進行 LDAP 帳號驗證
  if ($LDAP['enable']) {
    $LDAP_LOGIN=login_chk_from_ldap("教師");
  } 
 
   //若 LDAP登入成功或LDAP未啟動則以正常程序檢驗
  if (($LDAP['enable']==1 and $LDAP_LOGIN==true) or $LDAP['enable']==0) {  	 


	$sql_select = " select teacher_sn,name, login_pass, last_chpass_time from teacher_base where teach_condition = 0 and teach_id='$log_id' and login_pass='$log_pass' and teach_id<>''";
	$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);
        
	while(list($teacher_sn, $name , $login_pass, $last_chpass_time) = $recordSet -> FetchRow()){
		$_SESSION['session_log_id'] = $log_id;
		$_SESSION['session_log_pass'] = $login_pass;
		$_SESSION['session_tea_sn'] = $teacher_sn;
		$_SESSION['session_tea_name'] = $name;
		$_SESSION['session_who'] = "教師";
		$_SESSION[$session_prob] = get_prob_power($teacher_sn,"教師");
		login_logger($teacher_sn,"教師");

		// 更新 ldap 密碼
		$ldap_password = createLdapPassword($_POST['log_pass']);
		$query = "UPDATE teacher_base SET ldap_password='$ldap_password' WHERE teach_id='$log_id'";
		$CONN -> Execute($query);
		
		// 記錄使用者狀態
		$query = "insert into pro_user_state (teacher_sn,pu_state,pu_time,pu_ip) values($teacher_sn,1,now(),'{$_SERVER['REMOTE_ADDR']}')";
		$CONN -> Execute($query) or user_error("新增失敗！<br>$query",256);
		$REFERER=($_SERVER[HTTP_REFERER]==($SFS_PATH_HTML."login.php"))?$SFS_PATH_HTML."index.php":$_SERVER[HTTP_REFERER];

//強制幾日後需更改新密碼		
$m_arr = get_module_setup("chpass");
$vd=$m_arr['chpass_period']?$m_arr['chpass_period']:90;
$chdate=date("Y-m-d", strtotime("-$vd days"));
if($last_chpass_time<$chdate)
{
	//$REFERER=$SFS_PATH_HTML."modules/chpass/teach_cpass.php?alert=ok";
	$REFERER=$SFS_PATH_HTML."modules/chpass/remind.php";
}
//強制幾日後需更改新密碼

		return $REFERER;
	} // end while 
  
  }
	bad_login($log_id,2);
	return $_SERVER[HTTP_REFERER];
}


// 找出家長資料中符合該帳號密碼的家長
function login_chk_parent($log_id = "", $log_pass = ""){
	global $CONN,$SFS_PATH_HTML,$session_prob;

	if (!get_magic_quotes_gpc()) {
              $log_id=addslashes($log_id) ;
              $log_pass=addslashes($log_pass) ;
        }
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$AA = $CONN -> Execute("select * from parent_auth where 1=0") or user_error("讀取失敗！",256);
	if($AA){
		$sql_select = "select parent_id,parent_sn,parent_pass from parent_auth where (parent_id='$log_id' or login_id='$log_id') and parent_pass='$log_pass' and enable=2";
		//echo $sql_select;
		$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);
		while(list($login_id,$parent_sn,$parent_pass) = $recordSet -> FetchRow()){
			$_SESSION['session_log_id'] = $log_id;
			$_SESSION['session_log_pass'] = $parent_pass;
			$_SESSION['session_tea_sn'] = $parent_sn;
			//找出家長姓名
			$parent_name=parent_name($parent_sn);
			$_SESSION['session_tea_name'] = $parent_name;
			//echo $login_id.$parent_sn.$name;
			$_SESSION['session_who'] = "家長";
			$_SESSION[$session_prob] = get_prob_power($parent_sn,"家長");
			login_logger($teacher_sn,"家長");

			$REFERER=($_SERVER[HTTP_REFERER]==($SFS_PATH_HTML."login.php"))?$SFS_PATH_HTML."index.php":$_SERVER[HTTP_REFERER];
			return $REFERER;
		}
	}
	bad_login($log_id,2);
	return $_SERVER[HTTP_REFERER];	
}

// 找出學生資料中符合該帳號密碼的學生
function login_chk_student($log_id = "", $log_pass = ""){
	global $CONN,$SFS_PATH_HTML,$session_prob,$LDAP;
	if (!get_magic_quotes_gpc()) {
              $log_id=addslashes($log_id) ;
              $log_pass=addslashes($log_pass) ;
        }
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	//若 LDAP 模組啟用, 進行 LDAP 帳號驗證
	
  if ($LDAP['enable1']) {
    $LDAP_LOGIN=login_chk_from_ldap("學生");    
  } 
  
  //若 LDAP登入成功或LDAP未啟動則以正常程序檢驗
  if (($LDAP['enable1']==1 and $LDAP_LOGIN==true) or $LDAP['enable1']==0) {  	 
    
	$sql_select = "select student_sn,stud_name, email_pass from stud_base where stud_id='$log_id' and stud_study_cond in (0,15) and email_pass='$log_pass' and stud_id <>''";
	$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);
	
	while(list($student_sn,$name,$email_pass) = $recordSet -> FetchRow()){
		$_SESSION['session_log_id'] = $log_id;
		$_SESSION['session_tea_sn'] = $student_sn;
		$_SESSION['session_log_pass'] = $email_pass;
		$_SESSION['session_tea_name'] = $name;
		$_SESSION['session_who'] = "學生";
		$_SESSION[$session_prob] = get_prob_power($student_sn,"學生");
		login_logger($teacher_sn,"學生");
		
		// 更新 ldap 密碼
		$ldap_password = createLdapPassword($_POST['log_pass']);
		$query = "UPDATE stud_base SET ldap_password='$ldap_password' WHERE stud_id='$log_id'";
		$CONN -> Execute($query);
		
		
		// 記錄使用者狀態
		$query = "insert into pro_user_state (teacher_sn,pu_state,pu_time,pu_ip) values($student_sn,1,now(),'{$_SERVER['REMOTE_ADDR']}')";
		$CONN -> Execute($query) or user_error("新增失敗！<br>$query",256);
		$REFERER=($_SERVER[HTTP_REFERER]==($SFS_PATH_HTML."login.php"))?$SFS_PATH_HTML."index.php":$_SERVER[HTTP_REFERER];
		return $REFERER;
	}
  }
	bad_login($log_id,2);
	return $_SERVER[HTTP_REFERER];
}


//找出家長姓名
function parent_name($parent_sn = ""){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_parent_name = "select sd.guardian_name from  stud_domicile as sd , parent_auth as pa where  sd.guardian_p_id=pa.parent_id  and pa.parent_sn='$parent_sn'";	
	$rs_parent_name =$CONN -> Execute($sql_parent_name) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);		
	$parent_name=$rs_parent_name->fields['guardian_name'];	
	return $parent_name;
}

// 找出其他資料
function login_chk_other($log_id = "", $log_pass = ""){
	global $CONN,$SFS_PATH_HTML,$session_prob;
	
	return $_SERVER[HTTP_REFERER];
}

// 找出註冊憑證序號符合的教師
function login_chk_cdc(){
	global $CONN,$SFS_PATH_HTML,$session_prob,$_POST;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if ($_POST['id4']) {
		$cdc = new CDC();
		$cdc->setCerSn($_POST['serialnumber']);
		$cdc->setPtxt($_SESSION['ToBeSign']);
		$cdc->setEtxt($_POST['encrypted']);
		$cdc->setCert($_POST['pk']);
		$isValid = $cdc->cerLogin();
		if ($isValid == 1) {
			$_SESSION['CerSn'] = $cdc->cer_sn;
			$sql_select = " select teacher_sn, name, login_pass, teach_id from teacher_base where teach_condition = 0 and cerno='".$cdc->cer_sn."' and teach_id<>''";
			$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);

			if ($recordSet->RecordCount() == 0) 
				return  $SFS_PATH_HTML.'login.php?cdc=1&cdc_error=1';
			
			
			while(list($teacher_sn, $name , $login_pass, $log_id) = $recordSet -> FetchRow()){
				$_SESSION['session_log_id'] = $log_id;
				$_SESSION['session_log_pass'] = $login_pass;
				$_SESSION['session_tea_sn'] = $teacher_sn;
				$_SESSION['session_tea_name'] = $name;
				$_SESSION['session_who'] = "教師";
				$_SESSION[$session_prob] = get_prob_power($teacher_sn,"教師");
				login_logger($teacher_sn,"教師");

				// 記錄使用者狀態
				$query = "insert into pro_user_state (teacher_sn,pu_state,pu_time,pu_ip) values($teacher_sn,1,now(),'{$_SERVER['REMOTE_ADDR']}')";
				$CONN -> Execute($query) or user_error("新增失敗！<br>$query",256);
				$REFERER=($_SERVER[HTTP_REFERER]==($SFS_PATH_HTML."login.php?cdc=1")||$_SERVER[HTTP_REFERER]==($SFS_PATH_HTML."login.php"))?$SFS_PATH_HTML."index.php":$_SERVER[HTTP_REFERER];
				return $REFERER;
			}
		}
	}

	bad_login($log_id,2);
	
	return $_SERVER[HTTP_REFERER];
}

//記錄錯誤登入
function bad_login($log_id="",$err_kind=0) {
	global $CONN,$REMOTE_ADDR;

	$err_arr=array("1"=>"疑似資料隱碼攻擊(SQL Injection)","2"=>"一般登入錯誤","3"=>"帳號不存在","4"=>"該師非在職","5"=>"該生非在籍","6"=>"該家長帳號未開通");
	switch($err_kind){
		case 2:
			$query="select * from teacher_base where teach_id='$log_id'";
			$res=$CONN->Execute($query);
			if ($res->fields[teach_condition]=='') {
				$query="select * from stud_base where stud_id='$log_id' order by stud_study_year desc";
				$res=$CONN->Execute($query);
				if ($res->fields[stud_study_cond]=='') {
					$query="select * from parent_auth where parent_id='$log_id'";
					$res=$CONN->Execute($query);
					if ($res->fields[enable]=='') {
						$err_kind=3;
					} elseif ($res->fields[enable]!='2') {
						$err_kind=6;
					}
				} elseif ($res->fields[stud_study_cond]!='0') {
					$err_kind=5;
				}
			} elseif ($res->fields[teach_condition]!='0') {
				$err_kind=4;
			}
		case 1:
			$CONN->Execute("insert into bad_login (log_id,log_ip,err_kind,log_time) values ('$log_id','$REMOTE_ADDR','".$err_arr[$err_kind]."','".date("Y-m-d H:i:s")."')");
			break;
	}
}

//統計連續測試登入
function chk_login_err() {
	global $CONN,$REMOTE_ADDR,$UPLOAD_PATH;

	//讀設定檔
	$temp_file=$UPLOAD_PATH."system/bad_login_protect";

	if (!is_file($temp_file)) return false;

	$fp=fopen($temp_file,"r");
	$k=fgets($fp,10);
	fclose($fp);
	if (intval($k)<1) $k=3;

	$query="select * from bad_login where log_ip='$REMOTE_ADDR' and log_time>'".date("Y-m-d H:i:s",strtotime("-1 minute"))."'";
	$res=$CONN->Execute($query);
	if ($res) {
		if ($res->RecordCount()>$k) return true;
	}
}

//記錄登入記錄
function login_logger($tea_sn,$who) {
	global $CONN,$REMOTE_ADDR;

	if ($tea_sn!="" && $who!="") {
		$t=date("Y-m-d H:i:s", time());
		$query="select count(teacher_sn) as n from login_log_new where teacher_sn = '$tea_sn' and who = '$who'";
		$res=$CONN->Execute($query);
		$num=$res->fields['n'];
		if ($num>9) {
			$query="delete from login_log_new where teacher_sn = '$tea_sn' and who = '$who' and (no='0' or no>'9')";
			$CONN->Execute($query);
			$query="update login_log_new set no=no-1 where teacher_sn = '$tea_sn' and who = '$who' order by teacher_sn,no";
			$CONN->Execute($query);
			$num=9;
		}
		$CONN->Execute("insert into login_log_new (teacher_sn,who,no,login_time,ip) values ('$tea_sn','$who','$num','$t','$REMOTE_ADDR')");
	}
}

function set_encrypt() {
	$seed_arr = array_merge(range("0","9"),range("A","Z"));
	$seed_arr = array_merge($seed_arr,range("a","z"));
	mt_srand((double)microtime()*1000000);
	for($i=0;$i<24;$i++) $p .= $seed_arr[mt_rand(0,61)];
	$_SESSION['ToBeSign'] = $p;
}

//2013.09.27
function login_chk_from_ldap($who) {
	global $CONN,$LDAP;

	$log_id=$_POST['log_id'];
	$log_pass=$_POST['log_pass'];
		
	$server_ip = $LDAP['server_ip'];								//LDAP SERVER IP
	$server_port = $LDAP['server_port'];						//LDAP SERVER PORT
	$bind_dn = $LDAP['bind_dn'];										//LDAP 帳號要 bind 的 DN
	$dn=$log_id."@".$bind_dn;												//Windows AD bind 格式
	//$bind_dn_x=explode(".",$bind_dn);
	//$rdn="CN=".$log_id;
	//foreach($bind_dn_x as $v) { $rdn.=",DC=".$v; }	//Linux OpenLDAP bind 格式					
	
	//進行連線
	$ldap_conn=ldap_connect($server_ip,$server_port) or die("SORRY~~Could not cnnect to LDAP SERVER!!");
	//以下兩行務必加上，否則 Windows AD 無法在不指定 OU 下，作搜尋的動作
 	ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
 	ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);


  //AD方式
	$ldapbind=ldap_bind($ldap_conn,$dn,$log_pass);
	
	//OpenLDAP 格式 , 不加 ou
	if (!$ldapbind) {
		$rdn = $LDAP['base_uid']."=$log_id,".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn,$log_pass);	
	}

	//OpenLDAP 格式 , 加上教師 ou
	if (!$ldapbind and $LDAP['teacher_ou']!='') {
		$rdn1 = $LDAP['base_uid']."=$log_id,ou=".$LDAP['teacher_ou'].",".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn1,$log_pass);	
	}

	//OpenLDAP 格式 , 加上學生 ou
	if (!$ldapbind and $LDAP['stud_ou']!='') {
		$rdn2 = $LDAP['base_uid']."=$log_id,ou=".$LDAP['stud_ou'].",".$LDAP['base_dn'];
		$ldapbind=ldap_bind($ldap_conn,$rdn2,$log_pass);	
	}
	
	if ($ldapbind and $log_pass<>"") {
		ldap_unbind($ldap_conn);
	//登入成功 , 只接受學生及家長
	  $chk_ok=0;
		switch ($who) {
    	case '教師':
    		//檢查教師資料庫有無此人
        $sql_select = "select teacher_sn from teacher_base where teach_condition = 0 and teach_id='$log_id' and teach_id<>''";
				$res=$CONN->Execute($sql_select);
				if ($res->RecordCount()==1) {
				  $teacher_sn=$res->fields['teacher_sn'];
				  //回寫密碼
				  $sql="update teacher_base set login_pass ='".pass_operate($log_pass)."' where teacher_sn='$teacher_sn'";
				  $CONN->Execute($sql) or die('SQL發生錯誤! sql='.$sql);				  
				  $chk_ok=1;
				}
        break;
    	case '學生':
    		//檢查學生資料庫有無此人
    		$sql_select = "select student_sn,stud_name, email_pass from stud_base where stud_id='$log_id' and stud_study_cond in (0,15) and stud_id <>''";
				$res=$CONN->Execute($sql_select);
				if ($res->RecordCount()==1) {
				  $student_sn=$res->fields['student_sn'];
				  //回寫密碼
				  $sql="update stud_base set email_pass ='".$log_pass."' where student_sn='$student_sn'";
				  $CONN->Execute($sql) or die('SQL發生錯誤! sql='.$sql);				  
				  $chk_ok=1;
				}
        break;
		} // end switch
		if ($chk_ok) {
			return 1;
		} else {
			return 0;
		}
	} else {
	//登入失敗
		return 0;
	}

}
?>
