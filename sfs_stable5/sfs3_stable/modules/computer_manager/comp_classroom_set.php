<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//�q�X����
head("�����޲z - �]�w�q���Ы�");

$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "��p , �z�S���L�޲z�v��!";
 exit();
}

$comproom=$_POST['comproom'];

//�x�s
if ($_POST['act']=='save') {
    //
    echo "�x�s��".substr($comproom,-1)."�q���ЫǮy�� <br><br>";
    //����
    $telnet = new PHPTelnet();

    echo "�n�J������...(���U�ӥi��|����[�ɶ��A�Э@�ߵ��ԡA�n���X�{�u�����v�e���A�~�������Τ����e��)<br><br>";
    ob_flush();
    flush();
    $result = $telnet->Connect($firewall_ip,$firewall_user,$firewall_pwd);

    if ($result == 0) {
        echo "������n�J���\! <br>";
        ob_flush();
        flush();
        if ($VDOM) {
            echo "�Q�ը�����ҥ� VDOM ...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('config vdom', $result);
            $telnet->DoCommand('edit root', $result);
            echo str_replace("\n","<br>",$result);
            echo "<br>";
            ob_flush();
            flush();
        }
        foreach ($_POST['site_num'] as $net_edit=>$site_num) {
            $net_ip=$_POST['net_ip'][$net_edit];
            if ($net_ip!='') {
                //
                //�ˬd�O�_�������}�]�w
                echo "�ЫǮy��G".$site_num."  �A�q�� IP: ".$net_ip."<br>  �ˬd�����𤤬O�_���� IP address �w�q...<br>";
                ob_flush();
                flush();

                $telnet->DoCommand('show firewall address '.$net_ip, $result);
                // NOTE: $result may contain newlines
                $RES=explode("\n",$result);
                // $RES[0] �|���аe�X�����O
                //���w�q
                if (trim($RES[1])=='config firewall address') {
                    echo "�� IP address �w���w�q<br>";
                    foreach ($RES as $k=>$v) {
                        echo $v."<br>";
                        ob_flush();
                        flush();
                    }


                    //�B�zMySQL���
                    $sql="select * from comp_roomsite where net_edit='$net_edit'";
                    echo $sql."<br>";
                    ob_flush();
                    flush();
                    $res=$CONN->Execute($sql) or die ("SQL error! sql=".$sql);
                    if ($res->RecordCount()) {
                        $net_edit=$res->fields['net_edit'];
                        $query="update comp_roomsite set net_ip='$net_ip',site_num='$site_num' where net_edit='$net_edit'";
                        echo $query."<br>";
                        if (mysql_query($query)) {
                            echo "�w��s���y����.<br>";
                        } else {
                            echo "��s�y���ƥ���.<br>";
                        }

                    } else {
                        $query="insert into comp_roomsite (net_edit,net_ip,site_num) values ('$net_edit','$net_ip','$site_num')";
                        if (mysql_query($query)) {
                            echo "�w�x�s���y����.<br>";
                        } else {
                            echo "�x�s�y���ƥ���. $query <br>";
                        }

                    }

                    ob_flush();
                    flush();

                } else {
                    //�p�G�S��, �i��w�q
                    echo "�|���w�q�A�i��w�q...<br>";
                    $telnet->DoCommand('config firewall address', $result);
                    $telnet->DoCommand('edit '.$net_ip, $result);
                    $telnet->DoCommand('set subnet '.$net_ip." 255.255.255.255", $result);
                    $telnet->DoCommand('next', $result);
                    $telnet->DoCommand('end', $result);
                    $telnet->DoCommand('show firewall address '.$net_ip, $result);
                    $RES=explode("\n",$result);
                    if (trim($RES[1])=='config firewall address') {
                        echo "�]�w����<br>";
                        foreach ($RES as $k=>$v) {
                            echo $v."<br>";
                            ob_flush();
                            flush();
                        }

                        //�B�zMySQL���
                        $sql="select * from comp_roomsite where net_edit='$net_edit'";
                        echo $sql."<br>";
                        ob_flush();
                        flush();
                        $res=$CONN->Execute($sql) or die ("SQL error! sql=".$sql);
                        if ($res->RecordCount()) {
                            $net_edit=$res->fields['net_edit'];
                            $query="update comp_roomsite set net_ip='$net_ip',site_num='$site_num' where net_edit='$net_edit'";
                            if (mysql_query($query)) {
                                echo "�w��s���y����.<br>";
                            } else {
                                echo "��s�y���ƥ���.<br>";
                            }

                        } else {
                            $query="insert into comp_roomsite (net_edit,net_ip,site_num) values ('$net_edit','$net_ip','$site_num')";
                            if (mysql_query($query)) {
                                echo "�w�x�s���y����.<br>";
                            } else {
                                echo "�x�s�y���ƥ���. $query <br>";
                            }

                        }
                        echo "<br>";
                        ob_flush();
                        flush();
                    } else {
                        echo "�]�w����! <br>";
                        foreach ($RES as $k=>$v) {
                            echo $v."<br>";
                            ob_flush();
                            flush();
                        }
                        echo "<br>";
                        ob_flush();
                        flush();
                    }
                }
                echo "<br>";
            } // end if $net_ip

        } // end foreach

        //Ū���Ҧ��q��ip
        $sql="select * from comp_roomsite";
        $res=$CONN->Execute($sql);
        $row=$res->GetRows();
        $addrgrp="\"sfs3_addrgrp_deny_tag\"";
        foreach ($row as $v) {
            $addrgrp.=" \"".$v['net_ip']."\"";
        }
        if ($addrgrp_comp_all!="") {
            echo "�{�b, �N�Ҧ���}�s�J �q���Ы�ip�s�� : $addrgrp_comp_all ��... <br>";
            ob_flush();
            flush();
            $telnet->DoCommand('config firewall addrgrp', $result);
            $telnet->DoCommand('edit "'.$addrgrp_comp_all.'"', $result);
            $telnet->DoCommand('set member '.$addrgrp, $result);
            $telnet->DoCommand('next', $result);
            $telnet->DoCommand('end', $result);
            echo "<span style='color:#FF0000'>����!</span>";
        } else {
            echo "�Ҳ��ܼƥ��]�w�q���Ы�ip�s�ժ��W��, �L�k�b������W�w�q! <br>";
        }
        //���_ telnet �s�u
        $telnet->Disconnect();
        exit();
    } else {
        echo "������n�J����! <br>";
        exit();
    }

 
} // end if save


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 
 <select name="comproom" size="1" onchange="document.myform.act.value='';document.myform.submit()">
  <option value="">�п�ܭn�]�w���q���Ы�...</option>
  <option value="comproom1"<?php if ($comproom=='comproom1') echo " selected";?>>�Ĥ@�q���Ы�</option>
  <option value="comproom2"<?php if ($comproom=='comproom2') echo " selected";?>>�ĤG�q���Ы�</option>
  <option value="comproom3"<?php if ($comproom=='comproom3') echo " selected";?>>�ĤT�q���Ы�</option>
 </select>
