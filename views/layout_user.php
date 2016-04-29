<!doctype html>
<html lang="en" ng-app="myApp">
    <head>
        <meta charset="utf-8">
        <title>SmartHome - Upload Module</title>
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
        <script src="app/controllers/module.js"></script>
        <script src="app/controllers/skin.js"></script>
        <script src="app/controllers/mysettings.js"></script>
        <script src="app/controllers/adminmodule.js"></script>
        <script src="app/controllers/adminuser.js"></script>
        <!--Libarys for the helpside -->
        <link rel="stylesheet" href="app/vendor/helplib/github.min.css">
<script src="app/vendor/helplib/highlight.min.js"></script>
<script src="app/vendor/helplib/ui-bootstrap-tpls-1.3.2.min.js"></script>
<script src="app/vendor/helplib/angular-highlightjs.min.js"></script>



    </head>
    <body ng-controller="BaseController" id="page_{{getBodyId()}}" class="is-mobile-{{isMobile}}">
        <div id="header">
            <div id="main_navigation_wrap">
                <div id="main_navigation" class="container">
                    <div id="navigation_left">
                        <a href="#/" title="{{_t('nav_home')}}">
                            <img ng-src="app/img/app-logo-default.png" id="header_logo" alt="App logo" />
                        </a>
                            <!-- Modules -->
                            <span class="divider-vertical"></span>
                            <a href="#/" ng-class="isActive('modules')"><i class="fa fa-home"></i></a>
                     </div>
                    <div id="navigation_right">
                         <!-- Settings -->
                         <span class="divider-vertical"></span>
                           <a id="navi_settings" href="" ng-click="expandNavi('mainNav',$event)" ng-class="naviExpanded.mainNav ? 'active':''"><i class="fa fa-cog"></i></a> 
                            
                       </a>
                    </div>
                </div><!-- /#navigation --> 
            </div><!-- /#main_navigation_wrap --> 
            <div id="sub_navigation" ng-if="naviExpanded.mainNav">
                <div id="sub_navigation_in">
                    <div class="container">
                        <ul>
                             <?php if($user->role === 1): ?>
                            <!-- Admin modules -->
                            <li class="subnavi-modules" ng-class="isActive('admin-modules')">
                                <a href="#admin-modules"><i class="fa fa-cubes"></i> {{_t('Modules')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                            <!-- Users -->
                            <li class="subnavi-users" ng-class="isActive('admin-users')">
                                <a href="#admin-users"><i class="fa fa-users"></i> {{_t('Users')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                            <!-- Skins -->
                            <li class="subnavi-skins" ng-class="isActive('skins')">
                                <a href="#skins"><i class="fa fa-puzzle-piece"></i> {{_t('Skins')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                             <?php endif; ?>
                            <!-- My access -->
                            <li class="subnavi-myaccess" ng-class="isActive('mysettings')">
                                <a href="#mysettings"><i class="fa fa-user"></i> {{_t('nav_myaccess')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                            <!-- Help -->
                            <li class="subnavi-help" ng-class="isActive('help')">
                               <a href="#help"><i class="fa fa-question-circle"></i> {{_t('Help')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                            <!-- Logout -->
                            <li class="subnavi-logout">
                                 <a href="?uri=logout"><i class="fa fa-sign-out"></i> {{_t('nav_logout')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                        </ul>
                    </div>
                </div><!-- /#subnavigation_in --> 
            </div><!-- /#subnavigation --> 
        </div><!-- /#header --> 
        <!-- Ang Content Container -->
        <div id="main_content" class="container" ng-view></div>
        <div class="clearfix"></div>

        <div id="footer">
            
                <div class="container">
                    © 2015 Z-WAVE.ME
                </div>
        </div>
    </body>
</html>