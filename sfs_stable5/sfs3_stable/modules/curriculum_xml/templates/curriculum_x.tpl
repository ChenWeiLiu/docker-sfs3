<?xml version="1.0" encoding="UTF-8"?>
<教師配課交換資料>
	<學校資料>
		<學校代碼>{{$schoolid}}</學校代碼>
		<學校名稱>{{$schoolname}}</學校名稱>
		<適用學年>{{$this_year}}</適用學年>
		<適用學期>{{$this_semester}}</適用學期>
		<匯入帳號>{{$x_id}}</匯入帳號>
		<匯入密碼>{{$x_pwd}}</匯入密碼>
	</學校資料>
{{foreach from=$out_arr item=content key=arr_key}}
{{foreach from=$content.teacherdata item=teacherdata key=teacher_sn}}
		<教師配課資料>
			<教師身分證>{{$teacherdata.teach_person_id}}</教師身分證>
			<教師姓名>{{$teacherdata.name}}</教師姓名>
{{foreach from=$teacherdata.subjects item=subject key=ss_id}}
			<科目資料>
				<科目名稱>{{$subject.subject_name}}</科目名稱>
				<科目所屬領域>{{$subject.learningareas}}</科目所屬領域>
				<科目正課時數>{{if $subject.counter_0}}{{$subject.counter_0}}{{else}}0{{/if}}</科目正課時數>
				<科目兼課時數>{{if $subject.counter_1}}{{$subject.counter_1}}{{else}}0{{/if}}</科目兼課時數>
				<科目總時數>{{$subject.counter}}</科目總時數>
				<授課班級>{{$subject.class_list|substr:0:-1}}</授課班級>
			</科目資料>
{{/foreach}}
		</教師配課資料>
{{/foreach}}
{{/foreach}}
</教師配課交換資料>