(function() {
    'use strict';

    angular.module('app')
    .controller('LoginController',
    [
        '$scope',
        '$rootScope',
        'Authentication',
        '$location',
        LoginController
    ]);

    function LoginController ($scope, $rootScope, Authentication, $location) {
        $scope.clearLoginForm = function () {
            $scope.failed = true;
            $scope.processing = false;
            $scope.email = '';
            $scope.password = '';
        };

        $scope.finishedLogin = function () {
            $scope.failed = false;
            $scope.processing = false;
        };

        $scope.loginSubmit = function () {
            $scope.processing = true;

            Authentication
                .login($scope.email, $scope.password)
                .then($scope.finishedLogin, $scope.clearLoginForm);
        };

        // If we're already logged in
        if ($rootScope.loggedin) {
            $location.url('/');
        }

        $scope.finishedLogin();
    }
})();
