/**
 * Application factories
 * @author Martin Vach
 */
var myAppFactory = angular.module('myAppFactory', []);

/**
 * Caching the river...
 */
myAppFactory.factory('myCache', function($cacheFactory) {
    return $cacheFactory('myData');
});
/**
 * Underscore
 */
myAppFactory.factory('_', function() {
        return window._; // assumes underscore has already been loaded on the page
});

/**
 * Main data factory
 */
myAppFactory.factory('dataFactory', function($http,$q, myCache, cfg) {
    return({
        getApiLocal: getApiLocal,
        getApi: getApi,
        postApi: postApi,
        getRemoteData: getRemoteData,
        uploadModule: uploadModule,
        uploadFile: uploadFile,
        getLanguageFile: getLanguageFile,
        getHelp:getHelp
    });

    /// --- Public functions --- ///

    /**
     * Gets api local data
     */
    function getApiLocal(file) {
        return $http({
            method: 'get',
            url: cfg.local_data_url + file
        }).then(function(response) {
            if (typeof response.data === 'object') {
                return response;
            } else {// invalid response
                return $q.reject(response);
            }
        }, function(response) {// something went wrong
            return $q.reject(response);
        });

    }

    /**
     * API data
     */
    // Get api data
    function getApi(api, params, noCache) {
        // Cached data
        var cacheName = api + (params || '');
        var cached = myCache.get(cacheName);

        if (!noCache && cached) {
            var deferred = $q.defer();
            deferred.resolve(cached);
            return deferred.promise;
        }
        return $http({
            method: 'get',
            url: cfg.server_url + cfg.api[api] + (params ? params : '')
        }).then(function(response) {
            if (!angular.isDefined(response.data)) {
                return $q.reject(response);
            }
            if (typeof response.data === 'object') {
                myCache.put(cacheName, response);
                return response;
            } else {// invalid response
                return $q.reject(response);
            }

        }, function(response) {// something went wrong
            return $q.reject(response);
        });
    }
    // Post api data
    function postApi(api, data, params) {
        return $http({
            method: "post",
            //data: data,
            url: cfg.server_url + cfg.api[api] + (params ? params : ''),
            data: $.param(data),
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
                        //'ZWAYSession': ZWAYSession 
            }
        }).then(function(response) {
            return response;
        }, function(response) {// something went wrong
            return $q.reject(response);
        });
    }

    
    /**
     * Get remote data
     */
    function getRemoteData(url, noCache) {
        // Cached data
        var cacheName = 'cache_' + url;
        var cached = myCache.get(cacheName);

        if (!noCache && cached) {
            var deferred = $q.defer();
            deferred.resolve(cached);
            return deferred.promise;
        }
        // NOT Cached data
        return $http({
            method: 'get',
            url: url
        }).then(function(response) {
            return response;
        }, function(error) {// something went wrong

            return $q.reject(error);
        });
    }

    /**
     * Upload module
     */
    function uploadModule(data) {
        var uploadUrl = cfg.api.moduleupload;
        return  $http.post(uploadUrl, data, {
             withCredentials: true,
        headers: {'Content-Type': undefined },
            transformRequest: angular.identity
        }).then(function(response) {
             //return response;
            if (typeof response.data === 'object') {
                return response;
            } else {// invalid response
                response.status = 500;
                response.statusText = 'Unable to upload module!';
                return $q.reject(response);
            }
        }, function(response) {// something went wrong
            return $q.reject(response);
        });

    }
    
    /**
     * Upload file
     */
    function uploadFile(url,data) {
       // var uploadUrl = cfg.api.moduleupload;
        return  $http.post(url, data, {
             withCredentials: true,
        headers: {'Content-Type': undefined },
            transformRequest: angular.identity
        }).then(function(response) {
             return response;
        }, function(response) {// something went wrong
            return $q.reject(response);
        });

    }

    /**
     * Load language file
     */
    function getLanguageFile(lang) {
        var langFile = lang + '.json';
        var cached = myCache.get(langFile);

        if (cached) {
            var deferred = $q.defer();
            deferred.resolve(cached);
            return deferred.promise;
        }
        return $http({
            method: 'get',
            url: cfg.lang_dir + langFile
        }).then(function(response) {
            if (typeof response.data === 'object') {
                myCache.put(langFile, response);
                return response;
            } else {
                return $q.reject(response);
            }
        }, function(response) {// something went wrong
            return $q.reject(response);
        });
    }
    
     /**
     * Get help file
     */
    function getHelp(file) {
        return $http({
            method: 'get',
            url: cfg.help_data_url + file
        }).then(function(response) {
            return response;
        }, function(response) {// something went wrong
            return $q.reject(response);
        });

    }
});

