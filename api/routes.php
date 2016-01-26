<?php

/**
 * Routes
 *
 * @author Martin Vach
 */
// Route init
$route = new Route(trim($_GET['uri'], '/'));

// Wiews
$view = new stdClass();
$view->layout = 'layout_web';
$view->view = 'home';

// Form data
$form = new stdClass();
// Response init
$response = new Response;
// Db init
$db = new Db($cfg['db_' . $environment]);
$db->openConnection();
// Model init
$model = new Model($db);


// User init
$auth = Ut::user();
$user = new stdClass();
$user->id = null;
$user->role = null;
$user->mail = null;
$user->first_name = null;
$user->last_name = null;
$user->homepage = null;
$user->company = null;
if ($auth) {
    $user->id = (int) $auth->id;
    $user->role = (int) $auth->role;
    $user->mail = $auth->mail;
    $user->first_name = $auth->first_name;
    $user->last_name = $auth->last_name;
    $user->homepage = $auth->homepage;
    $user->company = $auth->company;
}

// Api init
$api = new AppApi($model, $cfg, $user);

// Home page
if ($route->match('home', null)) {
//    var_dump(Ut::user());
//    var_dump(Ut::formData('mail'));
//    $form->mail = Ut::formData('mail');
}
// Login page
elseif ($route->match('login', null)) {
    $view->view = 'login';
}
// Help page
elseif ($route->match('help', null)) {
    $view->view = 'help';
}
// Login post
elseif ($route->match('login/post', 2)) {
    // Check post 
    if (!$_POST) {
        Ut::redirectTo(Ut::uri('report'), array('404 Page not found'));
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $api->login();
    if ($api->getErrors()) {
        Ut::redirectWithValidation(Ut::uri('home'), $api->getErrors(), $api->getInputs());
        exit;
    }
    // User not found
    Ut::redirectTo(Ut::uri('user'));
    exit;
}
// Join page
elseif ($route->match('join', null)) {
    $view->view = 'join';
}
// Join page
elseif ($route->match('join/post', 2)) {
    // Check post 
    if (!$_POST) {
        Ut::redirectTo(Ut::uri('report'), array('404 Page not found'));
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    // Validation
    if (!Ut::isEmail($api->getInputVal('mail'))) {
        $api->setErrors('Invalid email address');
    }
    if (!Ut::strLenght($api->getInputVal('pw'), 6)) {
        $api->setErrors('Password must be min 6 characters length');
    }
    if ($api->getErrors()) {
        Ut::redirectWithValidation(Ut::uri('join'), $api->getErrors(), $api->getInputs());
        exit;
    }

    $api->join();
    if ($api->getErrors()) {
        Ut::redirectWithValidation(Ut::uri('join'), $api->getErrors(), $api->getInputs());
        exit;
    }
    Ut::redirectTo(Ut::uri('report'), array('Your account was created. Please check your email and confirm the registration'), 'success');
    exit;
}
// Join confirm page
elseif ($route->match('join/confirm', 2)) {
    $api->joinConfirm(strip_tags($route->getParam(0)));
    if ($api->getErrors()) {
        Ut::redirectTo(Ut::uri('report'), $api->getErrors());
        exit;
    }
    Ut::redirectTo(Ut::uri('home'), array('Your account was confirmed. Please login'), 'success');
    exit;
}
// Password page
elseif ($route->match('password', null)) {
    //Ut::authRequired();
    $view->view = 'password';
}

// Password post
elseif ($route->match('password/post', 2)) {
    if (!$_POST) {
        Ut::redirectTo(Ut::uri('report'), array('404 Page not found'));
        exit;
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);

    // Create new token
    $api->passwordToken();
    if ($api->getErrors()) {
        Ut::redirectTo(Ut::uri('password'), $api->getErrors());
        exit;
    }

    Ut::redirectTo(Ut::uri('report'), array('Your password request was created.Please check your email and confirm the password request'), 'success');
}
// Password confirm
elseif ($route->match('passwordnew', 1)) {
    $api->setInputs(array('token' => $route->getParam(0)));
    $api->passwordConfirm();
    if ($api->getErrors()) {
        Ut::redirectTo(Ut::uri('report'), $api->getErrors());
        exit;
    }
    $view->view = 'passwordnew';
}

// Password confirm post
elseif ($route->match('passwordupdate', null)) {
    // Check post 
    if (!$_POST) {
        Ut::redirectTo(Ut::uri('report'), array('404 Page not found'));
        exit;
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    // Validation
    if (!Ut::strLenght($api->getInputVal('pw'), 6)) {
        $api->setErrors('Password must be min 6 characters length');
    }
    if ($api->getErrors()) {
        Ut::redirectTo(Ut::uri('passwordnew/' . $api->getInputVal('token')), $api->getErrors());
        exit;
    }
    // Update password
    $api->passwordUpdate();
    if ($api->getErrors()) {
        Ut::redirectTo(Ut::uri('report'), $api->getErrors());
        exit;
    }
    Ut::redirectTo(Ut::uri('home'), array('Your password was changed. Login with new password'), 'success');
}
// Report page
elseif ($route->match('report', null)) {
    $view->view = 'report';
// User page
} elseif ($route->match('user', null)) {
    if (!$auth) {
        Ut::redirectTo(Ut::uri('home'), array('You are not authorized. Please login or create an account'));
    }
    $view->layout = 'layout_user';
}

// User session
elseif ($route->match('usersession', null)) {
//    $where = ($user->role > 1 ? array('user_id' => $user->id) : null);
//    $response->data = $model->modulesAll($where);
    if ($user->id < 1) {
        $response->status = 401;
        $response->message = 'Unauthorized';
        $response->json($response);
    }
    $response->json($user);
}

// API my modules
elseif ($route->match('mymodules', null)) {
    $where = ($user->role > 1 ? array('user_id' => $user->id) : null);
    $response->data = $model->modulesAll(array('user_id' => $user->id));
    $response->json($response);
}

// API admin modules
elseif ($route->match('modules', null)) {
    $response->data = $model->modulesAll(array('user_id' => $user->id));
    $response->json($response);
}
// API module id
elseif ($route->match('module', 1)) {
    // Prepare and sanitize post input
    $api->setInputs(array('id' => $route->getParam(0)));
    $where = ($user->role > 1 ? array('id' => $api->getInputVal('id'), 'user_id' => $user->id) : array('id' => $api->getInputVal('id')));
    $module = $model->moduleFind($where);
    if (!count($module)) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    $response->data = $module;
    $response->json($response);
}
// API module upload
elseif ($route->match('moduleupload', null)) {
    require_once 'api/upload.php';
    die;
}
// API module verify
elseif ($route->match('moduleverify', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $api->moduleVerify();
    if ($api->getErrors()) {
        $response->status = 500;
        $response->message = 'Unable to send verification request';
        $response->data = $api->getErrors();
        $response->json($response);
    }
    $response->json($response);
}
// API module update
elseif ($route->match('moduleupdate', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $where = ($user->role > 1 ? array('id' => $api->getInputVal('id'), 'user_id' => $user->id) : array('id' => $api->getInputVal('id')));
    $module = $model->moduleFind($where);
    if (!$module) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    $model->moduleUpdate($api->getInputs(), array('id' => $api->getInputVal('id')));
    $response->json($response);
}
// API token delete
elseif ($route->match('moduledelete', null)) {
    $api->setInputs($_POST);
    $where = ($user->role === 1 ? array('id' => $api->getInputVal('id')) : array('id' => $api->getInputVal('id'), 'user_id' => $user->id));
    $module = $model->moduleFind($where);
    if (!$module) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $model->moduleDelete(array('id' => $api->getInputVal('id')));
    $model->tokenDelete(array('module_id' => $api->getInputVal('id')));
    $model->langDelete(array('id' => $api->getInputVal('id')));
    $model->commentDelete(array('module_id' => $api->getInputVal('id')));
    $model->ratingDelete(array('module_id' => $api->getInputVal('id')));
    if (is_file('modules/' . $module->icon)) {
        unlink('modules/' . $module->icon);
    }
    if (is_file('modules/' . $module->file)) {
        unlink('modules/' . $module->file);
    }
    $response->json($response);
}
// API tokens
elseif ($route->match('tokens', 1)) {
    // Prepare and sanitize post input
    $api->setInputs(array('module_id' => $route->getParam(0)));
    $response->data = $model->tokensAll(array('module_id' => $api->getInputVal('module_id')));
    $response->json($response);
}
// API token create
elseif ($route->match('tokencreate', null)) {

    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $model->tokenCreate($api->getInputs());
    $response->json($response);
}
// API token delete
elseif ($route->match('tokendelete', null)) {

    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $model->tokenDelete($api->getInputs());
    $response->json($response);
}
// API comment delete
elseif ($route->match('commentdelete', null)) {

    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $model->commentDelete($api->getInputs());
    $response->json($response);
}
// API skins
elseif ($route->match('skins', null)) {
    $where = ($user->role > 1 ? array('user_id' => $user->id) : null);
    $response->data = $model->skinsAll($where);
    $response->json($response);
}
// API skin id
elseif ($route->match('skin', 1)) {
    // Prepare and sanitize post input
    $api->setInputs(array('id' => $route->getParam(0)));
    $where = ($user->role > 1 ? array('id' => $api->getInputVal('id'), 'user_id' => $user->id) : array('id' => $api->getInputVal('id')));
    $skin = $model->skinFind($where);
    if (!count($skin)) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $response->data = $skin;
    $response->json($response);
}
// API skin create
elseif ($route->match('skincreate', null)) {
    // Prepare and sanitize post input
    //$api->setInputs($_POST);

    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //var_dump(Ut::toSlug($_FILES['file']['name']));
    $file = false;
    $uploader = new Uploader();
    $uploader->setDir('storage/skins/');
    $uploader->setExtensions(array('gz', 'zip'));  //allowed extensions list//
    $uploader->setMaxSize(.5);
    $uploader->sameName(true); //set max file size to be allowed in MB//
    $uploader->setUniqueFile();

    if ($uploader->uploadFile('file')) {   //txtFile is the filebrowse element name //     
        $file = $uploader->getUploadName(); //get uploaded file name, renames on upload//
    } else {//upload failed
        //get upload error message 
        $response->status = 500;
        $response->message = $uploader->getMessage();
        $response->json($response);
    }
    if ($file) {
        $input = array(
            'user_id' => $user->id,
            'name' => Ut::toSlug($file),
            'title' => $file,
            'file' => $file,
            'author' => trim($user->first_name . ' ' . $user->last_name),
            'homepage' => $user->homepage,
        );
        if (!$model->skinCreate($input)) {
            $response->status = 500;
            $response->message = 'Unable to upload a skin';
            $response->json($response);
        }
        $input['id'] = $db->inserId();
    }
    $response->data = $input;
    $response->json($response);
}
// API skin update
elseif ($route->match('skinupdate', null)) {
    // Prepare and sanitize post input
    $_POST['active'] = 1;
    $api->setInputs($_POST);
    $skin = $model->skinFind(array('id' => $api->getInputVal('id'), 'user_id' => $user->id, 'name' => $api->getInputVal('name')));
    if (!$skin) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $model->skinUpdate($api->getInputs(), array('id' => $api->getInputVal('id')));
    $response->json($response);
}
// API skin delete
elseif ($route->match('skindelete', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $skin = $model->skinFind(array('id' => $api->getInputVal('id'), 'user_id' => $user->id));
    if (!count($skin)) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    if (!$model->skinDelete(array('id' => $api->getInputVal('id'), 'user_id' => $user->id))) {
        $response->status = 500;
        $response->message = 'Unable to delete a skin';
        $response->json($response);
    }
    $path = 'storage/skins/' . $skin->file;
    $img = 'storage/skins/' . $skin->icon;
    if (is_file($path)) {
        unlink($path);
    }
    if (is_file($img)) {
        unlink($img);
    }
    $response->json($response);
}
// API skin upload
elseif ($route->match('skinupload', null)) {
    // Prepare and sanitize post input
    //$api->setInputs($_POST);

    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //var_dump(Ut::toSlug($_FILES['file']['name']));
    $file = false;
    $uploader = new Uploader();
    $uploader->setDir('storage/skins/');
    $uploader->setExtensions(array('gz', 'zip'));  //allowed extensions list//
    $uploader->setMaxSize(.5);
    $uploader->sameName(true);

    if (!$uploader->uploadFile('file')) {
        $response->status = 500;
        $response->message = $uploader->getMessage();
        $response->json($response);
    }
    $response->json($response);
}

// API skin img upload
elseif ($route->match('skinimgupload', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);

    $skin = $model->skinFind(array('id' => $api->getInputVal('id'), 'user_id' => $user->id));
    if (!$skin) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $uploader = new Uploader();
    $uploader->setDir('storage/skins/');
    $uploader->setExtensions(array('png', 'jpg', 'gif'));  //allowed extensions list//
    $uploader->setMaxSize(.2);
    $uploader->setCustomName($api->getInputVal('id') . '-' . time());

    if (!$uploader->uploadFile('file')) {
        $response->status = 500;
        $response->message = $uploader->getMessage();
        $response->json($response);
    }
    $model->skinUpdate(array('icon' => $uploader->getUploadName()), array('id' => $skin->id));
    $path = 'storage/skins/' . $api->getInputVal('current');
    if (is_file($path)) {
        unlink($path);
    }
    $response->data = array('icon' => $uploader->getUploadName());
    $response->json($response);
}
// API user
elseif ($route->match('userread', null)) {
    // Prepare and sanitize post input
    $response->data = $model->userFind(array('id' => $user->id));
    $response->json($response);
}
// API user update
elseif ($route->match('userupdate', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $user_id = ($user->role === 1 ? $api->getInputVal('id') : $user->id);
    $model->userUpdate($api->getInputs(), array('id' => $user_id));
    $response->json($response);
}
// API admin modules
elseif ($route->match('adminmodules', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    //$response->data = $model->modulesAllNotVerified();
    //$where = ($user->role > 1 ? array('user_id' => $user->id) : null);
    $response->data = $model->modulesAll(NULL);
    $response->json($response);
}
// API module verify
elseif ($route->match('adminmoduleverify', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
//    var_dump($api->getInputs());
//    die;
    $api->adminModuleVerify();
    if ($api->getErrors()) {
        $response->status = 500;
        $response->message = 'Unable to update the module';
        $response->data = $api->getErrors();
        $response->json($response);
    }
    $response->json($response);
}
// API send module author email
elseif ($route->match('adminmoduleemail', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    //var_dump($api->getInputs());
    $api->adminModuleEmail();
    if ($api->getErrors()) {
        $response->status = 500;
        $response->message = 'Unable to send email';
        $response->data = $api->getErrors();
        $response->json($response);
    }
    $response->json($response);
}
// API admin modules
elseif ($route->match('adminusers', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    $response->data = $model->usersAll(NULL);
    $response->json($response);
}
// API admin user create
elseif ($route->match('adminusercreate', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    //$response->data = $model->usersAll(NULL);
    $user = $model->userFind(array('mail' => $api->getInputVal('mail')));
    if ($user) {
        $response->status = 409;
        $response->message = 'User with email ' . $api->getInputVal('mail') . ' already exists';
        $response->json($response);
    }
    if (!$model->userCreate(array(
                'sid' => Ut::token(),
                'mail' => $api->getInputVal('mail'),
                'pw' => md5($api->getInputVal('pw'))
            ))) {
        $response->status = 500;
        $response->message = 'Unable to create an user';
        $response->json($response);
    }
    $response->data = array('id' => $db->inserId());
    $response->json($response);
}
// API admin user read
elseif ($route->match('adminuserread', 1)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    $api->setInputs(array('id' => $route->getParam(0)));
    $response->data = $model->userFind(array('id' => $api->getInputVal('id')));
    $response->json($response);
}
// API admin reset user password
elseif ($route->match('adminresetpassword', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $pw = array(
        'pw' => md5($api->getInputVal('pw'))
    );
    if (!$model->userUpdate($pw, array('id' => $api->getInputVal('id')))) {
        $response->status = 500;
        $response->message = 'DB error';
        $response->json($response);
    }
    $response->json($response);
}

// API admin user delete
elseif ($route->match('adminuserdelete', null)) {
    // Admin only
    if ($user->role !== 1) {
        $response->status = 403;
        $response->message = 'Forbidden';
        $response->json($response);
    }
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $user = $model->userFind(array('id' => $api->getInputVal('id')));
    if (!count($user)) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    if (!$model->userDelete(array('id' => $api->getInputVal('id')))) {
        $response->status = 500;
        $response->message = 'DB error';
        $response->json($response);
    }
    $response->json($response);
}
// Public API modules
elseif ($route->match('api-modules', null)) {
    $tokens = '';
    $ids = '';
    if (count($_POST['token'])) {
        foreach ($_POST['token'] as $key => $value) {
            $tokens .= '\'' . $value . '\',';
        }
        $ids = $model->apiTokensModuleIds(rtrim($tokens, ','));
    }

    $response->data = $model->apiModulesAll(array('verified' => 1), $ids);
    $response->json($response);
}
// Public API module id
elseif ($route->match('api-modulesid', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $title_lang = 'title_' . $api->getInputVal('lang');
    $desc_lang = 'desc_' . $api->getInputVal('lang');
    $module = $model-> moduleFindJoin(array('m.id' => $api->getInputVal('id')));
    if (!$module) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $langs = $model->moduleLangFind(array('id' => $api->getInputVal('id')));
    // Set module title
//    if(property_exists($langs,$title_lang) &&  $langs->$title_lang !=''){
//        $module->title = $langs->$title_lang;
//    }
    // Set module description
    if (property_exists($langs, $desc_lang) && $langs->$desc_lang != '') {
        $module->description = $langs->$desc_lang;
    }
    $response->data = $module;
    $response->json($response);
}
// Public API module archives
elseif ($route->match('api-module-archive', 1)) {
    $response->data = $model->archiveAll(array('module_id' => $route->getParam(0)));
    $response->json($response);
}
// Public API modules installed
elseif ($route->match('api-modules-installed', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    $module = $model->moduleFind(array('id' => $api->getInputVal('id')));

    if (!$module) {
        $response->status = 404;
        $response->message = 'Not found';
        $response->json($response);
    }
    $input = array(
        'installed' => (int) $module->installed + 1
    );
    $model->moduleUpdate($input, array('id' => $api->getInputVal('id')));
    $response->json($response);
}
// Public API comment create
elseif ($route->match('api-comments-create', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);

    if (!$model->commentCreate($api->getInputs())) {
        $response->status = 500;
        $response->message = 'Unable to add a comment';
        $response->json($response);
    }
    $input['id'] = $db->inserId();
    $response->data = $input;
    $response->json($response);
}
// Public API comment get
elseif ($route->match('api-comments', 1)) {
    // Prepare and sanitize post input
    $api->setInputs(array('module_id' => $route->getParam(0)));
    //var_dump($api->getInputs());
    //die;
    $response->data = $model->commentsAll($api->getInputs());
    $response->json($response);
}
// Public API modules installed
elseif ($route->match('api-rating-create', null)) {
    // Prepare and sanitize post input
    $api->setInputs($_POST);
    // Already rated
    $rating = $model->ratingFind(array('module_id' => $api->getInputVal('module_id'),'remote_id' => $api->getInputVal('remote_id')));
    if ($rating) {
        $response->status = 409;
        $response->message = 'Already rated';
        $response->json($response);
    }
    if ((int)$api->getInputVal('score') > 5 || !$model->ratingCreate($api->getInputs())) {
        $response->status = 500;
        $response->message = 'Unable to rate the module';
        $response->json($response);
    }
    $input['id'] = $db->inserId();
    $response->data = $input;
    $response->json($response);
}
// Public API skins
elseif ($route->match('api-skins', null)) {
    $response->data = $model->skinsAll(array('active' => 1));
    $response->json($response);
}
// Logout
elseif ($route->match('logout', null)) {
    unset($_SESSION['user']);
    Ut::redirectTo(Ut::uri('home'), array('You are logged out'), 'success');
}
// Default - not found
else {
//    var_dump(strpos($_SERVER["HTTP_ACCEPT"], 'json'));
//    die;
    Ut::redirectTo(Ut::uri('report'), array('404 page not found'));
}

require_once 'views/' . $view->layout . '.php';

