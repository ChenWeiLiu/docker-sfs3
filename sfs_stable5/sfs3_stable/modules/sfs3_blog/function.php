<?php
// $Id: function.php 5310 2009-01-10 07:57:56Z hami $

//封面檔案上傳函式
    function cover_upload_file($bh_sn){
		global $CONN,$UPLOAD_PATH;
        //判斷上傳檔案是否存在
        if (!$_FILES['userdata']['tmp_name']) user_error("沒有傳入檔案代碼！請檢查！");
        if (!$_FILES['userdata']['name']) user_error("沒有傳入檔案代碼！請檢查！");
        if (!$_FILES['userdata']['size']) user_error("沒有傳入檔案代碼！請檢查！");
		if (!$bh_sn) user_error("沒有傳入目錄代碼！請檢查！");
		$new_name= $bh_sn.".jpg";
		unlink ($UPLOAD_PATH."blog/cover/$new_name");
        //複製檔案到指定位置
		if(!is_dir($UPLOAD_PATH."blog")) mkdir ($UPLOAD_PATH."blog", 0700);
		if(!is_dir($UPLOAD_PATH."blog/cover")) mkdir ($UPLOAD_PATH."blog/cover", 0700);
        copy($_FILES['userdata']['tmp_name'], $UPLOAD_PATH."blog/cover/$new_name");
		//呼叫縮圖函式
    	ImageCopyResizedTrue($UPLOAD_PATH."blog/cover/$new_name",$UPLOAD_PATH."blog/cover/$new_name",200,150		);
        //移除暫存檔
        unlink ($_FILES['userdata']['tmp_name']);

	}


