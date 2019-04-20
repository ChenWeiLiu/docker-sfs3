<?php
// $Id: function.php 5310 2009-01-10 07:57:56Z hami $
include_once "config.php";
function check_IE () {
	return strstr(getenv("HTTP_USER_AGENT"), 'MSIE')?true:false;
}

function xml_header () {
}

// ���Ҿ��y XML ��
// INPUT: XML �W�ǼȦs��
// RETURN: ���絲�G�r��
function validate_xml($tmpfile) {
	$phpver=explode(".",phpversion());
	//PHP5�ϥΨ禡
	if ($phpver[0]==5) {
		$dom = new DOMDocument();
		$dom->load($tmpfile);
		$mesg=($dom->validate())?"���ҥ��T�I�i�H�i��פJ�u�@":"��RXML�o�Ϳ��~";
		return $mesg;
	} else {
		//�ˬd�O�_��dtd
		if(check_dtd($tmpfile)){
			//��l�ƭ�R���A���w�s�X�覡��UTF-8
			$xml_parser = xml_parser_create("UTF-8");
			//�}���ɮ�
			if (!($fp = fopen($tmpfile, "r"))) {
				return $mesg.="�L�k�}�� $tmpfile �I<br>";
			}
			//Ū�J�ɮסA�í�RXML
			while ($data = fread($fp, 4096)) {
				//XML���~�ɳB�z
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					//���~����ܪ��T��
					$mesg.=sprintf("XML Error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser));
				}
				if($mesg) return $mesg;
			}
			//dom����
			if (!$dom = xmldocfile($tmpfile)) {
				return $mesg.="��RXML�o�Ϳ��~<br>";
			} else {
				//�i��DTD����
				$tmpfile=EscapeShellCmd($tmpfile);
				exec("xmllint --valid --noout $tmpfile 2>&1" , $err );
				if(is_array($err)) $err_str=implode("",$err);
				if($err_str) 
					return iconv("UTF-8","Big5",$err_str);
				else 
					return "���ҥ��T�I�i�H�i��פJ�u�@";
			} 
			//�B�z�����òM���O����
			xml_parser_free($xml_parser);
		}else{
			return $mesg="��󤤥��t��student_call-2_0.dtd��ơI";
		}
	}
}

// �ˬd�W��XML�ɤ��O�_���X�k����󫬺A�ŧi
// INPUT: XML �W�ǼȦs��
// RETURN: 1 ��, 0 �S��
function check_dtd($tmpfile) {
  $hfile=fopen($tmpfile, "r") or trigger_error("�}�� $tmpfile ���~�A���ˬd $tmpfile �O�_��Ū���v?", E_USER_ERROR);
  while ($data=fgets($hfile, 1024)) {
    $rs=ereg("\<!DOCTYPE .+ SYSTEM \"student_call\-2_0\.dtd\"\>", $data);
    if ($rs) { fclose($hfile); return 1; }
  }
  fclose($hfile);
  return 0;
}

function check_files_list($Path){
global $UPLOAD_URL;
$d = dir($Path);
$html_str="<table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600><TR bgcolor=#B7EBFF><TD width=10%>����</TD><TD width=80%>�إߤ��</TD><TD width=10%>�ʧ@</TD></TR>";
$f=0;
while (false !== ($entry = $d->read())) {
  if($entry!= '.' && $entry!= '..'){
    $f++;
    //path for javascript download
    $downloadpath="\'".$UPLOAD_URL."eduxcachange/".$entry."\'";
    $day_time=substr($entry,-18,4)."�~".substr($entry,-14,2)."��".substr($entry,-12,2)."��".substr($entry,-10,2)."��".substr($entry,-8,2)."��".substr($entry,-6,2)."��";
    $html_str.="<TR><TD>$f</TD><TD>$day_time</TD><td><input type=button value=�U�� onclick=\"go_download($downloadpath)\"></td></TR>";
  } 
}
if($f==0){
    $html_str.="<TR><TD>0</TD><TD>�S���������ɮ�</TD><TD></TD></TR></table>"; 
    $html_str.="<br><font size=\'7\' color=\'blue\'>�Х��I��y����XML�ɡz</a></font>";
}else{
   $html_str.="</table>";
$html_str.="<br><font size=\'5\' color=\'red\'>XML�ɮפ��ݭn�U���A�U���u�O�����˵���ƬO�_���~</font>";
$html_str.="<br><font size=\'7\' color=\'blue\'>�Ъ����e��<a href=\'upload_edu_xml.php\'><�W�ǭ���></a></font>"; 
}  
$d->close();
return $html_str;
}

function exist_file_path($Path){
global $UPLOAD_PATH;
$d = dir($Path);
while (false !== ($entry = $d->read())) {
  if($entry!= '.' && $entry!= '..'){
   $filepath=$UPLOAD_PATH."eduxcachange/".$entry;
  } 
}
$d->close();
return $filepath;
}
?>
