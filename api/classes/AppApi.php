<?php

/**
 * Module api class
 *
 * @author Martin Vach
 */
class AppApi {

    private $model;
    private $inputs = array();
    private $errors = array();
    private $cfg = array();
    private $user;

    /**
     * Class constructor
     * 
     * @param string $db
     * @return void
     */
    public function __construct($model, $cfg, $user) {
        $this->model = $model;
        $this->cfg = $cfg;
        $this->user = $user;
    }

    /**
     * Set and sanitize form inputs
     * 
     * @param type $array
     * @return void
     */
    public function setInputs($array,$white_list = array()) {
        foreach ($array as $key => $value) {
            if(count($white_list) && !in_array($key,$white_list)){
                continue;
            }
            $this->inputs[$key] = addslashes(strip_tags($value));
        }
    }

    /**
     * Get inputs
     * 
     * @return AppApi
     */
    public function getInputs() {
        return $this->inputs;
    }

    /**
     * Get a single input value
     * 
     * @param tstring $key
     * @return mixed
     */
    public function getInputVal($key) {
        return (isset($this->inputs[$key]) ? $this->inputs[$key] : null);
    }

    /**
     * Set erros
     * 
     * @param string $message
     * @return void
     */
    public function setErrors($message) {
        $this->errors[] = $message;
    }

    /**
     * Get errors
     * 
     * @return array/boolean
     */
    public function getErrors() {
        if (count($this->errors)) {
            return $this->errors;
        }
        return false;
    }

    /**
     * Get template
     * 
     *  @param string $uri
     * @param array $data
     * @return mixed
     */
    public function getTemplate($uri, $data) {
        extract($data);
        ob_start();
        include $uri;
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }

    /**
     * Confirm user registration
     * 
     * @return bool
     */
    public function login() {
        $user = $this->model->userFind(array('mail' => $this->getInputVal('mail'), 'pw' => md5($this->getInputVal('pw')), 'confirmed' => 1));
        if (!$user) {
            $this->setErrors('User not found. Wrong email or pasword');
            return false;
        }
        Ut::setUser($user);
        return true;
    }

    /**
     * Register an user
     * 
     * @return void
     */
    public function join() {
        $input = array(
            'sid' => Ut::token(),
            'mail' => $this->getInputVal('mail'),
            'pw' => md5($this->getInputVal('pw'))
        );
        $user = $this->model->userFind(array('mail' => $input['mail']));
        if ($user) {
            $this->setErrors('User with email ' . $input['mail'] . ' already exists');
            return;
        }
        $this->model->userCreate($input);
        // Template data
        $data = array(
            'link' => $this->cfg['server'] . '?uri=join/confirm/' . $input['sid'],
        );

        $email = array(
            'from' => 'noreply@zwaveeurope.com',
            'from_name' => 'Administrator',
            'to' => $input['mail'],
            'subject' => 'Module-Store Confirmation link',
            'body' => $this->getTemplate('views/emails/confirmation.html', $data),
                //'body' => 'Please confirm your registration on <a href="' . $this->cfg['server'] . '?uri=join/confirm/' . $input['sid'] . '">confirmation link</a> or copy link to your browser: ' . $this->cfg['server'] . '?uri=join/confirm/' . $input['sid'],
        );
        $this->sendEmail($email);
    }

    /**
     * Confirm user registration
     * 
     * @param string $token
     * @return void
     */
    public function joinConfirm($token) {
        if (!$token) {
            $this->setErrors('Token not found');
            return false;
        }

        $input = array(
            'sid' => Ut::token(),
            'confirmed' => 1
        );
        $user = $this->model->userFind(array('sid' => $token, 'confirmed' => 0));
        if (!$user) {
            $this->setErrors('User not found');
            return false;
        }
        if (!$this->model->userUpdate($input, array('id' => $user->id))) {
            $this->setErrors('Cannot confirm registration');
            return false;
        }
        return true;
    }

