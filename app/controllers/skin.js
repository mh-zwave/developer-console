/**
 * Skin controller
 * @author Martin Vach
 */

/**
 * Module controller
 */
myAppController.controller('SkinController', function($scope, $location, dataFactory, dataService, paginationService) {
    //Set elements to expand/collapse
    angular.copy({
        addnew: false
    }, $scope.expand);
    $scope.skin = {
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
        dataFactory.getApi('skins', null, true).then(function(response) {
            $scope.skin.data = response.data.data;
        }, function(error) {
        });

    };
    $scope.loadData();
    
     /**
     * Upload module
     */
    $scope.uploadSkin = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
        dataFactory.uploadFile($scope.cfg.api['skincreate'],fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
             $scope.loadData();
             if(response.data.data.id > 0){
                  $location.path('/skins/' +response.data.data.id);
             }
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload skin. "  + error.statusText);
        });
    };
    /**
     * Update skin
     */
    $scope.updateSkin = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('skinupdate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Skin successfully updated.')};
            $scope.loadData();
        }, function(error) {
           $scope.loading = false;
                alertify.alert("Unable to update skin. " + error.statusText);
        });
    };

    /**
     * Delete module
     */
    $scope.deleteSkin = function(id, message) {
        if (!id) {
            return;
        }
        var input = {id: id};
        alertify.confirm(message, function() {
            dataFactory.postApi('skindelete', input).then(function(response) {
                $scope.loadData();
            }, function(error) {
                 alertify.alert("Unable to delete skin");
            });
        }, function() {return;});


    };

});

/**
 * Skin ID controller
 */
myAppController.controller('SkinIdController', function($scope, $routeParams, $route, dataFactory, _) {
    $scope.skin = {
        id: parseInt($routeParams.id),
        data: [],
        tokens: [],
        input: {}
    };
    /**
     * Load data
     */
    $scope.loadData = function() {
        dataFactory.getApi('skin', '/' + $scope.skin.id, true).then(function(response) {
            $scope.skin.data = response.data.data;
            $scope.skin.data.active = parseInt($scope.skin.data.active,10)
            angular.extend($scope.skin.input, $scope.skin.data);

        }, function(error) {
            $scope.loading = false;
                alertify.alert("Unable to load data: " + error.statusText);
        });

    };
    if ($scope.skin.id > 0) {
        $scope.loadData();
    }

    /**
     * Update skin
     */
    $scope.updateSkin = function(form) {
        if (form.$invalid) {
            return;
        }
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('skinupdate', $scope.skin.input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Skin successfully updated.')};
            $scope.loadData();
        }, function(error) {
           $scope.loading = false;
                alertify.alert("Unable to update skin. " + error.statusText);
        });
    };
    
     /**
     * Upload skin
     */
    $scope.uploadSkin = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
         if(files[0].name !== $scope.skin.input.file){
            $scope.loading = false;
            alertify.alert("The uploaded file must be named  " + $scope.skin.input.file); 
            return;
        }
        dataFactory.uploadFile($scope.cfg.api['skinupload'],fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
             $scope.loadData();
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload skin. "  + error.statusText);
        });
    };
    
     /**
     * Upload skin
     */
    $scope.uploadSkinImg = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('file', files[0]);
        fd.append('current', $scope.skin.input.icon);
        fd.append('id', $scope.skin.input.id);
        //return;
        dataFactory.uploadFile($scope.cfg.api['skinimgupload'],fd).then(function(response) {
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
            angular.extend($scope.skin.input,{icon: response.data.data.icon});
           
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload image. "  + error.statusText);
        });
    };


});