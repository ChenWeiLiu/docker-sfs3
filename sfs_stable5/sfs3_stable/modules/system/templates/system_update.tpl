{{* $Id: system_update.tpl 8598 2015-11-19 06:42:56Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
<!--
function chg(a) {
	var b,i;
	for(i=0;i<24;i++) {
		if (a!=i) {
			b="tem"+i;
			document.getElementById(b).checked=false;
		}
	}
}
function copyToClipboard(txt) {
     if(window.clipboardData) {
             window.clipboardData.clearData();
             window.clipboardData.setData("Text", txt);
     } else if(navigator.userAgent.indexOf("Opera") != -1) {
          window.location = txt;
     } else if (window.netscape) {
          try {
               netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
          } catch (e) {
               alert("被瀏覽器拒絕！\n請在瀏覽器地址欄輸入'about:config'後按下Enter鍵\n然後將'signed.applets.codebase_principal_support'設置為'true'");
          }
          var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
          if (!clip)
               return;
          var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
          if (!trans)
               return;
          trans.addDataFlavor('text/unicode');
          var str = new Object();
          var len = new Object();
          var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
          var copytext = txt;
          str.data = copytext;
          trans.setTransferData("text/unicode",str,copytext.length*2);
          var clipid = Components.interfaces.nsIClipboard;
          if (!clip)
               return false;
          clip.setData(trans,null,clipid.kGlobalClipboard);
     }
     alert('已複製完成');
}
-->
</script>

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="1">
<form name="log" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
<td bgcolor="#FFFFFF">
<table  cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td>更新時間</td><td>常態更新</td><td>臨時更新</td><td style="width:1px;"></td><td>更新時間</td><td>常態更新</td><td>臨時更新</td>
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr bgcolor="white" style="text-align:center;">
<td>{{$v}}:00</td><td><input type="radio" name="upsch" value="{{$v}}" {{if $smarty.post.upsch==$v}}checked{{/if}}></td><td><input type="checkbox" name="tem[{{$v}}]" id="tem{{$i}}" OnClick="chg({{$i}});" {{if $smarty.post.uptemp==$v}}checked{{/if}}></td>
<td></td>
{{assign var=vv value=$v+12}}
<td>{{$vv}}:00</td><td><input type="radio" name="upsch" value="{{$vv}}" {{if $smarty.post.upsch==$vv}}checked{{/if}}></td><td><input type="checkbox" name="tem[{{$vv}}]" id="tem{{$i+12}}" OnClick="chg({{$i+12}});" {{if $smarty.post.uptemp==$vv}}checked{{/if}}></td>
</tr>
{{/foreach}}
</table>
<input type="submit" value="確定儲存">
</td><td style="vertical-align:top;width:50%;">
{{if $crontime}}
<span style="font-size:10pt;color:blue;">※定期排程最後執行時間：{{$crontime}}</span><br>
<span style="font-size:10pt;color:blue;">※欲重現本主機更新的script，請進入系統刪除{{$cron}}</span><br>
{{else}}
&nbsp; <textarea id="sct" style="font-size:8pt;color:grey;" cols="64" rows="10">
#!/usr/bin/php
&lt;?php
//1.1版
echo "#開始更新 sfs3......\n";

//sfs3 安裝目錄(請依需要修改)
$SFS_INSTALL_PATH="{{$SFS_PATH}}";

//sfs3 解壓暫存目錄(請依需要修改)
$SFS_TEMP_DIR="/tmp/sfs3_stable";

//sfs3 下載網址(勿修改)
$SFS_TAR_URL="http://sfscvs.tc.edu.tw/";

//記錄自動排程執行時間
$fp=fopen($SFS_INSTALL_PATH."/data/system/cron","w");
fputs($fp,date("Y-m-d H:i:s"));
fclose($fp);

//取得由網頁設定的變數值
$v_arr=array();
$v_arr['SCHEDULE']="";
$v_arr['TEMPORARY']="";
if (file_exists($SFS_INSTALL_PATH."/data/system/update")) {
	$fp=fopen($SFS_INSTALL_PATH."/data/system/update","r");
	while(!feof($fp)) {
		$temp_arr=array();
		$temp_arr=explode("=",fgets($fp,1024));
		if (count($temp_arr)==2) $v_arr[$temp_arr[0]]=sprintf("%02d",intval($temp_arr[1]));
	}
	fclose($fp);
}

//如果沒有設定定期更新時間, 則定期更新時間設定為早上六點
if ($v_arr['SCHEDULE']=="") $v_arr['SCHEDULE']="06";

//取得現在時間的小時別
$hour=date("H");

//若符合更新時間則進行更新
if ($v_arr['SCHEDULE']==$hour || $v_arr['TEMPORARY']==$hour || $argv[1]=="now") {

	//判斷PHP版本別
	if ( !function_exists('version_compare') || version_compare( phpversion(), '5', '<' ) )
		$SFS_TAR_FILE="sfs_stable.tar.gz";
	else
		$SFS_TAR_FILE="sfs_stable5.tar.gz";

	//判斷暫存目錄是否已存在
	if (is_dir($SFS_TEMP_DIR)) {
		exec("rm -rf ".$SFS_TEMP_DIR);
	}

	//判斷舊有程式碼是否已存在
	if (file_exists("/tmp/".$SFS_TAR_FILE)) {
		exec("rm -f /tmp/".$SFS_TAR_FILE);
	}

	//判斷sfs3是否安裝
	if (!is_dir($SFS_INSTALL_PATH)) {
		echo "Oh! Error! .... Directory *** sfs3 *** not exists!\n";
		echo "Please install sfs3 first!\n";
		exit;
	}

	//下載、解壓與複製主程式
	echo "#下載主程式......\n";
	exec("wget -q ".$SFS_TAR_URL.$SFS_TAR_FILE." --directory-prefix=/tmp");
	echo "#主程式解壓縮......\n";
	exec("tar zxf /tmp/".$SFS_TAR_FILE." -C /tmp");
	echo "#複製主程式......\n";
	exec("cp -a ".$SFS_TEMP_DIR."/* ".$SFS_INSTALL_PATH);

	//顯示更新版本號
	include $SFS_INSTALL_PATH."/sfs-release.php";
	echo "#更新至 ".$SFS_BUILD_DATE."\n";

	//回寫設定檔
	$fp=fopen($SFS_INSTALL_PATH."/data/system/update","w");
	fputs($fp,"SCHEDULE=".$v_arr['SCHEDULE']);
	fclose($fp);
}
?&gt;
</textarea>
<input type="button" onclick="copyToClipboard(sct.value);" value="將 Script 複製到剪貼簿"><br>
{{/if}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>本模組需與 Cron Table 設定搭配方能正常運作。</li>
	<li>更新時間與伺服器時間設定有關，建議伺服器做好自動校時。</li>
{{if !$crontime}}
	<li style="color:red;">請將上列的 script 複製後於伺服器 /root 目錄下貼入 upsfs.php 檔。</li>
	<li style="color:red;">將 upsfs.php 權限修改為可執行。</li>
	<li style="color:red;">在 cron table 中設定每小時定時執行 upsfs.php 。</li>
{{/if}}
	<li>確切更新時間與 crontab 設定中的「分」有關，亦即 crontab 的設定為15分，本頁面勾選04:00，則更新時間為04:15。</li>
	<li>本系統主程式由版本控制伺服器每三小時產生新壓縮檔，所以系統版本不一定會因更新時間而有所不同，例如：同一天00:30、01:30、02:30所更新到的系統版號會是同一版號。</li>
	<li>建議「常態更新」不要時常更換更新時間，「臨時更新」則是臨時需要更新版本時才進行設定。</li>
	</ol>
</td></tr>
</table>
</td></tr></table>
</td>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
