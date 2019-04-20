var app = angular.module('app', []);

app.controller('mainCtrl', function($scope, $window) {
  $scope.goServer = function() {
    $window.open('board/server/index.php');
  };


  $scope.goClient = function() {
    $window.open('board/client/index.php');
  };
});
