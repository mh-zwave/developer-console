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
    </head>
    <body class="is-mobile-false">
        <div id="header">
            <div id="main_navigation_wrap">
                <div id="main_navigation" class="container">
                    <div id="navigation_left">
                        <a href="<?php echo Ut::uri('home') ?>" title="Home">
                            <img src="app/img/app-logo-default.png" id="header_logo" alt="Logo">
                        </a>
                            <span class="divider-vertical"></span>
                            <a href="<?php echo Ut::uri('home') ?>"><i class="fa fa-home"></i></a>
                             <span class="divider-vertical"></span>
                            <a href="<?php echo Ut::uri('help') ?>"><i class="fa fa-question-circle"></i></a>

                    </div>
                    <!--<div id="navigation_right">
                        <a href="<?php echo Ut::uri('login') ?>"><i class="fa fa-sign-in"></i>  Login</a>
                    </div> --> 
                </div><!-- /#navigation --> 
            </div><!-- /#main_navigation_wrap -->
        </div><!-- /#header --> 
        <!-- Ang Content Container -->
        <div id="main_content" class="container">
             <?php Ut::flashHtml() ?>
            <?php require_once 'views/'.$view->view.'.php' ?>
        </div>
        <div class="clearfix"></div>

        <div id="footer">
            
                <div class="container">
                    © 2015 Z-WAVE.ME
                </div>
        </div>
    </body>
</html>
