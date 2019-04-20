{{* $Id: health_graph_sel2.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="radio" name="graph_kind" value="pie" {{if $smarty.post.graph_kind=="" || $smarty.post.graph_kind=="pie"}}checked {{/if}}OnClick="this.form.submit();">JPG
<input type="radio" name="graph_kind" value="flashpie" {{if $smarty.post.graph_kind=="flashpie"}}checked {{/if}}OnClick="this.form.submit();">Flash
{{include file="health_graph.tpl"}}
