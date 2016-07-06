<!doctype html>
<html lang="en" ng-app="myApp">
    <head>
        <?php require_once 'head_scripts.php' ?>
    </head>
    <body ng-controller="BaseController" id="page_{{getBodyId()}}" class="is-mobile-{{isMobile}}">
        <div id="header">
            <?php require_once 'header_public.php' ?>
        </div><!-- /#header --> 
        <!-- Ang Content Container -->
        <div id="main_content" class="container" ng-view></div>
        <div class="clearfix"></div>
        <div id="footer">
            <div id="footer_in">
                <div class="container">
                    © 2015 Z-WAVE.ME
                </div>
            </div>         
        </div>
    </body>
</html>