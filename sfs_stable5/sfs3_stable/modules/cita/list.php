<?php
include "config.php";
// 不需要 register_globals
if (!ini_get('register_globals')) {
    ini_set("magic_quotes_runtime", 0);
    extract($_POST);
    extract($_GET);
    extract($_SERVER);
}

//取得各類別名稱
$l_kind = "｜<a href=$PHP_SELF>最新榮譽</a>｜";
$num = count($grada);
for ($i = 0; $i < $num; $i++) {
    $l_kind.="<a href=$PHP_SELF?gra=$i>$grada[$i]</a>｜";
}

$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($gra <> "") {
    $stmt = $mysqliconn->prepare('select count(*) as cc  from cita_kind where grada = ?');
    $stmt->bind_param('s', $gra);
} else {
    $stmt = $mysqliconn->prepare('select count(*) as cc  from cita_kind');
}

$stmt->execute();

$stmt->bind_result($tol_num);
$stmt->fetch();
$stmt->close();
if ($topage != "")
    $page = $topage;
$page_count = 15;

if ($tol_num % $page_count > 0)
    $tolpage = intval($tol_num / $page_count) + 1;
else
    $tolpage = intval($tol_num / $page_count);

echo "
<table align=center width='90%'>
<form name='bform' method='post' action=$PHP_SELF>
<tr><td align=center ><font size=5 color=red face=標楷體>學生競賽榮譽榜</font>　　　　
<a href='citaList.php' target='_blank'>管理</a>　<a href='search.php'>搜尋</a>　第";
echo " <select name=\"topage\"  onchange=\"document.bform.submit()\">";
for ($i = 0; $i < $tolpage; $i++)
    if ($page == $i)
        echo sprintf(" <option value=\"%d\" selected>%2d</option>", $i, $i + 1);
    else
        echo sprintf(" <option value=\"%d\" >%2d</option>", $i, $i + 1);

echo "</select>頁 /共 $tolpage 頁";


echo "</td></tr><input type='hidden' name='gra' value=$gra></form>
             <tr><td>$l_kind</td></tr></table>";

echo "<table align=center width='90%' border='1' cellspacing='0' cellpadding='4' bgcolor='#CCFFFF' bordercolor='#33CCFF'>
  <tr bgcolor='#66CCFF'>
    <td >日期</td>
    <td >名稱　　　　($grada[$gra])</td>
    <td >類別</td>
  </tr>";
if ($gra <> "") {
    $stmt = $mysqliconn->prepare('select id,beg_date,end_date,doc,helper,grada from cita_kind where grada = ? order by beg_date DESC limit ' . $page * $page_count . ',' . $page_count);
    $stmt->bind_param('s', $gra);
} else {
    $stmt = $mysqliconn->prepare('select id,beg_date,end_date,doc,helper,grada from cita_kind order by beg_date DESC limit ' . $page * $page_count . ',' . $page_count);
}

$stmt->execute();
$stmt->bind_result($id, $beg_date, $end_date, $doc, $helper, $gra);


while ($stmt->fetch()) {


    echo "<tr>\n";
    echo " <td >$beg_date</td>";

    echo "
    <td><a href='view.php?id=$id'>$doc</a></td>
      <td >" . $grada[$gra] . "</td>
  </tr>";
}
?>

</table>

