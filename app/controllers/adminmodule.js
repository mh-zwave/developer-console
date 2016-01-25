/**
 * Admin module controller
 * @author Martin Vach
 */

/**
 * Module controller
 */
myAppController.controller('AdminModuleController', function($scope, $window, dataFactory, dataService, paginationService) {
    //Set elements to expand/collapse
    angular.copy({
        addnew: false
    }, $scope.expand);
    $scope.module = {
        data: [],
        input: {
            id: 0
        }
    };
    $scope.currentPage = 1;
     $scope.pageSize = $scope.cfg.page_results;
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
        dataFactory.getApi('adminmodules', null, true).then(function(response) {
            $scope.module.data = response.data.data;
        }, function(error) {
        });

    };
    $scope.loadData();
    
     /**
     * Update module
     */
    $scope.updateModule = function(input) {
//        var input = {
//            id: id,
//            verifed: verifed
//        };
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('moduleupdate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Module successfully updated.')};
            $scope.loadData();
        }, function(error) {
           $scope.loading = false;
           alertify.alert("Unable to update module: " + error.statusText);
        });


    };
    
    /**
     * Update module
     */
    $scope.verifiModule = function(id,verifed) {
        var input = {
            id: id,
            verifed: verifed
        };
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
            dataFactory.postApi('adminmoduleverify', input).then(function(response) {
                $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Module successfully updated')};
                $scope.loadData();
            }, function(error) {
                $scope.loading = false;
                alertify.alert("Unable to update the module");
            });;


    };
    
     /**
     * Upload module
     */
    $scope.uploadModule = function(files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('fileToUpload', files[0]);
        dataFactory.uploadModule(fd).then(function(response) {
             //alertify.alert("Unable to upload module");
             $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
            $scope.loadData();
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to upload module");
        });
    };

    /**
     * Delete module
     */
    $scope.deleteModule = function(id, message) {
        if (!id) {
            return;
        }
        var input = {id: id};
        alertify.confirm(message, function() {
            dataFactory.postApi('moduledelete', input).then(function(response) {
                $scope.loadData();
            }, function(error) {
            });
        }, function() {
            return;
        });


    };
});

/**
 * Module ID controller
 */
myAppController.controller('AdminModuleIdController', function($scope, $routeParams, $route, dataFactory, _) {
    $scope.module = {
        id: parseInt($routeParams.id),
        data: [],
        input: {
            body: '',
            subject: '',
            user_id: 0,
            id: parseInt($routeParams.id)
        }
    };

    /**
     * Load data
     */
    $scope.loadData = function() {
        dataFactory.getApi('module', '/' + $scope.module.id, true).then(function(response) {
            $scope.module.data = response.data.data;
             $scope.module.input.subject = response.data.data.title;
              $scope.module.input.user_id =  parseInt(response.data.data.user_id);
            //angular.extend($scope.module.input, $scope.module.data);

        }, function(error) {
        });

    };
    if ($scope.module.id > 0) {
        $scope.loadData();
    }

    /**
     * Send email
     */
    $scope.sendEmail = function() {
         $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Sending an email...')};
        console.log($scope.module.input)
        dataFactory.postApi('adminmoduleemail', $scope.module.input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Email successfully sent')};
            $scope.loadData();
        }, function(error) {
            $scope.loading = false;
             alertify.alert("Unable to send email");
        });
    };


});