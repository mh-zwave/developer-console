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
    public function __construct($model, $cfg,$user) {
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
    public function setInputs($array) {
        foreach ($array as $key => $value) {
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
    private function sendEmail($param) {
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

}
