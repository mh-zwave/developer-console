<?php
/**
 * Index script for front-end
 *
 * @author Martin Vach
 */
//ob_start("ob_gzhandler");
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));
ini_set('display_errors','On');

/**
 * Start the session
 */
if(ini_get('session.auto_start') != 1) {
    session_start();
}

/**
 * Includes and class init
 */
// Config
$cfg = require_once 'api/config.php';
if($_SERVER['HTTP_HOST'] == 'dev.dev'){
    $environment = 'local';

}else{
    $environment = 'live';
}
// Route
require_once 'api/classes/Route.php';
require_once 'api/classes/Response.php';
require_once 'api/classes/Db.php';
require_once 'api/classes/Model.php';
require_once 'api/classes/AppApi.php';
require_once 'api/classes/Ut.php';
require_once 'api/vendor/phpmailer/PHPMailerAutoload.php';
require_once 'api/vendor/upload/Uploader.php';
// Routes
require_once 'api/routes.php';

/*???????????????? REMOVE ????????????????*/
//ob_end_flush();
/*???????????????? REMOVE ????????????????*/