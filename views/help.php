<!-- Help -->

        <?php require_once 'head_scripts.php' ?>
<!--Libarys for the helpside -->
<link rel="stylesheet" href="app/vendor/helplib/bootstrap.css">
<!--  <link rel="stylesheet" href="app/vendor/helplib/github.min.css">
<script src="app/vendor/helplib/highlight.min.js"></script>
<script src="app/vendor/helplib/ui-bootstrap-tpls-1.3.2.min.js"></script>
<script src="app/vendor/helplib/angular-highlightjs.min.js"></script> -->



<nav class="navbar navbar-default">
    <a class="navbar-brand" href="#">
<i class="fa fa-question-circle"></i>
    </a>
<h1 class="navbar-text">How to create an automation module</h1>
</nav>
<div ng-controller="helpCtrl">

    <uib-tabset active="active" >
        <uib-tab index="$index + 1" ng-repeat="topic in topics"  heading="{{topic.name}}" active="topic.active" disable="tab.disabled">

            <div ng-include="topic.url"/> 

        </uib-tab>
    </uib-tabset>
</div>

<nav class="navbar navbar-default">
    <hr>
    <ul>
    <li><a href="https://github.com/Z-Wave-Me/home-automation/wiki">Documentation</a></li>
    <li><a href="http://docs.zwayhomeautomation.apiary.io/">API Documentation</a></li>
    <li><a href="http://razberry.z-wave.me/docs/zwayDev.pdf">Z-Way Developers Documentation</a></li>
    <li><a href="https://github.com/Z-Wave-Me/home-automation/issues">Issues, bugs and feature requests are welcome</a></li>
    <li><a href="http://www.alpacajs.org/">Alpaca.js</a></li>
    </ul>
    <br>
</nav>
           





