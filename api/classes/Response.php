<?php

/**
 * Response class
 *
 * @author Martin Vach
 */
class Response {

    public $status = 200;
    public $message = 'OK 200';
    public $error = null;
    public $data = array();
    
    /**
     * Response json output
     * 
     * @param Response $response
     * @return mixed
     */
    public function json($response) {
        header('Content-Type: application/json');
        header('HTTP/1.0 '.$response->status.' '.$response->message);
        die(json_encode($response));
    }

}
