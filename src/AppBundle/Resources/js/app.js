(function() {
    'use strict';

    angular.module('app',
    [
        'ngRoute',
        'ngResource',
        'LocalStorageModule',
        'angular-cache',
        'app.security'
    ])
    .constant('_', window._)
    .constant('CONST', {
        BACKEND_URL         : 'http://w.local/app_dev.php',
        API_URL             : 'http://w.local/app_dev.php/api',
        OAUTH_CLIENT_ID     : '1_6854cbs0cj8csg8w4ckcs8gsgo0o8cw0ww8wgsgk4gk04w88w0',
        OAUTH_CLIENT_SECRET : '43kwonp0vugw00g4o4ccs0wgokk8cskgo8gkowgcso4808cgws'
    })
    .run(function ($rootScope) {
       $rootScope._ = window._;
    })
    ;

})();