/*  Convert image size. true color*/
    //$src        來源檔案
    //$dest        目的檔案
    //$maxWidth    縮圖寬度
    //$maxHeight    縮圖高度
    //$quality    JPEG品質
    function ImageCopyResizedTrue($src,$dest,$maxWidth,$maxHeight,$quality=100) {

        //檢查檔案是否存在
        if (file_exists($src)  && isset($dest)) {

            $destInfo  = pathInfo($dest);
            $srcSize   = getImageSize($src); //圖檔大小
            $srcRatio  = $srcSize[0]/$srcSize[1]; // 計算寬/高
            $destRatio = $maxWidth/$maxHeight;
            if ($destRatio > $srcRatio) {
                $destSize[1] = $maxHeight;
                $destSize[0] = $maxHeight*$srcRatio;
            }
            else {
                $destSize[0] = $maxWidth;
                $destSize[1] = $maxWidth/$srcRatio;
            }


            //GIF 檔不支援輸出，因此將GIF轉成JPEG
            if ($destInfo['extension'] != "jpg") {
				echo "您所上傳的圖片副檔名不是jpg，系統已自動轉成jpg圖";
				$dest = substr_replace($dest, 'jpg', -3);
			}

            //建立一個 True Color 的影像
            $destImage = imageCreateTrueColor($destSize[0],$destSize[1]);

            //根據副檔名讀取圖檔
            switch ($srcSize[2]) {
                case 1: $srcImage = imageCreateFromGif($src); break;
                case 2: $srcImage = imageCreateFromJpeg($src); break;
                case 3: $srcImage = imageCreateFromPng($src); break;
                default: return false; break;
            }

            //取樣縮圖
            ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0,$destSize[0],$destSize[1],
                                $srcSize[0],$srcSize[1]);

            //輸出圖檔
            switch ($srcSize[2]) {
                case 1: case 2: imageJpeg($destImage,$dest,$quality); break;
                case 3: imagePng($destImage,$dest); break;
            }
            return true;
        }
        else {
            return false;
        }
    }

	//更新首頁資料
	function update_home($bh_sn){
		global $CONN,$style,$alias,$main,$direction;
		$alias=trim($alias);
		$main=trim($main);
		$direction=trim($direction);
		$sql="update blog_home set  style='$style' , alias='$alias' , main='$main' ,direction='$direction' where bh_sn='$bh_sn' ";
		//echo $sql;
		$CONN->Execute($sql) or trigger_error($sql,256);
	}



	function save_content($bc_sn){
		global $CONN,$content,$content2,$title;
		if(!$bc_sn){
			echo "<html><body>
			<script LANGUAGE=\"JavaScript\">
			alert(\"請先選擇文章類別和標題\");
			</script>
			</body>
			</html>";
		}else{
			$sql="update blog_content set title='$title' , content='$content' ,content2='$content2', dater=now() where bc_sn='$bc_sn' ";
			$CONN->Execute($sql) or trigger_error($sql,256);
		}
	}

	function del_content($bc_sn){
		global $CONN,$UPLOAD_PATH;
		$sql="delete from blog_content where bc_sn='$bc_sn' ";
		$CONN->Execute($sql) or trigger_error($sql,256);
		//順便把檔案刪除
		$handle=opendir($UPLOAD_PATH."blog/content/".$bc_sn);
		while ($file = readdir($handle)) {
			if($file != "." and $file != "..") unlink ($UPLOAD_PATH."blog/content/".$bc_sn."/".$file);
		}
		closedir($handle);
		rmdir ($UPLOAD_PATH."blog/content/".$bc_sn);
	}

	function del_kind($kind_sn){
		global $CONN;
		//檢查本類別是否有文章
		$sql="select count(*) from blog_content where kind_sn='$kind_sn' ";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$counter=$rs->fields[0];
		if($counter=="0"){
			$sql="delete from blog_kind where kind_sn='$kind_sn' ";
			$CONN->Execute($sql) or trigger_error($sql,256);
		}
		else{
			echo "<html><body>
			<script LANGUAGE=\"JavaScript\">
			alert(\"本類別尚有文章存在！\\n禁止刪除\");
			</script>
			</body>
			</html>";
		}
	}

	function blog_error($mesg){
		echo $mesg."<br><button onclick=\"window.close()\">關閉</button>";
		exit;
	}


	function blog_quota($bh_sn){
		global $CONN,$UPLOAD_PATH;
		//取出個人配額
		$sql="select * from blog_quota where teacher_sn='{$_SESSION['session_tea_sn']}' ";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$size=$rs->fields['size'];
		$many=$rs->fields['many'];

		//如果0的話立刻給預設值
		if(!$size) $CONN->Execute("insert into blog_quota (teacher_sn,size,many,enable) values('{$_SESSION['session_tea_sn']}','20','200','1')");

		if(!$size) $size=20;
		if(!$many) $many=200;


		//由bh_sn找出旗下的bc_sn
		$sql="select bc_sn from blog_content where bh_sn='$bh_sn' and enable=1";
		//echo $sql;
		$rs=$CONN->Execute($sql) or trigger($sql,256);
		$i=0;
		$count=0;
		$amount=0;
		while(!$rs->EOF){
			$bc_sn_arr[$i]=$rs->fields['bc_sn'];
			//echo $bc_sn_arr[$i]."<br>";
			$path[$i]=$UPLOAD_PATH."blog/content/".$bc_sn_arr[$i];
			//echo $path[$i]."<br>";
			$handle[$i]=opendir($path[$i]);
			while ($file = readdir($handle[$i])) {
				if($file!="." and $file!=".."){
				//echo $file."<br>";
				$amount=$amount+filesize ($path[$i]."/".$file);
				$count++;
				}
			}
			closedir($handle[$i]);
			$i++;
			$rs->MoveNext();
		}
		$amount=round($amount/1024/1024,2);
		if(($amount>=$size) || ($count>=$many)) $q_mess[0]=0;//滿了
		else $q_mess[0]=1;//還可寫入
		$less=($size-$amount<0)?0:$size-$amount;
		$q_mess[1]="<font color='#606585'>你的配額為 $size MB，最多檔案數為 $many 目前尚有 $less MB的空間和尚可容納 ".($many-$count)."個圖檔！</font>";

		return $q_mess;
	}

	//判斷是否為系統管理員
	function is_blog_admin(){
		global $CONN;
		$sql0="SELECT id_sn FROM pro_check_new WHERE pro_kind_id='1' and id_kind='教師' ";
		$rs0=$CONN->Execute($sql0) or trigger_error($sql0,256);
		if ($rs0) {
			while( $ar = $rs0->FetchRow() ) {
				$admin_arr[]=$ar['id_sn'];
			}
		}
		if(in_array( $_SESSION['session_tea_sn'],$admin_arr)) return 1;
		else return 0;
	}

?>
