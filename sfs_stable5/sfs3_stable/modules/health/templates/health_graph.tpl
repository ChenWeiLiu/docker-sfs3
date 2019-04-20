{{* $Id: health_graph.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<br>
{{if ($smarty.session.graph_kind|substr:0:5)=="flash"}}
{{php}}
open_flash_chart_object(640, 420, 'health_graph.php' , true, '');
{{/php}}
{{else}}
<img src="health_graph.php">
{{/if}}
<br>
