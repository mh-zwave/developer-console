/**
 * Module controller
 * @author Martin Vach
 */

/**
 * Module controller
 */
myAppController.controller('ModuleController', function ($scope, $window, dataFactory, dataService, paginationService) {
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
    $scope.pageSize = 10;
    /**
     * Watch for pagination change
     */
    $scope.$watch('currentPage', function (page) {
        paginationService.setCurrentPage(page);
    });

    $scope.setCurrentPage = function (val) {
        $scope.currentPage = val;
    };

    /**
     * Load data
     */
    $scope.loadData = function () {
        dataFactory.getApi('modules', null, true).then(function (response) {
            $scope.module.data = response.data.data;
        }, function (error) {
        });

    };
    $scope.loadData();

    /**
     * Upload module
     */
    $scope.uploadModule = function (files) {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('uploading')};
        var fd = new FormData();
        fd.append('fileToUpload', files[0]);
        dataFactory.uploadModule(fd).then(function (response) {
            dataFactory.postApi('tokencreate',{module_id:  response.data.data.id,token: $scope.cfg.default_token}).then(function (response) {});
            //alertify.alert("Unable to upload module");
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('success_upload')};
            $scope.expand.addnew = false;
            $scope.loadData();
        }, function (error) {
            $scope.loading = false;
            alertify.alert('An error has occurred! ' + error.statusText || '');
        });
    };

    /**
     * Delete module
     */
    $scope.deleteModule = function (id, message) {
        if (!id) {
            return;
        }
        var input = {id: id};
        alertify.confirm(message, function () {
            dataFactory.postApi('moduledelete', input).then(function (response) {
                $scope.loadData();
            }, function (error) {
            });
        }, function () {
            return;
        });


    };

    /**
     * Verrification request
     */
    $scope.verifiModule = function (module) {
        var input = {
            id: module.id,
            title: module.title,
            author: module.author
        };
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Sending a request...')};
        dataFactory.postApi('moduleverify', input).then(function (response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Request successfully sent.')};
            $scope.loadData();
        }, function (error) {
            $scope.loading = false;
            alertify.alert("Unable to send the verification request");
        });
        ;


    };

});

/**
 * Module ID controller
 */
myAppController.controller('ModuleIdController', function ($scope, $routeParams, $route, dataFactory, _) {
    $scope.module = {
        id: parseInt($routeParams.id),
        data: [],
        tokens: [],
        input: {
            author: '',
            category: '',
            contributed: '',
            description: '',
            patchnotes: '',
            detail_images: '',
            homepage: '',
            icon: '',
            id: 0,
            last_updated: '',
            maturity: '',
            modulename: '',
            title: '',
            user_id: 0,
            verified: 0,
            version: ''
        }
    };
    $scope.token = {
        data: [],
        input: {
            //id: 0,
            module_id: $scope.module.id,
            token: ''
        }
    };
    $scope.comments = {
        all: {},
        model: {
            module_id: parseInt($routeParams.id, 10),
            content: '',
            name: '',
            remote_id: '',
            type: 1
        },
        show: true
    };
     $scope.archive = {
        all: {}
    };
    /**
     * Load data
     */
    $scope.loadData = function () {
        if($routeParams.author){
             dataFactory.postApi('commentnotnew', {module_id: $scope.module.id}).then(function (response) {
        }, function (error) {});
            console.log('Updating comments...')
        }
        
        dataFactory.getApi('module', '/' + $scope.module.id, true).then(function (response) {
            $scope.module.data = response.data.data;
            $scope.comments.model.name = $scope.module.data.author;
            angular.extend($scope.module.input, $scope.module.data);

        }, function (error) {
            $scope.loading = false;
            alertify.alert("Unable to load module: " + error.statusText);
        });

    };
    if ($scope.module.id > 0) {
        $scope.loadData();
    }

    /**
     * Update module
     */
    $scope.updateModule = function () {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('Updating...')};
        dataFactory.postApi('moduleupdate', $scope.module.input).then(function (response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('Module successfully updated.')};
            $scope.loadData();
        }, function (error) {
            $scope.loading = false;
            alertify.alert("Unable to update module: " + error.statusText);
        });
    };


    /**
     * Load tokens
     */
    $scope.loadTokens = function () {
        dataFactory.getApi('tokens', '/' + $scope.module.id, true).then(function (response) {
            $scope.token.data = response.data.data;
        }, function (error) {
        });

    };
    $scope.loadTokens();

    /**
     * Add token
     */
    $scope.addToken = function () {
        if ($scope.token.input.token === '') {
            return;
        }
        dataFactory.postApi('tokencreate', $scope.token.input).then(function (response) {
            $scope.token.input.token = '';
            $scope.loadTokens();
        }, function (error) {
        });
    };

    /**
     * Delete token
     */
    $scope.deleteToken = function (id, message) {
        if (!id) {
            return;
        }
        var input = {id: id, module_id: $scope.module.id};
        alertify.confirm(message, function () {
            dataFactory.postApi('tokendelete', input).then(function (response) {
                $scope.loadTokens();
            }, function (error) {
                $scope.loading = false;
                alertify.alert("Unable to delete token");
            });
        }, function () {
            return;
        });

    };

    /**
     * Load comments
     */
    $scope.loadComments = function () {
        dataFactory.getApi('comments', '/' + $scope.module.id, true).then(function (response) {
            $scope.comments.all = response.data.data;
            if (_.isEmpty(response.data.data)) {
                $scope.comments.show = false;
            } else {
                $scope.comments.show = true;
            }

        }, function (error) {
            $scope.loading = false;
            alertify.alert("Unable to load comments: " + error.statusText);
        });

    };
    $scope.loadComments();

    /**
     * Add comment
     */
    $scope.addComment = function (form, input) {
        if (form.$invalid) {
            return;
        }
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('updating')};
        dataFactory.postApi('commentscreate', input).then(function (response) {
            $scope.loading = {status: 'loading-fade', icon: 'fa-check text-success', message: $scope._t('New comment created')};
            $scope.expand.appcommentadd = false;
            $scope.loadComments();
            input.content = '';

        }, function (error) {
            $scope.loading = false;
            alertify.alert('Unable to create a comment');

        });

    };

    /**
     * Delete comment
     */
    $scope.deleteComment = function (id, message) {
        if (!id) {
            return;
        }
        var input = {id: id, module_id: $scope.module.id};
        alertify.confirm(message, function () {
            dataFactory.postApi('commentdelete', input).then(function (response) {
                $scope.loadComments();
            }, function (error) {
                $scope.loading = false;
                alertify.alert("Unable to delete comment");
            });
        }, function () {
            return;
        });

    };
    
     /**
     * Load archive
     */
    $scope.loadArchive = function () {
        dataFactory.getApi('archive', '/' + $scope.module.id, true).then(function (response) {
             $scope.archive.all = response.data.data;

        }, function (error) {
            $scope.loading = false;
            alertify.alert("Unable to load an archive: " + error.statusText);
        });

    };
    $scope.loadArchive();


});