    /**
     * Create a new password token
     * 
     * @return void
     */
    public function passwordToken() {
        $user = $this->model->userFind(array('mail' => $this->getInputVal('mail'), 'confirmed' => 1));

        if (!$user) {
            $this->setErrors('Email address ' . $this->getInputVal('mail') . ' not found');
            return false;
        }
        $input = array(
            'token' => Ut::token(),
            'user_id' => $user->id,
        );

        $this->model->passwordCreate($input);
        // Template data
        $data = array(
            'link' => $this->cfg['server'] . '?uri=passwordnew/' . $input['token'],
        );
        $email = array(
            'from' => 'noreply@zwaveeurope.com',
            'from_name' => 'Administrator',
            'to' => $this->getInputVal('mail'),
            'subject' => 'Module-Store Password',
            'body' => $this->getTemplate('views/emails/password_forgot.html', $data),
                //'body' => 'Please confirm your new password request on <a href="' . $this->cfg['server'] . '?uri=passwordnew/' . $input['token'] . '">confirmation link</a> or copy link to your browser: ' . $this->cfg['server'] . 'passwordnew/' . $input['token'],
        );
        $this->sendEmail($email);
    }

    /**
     * Confirm password request
     * 
     * @return void
     */
    public function passwordConfirm() {
        $token = $this->getInputVal('token');

        if (!$token) {
            $this->setErrors('Token not found');
            return false;
        }
        $password = $this->model->passwordFind(array('token' => $token, 'confirmed' => 0));
        if (!$password) {
            $this->setErrors('Password request not found');
            return false;
        }
        return true;
    }

    /**
     * Update password
     * 
     * @return void
     */
    public function passwordUpdate() {
        $token = $this->getInputVal('token');
        if (!$token) {
            $this->setErrors('Token not found');
            return false;
        }

        $password = $this->model->passwordFind(array('token' => $token, 'confirmed' => 0));
        if (!$password) {
            $this->setErrors('Password request not found');
            return false;
        }
        $input = array(
            'sid' => Ut::token(),
            'pw' => md5($this->getInputVal('pw')),
        );
        if (!$this->model->userUpdate($input, array('id' => $password->user_id))) {
            $this->setErrors('Cannot update password');
            return false;
        }
        $this->model->passwordUpdate(array('confirmed' => 1), array('id' => $password->id));
        return true;
    }

    /**
     * Send a verification request
     * 
     * @return void
     */
    public function moduleVerify() {
        $input = array(
            'id' => $this->getInputVal('id'),
            'title' => $this->getInputVal('title'),
            'author' => $this->getInputVal('author')
        );
        $module = $this->model->moduleFind(array('id' => $input['id'], 'verified' => 0));
        if (!$module) {
            $this->setErrors('Module id ' . $input['id'] . ' not found');
            return;
        }
        // Template data
        $data = array(
            'module_id' => $input['id'],
            'module_title' => $input['title'],
            'user_id' => $this->user->id,
            'user_email' => $this->user->mail,
        );

        $email = array(
            'from' => $this->cfg['email']['noreply'],
            'from_name' => 'Module author',
            'to' => $this->cfg['email']['module_verification'],
            'subject' => 'Module-Store Verification request for module ',
            'body' => $this->getTemplate('views/emails/verification_request.html', $data),
                //'body' => 'Please verify module ' . $input['title'] . '. ID: ' . $input['id'],
        );
        if ($this->sendEmail($email)) {
            $this->model->moduleUpdate(array('verified' => 2), array('id' => $input['id']));
        }
    }

    /**
     * Admin module verification
     * 
     * @return void
     */
    public function adminModuleVerify() {
        $input = array(
            'id' => (int) $this->getInputVal('id'),
            'verifed' => (int) $this->getInputVal('verifed')
        );
        $module = $this->model->moduleFind(array('id' => $input['id']));
        if (!$module) {
            $this->setErrors('Module id ' . $input['id'] . ' not found');
            return;
        }
        $this->model->moduleUpdate(array('verified' => $input['verifed']), array('id' => $input['id']));
    }

