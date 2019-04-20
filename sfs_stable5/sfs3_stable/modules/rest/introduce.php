<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check(); 
//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//讀取目前操作的老師有沒有管理權 , 搭配 module-cfg.php 裡的設定
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;

$URL='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<div>
 <p style="font-size:12pt">SFS RESTful API 服務說明：</p>
 <ol>
     <li>你必須在模組變數中設定允許呼叫本 API 的 IP，若未設定，表示不允許呼叫。</li>
     <li>你必須在模組變數中設定 S_ID 及 S_PWD，當 client 端呼叫時，將這兩個變數放在 Header 中，例如：<br>
         當 S_IP=api ，S_PWD=12356時，以 php 的 curl 呼叫時，可以這麼設定 <br>
         <p style="border-color: #000000;border-style: solid;border-width: thin;padding: 10px">curl_setopt($ch, CURLOPT_HTTPHEADER, array('S_ID:api','S_PWD:123456'));</p>
     </li>
     <li>呼叫網址：<?php //= $SFS_PATH_HTML."modules/rest/api.php"; ?>
         <?= substr($URL,0,strlen($URL)-13)."api.php" ?>

     </li>
     <li> sfs3 的 http 根網址：<?= $SFS_PATH_HTML ?></li>
     <li> sfs3 主機 IP 位址：<?php echo $_SERVER['SERVER_ADDR'];?></li>
 </ol>
</div>

<br>
呼叫功能說明：
<table border="1" style="font-size:10pt;border-collapse: collapse" width="100%">
    <tr style="background-color: #00aa00">
        <td>功能</td>
        <td>呼叫方法</td>
        <td>參數</td>
        <td>資料類型</td>
    </tr>
    <tr>
        <td>取得學年學期</td>
        <td>POST、GET</td>
        <td>
            search=year_seme (必要)<br>
        </td>
        <td>陣列<br>
            如: $data['1051']='105學年第1學期'
        </td>
    </tr>
    <tr>
        <td>取得目前學年及學期</td>
        <td>POST、GET</td>
        <td>
            search=curr_year_seme (必要)<br>
        </td>
        <td>陣列, 如:<br>
            $data['curr_year']=105<br>
            $data['curr_seme']=1
        </td>
    </tr>
    <tr>
        <td>取得本學期的班級資料</td>
        <td>POST、GET</td>
        <td>
            search=classroom (必要)<br>
            c_year=int (限定年級, 非必要,國中為 7-9,國小為1-6)<br>
            curr_year=int (限定學年, 非必要, 如 105)<br>
            curr_seme=int (限定學期, 非必要, 1 或 2)
        </td>
        <td>陣列, 如:<br>
            $data['104_2_07_01'] = 一年1班
        </td>
    </tr>
    <tr>
        <td>取得班級課表</td>
        <td>POST</td>
        <td>
            search=class_table (必要)<br>
            class_id=string (必要, 如: 104_2_07_01)
        </td>
        <td>陣列:<br>
            $data[$key]['subject']=科目<br>
            $data[$key]['teacher']=教師<br>
            $data[$key]['co_teacher']=協同教師<br>
            $data[$key]['room']=上課地點<br>
        </td>
    </tr>
    <tr>
        <td>取得教師課表</td>
        <td>POST</td>
        <td>
            search=teacher_table (必要)<br>
            teacher_sn=int (必要)
        </td>
        <td>陣列:<br>
            $data[$key]['subject']=科目<br>
            $data[$key]['class_name']=班級<br>
            $data[$key]['room']=上課地點<br>
        </td>
    </tr>
    <tr>
        <td>取得班級名單</td>
        <td>POST</td>
        <td>
            search=class_students_list (必要)<br>
            class_id=string (必要, 如: 104_2_07_01)
        </td>
        <td>陣列:<br>
            $data[$key]['student_sn']=流水號<br>
            $data[$key]['stud_id']=學號<br>
            $data[$key]['stud_name']=姓名<br>
            $data[$key]['stud_class']=班級<br>
            $data[$key]['stud_sex']=性別 (1男,2女)<br>
            $data[$key]['stud_sitenum']=座號
        </td>
    </tr>
    <tr>
        <td>取得在職教師名單</td>
        <td>POST</td>
        <td>
            search=teachers_list (必要)<br>
            key=teacher_sn (以teacher_sn當作陣列的 key ,省略自動以流水號作為 key)<br>
        </td>
        <td>陣列:<br>
            $data[$key]['teacher_sn']=流水號<br>
            $data[$key]['teacher_id']=帳號<br>
            $data[$key]['teacher_name']=姓名<br>
            $data[$key]['teacher_sex']=性別 (1男,2女)<br>
            $data[$key]['room_name']=所在處室<br>
            $data[$key]['title_name']=職稱
        </td>
    </tr>
    <tr>
        <td>取得學生人數統計</td>
        <td>POST</td>
        <td>
            search=stud_status (必要)<br>
            year=int (學年, 非必要 , 省略時默認為當學年)<br>
            semester=int (學期, 非必要 , 省略時默認為當學期)

        </td>
        <td>陣列:<br>
            $data[$key][$class_id]=班級名稱<br>
            $data[$key]['boy']=男生數<br>
            $data[$key]['girl']=女生數<br>
            $data[$key]['stud_all']=總數
        </td>
    </tr>
    <!-- 2017.04.30 增加 -->
    <tr>
        <td>取得教師職稱陣列</td>
        <td>POST、GET</td>
        <td>
            search=teacher_title (必要)<br>
        </td>
        <td>陣列:<br>
            $data[$key]['title_name']=職稱<br>
            $data[$key]['title_short_name']=簡稱<br>
            $data[$key]['title_kind']=職稱類別<br>
            $data[$key]['room_name']=處室
        </td>
    </tr>
    <!-- 2017.06.09 增加 -->
    <tr>
        <td>取得處室資料</td>
        <td>POST、GET</td>
        <td>
            search=room_office (必要)<br>
        </td>
        <td>陣列:<br>
            $data[$key]['room_id']=流水號<br>
            $data[$key]['room_name']=處室名稱
        </td>
    </tr>
</table>

 
<?php
//  --程式檔尾
foot();
?>