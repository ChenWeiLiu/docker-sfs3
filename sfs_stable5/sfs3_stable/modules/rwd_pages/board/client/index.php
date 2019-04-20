<!DOCTYPE html>

<html ng-app='myApp'>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="css/bootstrap.min.css">
<style>
table, th , td  {
  border: 1px solid grey;
  border-collapse: collapse;
  padding: 5px;
  
}
table tr:nth-child(odd)	{
  background-color: #f1f1f1;
}
table tr:nth-child(even) {
  background-color: #ffffff;
}
body {
	font-family: "Helvetica Neue", Helvetica, Arial, "微軟正黑體", sans-serif;
}
</style>
</head>

<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-animate.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-sanitize.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular-route.js"></script>
<script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.1.3.js"></script>
<script src="js/app.js"></script>


<body>
<div ng-view>  
</div>



</body>
</html>
