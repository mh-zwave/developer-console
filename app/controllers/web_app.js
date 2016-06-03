/**
 * Web apps controller
 * @author Martin Vach
 */

/**
 * Module controller
 */
myAppController.controller('WebAppController', function ($scope, $cookies,cfg,dataFactory, _) {
    $scope.apps = {
        categories: _.indexBy(cfg.categories,'id'),
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
        dataFactory.getApi('modulesweb', null, true).then(function (response) {
            //$scope.apps.all = response.data.data;
            setApps(response.data.data);
        }, function (error) {
        });

    };
    $scope.loadData();
    
     /**
     * Set filter
     */
    $scope.setFilter = function (filter) {
        if (!filter) {
            angular.extend($scope.apps, {filter: {}});
            //$cookies.put('filterApps', angular.toJson({}));
            $cookies.putObject('filterApps',{});
        } else {
            angular.extend($scope.apps, {filter: filter});
            //$cookies.put('filterApps', angular.toJson(filter));
             $cookies.putObject('filterApps',filter);
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
        $scope.loading = false;
    }
    ;


});