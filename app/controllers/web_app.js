/**
 * Web apps controller
 * @author Martin Vach
 */

/**
 * Web app controller
 */
myAppController.controller('WebAppController', function ($scope, $cookies, cfg, dataFactory, _) {
    $scope.apps = {
        categories: _.indexBy(cfg.categories, 'id'),
        show: true,
        //all: {},
        find: {},
        ratingRange: [1, 2, 3, 4, 5],
        //filter: ($cookies.get('filterApps') ? angular.fromJson($cookies.get('filterApps')) : {}),
        filter: ($cookies.getObject('filterApps') ? $cookies.getObject('filterApps') : {}),
        orderBy: ($cookies.get('orderByApps') ? $cookies.get('orderByApps') : 'titleASC'),
        cnt: {
            apps: 0,
            collection: 0,
            appsCat: 0,
            featured: 0
        }
    };

    /**
     * Load data
     */
    $scope.loadData = function () {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('loading')};
        dataFactory.getApi('modulesweb', null, true).then(function (response) {
        $scope.loading = false;
            setApps(response.data.data);
        }, function (error) {
            $scope.loading = false;
             $scope.apps.show = false;
        });

    };
    $scope.loadData();

    /**
     * Set filter
     */
    $scope.setFilter = function (filter) {
        if (!filter) {
            angular.extend($scope.apps, {filter: {}});
            $cookies.putObject('filterApps', {});
        } else {
            angular.extend($scope.apps, {filter: filter});
            $cookies.putObject('filterApps', filter);
        }
        $scope.loadData();
    };

    /**
     * Set order by
     */
    $scope.setOrderBy = function (key) {
        angular.extend($scope.apps, {orderBy: key});
        $cookies.put('orderByApps', key);
        $scope.loadData();
    };

    /// --- Private functions --- ///

    /**
     * Set apps
     */
    function setApps(data) {
        // Reset featured cnt
        $scope.apps.cnt.featured = 0;
        var onlineModules = _.chain(data)
                .flatten()
                .filter(function (v) {
                    if (v.featured == 1) {
                        $scope.apps.cnt.featured += 1;
                    }
                    v.category_name = ($scope.apps.categories[v.category] ? $scope.apps.categories[v.category].name : 'Undefined');
                    return v;
                });
        // Count apps in categories
        $scope.apps.cnt.appsCat = onlineModules.countBy(function (v) {
            return v.category;
        }).value();

        // Count all apps
        $scope.apps.cnt.apps = onlineModules.size().value();
        $scope.apps.all = onlineModules.where($scope.apps.filter).value();
        // Count collection
        $scope.apps.cnt.collection = _.size($scope.apps.all);
    }
    ;


});

/**
 * Web app controller
 */
myAppController.controller('WebAppIdController', function ($scope, $routeParams, cfg, dataFactory, _) {
    $scope.apps = {
        categories: _.indexBy(cfg.categories, 'id'),
        show: true,
        //all: {},
        find: {},
        ratingRange: [1, 2, 3, 4, 5]
    };

    /**
     * Load data
     */
    $scope.loadData = function () {
        $scope.loading = {status: 'loading-spin', icon: 'fa-spinner fa-spin', message: $scope._t('loading')};
        dataFactory.getApi('modulesidweb', '/' + parseInt($routeParams.id, 10), true).then(function (response) {
            $scope.loading = false;
             response.data.data.category_name = ($scope.apps.categories[response.data.data.category] ? $scope.apps.categories[response.data.data.category].name : 'Undefined');
            $scope.apps.find = response.data.data;
        }, function (error) {
            $scope.loading = false;
            $scope.apps.show = false;
            alertify.alert("Unable to load data: " + error.statusText);
        });

    };
    $scope.loadData();


});