/**
 * Application controllers
 * @author Martin Vach
 */

/**
 * Error controller
 */
myAppController.controller('ErrorController', function($scope, $routeParams, dataService) {
    $scope.errorCfg = {
        code: false,
        icon: 'fa-warning'
    };
    /**
     * Load error
     */
    $scope.loadError = function(code) {
        if (code) {
            $scope.errorCfg.code = code;
        } else {
            $scope.errorCfg.code = 0;
        }

    };
    $scope.loadError($routeParams.code);

});

