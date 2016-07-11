<?php

/**
 * Loggit class
 *
 * @author Martin Vach
 */
class Loggit {
    private $file = 'storage/log.txt';
    private $date = null;
    private $status = 500;
    private $message = 'An error has occured';
    public $data = array();
    
    /**
     * Set path to the logfile
     * 
     * @param string $file
     */
    public function setFile($file){
        $this->file = $file;
    }
    
     /**
     * Set log message
     * 
     * @param string $message
     */
    public function setMessage($message){
        $this->message = $message;
    }
    
    /**
     * Set status
     * 
     * @param string $status
     */
    public function setStatus($status){
        $this->status = $status;
    }
    
    /**
     * Response json output
     * 
     * @param Response $response
     * @return mixed
     */
    public function create($message = null) {
        $txt = date('Y-m-d H:i:s').';'.$this->status.';'.($message ? $message : $this->message);
        $myfile = file_put_contents($this->file, $txt.PHP_EOL , FILE_APPEND);
    }

}
