<?php
/**
 * Date: 2017/2/11
 * Time: 下午 01:01
 * 即時檢測打字情況的 javascript
 */
$_session['timer']=0;
?>

<Script>

    var TT = "1234";

    (function () {
        $.ajax({
            type: 'get',
            url: '<?= $SFS_PATH_HTML ?>modules/contest_student/ct_ajax_get_type.php?type_id=<?php echo $type_id;?>',
            dataType: 'text',
            error: function (xhr) {
                alert('系統連線異常，無法取得文章，請重新登入!');
            },
            success: function (response) {
                TT="";
                var S=response.split("\r\n");
                var V='';
                for (i=0;i<S.length;i++) {
                    //if (S[i]!='') {
                    if (V!='') {
                        V=V+S[i]+"<br>";
                        TT=TT+S[i]+"\n";
                    } else {
                        V=S[i]+"<br>";
                        TT=S[i]+"\n";
                    }
                    //}
                } // end for
                $("#SHOW2").html(V);
            }
        });

    })();

    $("#SHOW2").click(function(){
        alert('禁止複製哦！');
        $("#typetest").focus();
    });

    //鎖 Ctrl+A Ctrl+a
    $(document).keydown(function(e){
        if(e.ctrlKey && (e.keyCode =='65' || e.keyCode=='83')){
            return false;
        }
        //把頁面上的 ESC 鍵功能取消 , 避免在 IE 中多按了 ESC 鍵，　textarea 中的內容被清除
        if (e.keyCode=='27') {
            return false;
        }
    });

    //鎖滑鼠右鍵
    document.oncontextmenu = new Function("return false");

    //使整個頁面不能進行選取
    disableSelection(document.body);

    function disableSelection(target){
        if (typeof target.onselectstart!="undefined") //IE route
            target.onselectstart=function() {
                return false;
            }
        else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
            target.style.MozUserSelect="none";
        else //All other route (ie: Opera)
            target.onmousedown=function() {
                return false;
            }
        target.style.cursor = "default";
    }

    <?php
     if ($_POST['start']) {
       //若為競賽則寫入開始時間.
      if ($rec_id>0) {
      //寫入開始時間
        $sql="update contest_typerec set sttime_{$type_times}='".date("Y-m-d H:i:s")."' where id='{$rec_id}' and student_sn='{$_SESSION['session_tea_sn']}'";
        $CONN->Execute($sql) or die("SQL Error! SQL=".$sql);
        //記下是第幾次檢測
        echo "var T_times=".$type_times.";\n";
      }
    ?>
    var T_length=TT.length;

    document.myform.typetest.focus();
    //alert("ok");

    // textarea 中的任何變動，立即在提示區域上標示目前該打的字
    $("#typetest").on("change input paste keydown keyup", function() {
        var Type_words=$("#typetest").val();
        var S=Type_words.split("\n");
        var line=S.length;   //已經打了幾行
        line--;
        var key_type=S[line].length;    //第幾個字

        h = (line>3)?(line-3)*22:0;
        //取得目前游標
        //var cursorPosition = $('#typetest').prop("selectionStart");
        var cursorPosition=getCaretPosition(document.getElementById("typetest"));
        var pos2word=Type_words.substr(0,cursorPosition.start);
        var PS=pos2word.split("\n");
        var PS_line=PS.length-1;
        var PS_key_type=PS[PS_line].length;
        var PS_h=h = (PS_line>3)?(PS_line-3)*22:0;
        //顯示目前游標對應的字 2017.02.21 改
        showtype(PS_line,PS_key_type,PS_h);
        //顯示最後要打的字
        //showtype(line,key_type,h);

    });


    function showtype(i,j,h){
        var S=TT.split("\n");
        var s1=S[i].substr(0,j);
        var s2=S[i].substr(j,1);
        var s3=S[i].substr(j+1);
        //if (s2=='') {
        //    S[i]=s1+s2+s3;
        //} else {
        if (S[i].length>0) {
            S[i]=s1+'<span style="color:#0000FF;background-color: #d9ab3d">'+s2+'</span>'+s3;
        }

        var V='';
        for (i=0;i<S.length;i++) {
            if (i==S.length-1) {
                V=V+'<span style="background-color: #c6caf8">'+S[i]+'</span>';
            } else {
                if (S[i]=='') {
                    V=V+"<br>";
                } else {
                    V=V+"<span style=\"background-color: #c6caf8\">"+S[i]+"</span><br>";
                }

            }
        }

        $("#SHOW2").html(V);
        //自動捲動提示頁面
        $("#SHOW2").scrollTop(h);
    }

    //開始打字
    function chk_race(end) {

        var typing_words=document.myform.typetest.value;
        var S=typing_words.split("\n");
        //換行符號須加脫位字元
        var V='';
        for (i=0;i<S.length;i++) {
            if (i== S.length - 1) {
                V=V+S[i];
            } else {
                V=V+S[i]+"\\n";
            }

        }

        <?php
            if ($rec_id>0) {
            ?>
        var params = {
            typing_words: V,
            //typing_words: typing_words,
            type_id:<?= $type_id ?>,
            rec_id:<?= $rec_id ?>,
            type_times:<?= $type_times ?>,
            ending:end,
        };

        <?php
        } else {
        ?>
        var params = {
            typing_words: V,
            //typing_words: typing_words,
            type_id:<?= $type_id ?>,
            ending:end,
        };

        <?php
        }
     ?>

        $.ajax({
            type: 'post',
            url: '<?= $SFS_PATH_HTML ?>modules/contest_student/ct_ajax_check_type.php',
            data: params,
            dataType: 'text',
            error: function (xhr) {
                alert('系統連線異常，無法比對文章，請重新登入!');
            },
            success: function (response) {

                // 因為是 big5 中文，用 json 傳遞好像有問題
                var res_data=response.split(",");
                $("#timer").html(res_data[0]);
                $("#speed").html(res_data[1]);
                $("#correct").html(res_data[2]);
                $("#score").html(res_data[3]);
                //時間已到
                if (res_data[4]=='2') {
                    clearTimeout(timeout);
                    $("#typetest").blur();
                    $('#typetest').attr('disabled','disabled');
                    //讓 textarea 脫離 focus 後，再傳遞一次，會送回 res_data[4]=='-1'
                    chk_race(1);
                }
                if (res_data[4]=='-1') {
                    clearTimeout(timeout);
                    <?php
                    if ($rec_id>0) {
                    ?>
                        if (T_times==1) {
                            alert("時間到!\n你的速度:"+res_data[1]+" 字/分 \n正確率:"+res_data[2]+"\n總積分："+res_data[3]+"\n\n這是你的第一次成績, 請再次進入進行第二次檢測!");
                        } else {
                            alert("時間到!\n你的速度:"+res_data[1]+" 字/分 \n正確率:"+res_data[2]+"\n總積分："+res_data[3]+"\n\n這是你的第二次成績, 比賽結束!!");
                        }
                    <?php
                    } else {
                    ?>
                        alert("時間到!\n你的速度:"+res_data[1]+" 字/分 \n正確率:"+res_data[2]+"\n總積分："+res_data[3]);
                    <?php
                    }
                    ?>
                    $('#typetest').attr('disabled','disabled');
                }
            }
        });

        var timeout=setTimeout(chk_race,1000);

    } // end function chk_race

    chk_race(0);


    //獲取游標所在位置
    function getCaretPosition (ctrl) {
        // IE < 9 Support
        if (document.selection) {
            ctrl.focus();
            var range = document.selection.createRange();
            var rangelen = range.text.length;
            range.moveStart ('character', -ctrl.value.length);
            var start = range.text.length - rangelen;
            return {'start': start, 'end': start + rangelen };
        }
        // IE >=9 and other browsers
        else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
            return {'start': ctrl.selectionStart, 'end': ctrl.selectionEnd };
        } else {
            return {'start': 0, 'end': 0};
        }
    }






    <?php
    } // end if $_POST['start']
    ?>

</Script>

