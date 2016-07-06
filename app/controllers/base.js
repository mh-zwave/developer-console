/**
 * Application base controller
 * @author Martin Vach
 */

/*** Controllers ***/
var myAppController = angular.module('myAppController', ['ui.bootstrap','hljs','ngRoute']);
/**
 * Base controller
 */
myAppController.controller('BaseController', function ($scope, $cookies, $location, cfg, dataFactory, dataService) {
    /**
     * Global scopes
     */
    $scope.cfg = cfg;
    $scope.loading = false;
    $scope.alert = {message: false, status: 'is-hidden', icon: false};
    $scope.user = {};

    dataFactory.getApi('usersession', false, true).then(function (response) {
        angular.extend($scope.user, response.data);
    }, function (error) {});

    /**
     * Language settings
     */
    $scope.lang_list = cfg.lang_list;
    // Set language
    //$scope.lang = cfg.lang;
    $scope.getLang = function () {
        if ($scope.user) {
            $scope.lang = $scope.user.lang;
        } else {
            $scope.lang = angular.isDefined($cookies.lang) ? $cookies.lang : cfg.lang;
        }
    };
    $scope.getLang();
    $cookies.lang = $scope.lang;

    // Load language files
    $scope.loadLang = function (lang) {
        // Is lang in language list?
        var lang = (cfg.lang_list.indexOf(lang) > -1 ? lang : cfg.lang);
        dataFactory.getLanguageFile(lang).then(function (response) {
            $scope.languages = response.data;
        }, function (error) {});
    };
    // Get language lines
    $scope._t = function (key) {
        return dataService.getLangLine(key, $scope.languages);
    };

    // Watch for lang change
    $scope.$watch('lang', function () {
        $scope.loadLang($scope.lang);
    });

    // Order by
    $scope.orderBy = function (field) {
        $scope.predicate = field;
        $scope.reverse = !$scope.reverse;
    };

    /**
     * Get body ID
     */
    $scope.getBodyId = function () {
        var path = $location.path().split('/');
        return path[1] || 'home';

    };
    /**
     *
     * Mobile detect
     */
    $scope.isMobile = dataService.isMobile(navigator.userAgent || navigator.vendor || window.opera);
    /*
     * Menu active class
     */
    $scope.isActive = function (route) {
        return (route === $scope.getBodyId() ? 'active' : '');
    };

    /**
     * Expand/collapse navigation
     */
    $scope.naviExpanded = {};
    $scope.expandNavi = function (key,event,status) {
        if (typeof status === 'boolean') {
            $scope.naviExpanded[key] = status;
        } else {
            $scope.naviExpanded[key] = !($scope.naviExpanded[key]);
        }

        event.stopPropagation();
    };
    // Collaps element/menu when clicking outside
    window.onclick = function () {
        if ($scope.naviExpanded) {
            angular.copy({}, $scope.naviExpanded);
            $scope.$apply();
        }
    };
    /**
     * Expand/collapse element
     */
    $scope.expand = {};
    $scope.expandElement = function (key) {
        $scope.expand[key] = !($scope.expand[key]);
    };
    
    $scope.modalArr = {};
    /**
     * Open/close a modal window
     * @param {string} key
     * @param {object} $event
     * @param {boolean} status
     * @returns {undefined}
     */
    $scope.handleModal = function (key, $event, status) {
        if (typeof status === 'boolean') {
            $scope.modalArr[key] = status;
        } else {
            $scope.modalArr[key] = !($scope.modalArr[key]);
        }

        $event.stopPropagation();
    };
    $scope.expand = {};


    /**
     * Alertify defaults
     */
    alertify.defaults.glossary.title = cfg.app_name;
    alertify.defaults.glossary.ok = 'OK';
    alertify.defaults.glossary.cancel = 'CANCEL';

});