<?php
$net_ip='';
$site_num='';
$ipmac='';
if ($comproom!="") {

 $COMP_INT=substr($comproom,-1);
 $COMP[1]=100;
 $COMP[2]=200;
 $COMP[3]=300;
 //Ū���{���]�w
 $query="select * from comp_roomsite where net_edit like '".$COMP_INT."%' and site_num>'0' and net_ip!=''";
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
   	 	$net_ip[$row['net_edit']]=$row['net_ip'];
      $site_num[$row['net_edit']]=$row['site_num'];
      $iflock[$row['net_edit']]=$row['iflock'];
 } // end while


?> 
 <table border="0" width="600">
   <tr><td align="center" style="color:#0000FF">��<?php echo $COMP_INT;?>�q���Ыǰt�m��</td></tr> 
 </table>
 <table width="600" style="border-collapse:collapse" bordercolor="#000000" border="0">
 	<tr>
  	<td width="150" align="center">�s���@-=�q�� IP =-�@</td><td width="150" align="center">�s���@-=�q�� IP =-�@</td><td width="150" align="center">�s���@-=�q�� IP =-�@</td><td width="150" align="center">�s���@-=�q�� IP =-�@</td>
 	</tr>
  <tr>
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=10;$i>0;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=20;$i>10;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=30;$i>20;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=40;$i>30;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
  </tr>
 </table>
  <br>
 <table width="600" border="0" style="border-collapse:collapse" bordercolor="#000000">
 	<tr>
 		<td align="center">
 			<table width="80" border="1" style="border-collapse:collapse" bordercolor="#000000">
 			 <tr><td align="center">�Юv��m</td></tr>
 			</table>
 		</td>
 </tr>
 </table>

<input type="button" value="�x�s" onclick="document.myform.act.value='save';document.myform.submit()">
<br><font color=red size=2>���`�N�I�w�]�w�n�� IP , �ɥi�ण�n�H�N��ʡC</font>�C
<br><font color=red size=2>���p�G�z�����IP�A�Ҧp�G�즳���ϥΡA��Ӥ��ϥΤF�A�{���L�k���z�ۨ����𤤧R���A�z�����ۦ��ʧR���өw�q�C</font>�C
<?php
} // end if comproom
?>

</form>

<?php

//��ܨC�Ӯy�쪺��p
function pc_site ($edit_num) {
 global $net_ip,$site_num,$iflock;
 ?>
 <table border="1" style="border-collapse:collapse" bordercolor="#000000" width="100%">
  <tr>
   <td bgcolor="<?php if ($iflock[$edit_num]==0) { echo "#CCFFCC"; } else { echo "#FFCCCC"; } ?>">
   	<input type="text" name="site_num[<?php echo $edit_num;?>]" value="<?php echo $site_num[$edit_num];?>" size="3">
   	<input type="text" name="net_ip[<?php echo $edit_num;?>]" value="<?php echo $net_ip[$edit_num];?>" size="12">
   </td>
  </tr>
 </table>
 
 <?php
}

?>
<Script Language="JavaScript">
function check_tag(SOURCE,STR) {
	var j=0;
	while (j < document.myform.elements.length)  {
	 if (document.myform.elements[j].name==SOURCE) {
	  if (document.myform.elements[j].checked) {
	   k=1;
	  } else {
	   k=0;
	  }	
	 }
	 	j++;
	}
	
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=k;
    }
    i++;
  }
 } // end function
</Script>