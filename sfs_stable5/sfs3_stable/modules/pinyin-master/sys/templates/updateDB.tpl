<{*$route = "updateDB"*}>

<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.9/angular-animate.js"></script>
    <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-1.1.2.js"></script>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

<style type="text/css">
        body {
                font-family: "Helvetica Neue", Helvetica, Arial, "微軟正黑體", sans-serif;
        }

        table.table {
                font-size: 18px;
		margin-left:auto; 
		margin-right:auto;
		text-align:center;
        }
        table.table td{
		text-align:center; vertical-align:middle;
	}
        table.table th {
		text-align:center; vertical-align:middle;
	}

        table.submitdata {
                font-size: 16px;
        }

</style>


  </head>
  <body ng-app="ui.bootstrap.demo" ng-controller="ButtonsCtrl" class="container">

 <table class="table table-hover table-condensed table-striped table-bordered" cellspacing="0" width="100%">
    <tr class="danger">
      <th width="5%">
    <button type="button" class="btn btn-warning" ng-model="model999" uib-btn-checkbox btn-checkbox-true="1" btn-checkbox-false="0" ng-click="setAll(model999)"> 全部 </button>

</th>
      <th>輸出</th>
      <th>座號</th>
      <th>學號</th>
      <th>姓名</th>
      <th>新譯音</th>
      <th>原記錄</th>
    </tr>

<{foreach key=id item=name from=$keep_data }>
    <tr>
      <td>
    <button type="button" class="btn btn-primary" ng-model="obj[<{$id}>].model" uib-btn-checkbox btn-checkbox-true="1" btn-checkbox-false="0" ng-click="switchValue(<{$id}>)" > {{obj[<{$id}>].button_msg}}</button>
</td>
      <td>{{obj[<{$id}>].submit_name}} </td>
      <td><{$keep_data.$id['number']}></td>
      <td><{$id}></td>
      <td><{$keep_data.$id['user_name']}></td>
      <td><{$keep_data.$id['new_eng_name']}></td>
      <td><{$keep_data.$id['exist_eng_name']}></td>
    </tr>
<{/foreach}>
  </tbody>
</table>

    <button type="button" class="btn btn-danger" ng-model="getAll" ng-click="getAll()"> 送出 </button>

<script type="text/ng-template" id="myModalContent.html">
        <div class="modal-header">
            <h3 class="modal-title">異動結果 </h3>
        </div>
	 <div class="modal-body">
	    <b>{{ selected.item }}</b>
	</div>
        <div class="modal-footer">
            <button class="btn btn-warning" type="button" ng-click="cancel()">OK</button>
        </div>
</script>



<script>
//http://blog.miniasp.com/post/2013/07/23/AngularJS-five-ways-to-register-ngController.aspx
angular.module('ui.bootstrap.demo', ['ngAnimate', 'ui.bootstrap']);

angular.module('ui.bootstrap.demo').controller('ButtonsCtrl', ['$scope', '$http','$uibModal','$log', function ($scope,$http,$uibModal,$log) {

$scope.animationsEnabled = true;
$scope.items = 'null';
$scope.obj = new Object();

$scope.model999 = 1;

<{foreach key=id item=name from=$keep_data }>
	$scope.obj[<{$id}>] = new Object();
	$scope.obj[<{$id}>].id = "<{$id}>";
	$scope.obj[<{$id}>].model = 1;
	$scope.obj[<{$id}>].exist_eng_name = "<{$keep_data.$id['exist_eng_name']}>";
	$scope.obj[<{$id}>].new_eng_name = "<{$keep_data.$id['new_eng_name']}>";
	$scope.obj[<{$id}>].submit_name = "<{$keep_data.$id['new_eng_name']}>";
	$scope.obj[<{$id}>].button_msg = "新";

<{/foreach}>


  $scope.switchValue = function (id){
	if (id in $scope.obj){
		if ($scope.obj[id].model == 1){
		  //console.log($scope.obj[id].new_eng_name)
		  $scope.obj[id].submit_name = $scope.obj[id].new_eng_name;
		  $scope.obj[id].button_msg = "新";
		}else{
		  //console.log($scope.obj[id].exist_eng_name)
		  $scope.obj[id].submit_name = $scope.obj[id].exist_eng_name;
		  $scope.obj[id].button_msg = "原";
		  
		}
	}
    //console.log($scope.obj[9902]);
  }

  $scope.setAll = function (value){
	for(var key in $scope.obj) {
		//console.log($scope.obj[key]);
		if (value == 1){
			$scope.obj[key].model = 1;
			$scope.obj[key].submit_name = $scope.obj[key].new_eng_name;
			$scope.obj[key].button_msg = "新";
		}else{
			$scope.obj[key].model = 0;
			$scope.obj[key].submit_name = $scope.obj[key].exist_eng_name;
			$scope.obj[key].button_msg = "原";
		}
		//console.log(value);
	}
  }


  $scope.getAll = function (){
	var users = new Object();
	for(var key in $scope.obj) {
		if ($scope.obj[key].model == 1){
		$scope.method = 'POST';
		$scope.url = '../json_update_stud_eng.php';
		//console.log($scope.obj[key].id);
		//console.log($scope.obj[key].submit_name);

		users[key] = {
				id: $scope.obj[key].id,
				name: $scope.obj[key].submit_name
			     };


	
		} //end if
	}//end for

		  $http({method: $scope.method,
			data: users,
			headers: {
			   'Content-Type': 'application/x-www-form-urlencoded'
			 },
			 url: $scope.url})
		    .then(function(response) {
			$scope.status = response.status;
			  $scope.items = response.data;
			  console.log($scope.items);
			
			  $scope.open('lg');
				
			}, function(response) {
			$scope.data = response.data || "Request failed";
			  $scope.items = response.data;
			$scope.status = response.status;
		});



  } //end $scope.getAll

  //$scope.items = ['item1', 'item2', 'item3'];
   $scope.items = "item1";
   $scope.open = function(size) {
  
      var modalInstance = $uibModal.open({
        animation: $scope.animationsEnabled,
        templateUrl: 'myModalContent.html',
        controller: 'ModalInstanceCtrl',
        size: size,
        resolve: {
          items: function() {
            return $scope.items;
          }
        }
      });
  
      modalInstance.result.then(function(selectedItem) {
        $scope.selected = selectedItem;
      }, function() {
        $log.info('Modal dismissed at: ' + new Date());
      });
  };




}]);

</script>


<script>

angular.module('ui.bootstrap.demo').controller('ModalInstanceCtrl', function($scope, $uibModalInstance, items,$window) {

    $scope.items = items;
    $scope.selected = {
    item: $scope.items
    };

  $scope.cancel = function() {
    $uibModalInstance.dismiss('cancel');
	$window.close()
  };
});


</script>


</body>
</html>

