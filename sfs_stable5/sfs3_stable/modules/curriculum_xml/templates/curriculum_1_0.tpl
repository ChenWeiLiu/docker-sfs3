<?xml version="1.0" encoding="UTF-8"?>
{{$stylesheet}}
<base:curriculumexchange
    createdate="2011-11-10"
    xmlns:base="http://inservice.edu.tw/curriculumexchange/2011/10/base"
    xmlns:curriculum10  ="http://inservice.edu.tw/curriculumexchange/2011/10/curriculum10"
    xmlns:curriculum20  ="http://inservice.edu.tw/curriculumexchange/2011/10/curriculum20"
    xmlns:curriculum3040="http://inservice.edu.tw/curriculumexchange/2011/10/curriculum3040"
    xmlns:curriculum30  ="http://inservice.edu.tw/curriculumexchange/2011/10/curriculum30"
    xmlns:curriculum40  ="http://inservice.edu.tw/curriculumexchange/2011/10/curriculum40"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://inservice.edu.tw/curriculumexchange/2011/10/base base.xsd
                        http://inservice.edu.tw/curriculumexchange/2011/10/curriculum10   curriculum10.xsd
                        http://inservice.edu.tw/curriculumexchange/2011/10/curriculum20   curriculum20.xsd
                        http://inservice.edu.tw/curriculumexchange/2011/10/curriculum3040 curriculum3040.xsd
                        http://inservice.edu.tw/curriculumexchange/2011/10/curriculum30   curriculum30.xsd
                        http://inservice.edu.tw/curriculumexchange/2011/10/curriculum40   curriculum40.xsd">
	<exchangecity cityname="{{$cityname}}">
		<exchangeschool schoolname="{{$schoolname}}" schoolid="{{$schoolid}}">
{{foreach from=$out_arr item=content key=arr_key}}
			<curriculumdata syear="{{$out_arr[$arr_key].year}}" session="{{$out_arr[$arr_key].semester}}">
				<teacherdata>
{{foreach from=$content.teacherdata item=teacherdata key=teacher_sn}}
					<teacher idnumber="{{$teacherdata.teach_person_id}}">
						<teacheruname>{{$teacherdata.name}}</teacheruname>
{{if $cert>1}}
						<certificates>
{{foreach from=$teacherdata.certificates item=certificates key=certificate_sn}}
							<certificate certdate="{{$certificates.certdate}}">
{{if $cert>2}}
								<certgroup>{{$certificates.certgroup}}</certgroup>
								<certsujbect>{{$certificates.certsujbect}}</certsujbect>
								<certarea>{{$certificates.certarea}}</certarea>
{{/if}}
								<certnumber>{{$certificates.certnumber}}</certnumber>
							</certificate>
{{/foreach}}
						</certificates>
{{/if}}
						<teachersubjects>
{{foreach from=$teacherdata.teachersubjects item=teachersubjects key=subject_sn}}
							<teachersubject>
								<teachersubjectdomain>{{$teachersubjects.domain}}</teachersubjectdomain>
								<teachersubjectexpertise>{{$teachersubjects.expertise}}</teachersubjectexpertise>
							</teachersubject>
{{/foreach}}
						</teachersubjects>
					</teacher>
{{/foreach}}                
				</teacherdata>
				<curriculums>
{{foreach from=$content.curriculums item=curriculums key=ss_id}}
					<curriculum teacheridnumber="{{$curriculums.teacheridnumber}}" classyear="{{$curriculums.classyear}}" classname="{{$curriculums.classname}}">
						<week>{{$curriculums.week}}</week>
						<classtime>{{$curriculums.classtime}}</classtime>
						<curriculum3040:classsubject>
							<category>{{$curriculums.category}}</category>
							<learningareas>{{$curriculums.learningareas}}</learningareas>
							<subject>{{$curriculums.subject}}</subject>
						</curriculum3040:classsubject>
					</curriculum>
{{/foreach}}
				</curriculums>
			</curriculumdata>
{{/foreach}}
		</exchangeschool>
	</exchangecity>
</base:curriculumexchange>
<!--xmllint - -schema base.xsd  test.xml - -noout - -timing -repeat-->
