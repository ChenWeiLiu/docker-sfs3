{{* $Id: health_graph_sel.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="radio" name="graph_kind" value="bar" {{if $smarty.post.graph_kind=="" || $smarty.post.graph_kind=="bar"}}checked {{/if}}OnClick="this.form.submit();">JPG
<input type="radio" name="graph_kind" value="flashbar" {{if $smarty.post.graph_kind=="flashbar"}}checked {{/if}}OnClick="this.form.submit();">Flash
{{include file="health_graph.tpl"}}
