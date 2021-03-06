/**
 * Application base
 * @author Martin Vach
 */

//Define an angular module for our app
var myApp = angular.module('myApp', [
    'ngRoute',
    'ngCookies',
    'myAppConfig',
    'myAppController',
    'myAppFactory',
    'myAppService'

]);

//Define Routing for app
myApp.config(['$routeProvider', function($routeProvider) {
        var cfg = config_data.cfg;
        $routeProvider.
                // Home
                when('/', {
                    requireLogin: true,
                    templateUrl: 'app/views/modules/modules.html',
                }).
                //Module ID
                when('/modules/:id/:author?', {
                    templateUrl: 'app/views/modules/modules_id.html',
                    requireLogin: true
                }).
                 // skins
                when('/skins', {
                    requireLogin: true,
                    templateUrl: 'app/views/skins/skins.html',
                }).
                //Skin ID
                when('/skins/:id', {
                    templateUrl: 'app/views/skins/skins_id.html',
                    requireLogin: true
                }).
                // Icons
                when('/icons', {
                    requireLogin: true,
                    templateUrl: 'app/views/icons/icons.html',
                }).
                //Icon ID
                when('/icons/:id', {
                    templateUrl: 'app/views/icons/icons_id.html',
                    requireLogin: true
                }).
                //My settings
                when('/mysettings', {
                    templateUrl: 'app/views/mysettings/mysettings.html',
                    requireLogin: true
                }).
                 //Help
                when('/help', {
                    templateUrl: 'views/help.php',
                    requireLogin: true
                }).
                //Login
                when('/logout', {
                    templateUrl: 'app/views/auth/logout.html',
                    requireLogin: true
                }).
                //Admin modules
                when('/admin-modules', {
                    templateUrl: 'app/views/admin/modules.html',
                    requireLogin: true
                }).
                //Admin module ID
                when('/admin-modules/:id', {
                    templateUrl: 'app/views/admin/modules_id.html',
                    requireLogin: true
                }).
                //Admin users
                when('/admin-users', {
                    templateUrl: 'app/views/admin/users.html',
                    requireLogin: true
                }).
                //Admin user ID
                when('/admin-users/:id', {
                    templateUrl: 'app/views/admin/users_id.html',
                    requireLogin: true
                }).
                //Public spps
                when('/web/apps', {
                    templateUrl: 'app/views/web/apps.html',
                    requireLogin: true
                }).
                //Public spps
                when('/web/apps/:id', {
                    templateUrl: 'app/views/web/apps_id.html',
                    requireLogin: true
                }).
                // Error page
                when('/error/:code?', {
                    templateUrl: 'app/views/error.html'
                }).
                otherwise({
                    redirectTo: '/error/404'
                });
    }]);

/**
 * App configuration
 */

var config_module = angular.module('myAppConfig', []);

angular.forEach(config_data, function(key, value) {
    config_module.constant(value, key);
});

/**
 * Angular run function
 * @function run
 */
myApp.run(function ($rootScope, $location, dataService, cfg) {
    // Run underscore js in views
    $rootScope._ = _;
    
});

// Intercepting HTTP calls with AngularJS.
myApp.config(function($provide, $httpProvider) {
    $httpProvider.defaults.timeout = 5000; 
    // Intercept http calls.
    $provide.factory('MyHttpInterceptor', function($q,$location,dataService) {
         var path = $location.path().split('/');
        return {
            // On request success
            request: function(config) {
                // Return the config or wrap it in a promise if blank.
                return config || $q.when(config);
            },
            // On request failure
            requestError: function(rejection) {
                // Return the promise rejection.
                return $q.reject(rejection);
            },
            // On response success
            response: function(response) {
                // Return the response or promise.
                return response || $q.when(response);
            },
            // On response failture
            responseError: function(rejection) {
                //dataService.logError(rejection);
               if(rejection.status == 401){
                   if (path[1] !== 'web') {
                        window.location.href='?uri=logout';

                    }
                   return $q.reject(rejection);
                    
                }else{
                    // Return the promise rejection.
                    return $q.reject(rejection);
                }
                //
                
            }
        };
    });

    // Add the interceptor to the $httpProvider.
    $httpProvider.interceptors.push('MyHttpInterceptor');

});


