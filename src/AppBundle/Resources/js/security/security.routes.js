(function() {
    'use strict';

    angular.module('app')

    .config(function($routeProvider, $locationProvider) {
      $routeProvider
       .when('/login', {
        templateUrl: '/frontend/login.html',
        controller: 'LoginController',
      });

      // $locationProvider.html5Mode(true);
    });
})();
