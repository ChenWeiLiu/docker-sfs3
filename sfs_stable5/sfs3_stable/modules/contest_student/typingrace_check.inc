<?php
/**
 * Date: 2017/2/11
 * Time: �U�� 01:01
 * �Y���˴����r���p�� javascript
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
                alert('�t�γs�u���`�A�L�k���o�峹�A�Э��s�n�J!');
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
        alert('�T��ƻs�@�I');
        $("#typetest").focus();
    });

    //�� Ctrl+A Ctrl+a
    $(document).keydown(function(e){
        if(e.ctrlKey && (e.keyCode =='65' || e.keyCode=='83')){
            return false;
        }
        //�⭶���W�� ESC ��\����� , �קK�b IE ���h���F ESC ��A�@textarea �������e�Q�M��
        if (e.keyCode=='27') {
            return false;
        }
    });

    //��ƹ��k��
    document.oncontextmenu = new Function("return false");

    //�Ͼ�ӭ�������i����
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
       //�Y���v�ɫh�g�J�}�l�ɶ�.
      if ($rec_id>0) {
      //�g�J�}�l�ɶ�
        $sql="update contest_typerec set sttime_{$type_times}='".date("Y-m-d H:i:s")."' where id='{$rec_id}' and student_sn='{$_SESSION['session_tea_sn']}'";
        $CONN->Execute($sql) or die("SQL Error! SQL=".$sql);
        //�O�U�O�ĴX���˴�
        echo "var T_times=".$type_times.";\n";
      }
    ?>
    var T_length=TT.length;

    document.myform.typetest.focus();
    //alert("ok");

    // textarea ���������ܰʡA�ߧY�b���ܰϰ�W�Хܥثe�ӥ����r
    $("#typetest").on("change input paste keydown keyup", function() {
        var Type_words=$("#typetest").val();
        var S=Type_words.split("\n");
        var line=S.length;   //�w�g���F�X��
        line--;
        var key_type=S[line].length;    //�ĴX�Ӧr

        h = (line>3)?(line-3)*22:0;
        //���o�ثe���
        //var cursorPosition = $('#typetest').prop("selectionStart");
        var cursorPosition=getCaretPosition(document.getElementById("typetest"));
        var pos2word=Type_words.substr(0,cursorPosition.start);
        var PS=pos2word.split("\n");
        var PS_line=PS.length-1;
        var PS_key_type=PS[PS_line].length;
        var PS_h=h = (PS_line>3)?(PS_line-3)*22:0;
        //��ܥثe��й������r 2017.02.21 ��
        showtype(PS_line,PS_key_type,PS_h);
        //��̫ܳ�n�����r
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
        //�۰ʱ��ʴ��ܭ���
        $("#SHOW2").scrollTop(h);
    }

    //�}�l���r
    function chk_race(end) {

        var typing_words=document.myform.typetest.value;
        var S=typing_words.split("\n");
        //����Ÿ����[���r��
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
                alert('�t�γs�u���`�A�L�k���峹�A�Э��s�n�J!');
            },
            success: function (response) {

                // �]���O big5 ����A�� json �ǻ��n�������D
                var res_data=response.split(",");
                $("#timer").html(res_data[0]);
                $("#speed").html(res_data[1]);
                $("#correct").html(res_data[2]);
                $("#score").html(res_data[3]);
                //�ɶ��w��
                if (res_data[4]=='2') {
                    clearTimeout(timeout);
                    $("#typetest").blur();
                    $('#typetest').attr('disabled','disabled');
                    //�� textarea ���� focus ��A�A�ǻ��@���A�|�e�^ res_data[4]=='-1'
                    chk_race(1);
                }
                if (res_data[4]=='-1') {
                    clearTimeout(timeout);
                    <?php
                    if ($rec_id>0) {
                    ?>
                        if (T_times==1) {
                            alert("�ɶ���!\n�A���t��:"+res_data[1]+" �r/�� \n���T�v:"+res_data[2]+"\n�`�n���G"+res_data[3]+"\n\n�o�O�A���Ĥ@�����Z, �ЦA���i�J�i��ĤG���˴�!");
                        } else {
                            alert("�ɶ���!\n�A���t��:"+res_data[1]+" �r/�� \n���T�v:"+res_data[2]+"\n�`�n���G"+res_data[3]+"\n\n�o�O�A���ĤG�����Z, ���ɵ���!!");
                        }
                    <?php
                    } else {
                    ?>
                        alert("�ɶ���!\n�A���t��:"+res_data[1]+" �r/�� \n���T�v:"+res_data[2]+"\n�`�n���G"+res_data[3]);
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


    //�����ЩҦb��m
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
