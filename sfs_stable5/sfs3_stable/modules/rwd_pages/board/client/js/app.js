 'use strict';
var app = angular.module('myApp',['ngRoute','ngSanitize','ngAnimate','ui.bootstrap']);

app.run(function($rootScope){
	$rootScope.msg = encodeURIComponent("This is main part.");
	//console.log($rootScope.msg);
});


app.config(function($routeProvider){

	$routeProvider
		.when('/page', {
			controller: 'mainCtrl',
			templateUrl: 'partials/page.html'
		})

		
		.when('/page/:page', {
			controller: 'mainCtrl',
			templateUrl: 'partials/page.html'
		})


		.when('/page/:page/article/:articleId', {
			controller: 'articleCtrl',
			templateUrl: 'partials/article.html'
		})



		.otherwise({
        redirectTo: '/page'
    });

});

app.controller('mainCtrl', function($scope,$http,$routeParams,$uibModal, $log) {
		var self = this;
		$scope.main = {
			page: 0,
			pages: 10
		};
		//console.log($routeParams.page);

		self.say = function($scope){
			 console.log("say Hello, world");
			 return "hello";
		}
		//self.say();
		//console.log($scope.main.page);
		//console.log($scope.jsonData);
		self.$routeParams = $routeParams;

		self.loadPage = function(page){	
			//console.log("current page: "+page);
			var str = '{"page":'+page+'}';
			var parameters = encodeURIComponent(str);
			//console.log(str);
			//console.log(parameters);
			$http({
			method: 'GET',
			params: {
				"page": parameters
			},

			//$scope.jsonData= "json/board"+$scope.main.page+".json";
			//url:jsonDataPath
			url:'page.php'
			}).then(function successCallback(response) {
				//$scope.articles = response.data.records;
				$scope.articles = rawUrlDecode(response.data.records);
				//console.log($scope.articles);
				//console.log(response.data);
			}, function errorCallback(response) {
				console.log("error");
				//alert("Something went error");
				// or server returns response with an error status.
			});

		};
		
		if ($routeParams.page>0) {
			//console.log($routeParams.page);
      $scope.main.page = $routeParams.page ;
			self.loadPage($scope.main.page) ;
		}else{
			self.loadPage($scope.main.page) ;
		}
		

		$scope.nextPage = function() {
			if ($scope.main.page < $scope.main.pages) {
				$scope.main.page++;
				self.loadPage($scope.main.page) ;
				//console.log($scope.main.page);
			}
		};

		$scope.previousPage = function() {
			if ($scope.main.page > 0) {
				$scope.main.page--;
				self.loadPage($scope.main.page) ;
				//console.log($scope.main.page);
			}
		};


		self.zip = function() {
			console.log("zip");
			$http({
				method: 'GET',
				url: 'zipFile.php'
			}).then(function successCallback(response) {
				console.log(response.data);
			  $scope.items = response.data;
					// this callback will be called asynchronously
					// when the response is available
				}, function errorCallback(response) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
				});
		
		}

		self.zip();
    
	  $scope.open = function(size) {
			console.log("open");

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
 


		}


});

app.controller('articleCtrl',function($scope,$http,$routeParams){
  $scope.firstName= "Mary";
  $scope.ctrlName= "articleCtrl";
  $scope.$routeParams = $routeParams;
  $scope.articleId = $scope.$routeParams.articleId;
  $scope.currentPage = $scope.$routeParams.page;
	//console.log($scope.$routeParams.page);
	//console.log($scope.articleId);
  //console.log($scope.currentPage);	
	var str = '{"page":'+$scope.currentPage+',"articleId":'+$scope.articleId+'}';
	console.log(str);
  var parameters = encodeURIComponent(str);
	//var jsonDataPath = "json/board"+$scope.currentPage+".json";

    $http({
      method: 'GET',
      params: {
	"page": parameters
      },

      url: 'page.php'
		
  }).then(function successCallback(response) {
      //console.log("looks good");
      //$scope.articles = response.data.records;
      $scope.articles = rawUrlDecode(response.data.records);
    //console.log($scope.articles);
    }, function errorCallback(response) {
      console.log("error");
			//alert("Something went error");
      // called asynchronously if an error occurs
      // or server returns response with an error status.
    });



});

function rawUrlDecode(jsonObj){
  //console.log(jsonObj instanceof Object);
  var key;
  if (jsonObj instanceof Object) {
    for (key in jsonObj){
      if (jsonObj.hasOwnProperty(key)){
	//recursive call to scan property
	jsonObj[key] = rawUrlDecode(jsonObj[key]);  
      }                
    }
  } else {
    //console.log(decodeURIComponent(jsonObj));
    return decodeURIComponent(jsonObj);
  };

  return jsonObj;
}

/*
app.filter('decodeURIComponent', function($window) {
    return  $window.decodeURIComponent;
});
*/



angular.module('myApp').controller('ModalInstanceCtrl', function($scope, $uibModalInstance, items,$window) {

     $scope.items = items;
     $scope.selected = {
       item: $scope.items
     };

   $scope.cancel = function() {
     $uibModalInstance.dismiss('cancel');
     $window.close()
   };

 });




