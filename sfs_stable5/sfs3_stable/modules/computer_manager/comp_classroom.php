<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//�q�X����
head("�����޲z - �q���ЫǤW���޲z");

$tool_bar=&make_menu($school_menu_p);
$check_real=$_POST['check_real'];  //����ڸ�Ʈw
//�C�X���
echo $tool_bar;


//Ū��������b�K
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="") {
    echo "�z�|���]�w�����������T, �ХѼҲ��ܼƶi��]�w!";
    exit();
}

$comproom=$_POST['comproom'];


 //�Y���F�x�s
if ($_POST['act']=="save" and $comproom!="") {

    $COMP_INT=substr($comproom,-1);
    $COMP[1]=100;
    $COMP[2]=200;
    $COMP[3]=300;

    echo "\n<div id=\"show_process\" style=\"display:block\">";

    //�B�J
    //1. ����Ҧ����q���ЫǪ��q�� iflock �]�w�g�W,
    //2. ����, ���o�Ҧ� post �L�Ӫ� key , �� iflock ����
    //3. Ū�X�Ҧ��n lock �� ip
    //4. �n�J������, ���Ʈw���Ҧ� iflock=1 �� ipaddress �g�J

    //1. ����Ҧ����q���ЫǪ��q�� iflock �]�w�g�W
    $query="update comp_roomsite set iflock='1' where net_edit like '".$COMP_INT."%' and site_num>'0' and net_ip!=''";
    $CONN->Execute($query) or die ("Error! SQL=".$query);

    //2. ����, ���o�Ҧ� post �L�Ӫ� key , �� iflock ����
    foreach ($_POST['net_ip_mode'] as $k => $v) {
        $sql="update comp_roomsite set iflock='0' where net_edit='" . $k . "'";
       // echo $sql."<br>";
        $CONN->Execute($sql) or die ("Error! SQL=".$sql);
    }

    //3. Ū�X�Ҧ��n lock �� ip
    $sql="select * from comp_roomsite where iflock='1'";
    $res=$CONN->Execute($sql) or die ("Error! SQL=".$sql);
    $row=$res->GetRows();
    $lock_address="\"sfs3_addrgrp_deny_tag\"";
    foreach ($row as $v) {
        $lock_address.=" \"".$v['net_ip']."\"";
    }

    //4. �n�J������, ���Ʈw���Ҧ� iflock=1 �� ip address �g�J
    //telnet����
    $telnet = new PHPTelnet();
    echo "�i�樾����s�u<br>";
    ob_flush();
    flush();
    //�i��s�u
    $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
    //�s�u���\�~��i��
    if ($result == 0) {
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
        //�w����Ӧ�}�s��, �i�� update , ��n�W�ꪺ�s�ռg�J
        //�t�γ]�w�w�� $addrgrp ��}�s�նi��W������ , �]���� $lock_address �g�즹�s��
        echo "<span style='color:#FF0000'><br>���b�g�J������O��, �еy��....<br>(�ɶ��i�঳�I�[, �Э@�ߵ��ݵe�����^�q���Ыǰt�m�ϡA�]�w�~�⦨�\!!!)</span></br>";
        ob_flush();
        flush();
        //�]�w firewall addrgrp ��
        $telnet->DoCommand('config firewall addrgrp', $result);
        $telnet->DoCommand('edit "'.$addrgrp_deny.'"', $result);
        $telnet->DoCommand('set member '.$lock_address, $result);
        $telnet->DoCommand('next', $result);
        $telnet->DoCommand('end', $result);
        //Ū�X�]�w
        if ($check_real) {
            echo "<br>�˴��]�w��...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_deny, $result);
            $RES=explode("\n",$result);
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //���o�w�n����}
                if (substr(trim($v),0,10)=='set member')  {
                    $all_deny_ip=get_all_ip(trim($v));
                    $lock_ips=explode("\n",$all_deny_ip);
                }
            }
        }
        $telnet->Disconnect();
        echo "</div>";
        echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
    } else {
        echo "�L�k�n�J������I</div>";
        exit();
    } // end if result=0

}  elseif ($comproom!="") {
    if ($check_real) {
        echo "\n<div id=\"show_process\" style=\"display:block\">";
        $telnet = new PHPTelnet();
        echo "�i�樾����s�u...<br>";
        ob_flush();
        flush();
        //�i��s�u
        $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
        //�s�u���\�~��i��
        if ($result == 0) {
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
            echo "<br><span style='color:#FF0000'><br>Ū��������]�w�Ȥ��A�ûP��Ʈw�O�����A�еy��....<br>(�q���ƶq�h�ɡA�ɶ��i�঳�I�[�A�е��ݵe�����X�q���Ыǰt�m�ϡC)</span></br>";
            ob_flush();
            flush();
            $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_deny, $result);
            $RES=explode("\n",$result);
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //���o�w�n����}
                if (substr(trim($v),0,10)=='set member')  {
                    $all_deny_ip=get_all_ip(trim($v));
                    $lock_ips=explode("\n",$all_deny_ip);
                }
            }
            $telnet->Disconnect();
            echo "</div>";
            echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
        } else {
            echo "������s�u����!";
            echo "</div>";
            exit();
        }
    }
} // end if $_POST['act']!='') else


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
    <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">

    <select name="comproom" size="1" onchange="document.myform.act.value='';document.myform.submit()">
        <option value="">�п�ܭn�޲z���q���Ы�...</option>
        <option value="comproom1"<?php if ($comproom=='comproom1') echo " selected";?>>�Ĥ@�q���Ы�</option>
        <option value="comproom2"<?php if ($comproom=='comproom2') echo " selected";?>>�ĤG�q���Ы�</option>
        <option value="comproom3"<?php if ($comproom=='comproom3') echo " selected";?>>�ĤT�q���Ы�</option>
    </select>
    <input type="checkbox" name="check_real" value="1"<?php if ($check_real==1) echo " checked";?> onclick="document.myform.submit()">�ߧY�n�J�������˴���ڱ���
  <?php


