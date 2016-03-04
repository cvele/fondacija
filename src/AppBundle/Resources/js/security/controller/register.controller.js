(function() {
    'use strict';

    angular.module('app')
    .controller('RegisterController',
    [
        '$rootScope',
        '$scope',
        'Registration',
        '$location',
        RegisterController
    ]);

    function RegisterController ($rootScope, $scope, Registration, $location) {
        $scope.failed = false;
        $scope.processing = false;

        function registerSuccess() {
            $scope.failed = false;
            $scope.processing = false;

            $location.url('/');
        }

        function registerFailed(errorResponse) {
            $scope.failed = true;
            $scope.processing = false;
        }

        $scope.registerSubmit = function () {
            $scope.processing = true;

            Registration
                .register($scope.realname, $scope.email, $scope.password)
                .then(registerSuccess, registerFailed);
        };

        // user already has account
        if ($rootScope.loggedin) {
            $location.url('/');
        }
    }
})();
