<?php

// $Id:$

//include_once "../../include/sfs_case_ado.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_oo_zip2.php";
include_once "../12basic_career/my_fun.php";

class sfsxmlfile
{
	var $student_sn;
	var $out_arr;
	var $sn_str;
	var $zip;

	function sfsxmlfile()
	{
		$this->init();
	}

	function init()
	{
		$this->out_arr=array();
		$this->zip = new EasyZip;
	}

	function output()
	{
		if (count($this->student_sn)>0) {
			$this->sn_str="'".implode("','",$this->student_sn)."'";
			$this->base();
//			$this->seme();
//			$this->move();
//			$this->mid_seme();
		}
	}

	function base()
	{
		global $CONN,$addr,$all_reward,$IS_JHORES;
		
		//���X�Z�žɮv�m�W�ѷӡA�U�����Ǵ���Ʒ|�Ψ�
		$class_teacher_name=class_teacher();
		
		//���o���W��(�u�ʽҵ{����ܦW��)
        $subject_name_arr=get_subject_name_arr();
		
		//���ɸ�ưѷӰ}�C
		$sse_relation_arr = sfs_text("�������Y");
		$sse_family_kind_arr=sfs_text("�a�x����");
		$sse_family_air_arr=sfs_text("�a�x��^");
		$sse_teach_arr=sfs_text("�ޱФ覡");
		$sse_live_state_arr=sfs_text("�~����");
		$sse_rich_state_arr=sfs_text("�g�٪��p");
		
		$sse_arr= array("1"=>"�߷R�x�����","2"=>"�߷R�x�����","3"=>"�S��~��","4"=>"����","5"=>"�ͬ��ߺD","6"=>"�H�����Y","7"=>"�~�V�欰","8"=>"���V�欰","9"=>"�ǲߦ欰","10"=>"���}�ߺD","11"=>"�J�{�欰");
		while(list($id,$val)= each($sse_arr)){
			$temp_sse_arr = sfs_text("$val");
			${"sse_arr_$id"} = $temp_sse_arr;
		}
		
		//��`�ͬ���{���O�ѷ�
		$ss_arr=array('0'=>'[��`�欰]','1'=>'[���鬡��]','2'=>'[���@�A��-�դ�]','3'=>'[���@�A��-����]','4'=>'[�S���{-�դ�]','4'=>'[�S���{-�ե~]');
		
		//���ʰ}�C�ѷ�
		$study_cond_arr=study_cond();
		
		//���X stud_base ���
		$query="select a.*,left(a.curr_class_num,length(a.curr_class_num)-4) as year_num,mid(a.curr_class_num,length(a.curr_class_num)-3,2) as class_num,right(a.curr_class_num,2) as site_num,b.grad_kind,b.grad_date,b.grad_word,b.grad_num from stud_base a left join grad_stud b on a.student_sn=b.student_sn where a.student_sn in ($this->sn_str) order by a.student_sn";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			//�B�z�ǥͨ����O 1~18 ���зǿﶵ
			$oth_arr=array();
			$stud_kind_arr=explode(",",$res->fields[stud_kind]);
			$stud_id=$res->fields['stud_id'];
			$student_sn=$res->fields['student_sn'];
			reset($stud_kind_arr);
			while(list($k,$v)=each($stud_kind_arr)) {
				if (intval($v)<19 and $v!="") $oth_arr[stud_kind][]=$v;
			}
			//�B�z���y��}�M�s����}
			
			//���ϽեX�����̭����O���E���s�}	�N�a�}�������ӷs�}	
			//$sql_move="select new_address from stud_seme_move where student_sn=$student_sn and move_id=8 order by move_date desc";
			/*
                        $sql_move="select new_address from stud_move where stud_id='$stud_id' and move_kind='8' order by move_date desc";
			$res_move=$CONN->Execute($sql_move);
			$addr=$res_move->fields['new_address'];

			if($addr) $oth_arr[stud_addr_1]=change_addr_str($addr);	else
			{			
				$addr=$res->fields[stud_addr_1];
				$oth_arr[stud_addr_1]=change_addr_str($addr);
				//$oth_arr[stud_addr_1][12]=implode("",array_slice($oth_arr[stud_addr_1],4,8));
			}
			$addr=$res->fields[stud_addr_2];
			$oth_arr[stud_addr_2]=change_addr_str($addr);
			//$oth_arr[stud_addr_2][12]=implode("",array_slice($oth_arr[stud_addr_2],4,8));
			$this->out_arr[$res->fields[student_sn]]=array_merge($res->FetchRow(),$oth_arr);
                         * 
                         */
			$addr="";
			$oth_arr[stud_addr_2]="";
			$this->out_arr[$res->fields[student_sn]]=array_merge($res->FetchRow(),$oth_arr);
			
	
			
		}
		
