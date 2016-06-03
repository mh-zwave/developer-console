/**
 * Web apps controller
 * @author Martin Vach
 */

/**
 * Module controller
 */
myAppController.controller('WebAppController', function($scope, $location, dataFactory, dataService, paginationService) {
    $scope.apps = {
        all:{},
        find: {},
        ratingRange: [1,2,3,4,5]
    };
    
    /**
     * Load data
     */
    $scope.loadData = function () {
        dataFactory.getApi('modulesweb', null, true).then(function (response) {
            $scope.apps.all = response.data.data;
        }, function (error) {
        });

    };
    $scope.loadData();


});