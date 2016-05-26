/**
 * Icon controller
 * @author Martin Vach
 */

/**
 * Icon controller
 */
myAppController.controller('IconController', function($scope, $location, dataFactory, dataService, paginationService) {
    //Set elements to expand/collapse
    angular.copy({
        addnew: false
    }, $scope.expand);
    $scope.icon = {
        data: [],
        input: {
            id: 0
        }
    };
    $scope.currentPage = 1;
    $scope.pageSize = 10;
    /**
     * Watch for pagination change
     */
    $scope.$watch('currentPage', function(page) {
        paginationService.setCurrentPage(page);
    });

    $scope.setCurrentPage = function(val) {
        $scope.currentPage = val;
    };

    /**
     * Load data
     */
    $scope.loadData = function() {
        dataFactory.getApi('icons', null, true).then(function(response) {
            $scope.icon.data = response.data.data;
        }, function(error) {
        });

    };
    $scope.loadData();
    
     /**
     * Upload an icon
     */
    $scope.uploadIcon= function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
        dataFactory.uploadFile($scope.cfg.api['iconcreate'],fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
             $scope.loadData();
             if(response.data.data.id > 0){
                  $location.path('/icons/' +response.data.data.id);
             }
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload icons. "  + error.statusText);
        });
    };
    /**
     * Update an icon
     */
    $scope.updateIcon = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('iconupdate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Icon successfully updated.')};
            $scope.loadData();
        }, function(error) {
           $scope.loading = false;
                alertify.alert("Unable to update icon. " + error.statusText);
        });
    };

    /**
     * Delete an icon
     */
    $scope.deleteIcon = function(id, message) {
        if (!id) {
            return;
        }
        var input = {id: id};
        alertify.confirm(message, function() {
            dataFactory.postApi('icondelete', input).then(function(response) {
                $scope.loadData();
            }, function(error) {
                 alertify.alert("Unable to delete icon");
            });
        }, function() {return;});


    };

});

/**
 * Icon ID controller
 */
myAppController.controller('IconIdController', function($scope, $routeParams, $route, dataFactory, _) {
    $scope.icon = {
        id: parseInt($routeParams.id),
        data: [],
        tokens: [],
        input: {}
    };
    /**
     * Load data
     */
    $scope.loadData = function() {
        dataFactory.getApi('icon', '/' + $scope.icon.id, true).then(function(response) {
            $scope.icon.data = response.data.data;
            $scope.icon.data.active = parseInt($scope.icon.data.active,10);
            angular.extend($scope.icon.input, $scope.icon.data);

        }, function(error) {
            $scope.loading = false;
                alertify.alert("Unable to load data: " + error.statusText);
        });

    };
    if ($scope.icon.id > 0) {
        $scope.loadData();
    }

    /**
     * Update icon
     */
    $scope.updateIcon = function(form) {
        if (form.$invalid) {
            return;
        }
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('iconupdate', $scope.icon.input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Icons successfully updated.')};
            $scope.loadData();
        }, function(error) {
           $scope.loading = false;
                alertify.alert("Unable to update icons. " + error.statusText);
        });
    };
    
     /**
     * Upload icon
     */
    $scope.uploadIcon = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
        dataFactory.uploadFile($scope.cfg.api['iconupload'] +  '/' + $scope.icon.input.name,fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
             $scope.loadData();
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload icon. "  + error.statusText);
        });
    };
    
     /**
     * Upload icon screenshot
     */
    $scope.uploadIconImg = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
        fd.append('current', $scope.icon.input.icon);
        fd.append('id', $scope.icon.input.id);
        //return;
        dataFactory.uploadFile($scope.cfg.api['iconimgupload'],fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
            angular.extend($scope.icon.input,{icon: response.data.data.icon});
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload image. "  + error.statusText);
        });
    };


});