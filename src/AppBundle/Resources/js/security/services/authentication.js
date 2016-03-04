(function() {
    'use strict';

    angular.module('app')
    .factory('Authentication', [
        '$rootScope',
        '$http',
        '$q',
        'Util',
        'CONST',
        'Session',
        'UserEndpoint',
        Authentication
      ]);

    function Authentication ($rootScope,$http,$q,Util,CONST,Session,UserEndpoint) {
        // check whether we have initially an old access_token and userId
        // and assume that, if yes, we are still loggedin
        var loginStatus = !!Session.getSessionDataEntry('accessToken') && !!Session.getSessionDataEntry('userId'),


        setToLoginState = function (userData) {
            Session.setSessionDataEntries({
                userId: userData.id,
                username: userData.username,
                email: userData.email
            });

            loginStatus = true;
        },

        setToLogoutState = function () {
            Session.clearSessionData();
            loginStatus = false;
        };

        return {

            login: function (username, password) {
                var payload = {
                    username: username,
                    password: password,
                    grant_type: 'password',
                    client_id: CONST.OAUTH_CLIENT_ID,
                    client_secret: CONST.OAUTH_CLIENT_SECRET
                },

                deferred = $q.defer(),

                handleRequestError = function () {
                    deferred.reject();
                    setToLogoutState();
                    $rootScope.$broadcast('event:authentication:login:failed');
                },

                handleRequestSuccess = function (authResponse) {
                    var accessToken = authResponse.data.access_token;
                    Session.setSessionDataEntry('accessToken', accessToken);

                    $http.get(Util.apiUrl('/users/me')).then(
                    function (userDataResponse) {
                      setToLoginState(userDataResponse.data);
                      $rootScope.$broadcast('event:authentication:login:succeeded');
                      deferred.resolve();
                    }, handleRequestError);
                };

                $http.post(Util.url('/oauth/v2/token'), payload).then(handleRequestSuccess, handleRequestError);

                return deferred.promise;
            },

            logout: function (silent) {
                //TODO: ASK THE BACKEND TO DESTROY SESSION

                setToLogoutState();
                if (!silent) {
                    $rootScope.$broadcast('event:authentication:logout:succeeded');
                }
            },

            getLoginStatus: function () {
                return loginStatus;
            }
        };

    }
})();
