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
            <?php require_once 'header_public.php' ?>
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
