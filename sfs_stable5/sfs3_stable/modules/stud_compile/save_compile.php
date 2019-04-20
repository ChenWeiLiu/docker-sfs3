<?php
	// $Id: save_compile.php 7769 2013-11-15 06:26:15Z smallduh $
	require "config.php";
    //使用者認證
    sfs_check();


	// 不需要 register_globals
    /*
    if (!ini_get('register_globals')) {
		ini_set("magic_quotes_runtime", 0);
		extract( $_POST );
		extract( $_GET );
		extract( $_SERVER );
	}
    */
    $year_name=$_POST['year_name'];
    $many_class=$_POST['many_class'];
	$bs=$_POST['bs'];
    $class=$_POST['class'];
    $Submit4=$_POST['Submit4'];
    $NEW=$_GET['NEW'];
    $NEW2=$_GET['NEW2'];
    $new_year_name=$_GET['new_year_name'];
    $select_stud=$_POST['select_stud'];
    $r2e=$_POST['r2e'];
    $e2r=$_POST['e2r'];

    //程式檔頭
    head("調整編班");

    //設定主網頁顯示區的背景顏色
    echo "
    <table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
    <tr>
    <td bgcolor='#FFFFFF'>";
    //網頁內容請置於此處


if($e2r==">>"){
//echo $NEW."-->".$NEW2."-->".$new_year_name."<br>";
    $new_year_name=$_POST['new_year_name'];
    $NEW=$_POST['NEW'];
    $NEW2=$_POST['NEW2'];
    if(!$select_stud) $select_stud=array();
    reset($select_stud);
    while(list($key,$value) = each($select_stud)) {
       //echo "$key: $value<br>";
       $sql="UPDATE stud_compile set new_class='$NEW2' where student_sn=$value ";
       $CONN->Execute($sql) or die($sql);
    }
}

if($r2e=="<<"){
//echo $NEW2."-->".$NEW."-->".$new_year_name;
    $new_year_name=$_POST['new_year_name'];
    $NEW=$_POST['NEW'];
    $NEW2=$_POST['NEW2'];
    if(!$select_stud) $select_stud=array();
    reset($select_stud);
    while(list($key,$value) = each($select_stud)) {
       //echo "$key: $value<br>";
       $sql="UPDATE stud_compile set new_class='$NEW' where student_sn=$value ";
       $CONN->Execute($sql) or die($sql);
    }
}

//寫入編班資料表stud_compile table
    //升上去是幾年級
if($year_name && $many_class){
    if($new_year_name=="") $new_year_name=$year_name;
    $update_time=date ("Y-m-d H:i:s");
    for($k=0;$k<$many_class;$k++){
        $kk=$k+1;
        //幾班，座號未定
        $ben=sprintf("%02d",$kk);
        for($m=0;$m<count($class[$k]);$m++){
            //查詢編班資料表該生是否存在
            $Class=$class[$k][$m];
            $Class = explode ("_", $Class);
            $sql_c="select * from stud_compile where student_sn='$Class[0]'";
            $rs_c=$CONN->Execute($sql_c) or die($sql_c) ;
            $student_sn=$rs_c->fields["student_sn"];
            //查訊該生的目前就讀的班級
            $sql_d="select * from stud_base where student_sn='$Class[0]'";
            $rs_d=$CONN->Execute($sql_d) or die($sql_d) ;
            $stud_birthday=$rs_d->fields["stud_birthday"];
            $old_class=$rs_d->fields["curr_class_num"];
            $sex=$rs_d->fields["stud_sex"];
            $new_class=$new_year_name.$ben;
            //若有的話則更新
            if($student_sn){
                $sql="UPDATE stud_compile set sort='$Class[1]' , old_class='$old_class' , new_class='$new_class' , sex='$sex' , stud_birthday='$stud_birthday' , update_time='$update_time',bs='$bs' where student_sn='$Class[0]' ";
                $CONN->Execute($sql) ;
            }
            //若無的話則新增
            else{
                $sql="INSERT INTO stud_compile(student_sn,sort,old_class,new_class,sex,stud_birthday,update_time,bs) values('$Class[0]','$Class[1]','$old_class','$new_class','$sex','$stud_birthday','$update_time','$bs')";
                $CONN->Execute($sql) or die($sql);
            }
        }
        //首次寫入之後，依照性別，生日給定座號
        $sql="select * from stud_compile where new_class='$new_class' order by sex , stud_birthday ";
        $rs=$CONN->Execute($sql) or die($sql);
        $i=0;
        while (!$rs->EOF) {
            $student_sn[$i]=$rs->fields["student_sn"];
            $site_num[$i]=$i+1;
            $CONN->Execute("UPDATE stud_compile set site_num='$site_num[$i]' where student_sn=$student_sn[$i] ");
            $i++;
            $rs->MoveNext();
        }
    }
}
//---------------------------------------------------