    /**
     * Send an email from admin
     * 
     * @return void
     */
    public function adminModuleEmail() {
        $input = array(
            'id' => $this->getInputVal('id'),
            'user_id' => $this->getInputVal('user_id'),
            'subject' => $this->getInputVal('subject'),
            'body' => $this->getInputVal('body'),
        );

        $user = $this->model->userFind(array('id' => $input['user_id']));
        if (!$user) {
            $this->setErrors('User id ' . $input['user_id'] . ' not found');
            return;
        }

//        if ($user->mail == '') {
//            $this->setErrors('User id ' . $input['user_id'] . ' has no email address');
//            return;
//        }
        // Template data
        $data = array(
            'reason' => $input['body'],
        );

        $email = array(
            'from' => $this->cfg['email']['noreply'],
            'from_name' => 'Administrator',
            'to' => $user->mail, //$this->cfg['email']['module_verification'],
            'subject' => $input['subject'],
            'body' => $this->getTemplate('views/emails/verification_failed.html', $data),
                //'body' => $input['body'],
        );
        $this->sendEmail($email);
    }

    /**
     * Send email
     * 
     * @param array $param
     * return bool
     */
    public function sendEmail($param) {
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->IsSMTP();
            $mail->Host = "smtprelaypool.ispgateway.de";
            //$mail->SMTPDebug  = 2;                    
            $mail->SMTPAuth = true;
            $mail->Host = "smtprelaypool.ispgateway.de";
            $mail->Port = 25;
            $mail->Username = "info@popp.eu";
            $mail->Password = "wmNgkx.qUgj7";

            $mail->SetFrom($param['from'], $param['from_name']);
            $mail->AddAddress($param['to'], 'Unknown');
            $mail->Subject = $param['subject'];
            $mail->Body = $param['body'];
            $mail->IsHTML(true);
            $mail->Send();
            return true;
        } catch (phpmailerException $e) {
            $this->setErrors($e->errorMessage());
            return false;
        }
    }

    /**
     * Unpack a zip file
     * 
     * @param string $path
     * @param string $target
     * return bool
     */
    public function unpackZip($path, $target) {
        $zip = new ZipArchive;
        if ($zip->open($path) === TRUE) {
            $zip->extractTo($target);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unpack a tar.gz file
     * 
     * @param string $path
     * @param string $target
     * return bool
     */
    public function unpackTarGz($path,$target) {
       
        // decompress from gz
        $p = new PharData($path);
        $p->decompress(); // creates /path/to/my.tar
// unarchive from the tar
        $phar = new PharData(strtok($path, '.').'.gz');
        $phar->extractTo(strtok($path, '.'));
        return true;
        //var_dump($path,$target,strtok($path, '.'));
        //die;
    }

    /**
     * Unpack a tar.gz file
     * 
     * @param string $path
     * @param string $target
     * return bool
     */
    public function unpackTarGz_($path, $target) {
        if (!is_dir($target)) {
            mkdir($target);
        }
        // This input should be from somewhere else, hard-coded in this example
        $file_name = $path;

        // Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $file_name);

        // Open our files (in binary mode)
        $file = gzopen($file_name, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }
        // Files are done, close files
        fclose($out_file);
        gzclose($file);

        //var_dump($out_file_name);
        //return;

        $phar_data = new PharData($out_file_name);
        $phar_data->extractTo(str_replace(".tar", "", $out_file_name));

        //unlink($out_file_name);
        unset($phar_data);
        Phar::unlinkArchive($out_file_name);
        return true;
    }

    /**
     * Pack to a tar.gz archive
     * 
     * @param string $path
     * @param string $target
     * return bool
     */
    public function packTargz($source, $target) {
        try {
            $a = new PharData($target . '.tar');
            $a->buildFromDirectory($source);
            $a->compress(Phar::GZ);
            unset($a);
            Phar::unlinkArchive($target . '.tar');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Upload and repack a file
     * 
     * @param string $input
     * @param object $uploader
     * @param string  $file_path
     * @param string $file_path_temp
     * return bool
     */
    public function uploadRepackFile($input, $uploader, $file_path, $file_path_temp) {
        //file is the filebrowse element name
        if (!$uploader->uploadFile($input)) {
            $this->setErrors($uploader->getMessage());
            return false;
        }
        //get uploaded file name, renames on upload//
        $file = $uploader->getUploadName();
        $file_name = strtok($file, '.');
        $file_extension = $uploader->getExtension($file);
        switch ($file_extension) {
            case 'gz':
                //unpackTarGz($path, $target)
                if (!$this->unpackTarGz($file_path . $file_name.'.tar.gz', $file_path_temp . $file_name)) {
                    $this->setErrors('Unable to unpack file "' . $file . '"');
                    return false;
                }
                //$this->setErrors('Unknown file extension in "' . $file . '"');
                //return $file_name . '.tar.gz';
                break;
            case 'zip':
                if (!$this->unpackZip($file_path . $file, $file_path_temp . $file_name)) {
                    $this->setErrors('Unable to unpack file "' . $file . '"');
                    return false;
                }
                break;
            default:
                $this->setErrors('Unknown file extension in "' . $file . '"');
                return false;
        }
        //do {
        /* if ($file_extension !== 'zip') {
          break;
          } */
        // Unpack zip file
        // Unlink uploaded Zip file
        unlink($file_path . $file);
        if (is_file($file_path . $file_name . '.tar.gz')) {
            unlink($file_path . $file_name . '.tar.gz');
        }
        // Create a tar.gz archive
        if (!$this->packTargz($file_path_temp . $file_name, $file_path . $file_name)) {
            $this->setErrors('Unable to create tar.gz from file "' . $file . '"');
            return false;
        }
        // Clean temp directory
        //Ut::cleanDirectory($file_path_temp . $file_name);
        $file = $file_name . '.tar.gz';
        //} while (false);
        return $file;
    }

    /**
     * Pack to a tar.gz archive
     * 
     * @param string $path
     * @param string $target
     * return bool
     */
    public function uploadSkin($uploader, $skin_path, $skin_path_temp) {
        //file is the filebrowse element name
        if (!$uploader->uploadFile('file')) {
            $this->setErrors($uploader->getMessage());
            return false;
        }
        //get uploaded file name, renames on upload//
        $file = $uploader->getUploadName();
        $file_name = strtok($file, '.');
        $file_extension = $uploader->getExtension($file);

        // Filename default is not allowed
        if ($file_name === 'default') {
            if (is_file($skin_path . $file)) {
                unlink($skin_path . $file);
            }
            $this->setErrors('File name "' . $file_name . '" is not allowed. Please select a different name and try again.');
            return false;
        }
        do {
            if ($file_extension !== 'zip') {
                break;
            }
            // Unpack zip file
            if (!$this->unpackZip($skin_path . $file, $skin_path_temp . $file_name)) {
                $this->setErrors('Unable to unpack file "' . $file . '"');
                return false;
            }
            // Unlink uploaded Zip file
            unlink($skin_path . $file);
            if (is_file($skin_path . $file_name . '.tar.gz')) {
                unlink($skin_path . $file_name . '.tar.gz');
            }
            // Create a tar.gz archive
            if (!$this->packTargz($skin_path_temp . $file_name, $skin_path . $file_name)) {
                $this->setErrors('Unable to create tar.gz from file "' . $file . '"');
                return false;
            }
            // Clean temp directory
            Ut::cleanDirectory($skin_path_temp . $file_name);
            $file = $file_name . '.tar.gz';
        } while (false);

        return $file;
    }

}
