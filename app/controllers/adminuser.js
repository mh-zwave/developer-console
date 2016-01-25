/**
 * Admin user controller
 * @author Martin Vach
 */

/**
 * User controller
 */
myAppController.controller('AdminUserController', function($scope, $location, dataFactory, dataService, paginationService) {
    //Set elements to expand/collapse
    angular.copy({
        addnew: false
    }, $scope.expand);
    $scope.users = {
        data: [],
        model: {
            mail: null,
            pw: null
        },
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
    $scope.loadUsers= function() {
        dataFactory.getApi('adminusers', null, true).then(function(response) {
            $scope.users.data = response.data.data;
        }, function(error) {
        });

    };
    $scope.loadUsers();
    
     /**
     * Update user
     */
    $scope.updateUser = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('userupdate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Data successfully updated.')};
            $scope.loadUsers();
        }, function(error) {
           $scope.loading = false;
           alertify.alert("Unable to update data: " + error.statusText);
        });


    };
    
    /**
     * Create user
     */
    $scope.createUser = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('adminusercreate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Data successfully updated.')};
            if(response.data.data.id){
                  $location.path('/admin-users/' +response.data.data.id);
             }
            $scope.loadUsers();
        }, function(error) {
           $scope.loading = false;
           alertify.alert("Unable to create an user! " + error.statusText);
        });


    };

    /**
     * Delete user
     */
    $scope.deleteUser = function(id, message) {
        if (!id) {
            return;
        }
        var input = {id: id};
        alertify.confirm(message, function() {
            dataFactory.postApi('adminuserdelete', input).then(function(response) {
               $scope.loadUsers();
            }, function(error) {
            });
        }, function() {
            return;
        });


    };
});

/**
 * User ID controller
 */
myAppController.controller('AdminUserIdController', function($scope, $routeParams, $route, dataFactory, _) {
   $scope.users = {
       input: {},
        password: {
            id: $routeParams.id,
            pw: null
        }
    };

    /**
     * Load user
     */
    $scope.loadUser = function() {
        dataFactory.getApi('adminuserread', '/' + $routeParams.id, true).then(function(response) {
            $scope.users.input = response.data.data;

        }, function(error) {
        });

    };
    if ($routeParams.id > 0) {
        $scope.loadUser();
    }
    
     /**
     * Update user
     */
    $scope.updateUser = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('userupdate', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Data successfully updated.')};
            $scope.loadUser();
        }, function(error) {
           $scope.loading = false;
           alertify.alert("Unable to update data! " + error.statusText);
        });


    };
    
    /**
     * Reset password
     */
    $scope.resetPassword = function(input) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('adminresetpassword', input).then(function(response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Data successfully updated.')};
            $scope.loadUser();
        }, function(error) {
           $scope.loading = false;
           alertify.alert("Unable to reset a password! " + error.statusText);
        });


    };


});