		//���X stud_domicile ���
		$query="select * from stud_domicile where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]]=array_merge($this->out_arr[$res->fields[student_sn]],$res->FetchRow());
		}
		//���X stud_brother_sister ���
		$query="select bs_id,bs_name,bs_calling,bs_gradu,bs_birthyear,student_sn from stud_brother_sister where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][bro_sis][$res->fields[bs_id]]=$res->FetchRow();
		}
		//���X stud_kinfolk ���
		$query="select kin_id,kin_name,kin_calling,kin_phone,kin_hand_phone,kin_email,student_sn from stud_kinfolk where student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][kinfolk][$res->fields[kin_id]]=$res->FetchRow();
		}
		
		//���X�������(stud_subkind)
		$query="select student_sn,clan,area from stud_subkind where type_id=9 AND student_sn in ($this->sn_str) order by student_sn";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			 $this->out_arr[$res->fields[student_sn]][yuanzhumin]=$res->FetchRow();
		}
			
		//���X���ʬ��� (�p��stud_move & stud_move_import)
		$query="(select * from stud_move_import where student_sn in ($this->sn_str)) UNION DISTINCT (select * from stud_move where student_sn in ($this->sn_str)) order by student_sn,move_date";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$move_id=$res->fields[move_id];
			$move_kind=$res->fields[move_kind];
			$row_data=$res->FetchRow();
			
			//�ഫ�������O����r
			$row_data[move_kind]=$study_cond_arr[$move_kind];
			$this->out_arr[$current_student_sn][stud_move][$move_id]=$row_data;			
		}
		

		
		//���X���մN�ǾǴ��P���Z���
		$query="select seme_year_seme,left(seme_year_seme,3) as year,right(seme_year_seme,1) as semester,left(seme_class,1) as study_year,right(seme_class,2) as study_class,seme_class_name,seme_num,student_sn from stud_seme where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme]=$row_data;
			//�[�J�Z�žɮv�m�W
			$class_id=sprintf("%03d_%d_%02d_%02d",$row_data['year'],$row_data['semester'],$row_data['study_year'],$row_data['study_class']);
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme][teacher]=$class_teacher_name[$class_id];
			
			//������;Ǵ����Z���
			$query="select distinct seme_year_seme from stud_seme_score where student_sn=$current_student_sn order by seme_year_seme";
			$res_score=$CONN->Execute($query) or die("SQL���~�G $query");
			while(!$res_score->EOF) {
				 $score_semester_list_arr[]=$res_score->fields['seme_year_seme'];
				 $res_score->MoveNext();
			}
			$current_student_score=cal_fin_score(array($current_student_sn),$score_semester_list_arr,"","",2);
			$this->out_arr[$current_student_sn][semester_score]=$current_student_score[$current_student_sn];
		}
		
		//���X�L�մN�ǾǴ��P���Z���
		$query="select seme_year_seme,left(seme_year_seme,3) as year,right(seme_year_seme,1) as semester,seme_class_grade as study_year,seme_class_name,seme_num,student_sn,teacher_name from stud_seme_import where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
		$res=$CONN->Execute($query) or die("SQL���~�G $query");
		while(!$res->EOF) {
			$current_student_sn=$res->fields[student_sn];
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme]=$row_data;
			//�[�J�Z�žɮv�m�W
			//$class_id=sprintf("%03d_%d_%02d_%02d",$row_data['year'],$row_data['semester'],$row_data['study_year'],$row_data['study_class']);
			$this->out_arr[$current_student_sn][semester][$current_seme_year_seme][teacher]=$res->fields[teacher_name];
			
			//������;Ǵ����Z���
			$query="select distinct seme_year_seme from stud_seme_score where student_sn=$current_student_sn order by seme_year_seme";
			$res_score=$CONN->Execute($query) or die("SQL���~�G $query");
			while(!$res_score->EOF) {
				 $score_semester_list_arr[]=$res_score->fields['seme_year_seme'];
				 $res_score->MoveNext();
			}
			$current_student_score=cal_fin_score(array($current_student_sn),$score_semester_list_arr,"","",2);
			$this->out_arr[$current_student_sn][semester_score]=$current_student_score[$current_student_sn];
		}
			
											
			//����N�ǾǴ����
			$stud_seme_arr=array();
			$table=array('stud_seme_import','stud_seme');
			foreach($table as $key=>$value){
				$query="select * from $value where student_sn in ($this->sn_str) order by student_sn,seme_year_seme";
				$res=$CONN->Execute($query) or die("SQL���~�G $query");
				while(!$res->EOF){
					$current_student_sn=$res->fields['student_sn'];
					$stud_grade=substr($res->fields['seme_class'],0,-2);
					$year_seme=$res->fields['seme_year_seme'];
					$semester=substr($year_seme,-1);	
					$stud_seme_arr[$current_student_sn][$year_seme]['grade']=$stud_grade;
					$res->MoveNext();
				}
			}
	
/*
echo '<pre>';
print_r($this->out_arr[$current_student_sn]['career']['ponder']);
echo '</pre>';
exit;	
*/

		}		
		
	}

/* An easy way to keep in track of external processes.
* Ever wanted to execute a process in php, but you still wanted to have somewhat controll of the process ? Well.. This is a way of doing it.
* @compability: Linux only. (Windows does not work).
* @author: Peec
*/
class Process{
    private $pid;
    private $command;

    public function __construct($cl=false){
        if ($cl != false){
            $this->command = $cl;
            $this->runCom();
        }
    }
    private function runCom(){
        $command = 'nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $this->pid = (int)$op[0];
    }

    public function setPid($pid){
        $this->pid = $pid;
    }

    public function getPid(){
        return $this->pid;
    }

    public function status(){
        $command = 'ps -p '.$this->pid;
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }

    public function start(){
        if ($this->command != '')$this->runCom();
        else return true;
    }

    public function stop(){
        $command = 'kill '.$this->pid;
        exec($command);
        if ($this->status() == false)return true;
        else return false;
    }
}

?>