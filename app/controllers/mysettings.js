/**
 * MySettings controller
 * @author Martin Vach
 */

/**
 * MySettings controller
 */
myAppController.controller('MySettingsController', function($scope, $routeParams, dataFactory, dataService, paginationService) {
    $scope.mySettings = {
        id: parseInt($routeParams.id),
        data: [],
        input: {
            company: '',
            first_name: '',
            homepage: '',
            last_name: '',
            mail: ''
        }
    };

    /**
     * Load data
     */
    $scope.loadData = function() {
       dataFactory.getApi('userread', false, true).then(function(response) {
            $scope.mySettings.data = response.data.data;
            angular.extend($scope.mySettings.input, $scope.mySettings.data);
        }, function(error) {
        });

    };
    $scope.loadData();
    
    /**
     * Update module
     */
    $scope.updateUser = function() {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('userupdate', $scope.mySettings.input).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Data successfully updated.')};
            $scope.loadData();
        }, function(error) {
            $scope.loading = false;
                alertify.alert("Unable to update data");
        });
    };

});