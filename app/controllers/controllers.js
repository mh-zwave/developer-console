/**
 * Application controllers
 * @author Martin Vach
 */

/**
 * Error controller
 */
myAppController.controller('ErrorController', function ($scope, $routeParams, dataService) {
    $scope.errorCfg = {
        code: false,
        icon: 'fa-warning'
    };
    /**
     * Load error
     */
    $scope.loadError = function (code) {
        if (code) {
            $scope.errorCfg.code = code;
        } else {
            $scope.errorCfg.code = 0;
        }

    };
    $scope.loadError($routeParams.code);

});

myAppController.controller('helpCtrl', function ($scope, $window) {

    $scope.status = {
        isFirstOpen: true,
        isFirstDisabled: false
    };
    $scope.topics = [
        {
            "id": 1,
            "name": "Theory",
            "url": 'app/views/help/theory.html'
        },
        {
            "id": 2,
            "name": "Structure",
            "url": 'app/views/help/structure.html'
        },
        {
            "id": 3,
            "name": "Operation Logic",
            "url": 'app/views/help/operationLogic.html'
        },
        {
            "id": 4,
            "name": "Interface Logic",
            "url": 'app/views/help/interfaceLogic.html'
        },
        {
            "id": 5,
            "name": "Stuff",
            "url": 'app/views/help/stuff.html'
        },
        {
            "id": 6,
            "name": "Upload-Process",
            "url": 'app/views/help/uploadProcess.html'
        }
    ];

    $scope.model = {
        name: 'Topics'
    };

    $scope.setActive = function (id) {
        $scope.active = id;
    };





});

myAppController.controller('SideCtrl', function ($scope) {

    $scope.status = {
        isFirstOpen: true,
        isFirstDisabled: false
    };

    $scope.getSnippetLocation = function (id) {
        return "app/views/help/snippets/" + id + ".html";
    };
});

myAppController.config(function (hljsServiceProvider) {
    hljsServiceProvider.setOptions({
        // replace tab with 4 spaces
        tabReplace: '    '

    });
});