<!-- Help -->

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- Vendor css -->
<link media="screen" type="text/css" rel="stylesheet" href="app/css/bootstrap.css">
<link media="screen" type="text/css" rel="stylesheet" href="app/css/font-awesome-4.4.0/css/font-awesome.min.css">
<!-- App css -->
<link media="screen" type="text/css" rel="stylesheet" href="app/css/main.css">
<!-- App config -->
<script src="app/config.js"></script>
<!-- Vendors js -->
<script src="app/vendor/jquery/jquery-1.11.1.js"></script>
<script src="app/vendor/underscore/underscore-1.8.3/underscore.js"></script>
<script src="app/vendor/alertify/alertify.min.js"></script>
<!-- Angular js -->
<script src="app/vendor/angular/angular-1.4.7/angular.js"></script>
<script src="app/vendor/upload/angular-file-upload.js"></script>
<script src="app/vendor/angular/angular-1.4.7/angular-route.js"></script>
<script src="app/vendor/angular/angular-1.4.7/angular-cookies.js"></script>

<!-- App js -->
<script src="app/app.js"></script>
<script src="app/services/factories.js"></script>
<script src="app/services/services.js"></script>
<script src="app/directives/directives.js"></script>
<script src="app/directives/dir-pagination.js"></script>
<script src="app/vendor/alertify/ngAlertify.js"></script>
<script src="app/filters/filters.js"></script>
<script src="app/controllers/base.js"></script>
<script src="app/controllers/controllers.js"></script>
<!--Libarys for the helpside -->
<link rel="stylesheet" href="app/vendor/helplib/github.min.css">
<script src="app/vendor/helplib/highlight.min.js"></script>
<script src="app/vendor/helplib/ui-bootstrap-tpls-1.3.2.min.js"></script>
<script src="app/vendor/helplib/angular-highlightjs.min.js"></script>




<h1>How to create an automation module</h1>
<div ng-controller="helpCtrl">

    <uib-tabset active="active" >
        <uib-tab index="$index + 1" ng-repeat="topic in topics"  heading="{{topic.name}}" active="topic.active" disable="tab.disabled">

            <div ng-include="topic.url"/> 

        </uib-tab>
    </uib-tabset>
</div>

<footer>
    <hr>
    <li><a href="https://github.com/Z-Wave-Me/home-automation/wiki">Documentation</a></li>
    <li><a href="http://docs.zwayhomeautomation.apiary.io/">API Documentation</a></li>
    <li><a href="http://razberry.z-wave.me/docs/zwayDev.pdf">Z-Way Developers Documentation</a></li>
    <li><a href="https://github.com/Z-Wave-Me/home-automation/issues">Issues, bugs and feature requests are welcome</a></li>
</footer>





