<?php

/**
 * Route class
 *
 * @author Martin Vach
 */
class Route {

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    protected $uri = 'home';
    
    /**
     * The URI from the GET
     *
     * @var string
     */
    protected $uriGet = 'home';

    /**
     * Page params.
     *
     * @var array
     */
    protected $params = array();

    /**
     * Create a new Route instance.
     *
     * @param  string  $uri
     * @return void
     */
    public function __construct($uri) {
        $this->uriGet = trim($uri, '/');
        $this->uri = (!isset($uri) || $uri === '' ? $this->uri : trim($uri, '/'));
    }

    /**
     * Set the URI that the route responds to.
     *
     * @return string
     */
    private function setUri($array) {
        $uri = '';
        if (is_array($array)) {
            foreach ($array as $value) {
                if ($value !== '') {
                    $uri .= $value . '/';
                }
            }
        }
        $this->uri = ($uri !== '' ? rtrim($uri, '/') : $this->uri);
    }
    /**
     * Get uri
     * 
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Set params
     *
     * @param  int  $offset
     * @return void
     */
    private function setParams($offset) {
        $array = explode('/', $this->uriGet);
         $params = array_slice($array, (int)$offset);
        if (is_array($params)) {
            foreach ($params as $value) {
                if ($value === '') {
                    continue;
                }
                $this->params[] = $value;
            }
        }
    }

    /**
     * Get params
     * 
     * @return void
     */
    public function getParams() {
        
        return $this->params;
    }

    /**
     * Get a single param by index
     * 
     * @param  int  $index
     * @return void
     */
    public function getParam($index) {
        return isset($this->params[$index]) ? $this->params[$index] : null;
    }

    /**
     * Parse uri string
     *
     * @param int $offset
     * @return void
     */
    private function parseUri($offset) {
        $array = explode('/', trim($this->uri));
        $this->setUri(array_slice($array, 0, (int) $offset));
    }

    /**
     * Determine if the route matches given request.
     *
     * @return bool
     */
    private function matchRoute($request) {
        $match = false;
        $request = trim($request, '/');
        if ($request === $this->uri) {
            $match = true;
        }
        return $match;
    }

    /**
     * Run the route action and return the response.
     * 
     * @param dtring $request
     * @param int $offset
     * @return void
     */
    public function match($request, $offset) {
        $this->parseUri($offset);
        if($this->matchRoute($request)){
             $this->setParams($offset);
             return true;
        }
       
         //var_dump($this->params);
        //return $this->matchRoute($request);
    }

}