//echo"<table bgcolor='#ffffff'><tr><td valign='top'>";
    $B=school_class_info();
    //秀出各班的名單，並開放調整
    $sql="select * from stud_compile where new_class like '$new_year_name%' order by new_class,stud_birthday";
    $rs=$CONN->Execute($sql) or die($sql);
    $option="<option value=''>選擇班級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $NEW_class[$i]=$rs->fields["new_class"];
        $i++;
        $rs->MoveNext();
    }
    $NEW_class=deldup($NEW_class);
    for($i=0;$i<count($NEW_class);$i++){
        $selected=($NEW==$NEW_class[$i])?"selected":"";
        $c=substr($NEW_class[$i],-2,2);
        settype($c,"integer");
        $B_name=$B[$new_year_name][$c];
        if($B_name=="") $B_name=$c;
        $c_name[$i]=$new_year_name."年".$B_name."班";
        $option.="<option value='$NEW_class[$i]' $selected>".$c_name[$i]."</option>\n";
    }
    //echo $NEW;
    $col_name="NEW";
    $id=$NEW;
    $select_new_class_name="
    <form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
        <input type='hidden' name='new_year_name' value='$new_year_name'>
        <select name='$col_name' onChange='jumpMenu1()'>
            $option
        </select>
    </form>";

    //echo $select_new_class_name;
    $sql="select * from stud_compile where new_class='$NEW' order by sex , stud_birthday ";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    while (!$rs->EOF) {
        $student_sn[$i]=$rs->fields["student_sn"];
		$bs[$i]=$rs->fields['bs'];
        $sort[$i]=$rs->fields["sort"];
		if($bs[$i]=='small') $sort[$i]=sprintf("%d-%d",substr("$sort[$i]",-2),substr("$sort[$i]",0,-2));
        $stud_name[$i]=student_sn_to_stud_name($student_sn[$i]);
        $sex[$i]=$rs->fields["sex"];
        $stud_birthday[$i]=$rs->fields["stud_birthday"];
        $site_num[$i]=$i+1;
        $CONN->Execute("UPDATE stud_compile set site_num='$site_num[$i]' where student_sn=$student_sn[$i] ");
        //echo $student_sn[$i]."-->".$stud_name[$i]."-->".$sex[$i]."-->".$stud_birthday[$i]."<br>";
        if($sex[$i]==1) $color="blue";
        else $color="magenta";
        $stud_table1[$i]="<tr bgcolor='#ffffff'  valign=top><td><input type='checkbox' name='select_stud[$i]' value='$student_sn[$i]'></td><td align=center>$site_num[$i]</td><td><font color=$color>$stud_name[$i]</font>($sort[$i])</td></tr>";

        //echo $stud_table1[$i];
        $i++;
        $rs->MoveNext();
    }


    $B2=school_class_info();
    //秀出各班的名單，並開放調整
    $sql="select * from stud_compile where new_class like '$new_year_name%' order by new_class,stud_birthday";
    $rs=$CONN->Execute($sql) or die($sql);
    $option2="<option value=''>選擇班級</option>\n";
    $i=0;
    while (!$rs->EOF) {
        $NEW2_class[$i]=$rs->fields["new_class"];
        $i++;
        $rs->MoveNext();
    }
    $NEW2_class=deldup($NEW2_class);
    for($i=0;$i<count($NEW2_class);$i++){

        $selected=($NEW2==$NEW2_class[$i])?"selected":"";
        $c2=substr($NEW2_class[$i],-2,2);
        settype($c2,"integer");
        $B2_name=$B2[$new_year_name][$c2];
        if($B2_name=="") $B2_name=$c2;
        $c2_name[$i]=$new_year_name."年".$B2_name."班";
        $option2.="<option value='$NEW2_class[$i]' $selected>".$c2_name[$i]."</option>\n";
    }
    //echo $NEW;
    $col_name2="NEW2";
    $id2=$NEW2;
    $select_new2_class_name="
    <form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
        <input type='hidden' name='new_year_name' value='$new_year_name'>
        <select name='$col_name2' onChange='jumpMenu3()'>
            $option2
        </select>
    </form>";

    //echo $select_new2_class_name;
    $sql="select * from stud_compile where new_class='$NEW2' order by sex , stud_birthday ";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    while (!$rs->EOF) {
        $student_sn[$i]=$rs->fields["student_sn"];
 		$bs[$i]=$rs->fields['bs'];
        $sort[$i]=$rs->fields["sort"];
		if($bs[$i]=='small') $sort[$i]=sprintf("%d-%d",substr("$sort[$i]",-2),substr("$sort[$i]",0,-2));
        $stud_name[$i]=student_sn_to_stud_name($student_sn[$i]);
        $sex[$i]=$rs->fields["sex"];
        $stud_birthday[$i]=$rs->fields["stud_birthday"];
        $site_num[$i]=$i+1;
        $CONN->Execute("UPDATE stud_compile set site_num='$site_num[$i]' where student_sn=$student_sn[$i] ");
        //echo $student_sn[$i]."-->".$stud_name[$i]."-->".$sex[$i]."-->".$stud_birthday[$i]."<br>";
        if($sex[$i]==1) $color="blue";
        else $color="magenta";
        $stud_table2[$i]="<tr bgcolor=#ffffff  valign=top><td><input type='checkbox' name='select_stud[$i]' value='$student_sn[$i]'></td><td align=center>$site_num[$i]</td><td><font color=$color>$stud_name[$i]</font>($sort[$i])</td></tr>";
        //echo $stud_table2[$i];
        $i++;
        $rs->MoveNext();
    }

