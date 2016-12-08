<!doctype html>
<html lang="en" ng-app="myApp">
    <head>
        <?php require_once 'head_scripts.php' ?>
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
                            <!-- icons -->
                            <li class="subnavi-skins" ng-class="isActive('icons')">
                                <a href="#icons"><i class="fa fa-picture-o"></i> {{_t('Icons')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
                             <?php endif; ?>
                            <!-- Skins -->
                            <li class="subnavi-skins" ng-class="isActive('skins')">
                                <a href="#skins"><i class="fa fa-puzzle-piece"></i> {{_t('Skins')}} <i class="fa fa-chevron-right subnavi-arrow"></i></a>
                            </li>
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
            <div id="footer_in">
                <div class="container">
                    © 2015 Z-WAVE.ME
                </div>
            </div>         
        </div>
    </body>
</html>