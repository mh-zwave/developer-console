<?php

/**
 * Simple database class
 *
 * @author Martin Vach
 */
class Db {

    private $hostname;
    private $username;
    private $password;
    private $database;
    public $prefix;
    private $mysqli;

    /**
     * Class constructor
     * 
     * @param array $cfg
     * @return void
     */
    public function __construct($cfg) {
        //extract($cfg);
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * Open a DB connection
     * 
     * @return void
     */
    public function openConnection() {
        //$this->mysqli = new mysqli($this->hostname, $this->username, $this->password, $this->database);
        if (mysqli_connect_errno()) {
            header( 'HTTP/1.1 500 DB Error' );
            die(mysqli_connect_error());
        }
    }

    public function closeConnection() {
        return $this->mysqli->close();
    }

    public function ecapeString($string) {
        return addslashes($string);
    }
    public function inserId() {
        return $this->mysqli->insert_id;
    }

    public function query($query) {
        $query = str_replace("}", "", $query);
        $query = str_replace("{", $this->prefix, $query);
        $this->mysqli->query("SET NAMES utf8");
        $this->mysqli->query("set character_set_client='utf8'");
        $this->mysqli->query("set collation_connection='utf8_general_ci'");
        //$result = $this->mysqli->query($this->ecapeString($query));
        $result = $this->mysqli->query($query);
       if(!$result) {
           header( 'HTTP/1.1 500 DB Error' );
           die($this->mysqli->error);
       }
       return $result;
    }

}
