{{* $Id: system_bad_login_csv.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
"登入時間","登入IP","登入帳號","登入狀況"
{{foreach from=$rowdata item=v key=i}}
"{{$v.log_time}}","{{$v.log_ip}}","{{$v.log_id}}","{{$v.err_kind}}"
{{/foreach}}
