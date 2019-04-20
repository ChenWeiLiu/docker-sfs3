{{* $Id: *}}

{{strip}}
<HTML>
  <HEAD>
    <TITLE>{{$html_title}}</TITLE>
    <STYLE type="text/css">
      TABLE { border-collapse:collapse; empty-cells: show;text-align:center;font-size:10pt;line-height:12pt;  }
      TD    {border-top:0;border-left:0;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
      TD.left {border-top:0px;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
      TD.empty {border-top:0px solid;border-left:0px solid;border-right:0px solid;border-bottom:0px solid;vertical-align: middle}
{{if $smarty.post.type!=1}}
      TD.memo    {border-top:0;border-left:0;border-right:1px solid;border-bottom:1px solid;vertical-align:top;text-align:left;}
      TD.top {border-top:1px solid;border-left:0px;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
      TD.top_left {border-top:1px solid;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
{{else}}
			TD.memo    {border-top:0;border-left:2px solid;border-right:2px solid;border-bottom:2px solid;vertical-align:top;text-align:left;}
      TD.top {border-top:2px solid;border-left:0px;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
      TD.top_left {border-top:2px solid;border-left:2px solid;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
			TD.right {border-top:0px;border-left:1px solid;border-right:2px solid;border-bottom:1px solid;vertical-align: middle}
			TD.both {border-top:0px;border-left:2px solid;border-right:2px solid;border-bottom:1px solid;vertical-align: middle}
			TD.both_top {border-top:1px solid;border-left:2px solid;border-right:2px solid;border-bottom:1px solid;vertical-align: middle}
			TD.empty_right {border-top:0;border-left:0;border-right:0px solid;border-bottom:1px solid;vertical-align: middle}
			TD.top_right {border-top:2px solid;border-left:2px solid;border-right:2px solid;border-bottom:1px solid;vertical-align: middle}
			TD.left_left {border-top:1px solid;border-left:2px solid;border-right:1px solid;border-bottom:1px solid;vertical-align: middle}
			TD.bottom_left {border-top:1px solid;border-left:2px solid;border-right:1px solid;border-bottom:2px solid;vertical-align: middle}
{{/if}}
    </STYLE>
  </HEAD>
  <BODY>
{{/strip}}