if ($comproom!="") {

$COMP_INT=substr($comproom,-1);
$COMP[1]=100;
$COMP[2]=200;
$COMP[3]=300;

 //Ū���{���]�w
 $query="select * from comp_roomsite where net_edit like '".$COMP_INT."%' and site_num>0 and net_ip!=''";
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
   	$net_ip[$row['net_edit']]=$row['net_ip'];
    $site_num[$row['net_edit']]=$row['site_num'];
     if ($check_real) {
         //�� �������ڭ��� ���Q���, ��b $lock_ips array ��
         if (in_array($row['net_ip'],$lock_ips)) {
             $iflock[$row['net_edit']]=1;
             $net_ip_color[$row['net_edit']]="#FFCCCC";
         } else {
             $iflock[$row['net_edit']]=0;
             $net_ip_color[$row['net_edit']]="#CCFFCC";
         }

     } else {
         //�Ȩ� MySQL ��Ʈw�n��
         //�}��
         if ($row['iflock']==0) {
             $iflock[$row['net_edit']]=0;
             $net_ip_color[$row['net_edit']]="#CCFFCC";
         } else {
             $iflock[$row['net_edit']]=1;
             $net_ip_color[$row['net_edit']]="#FFCCCC";
         }

     }
 } // end while


 if (count($site_num)==0) {
  echo "<br><font color=red>���ըå��]�m��".$COMP_INT."�q���Ы�</font>";
  exit();
 }


  ?>
 <table border="0" width="720">
   <tr><td align="center" style="color:#0000FF">��<?php echo $COMP_INT;?>�q���Ыǰt�m��</td></tr> 
 </table>
 <table width="720" style="border-collapse:collapse" bordercolor="#000000" border="0">
  <tr>
    <td width="180">
      <table border="0" width="100%">
        <?php
         for ($i=10;$i>0;$i--) {
         ?>
          <tr>
           <td align="center">
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
    <td width="180">
      <table border="0" width="100%">
        <?php
         for ($i=20;$i>10;$i--) {
         ?>
          <tr>
           <td align="center">
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
    <td width="180">
      <table border="0" width="100%">
        <?php
         for ($i=30;$i>20;$i--) {
         ?>
          <tr>
           <td align="center">
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
    <td width="180">
      <table border="0" width="100%">
        <?php
         for ($i=40;$i>30;$i--) {
         ?>
          <tr>
           <td align="center">
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
 <table width="720" border="0" style="border-collapse:collapse" bordercolor="#000000">
 	<tr>
 		<td align="center">
 			<table width="80" border="1" style="border-collapse:collapse" bordercolor="#000000">
 			 <tr><td align="center">�Юv��m</td></tr>
 			</table>
 		</td>
 </tr>
 </table>
  
  
  <br>
  <table width="720" border="0">
   <tr>
     <td width="300">
      <table border="0">
        <tr>
         <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#FFCCCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
        <td style="font-size:9pt">�����~�s�u</td>
        <td>
          <table border="1" style="border-collapse:collapse" width="30" cellpadding="0" cellspacing="0" bordercolor="#000000">
           <tr><td bgcolor="#CCFFCC" width="30">&nbsp;</td></tr>
          </table>
         </td>
         <td style="font-size:9pt">�}��ۥѤW��</td>
         <td style="font-size:9pt">
         </td>
        </tr>
      </table>	
      </td> 

          <td align="right" width="420">
              <input type="button" style="color:#00AA00" onclick="check_tag('tag_it','net_ip_mode',1)" value="�����}��">
              <input type="button" style="color:#AA0000" onclick="check_tag('tag_it','net_ip_mode',0)" value="��������">
              <input type="button" value="�x�s�]�w" onclick="document.myform.act.value='save';document.myform.submit()">
          </td>
      </tr>
  </table>
  <br>
  <font size="2" color=red>���`�N! ���ĥN���\�ӹq���W��(�ե~����)�A�O�o�n�A���U �u�x�s�]�w�v�C</font>
    
   
  
  <?php

} // end if comproom
?>
</form>

<?php

//��ܨC�Ӯy�쪺��p
function pc_site ($edit_num) {
 global $net_ip,$net_ip_color,$lock_ips,$site_num,$iflock;
 if ($site_num[$edit_num]>0) {
 ?>
 	<table border="1" style="border-collapse:collapse" bordercolor="#000000" width="90%" height="28">
  	<tr>
   		<td id="id_<?php echo $edit_num;?>" style="background-color: <?php echo $net_ip_color[$edit_num];?>" height="28">
   			<table border="0" width="100%">
   			  <tr>
   			    <td width="50"><input type="checkbox" name="net_ip_mode[<?php echo $edit_num;?>]" value="<?php echo $net_ip[$edit_num];?>" <?php if ($iflock[$edit_num]==0) echo " checked"; ?>><?php echo $site_num[$edit_num];?></td>
   			    <td width="100" style="font-size:10pt"><?php echo $net_ip[$edit_num];?></td>
   			  </tr>
   			</table>
   		
   		</td>
  	</tr>
 	</table>
 
 <?php
 //�S���ҥΪ��y��, �e�{�L����
 } else {
 	?>
 	<table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC" width="80%" height="28">
  	<tr>
   		<td height="28">&nbsp;&nbsp;</td>
  	</tr>
 	</table>
 <?php
 } // end if site_num>0
}


function get_all_ip($ip_member) {
    // echo "�I�� <br>";
    $all_ip=explode(" ",$ip_member);
    $i=0;
    $save_ip="";
    foreach ($all_ip as $IP) {
        $IP=trim($IP);
        $i++;
        if ($i>3) {
            $save_ip.=substr($IP,1,strlen($IP)-2)."\n";
        }
    }

    return $save_ip;

}  // end function

?>
<Script Language="JavaScript">
function check_tag(SOURCE,STR,MODE) {
	/*
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
	*/
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=MODE;
    }
    i++;
  }
 } // end function


</Script>