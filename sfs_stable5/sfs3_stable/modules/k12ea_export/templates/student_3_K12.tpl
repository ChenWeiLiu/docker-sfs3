<���y�洫���>
{{foreach from=$data_arr item=content key=arr_key}}
	{{ if $content.stud_name!='' }}
	<�ǥ͸��>
		<�ǥͰ򥻸��>
			<�������ҷ�>
				<���y>{{$data_arr[$arr_key].stud_country}}</���y>
				{{assign var=id_kind value=$data_arr[$arr_key].stud_country_kind}}
				<�ҷӺ���>{{$id_kind_arr[$id_kind]}}</�ҷӺ���>
				<�ҷӸ��X>{{$data_arr[$arr_key].stud_person_id}}</�ҷӸ��X>
				<���~�a>{{$data_arr[$arr_key].stud_country_name}}</���~�a>
			</�������ҷ�>
			<�ǮեN�X>
				<�{�b�ǮեN�X>{{$school_edu_id}}</�{�b�ǮեN�X>
			</�ǮեN�X>
			<�򥻸��>
				<�ǥͩm�W>{{$data_arr[$arr_key].stud_name}} </�ǥͩm�W>
	{{assign var=stud_sex value=$data_arr[$arr_key].stud_sex}}
				<�ǥͩʧO>{{$sex_arr[$stud_sex]}}</�ǥͩʧO>
				<�ǥͥͤ�>{{$data_arr[$arr_key].stud_birthday}}</�ǥͥͤ�>
				<�{�b�~��>{{$data_arr[$arr_key].year_num}}</�{�b�~��>
				<�{�b�Z��>{{$data_arr[$arr_key].class_num}}</�{�b�Z��>
				<�{�b�y��>{{$data_arr[$arr_key].site_num}}</�{�b�y��>
			</�򥻸��>
			<�ǥͨ������O>
	{{assign var=stud_kind value=$data_arr[$arr_key].stud_kind}}
	{{foreach from=$stud_kind item=sk_arr key=sk_key}}
				<�ǥͨ������O_��Ƥ��e>
					<�ǥͨ������O_���O>{{$stud_kind_arr[$sk_key]}} </�ǥͨ������O_���O>
					<�ǥͨ������O_�Ƶ�>null</�ǥͨ������O_�Ƶ�>
				</�ǥͨ������O_��Ƥ��e>
	{{/foreach}}
			</�ǥͨ������O>
			<�s�����>
				<���y�a�}>
					<���y�a�}_�����W>{{$data_arr[$arr_key].stud_addr_1.0}} </���y�a�}_�����W>
					<���y�a�}_�m���ϦW>{{$data_arr[$arr_key].stud_addr_1.1}} </���y�a�}_�m���ϦW>
					<���y�a�}_����>{{$data_arr[$arr_key].stud_addr_1.2}} </���y�a�}_����>
					<���y�a�}_�F>{{$data_arr[$arr_key].stud_addr_1.3}} </���y�a�}_�F>
					<���y�a�}_����>{{$data_arr[$arr_key].stud_addr_1.4}} </���y�a�}_����>
					<���y�a�}_�q>{{$data_arr[$arr_key].stud_addr_1.5}} </���y�a�}_�q>
					<���y�a�}_��>{{$data_arr[$arr_key].stud_addr_1.6}} </���y�a�}_��>
					<���y�a�}_��>{{$data_arr[$arr_key].stud_addr_1.7}} </���y�a�}_��>
					<���y�a�}_��>{{$data_arr[$arr_key].stud_addr_1.8}} </���y�a�}_��>
					<���y�a�}_��>{{$data_arr[$arr_key].stud_addr_1.9}} </���y�a�}_��>
					<���y�a�}_��>{{$data_arr[$arr_key].stud_addr_1.10}} </���y�a�}_��>
					<���y�a�}_�Ӥ�>{{$data_arr[$arr_key].stud_addr_1.11}} </���y�a�}_�Ӥ�>
					<���y�a�}_��L>{{$data_arr[$arr_key].stud_addr_1.12}} </���y�a�}_��L>
				</���y�a�}>
				<�q�T�a�}>
					<�q�T�a�}_�����W>{{$data_arr[$arr_key].stud_addr_2.0}} </�q�T�a�}_�����W>
					<�q�T�a�}_�m���ϦW>{{$data_arr[$arr_key].stud_addr_2.1}} </�q�T�a�}_�m���ϦW>
					<�q�T�a�}_����>{{$data_arr[$arr_key].stud_addr_2.2}} </�q�T�a�}_����>
					<�q�T�a�}_�F>{{$data_arr[$arr_key].stud_addr_2.3}} </�q�T�a�}_�F>
					<�q�T�a�}_����>{{$data_arr[$arr_key].stud_addr_2.4}} </�q�T�a�}_����>
					<�q�T�a�}_�q>{{$data_arr[$arr_key].stud_addr_2.5}} </�q�T�a�}_�q>
					<�q�T�a�}_��>{{$data_arr[$arr_key].stud_addr_2.6}} </�q�T�a�}_��>
					<�q�T�a�}_��>{{$data_arr[$arr_key].stud_addr_2.7}} </�q�T�a�}_��>
					<�q�T�a�}_��>{{$data_arr[$arr_key].stud_addr_2.8}} </�q�T�a�}_��>
					<�q�T�a�}_��>{{$data_arr[$arr_key].stud_addr_2.9}} </�q�T�a�}_��>
					<�q�T�a�}_��>{{$data_arr[$arr_key].stud_addr_2.10}} </�q�T�a�}_��>
					<�q�T�a�}_�Ӥ�>{{$data_arr[$arr_key].stud_addr_2.11}} </�q�T�a�}_�Ӥ�>
					<�q�T�a�}_��L>{{$data_arr[$arr_key].stud_addr_2.12}} </�q�T�a�}_��L>
				</�q�T�a�}>
				<�q�T�q��>{{$data_arr[$arr_key].stud_tel_2}}</�q�T�q��>
				<��ʹq��>{{$data_arr[$arr_key].stud_tel_3}}</��ʹq��>
			</�s�����>
			<����>
				<����_�~��a>{{$data_arr[$arr_key].yuanzhumin.area}} </����_�~��a>
				<����_�ڧO>{{$data_arr[$arr_key].yuanzhumin.clan}} </����_�ڧO>
			</����>
			<�ǥͯZ�ũʽ�>
	{{assign var=class_kind value=$data_arr[$arr_key].stud_class_kind}}
	{{assign var=stud_spe_kind value=$data_arr[$arr_key].stud_spe_kind}}
	{{assign var=stud_spe_class_kind value=$data_arr[$arr_key].stud_spe_class_kind}}
	{{assign var=stud_spe_class_id value=$data_arr[$arr_key].stud_spe_class_id}}
				<�Z�ũʽ�>{{$class_kind_arr[$class_kind]}}</�Z�ũʽ�>
				<�S�ЯZ���O>{{$spe_kind_arr[$stud_spe_kind]}}</�S�ЯZ���O>
				<�S�ЯZ�Z�O>{{$spe_class_kind_arr[$stud_spe_class_kind]}}</�S�ЯZ�Z�O>
				<�S��Z�W�ҩʽ�>{{$spe_class_id_arr[$stud_spe_class_id]}}</�S��Z�W�ҩʽ�>
			</�ǥͯZ�ũʽ�>
			<�J�ǫe�Ш|���>
				<���X��J��>
	{{assign var=preschool_status value=$data_arr[$arr_key].stud_preschool_status}}
					<���X��J�Ǹ��>{{$preschool_status_arr[$preschool_status]}}</���X��J�Ǹ��>
					<���X��_�Ш|���ǮեN�X>{{$data_arr[$arr_key].stud_preschool_id}}</���X��_�Ш|���ǮեN�X>
					<���X��_�ǮզW��>{{$data_arr[$arr_key].stud_preschool_name}} </���X��_�ǮզW��>
				</���X��J��>
				<��p�J��>
	{{assign var=preschool_status value=$data_arr[$arr_key].stud_Mschool_status}}
					<��p�J�Ǹ��>{{$preschool_status_arr[$preschool_status]}}</��p�J�Ǹ��>
					<��p_�Ш|���ǮեN�X>{{$data_arr[$arr_key].stud_mschool_id}}</��p_�Ш|���ǮեN�X>
					<��p_�ǮզW��>{{$data_arr[$arr_key].stud_mschool_name}} </��p_�ǮզW��>
				</��p�J��>
			</�J�ǫe�Ш|���>
			<���׷~�֭�帹>
	{{assign var=grad_kind value=$data_arr[arr_key].grad_kind}}
				<���׷~�O>{{$grad_kind_arr[$grad_kind]}}</���׷~�O>
				<���׷~_���>{{$data_arr[$arr_key].grad_date}}</���׷~_���>
				<���׷~_�r>{{$data_arr[$arr_key].grad_word}}</���׷~_�r>
				<���׷~_��>{{$data_arr[$arr_key].grad_num}}</���׷~_��>
			</���׷~�֭�帹>
			<���˰򥻸��>
				<����_�m�W>{{$data_arr[$arr_key].fath_name}} </����_�m�W>
				<����_�X�ͦ~��>{{$data_arr[$arr_key].fath_birthyear}}</����_�X�ͦ~��>
				<����_����y></����_����y>
				<����_�w�J���إ�����y></����_�w�J���إ�����y>
	{{assign var=f_is_live value=$data_arr[$arr_key].fath_alive}}
				<����_�s�\>{{$is_live_arr[$f_is_live]}}</����_�s�\>
	{{assign var=f_rela value=$data_arr[$arr_key].fath_relation}}
				<�P�����Y>{{$f_rela_arr[$f_rela]}}</�P�����Y>
				<����_�����Ҹ�>{{$data_arr[$arr_key].fath_p_id}}</����_�����Ҹ�>
	{{assign var=f_edu value=$data_arr[$arr_key].fath_education}}
				<����_�Ш|�{��>{{$edu_kind_arr[$f_edu]}}</����_�Ш|�{��>
	{{assign var=f_grad_kind value=$data_arr[$arr_key].fath_grad_kind}}
				<����_���׷~�O>{{$grad_kind_arr[$f_grad_kind]}}</����_���׷~�O>
				<����_¾�~>{{$data_arr[$arr_key].fath_occupation}} </����_¾�~>
				<����_�A�ȳ��>{{$data_arr[$arr_key].fath_unit}} </����_�A�ȳ��>
				<����_¾��>{{$data_arr[$arr_key].fath_work_name}} </����_¾��>
				<����_�q�ܸ��X-��>{{$data_arr[$arr_key].fath_phone}}</����_�q�ܸ��X-��>
				<����_�q�ܸ��X-�v>{{$data_arr[$arr_key].fath_home_phone}}</����_�q�ܸ��X-�v>
				<����_��ʹq��>{{$data_arr[$arr_key].fath_hand_phone}}</����_��ʹq��>
				<����_�q�l�l��H�c>{{$data_arr[$arr_key].fath_email}}</����_�q�l�l��H�c>
			</���˰򥻸��>
			<���˰򥻸��>
				<����_�m�W>{{$data_arr[$arr_key].moth_name}} </����_�m�W>
				<����_�X�ͦ~��>{{$data_arr[$arr_key].moth_birthyear}}</����_�X�ͦ~��>
				<����_����y></����_����y>
				<����_�w�J���إ�����y></����_�w�J���إ�����y>
	{{assign var=m_is_live value=$data_arr[$arr_key].moth_alive}}
				<����_�s�\>{{$is_live_arr[$m_is_live]}}</����_�s�\>
	{{assign var=m_rela value=$data_arr[$arr_key].moth_relation}}
				<�P�����Y>{{$m_rela_arr[$m_rela]}}</�P�����Y>
				<����_�����Ҹ�>{{$data_arr[$arr_key].moth_p_id}}</����_�����Ҹ�>
	{{assign var=m_edu value=$data_arr[$arr_key].moth_education}}
				<����_�Ш|�{��>{{$edu_kind_arr[$m_edu]}}</����_�Ш|�{��>
	{{assign var=m_grad_kind value=$data_arr[$arr_key].moth_grad_kind}}
				<����_���׷~�O>{{$grad_kind_arr[$m_grad_kind]}}</����_���׷~�O>
				<����_¾�~>{{$data_arr[$arr_key].moth_occupation}} </����_¾�~>
				<����_�A�ȳ��>{{$data_arr[$arr_key].moth_unit}} </����_�A�ȳ��>
				<����_¾��>{{$data_arr[$arr_key].moth_work_name}} </����_¾��>
				<����_�q�ܸ��X-��>{{$data_arr[$arr_key].moth_phone}}</����_�q�ܸ��X-��>
				<����_�q�ܸ��X-�v>{{$data_arr[$arr_key].moth_home_phone}}</����_�q�ܸ��X-�v>
				<����_��ʹq��>{{$data_arr[$arr_key].moth_hand_phone}}</����_��ʹq��>
				<����_�q�l�l��H�c>{{$data_arr[$arr_key].moth_email}}</����_�q�l�l��H�c>
			</���˰򥻸��>
			<���@�H>
				<���@�H_�m�W>{{$data_arr[$arr_key].guardian_name}}</���@�H_�m�W>
				{{assign var=g_rela value=$data_arr[$arr_key].guardian_relation}}
				<�P���@�H�����Y>{{$g_rela_arr[$m_rela]}}</�P���@�H�����Y>
				<���@�H_�����Ҹ�>{{$data_arr[$arr_key].guardian_p_id}}</���@�H_�����Ҹ�>
				<���@�H_�a�}>{{$data_arr[$arr_key].guardian_address}}</���@�H_�a�}>
				<���@�H_�A�ȳ��>{{$data_arr[$arr_key].guardian_unit}} </���@�H_�A�ȳ��>
				<���@�H_¾��>{{$data_arr[$arr_key].grandmoth_name}}</���@�H_¾��>
				<���@�H_�s���q��>{{$data_arr[$arr_key].guardian_phone}}</���@�H_�s���q��>
				<���@�H_��ʹq��>{{$data_arr[$arr_key].guardian_hand_phone}}</���@�H_��ʹq��>
				<���@�H_�q�l�l��H�c>{{$data_arr[$arr_key].guardian_email}}</���@�H_�q�l�l��H�c>
			</���@�H>
			<�����򥻸��>
				<����_�m�W>{{$data_arr[$arr_key].grandfath_name}}</����_�m�W>
	{{assign var=gf_is_live value=$data_arr[$arr_key].grandfath_alive}}
				<����_�s�\>{{$is_live_arr[$gf_is_live]}}</����_�s�\>
			</�����򥻸��>
			<�����򥻸��>
				<����_�m�W>{{$data_arr[$arr_key].grandmoth_name}}</����_�m�W>
	{{assign var=gm_is_live value=$data_arr[$arr_key].grandmoth_alive}}
				<����_�s�\>{{$is_live_arr[$gm_is_live]}}</����_�s�\>
			</�����򥻸��>
			<��L����>
				{{assign var=kin_arr value=$data_arr[$arr_key].kinfolk}}
				{{foreach from=$kin_arr item=kin key=kin_key}}
				<��L����_��Ƥ��e>
					<��L����_�m�W>{{$kin_arr[$kin_key].kin_name}}</��L����_�m�W>
					{{assign var=kin_calling value=$kin_arr[$kin_key].kin_calling}}
					<��L����_�ٿ�>{{$g_rela_arr[$kin_calling]}}</��L����_�ٿ�>
					<��L����_�s���q��>{{$kin_arr[$kin_key].kin_phone}}</��L����_�s���q��>
					<��L����_��ʹq��>{{$kin_arr[$kin_key].kin_hand_phone}}</��L����_��ʹq��>
					<��L����_�q�l�l��H�c>{{$kin_arr[$kin_key].kin_email}}</��L����_�q�l�l��H�c>
				</��L����_��Ƥ��e>
				{{/foreach}}
			</��L����>
			<�S�̩n�f>
	{{assign var=bs_arr value=$data_arr[$arr_key].bro_sis}}
	{{foreach from=$bs_arr item=bs key=bs_key}}
				<�S�̩n�f_��Ƥ��e>
	{{assign var=bs_calling value=$bs_arr[$bs_key].bs_calling}}
					<�S�̩n�f_�ٿ�>{{$bs_calling_kind_arr[$bs_calling]}}</�S�̩n�f_�ٿ�>
					<�S�̩n�f_�m�W>{{$bs_arr[$bs_key].bs_name}} </�S�̩n�f_�m�W>
				</�S�̩n�f_��Ƥ��e>
	{{/foreach}}
			</�S�̩n�f>
		</�ǥͰ򥻸��>
		<�Ǵ����>
			{{assign var=semester_arr value=$data_arr[$arr_key].semester}}
			{{foreach from=$semester_arr item=semester key=semester_key}}
			<�ӧO�Ǵ����>
				<�Ǧ~�O>{{$semester_arr[$semester_key].year}}</�Ǧ~�O>
				<�Ǵ��O>{{$semester_arr[$semester_key].semester}}</�Ǵ��O>
				<�Z�Ůy��>
					{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
					<�~��>{{$study_year}}</�~��>
					<�Z��>{{$semester_arr[$semester_key].study_class}}</�Z��>
					<�y��>{{$semester_arr[$semester_key].seme_num}}</�y��>
				</�Z�Ůy��>
				<�Ǵ����Z>
					<�ɮv�m�W>{{$semester_arr[$semester_key].teacher}} </�ɮv�m�W>
					<�y��_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.language.$semester_key.score}}</�y��_�ǲ߻��ʤ���Z>
					<�y��_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.chinese}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.local}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.english}} </�y��_�ǲ߻���r�y�z>
					<����y��ʤ���Z>{{$data_arr[$arr_key].semester_score.chinese.$semester_key.score}}</����y��ʤ���Z>
					<���g�y��ʤ���Z>{{$data_arr[$arr_key].semester_score.local.$semester_key.score}}</���g�y��ʤ���Z>
					<���g�y�����O></���g�y�����O>
					<�^�y�ʤ���Z>{{$data_arr[$arr_key].semester_score.english.$semester_key.score}}</�^�y�ʤ���Z>
					<�ƾ�_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.math.$semester_key.score}}</�ƾ�_�ǲ߻��ʤ���Z>
					<�ƾ�_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.math}}</�ƾ�_�ǲ߻���r�y�z>
					<�۵M�P�ͬ����_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.nature.$semester_key.score}}</�۵M�P�ͬ����_�ǲ߻��ʤ���Z>
					<�۵M�P�ͬ����_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.nature}} </�۵M�P�ͬ����_�ǲ߻���r�y�z>
					<���|_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.social.$semester_key.score}}</���|_�ǲ߻��ʤ���Z>
					<���|_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.social}} </���|_�ǲ߻���r�y�z>
					<���d�P��|_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.health.$semester_key.score}}</���d�P��|_�ǲ߻��ʤ���Z>
					<���d�P��|_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.health}} </���d�P��|_�ǲ߻���r�y�z>
					<���N�P�H��_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.art.$semester_key.score}}</���N�P�H��_�ǲ߻��ʤ���Z>
					<���N�P�H��_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.art}} </���N�P�H��_�ǲ߻���r�y�z>
					<�ͬ��ҵ{_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.life.$semester_key.score}}</�ͬ��ҵ{_�ǲ߻��ʤ���Z>
					<�ͬ��ҵ{_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.life}}</�ͬ��ҵ{_�ǲ߻���r�y�z>
					<��X����_�ǲ߻��ʤ���Z>{{$data_arr[$arr_key].semester_score.complex.$semester_key.score}}</��X����_�ǲ߻��ʤ���Z>
					<��X����_�ǲ߻���r�y�z>{{$data_arr[$arr_key].semester_score_memo.$semester_key.complex}} </��X����_�ǲ߻���r�y�z>
					<�u�ʮɼ�>
						{{assign var=semester_elasticity_arr value=$data_arr[$arr_key].semester_score_memo.$semester_key.elasticity}}
						{{foreach from=$semester_elasticity_arr item=elasticity_data key=subject_id}}
						<�u�ʮɼ�_�������>
							<�u�ʮɼ�_��ئW��>{{$elasticity_data.subject_name}}</�u�ʮɼ�_��ئW��>
							<�u�ʮɼ�_��ئʤ���Z>{{$elasticity_data.score}}</�u�ʮɼ�_��ئʤ���Z>
						</�u�ʮɼ�_�������>
						{{/foreach}}
					</�u�ʮɼ�>
				</�Ǵ����Z>
				<��`�ͬ���{>
					<��`�ͬ���{_��r�y�z>{{$data_arr[$arr_key].semester_score_nor.$semester_key.ss_score_memo}} </��`�ͬ���{_��r�y�z>
					<�Ǵ��X�ʮu_���X�u���>{{$seme_course_date_arr[$semester_key].$study_year}}</�Ǵ��X�ʮu_���X�u���>
					<�Ǵ��X�ʮu_�ư���>{{$data_arr[$arr_key].semester_absence.$semester_key.1}}</�Ǵ��X�ʮu_�ư���>
					<�Ǵ��X�ʮu_�f����>{{$data_arr[$arr_key].semester_absence.$semester_key.2}}</�Ǵ��X�ʮu_�f����>
					<�Ǵ��X�ʮu_�m�Ҽ�>{{$data_arr[$arr_key].semester_absence.$semester_key.3}}</�Ǵ��X�ʮu_�m�Ҽ�>
					<�Ǵ��X�ʮu_��L����>{{$data_arr[$arr_key].semester_absence.$semester_key.others}}</�Ǵ��X�ʮu_��L����>
					<�Ǵ��X�ʮu_���>{{if $jhores>=6}}�`{{else}}��{{/if}}</�Ǵ��X�ʮu_���>
				</��`�ͬ���{>
				<�S���u�}��{>
					{{assign var=semester_spe_arr value=$data_arr[$arr_key].semester_spe.$semester_key}}
					{{foreach from=$semester_spe_arr item=semester_spe_data key=ss_id}}
					<�u�}��{����>
						<�u�}��{_���>{{$semester_spe_data.sp_date}}</�u�}��{_���>
						<�u�}��{_�ƥ�>{{$semester_spe_data.sp_memo}}</�u�}��{_�ƥ�>
					</�u�}��{����>
					{{/foreach}}
				</�S���u�}��{>
				<�߲z����>
					{{assign var=psy_test_arr value=$data_arr[$arr_key].psy_test.$semester_key}}
					{{foreach from=$psy_test_arr item=psy_test_data key=sn}}
					<�߲z����_��Ƥ��e>
						<�߲z����_�W��>{{$psy_test_data.item}} </�߲z����_�W��>
						<�߲z����_��l����>{{$psy_test_data.score}}</�߲z����_��l����>
						<�߲z����_�`�Ҽ˥�>{{$psy_test_data.model}}</�߲z����_�`�Ҽ˥�>
						<�߲z����_�зǤ���>{{$psy_test_data.standard}}</�߲z����_�зǤ���>
						<�߲z����_�ʤ�����>{{$psy_test_data.pr}}</�߲z����_�ʤ�����>
						<�߲z����_����>{{$psy_test_data.explanation}} </�߲z����_����>
					</�߲z����_��Ƥ��e>
					{{/foreach}}
				</�߲z����>
				<���ɬ���>
					<�������Y>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_relation}}</�������Y>
					<�a�x��^>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_family_air}}</�a�x��^>
					<���ޱФ覡>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_father}}</���ޱФ覡>
					<���ޱФ覡>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_mother}}</���ޱФ覡>
					<�~����>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_live_state}}</�~����>
					<�g�٪��p>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_rich_state}}</�g�٪��p>
					<�̳߷R�ǲ߻��>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s1 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�̳߷R�ǲ߻��_��Ƥ��e>{{$sse_data}}</�̳߷R�ǲ߻��_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�̳߷R�ǲ߻��>
					<�̧x���ǲ߻��>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s2 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�̧x���ǲ߻��_��Ƥ��e>{{$sse_data}}</�̧x���ǲ߻��_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�̧x���ǲ߻��>
					<�S��~��>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s3 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�S��~��_��Ƥ��e>{{$sse_data}}</�S��~��_��Ƥ��e>
						{{/if}}
						{{/foreach}}
						<�Z�N_��L></�Z�N_��L>
					</�S��~��>
					<����>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s4 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<����_��Ƥ��e>{{$sse_data}}</����_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</����>
					<�ͬ��ߺD>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s5 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�ͬ��ߺD_��Ƥ��e>{{$sse_data}}</�ͬ��ߺD_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�ͬ��ߺD>
					<�H�����Y>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s6 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�H�����Y_��Ƥ��e>{{$sse_data}}</�H�����Y_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�H�����Y>
					<�~�V�欰>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s7 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�~�V�欰_��Ƥ��e>{{$sse_data}}</�~�V�欰_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�~�V�欰>
					<���V�欰>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s8 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<���V�欰_��Ƥ��e>{{$sse_data}}</���V�欰_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</���V�欰>
					<�ǲߦ欰>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s9 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�ǲߦ欰_��Ƥ��e>{{$sse_data}}</�ǲߦ欰_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�ǲߦ欰>
					<���}�ߺD>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s10 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<���}�ߺD_��Ƥ��e>{{$sse_data}}</���}�ߺD_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</���}�ߺD>
					<�J�{�欰>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s11 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<�J�{�欰_��Ƥ��e>{{$sse_data}}</�J�{�欰_��Ƥ��e>
						{{/if}}
						{{/foreach}}
					</�J�{�欰>
				</���ɬ���>
				<���ɳX�ͬ���>
					{{foreach from=$data_arr[$arr_key].semester_talk.$semester_key item=talk_data key=sn}}
					<���ɳX�ͬ���_��Ƥ��e>
						<�������>{{$talk_data.sst_date}}</�������>
						<�s����H>{{$talk_data.sst_name}}</�s����H>
						<�s���ƶ�>{{$talk_data.sst_main}} </�s���ƶ�>
						<���e�n�I>{{$talk_data.sst_memo}} </���e�n�I>
					</���ɳX�ͬ���_��Ƥ��e>
					{{/foreach}}
				</���ɳX�ͬ���>
			</�ӧO�Ǵ����>
			{{/foreach}}
		</�Ǵ����>
		<���ʸ��>
			{{foreach from=$data_arr[$arr_key].stud_move item=move_data key=move_id}}
			<���ʸ��_��Ƥ��e>
				<��NŪ����>{{$move_data.move_c_unit}}</��NŪ����>
				<��NŪ�ǮզW��>{{$move_data.school}} </��NŪ�ǮզW��>
				<��NŪ�ǮեN�X></��NŪ�ǮեN�X>
				<���ʤ��>{{$move_data.move_date}}</���ʤ��>
				<���ʮ֭�����W��>{{$move_data.move_c_unit}}</���ʮ֭�����W��>
				<���ʭ�]>{{$move_data.move_kind}}</���ʭ�]>
				<�֭�帹_���>{{$move_data.move_c_date}}</�֭�帹_���>
				<�֭�帹_�r>{{$move_data.move_c_word}}</�֭�帹_�r>
				<�֭�帹_��>{{$move_data.move_c_num}}</�֭�帹_��>
			</���ʸ��_��Ƥ��e>
			{{/foreach}}
		</���ʸ��>
	</�ǥ͸��>
	{{/if}}
{{/foreach}}
</���y�洫���>