echo"<table  valign=top>
        <tr  valign=top>
            <td>
                <table cellspacing=1 cellpadding=2 border=0 bgcolor=#27A208 valign=top>
                    <tr  valign=top><td colspan=3  valign=top>$select_new_class_name</td></tr>";
                    if($NEW) echo "<tr bgcolor=#96FF73 align=center  valign=top><td></td><td>座號</td><td>姓名(排名)</td></tr>";
                    else echo "<tr><td></td><td></td><td></td></tr>";
                    echo "<form name='form5' method='post' action='{$_SERVER['PHP_SELF']}'>";
                    for($i=0;$i<count($stud_table1);$i++){
                        echo $stud_table1[$i];
                    }
                    echo "<input type='hidden' name='NEW' value=$NEW>";
                    echo "<input type='hidden' name='NEW2' value=$NEW2>";
                    echo "<input type='hidden' name='new_year_name' value=$new_year_name>";
            echo"<tr><td colspan=3 align=right><input type='submit' name='e2r' value='>>'></td></tr></form></table></td>
            <td>
            &nbsp;&nbsp;&nbsp;&nbsp;

            </td>
            <td>
                <table cellspacing=1 cellpadding=2 border=0 bgcolor=#27A208 valign=top>
                    <tr valign=top><td colspan=3  valign=top>$select_new2_class_name</td></tr>";
                    if($NEW2) echo "<tr bgcolor=#96FF73 align=center  valign=top><td></td><td>座號</td><td>姓名(排名)</td></tr>";
                    else echo "<tr><td></td><td></td><td></td></tr>";
                    echo"<form name='form6' method='post' action='{$_SERVER['PHP_SELF']}'>";
                    for($i=0;$i<count($stud_table2);$i++){
                        echo $stud_table2[$i];
                    }
                    echo "<input type='hidden' name='NEW' value=$NEW>";
                    echo "<input type='hidden' name='NEW2' value=$NEW2>";
                    echo "<input type='hidden' name='new_year_name' value=$new_year_name>";
            echo"<tr><td colspan=3><input type='submit' name='r2e' value='<<'></td></tr></form></table></td>
        </tr>
      </table>";

    //結束主網頁顯示區
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    //程式檔尾
    foot();


?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu1(){
	var str, classstr ;
 if (document.form1.NEW.options[document.form1.NEW.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?NEW=" + document.form1.NEW.options[document.form1.NEW.selectedIndex].value + "&NEW2=" + document.form3.NEW2.value + "&new_year_name=" + document.form1.new_year_name.value;
	}
}

function jumpMenu3(){
	var str, classstr ;
 if (document.form3.NEW2.options[document.form3.NEW2.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?NEW2=" + document.form3.NEW2.options[document.form3.NEW2.selectedIndex].value + "&NEW=" + document.form1.NEW.value  + "&new_year_name=" + document.form3.new_year_name.value;
	}
}



//  End -->
</script>
