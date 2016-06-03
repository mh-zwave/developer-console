<?php

/**
 * Utility class
 * 
 * @author Martin Vach
 */
class Ut {

    /**
     * Set url
     * 
     * @return string
     */
    public static function uri($uri) {
        return '?uri=' . $uri;
    }

    /**
     * 
     * @param string $uri
     * @param array $message
     * @param string $status
     * @return void 
     */
    public static function redirectTo($uri, $message = false, $status = 'danger') {
        if (is_array($message)) {
            static::setFlash($message, $status);
        }
        header('Location: ' . $uri);
        die;
    }

    /**
     * 
     * @param string $uri
     * @param array $message
     * @return void 
     */
    public static function redirectWithValidation($uri, $message, $data = array()) {
        if (is_array($message)) {
            static::setValidationError($message, $data);
            header('Location: ' . $uri);
            die;
        }
    }

    /**
     * Set user
     * 
     * @param array $param
     * @return void
     */
    public static function setUser($param) {
        static::setSession('user', $param);
    }

    /**
     * Get user
     * 
     * @return array
     */
    public static function user() {
        return static::session('user');
    }

    /**
     * Check if user is loged in
     * 
     * @return void
     */
    public static function authRequired() {
        if (is_null(static::user())) {
            static::setFlash(array('You are not authorized to sse this page. Please log in'));
            header('Location: ' . static::uri('login'));
            die;
        }
    }

    /**
     * Set session
     * 
     * @param string $name
     * @param mixed $session
     * @return void
     */
    public static function setSession($name, $param) {
        $_SESSION[$name] = $param;
    }

    /**
     * Unset session
     * 
     * @param string $name
     * @return void
     */
    public static function unsetSession($name) {
        unset($_SESSION[$name]);
    }

    /**
     * Get session
     * 
     * @param string $name
     * @return mixed
     */
    public static function session($name) {
        return $_SESSION[$name];
    }

    /**
     * Set flash message
     * 
     * @return void
     */
    public static function setFlash($message, $status = 'danger') {

        $param = array(
            message => $message,
            status => $status
        );
        static::setSession('flash', $param);
    }

    /**
     * Get html flash message
     * 
     * @return void
     */
    public static function flashHtml() {
        if ($flash = static::session('flash')) {
            require_once 'views/flash.php';
            static::unsetSession('flash');
        }
    }

    /**
     * Set validation error message
     * 
     * @param array $param
     * @return void
     */
    public static function setValidationError($param, $data = array()) {
        static::setSession('validation', $param);
        static::setSession('form_data', $data);
    }

    /**
     * Get html validation message
     * 
     * @return void
     */
    public static function validation() {
        if ($validation = static::session('validation')) {
            require_once 'views/validation.php';
            static::unsetSession('validation');
            static::unsetSession('form_data');
        }
    }

    /**
     * Get form data
     * 
     * @param string $key
     * @return mixed
     */
    public static function formData($key) {
        if ($data = static::session('form_data')) {
            return (isset($data[$key]) ? stripslashes($data[$key]) : null);
        }
        return null;
    }

    /**
     * Create token from a random string
     * 
     * @return string
     */
    public static function token() {
        return md5(time() . rand(100, 999));
    }

    /**
     * Check for empty input
     * 
     * @param mixed $input
     * @return bool
     */
    public static function isEmpty($input) {
        if ($input === '') {
            return true;
        }
        return false;
    }

    /**
     * Check email address
     * 
     * @param mixed $input
     * @return bool
     */
    public static function isEmail($input) {
        return true;
        // Eregi is deprecated
//        if (eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $input)) {
//            return true;
//        }
        return false;
    }

    /**
     * Check min string lenght
     * 
     * @param string $input
     * @param int $value
     * @return boolean
     */
    public static function strLenght($input, $value) {
        if (strlen($input) >= $value) {
            return true;
        }
        return false;
    }

    /**
     * Get content
     * 
     * @param string $url
     * @return mixed/bool
     */
    public static function getContent($url) {
        if (!$str = @file_get_contents($url)) {
            return false;
        }
        return $str;
    }

    /**
     * Get content from URL
     * 
     * @param string $url
     * @return mixed/bool
     */
    public static function getContentFromUrl($url) {
        if (ini_get('allow_url_fopen')) {
            return self::getContent($url);
        } else {
            return self::getContentCurl($url);
        }
    }

    /**
     * Get content CURL
     * 
     * @param string $url
     * @return mixed/bool
     */
    public static function getContentCurl($url) {
        if (function_exists('curl_version')) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
            curl_setopt($ch, CURLOPT_URL, $url);
            if (!$str = $str = curl_exec($ch)) {
                return false;
            }
            curl_close($ch);
            return $str;
        }
    }

    /**
     * Write string to a file
     * 
     * @param string $file
     * @param mixed $str
     * @return json/bool
     */
    public static function writeToFile($file, $str, $flags = null) {
        if (@file_put_contents($file, $str, $flags)) {
            return true;
        }
        return false;
    }

    /**
     * Check if a string is JSON
     * 
     * @param json $str
     * @return boolean
     */
    public static function isValidJson($str) {
        return is_array(json_decode($str, true));
    }

    /**
     * Scan directory
     * 
     * @param string $directory
     * @return array
     */
    public static function getFilesFromDir($directory) {
        return array_diff(scandir($directory), array('..', '.'));
    }

    /**
     * Get 
     *
     * @resource string $resource
     */
    public static function getImageFromResource($resource) {
        return @getimagesize($resource);
    }

    /**
     * Delete a file or recursively delete a directory
     * 
     * @param string $dir Path to file or directory
     * @return void
     */
    public static function cleanDirectory($dir) {
//        var_dump($dir);
//        return;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) === "dir") {
                        Ut::cleanDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Get files in the directory
     * 
     * @param string $dir Path to file or directory
     * @param array $ext List of allowed extensions
     * @return void
     */
    public static function getFilesIndDir($dir, $ext = array()) {
        $files = array();
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && is_file($dir . $object)) {
                    if (!empty($ext)) {
                        if (in_array(strtolower(pathinfo($object, PATHINFO_EXTENSION)), $ext)) {
                            array_push($files, $object);
                        }
                    } else {
                        array_push($files, $object);
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Fill template with data
     *
     * @resource string $resource
     */
    public static function getImageExtFromResource($resource) {
        $ext = false;

        if (!$image = @getimagesize($resource)) {
            return false;
        }
        switch ($image['mime']) {
            case 'image/png':
                $ext = '.png';
                break;
            case 'image/jpeg':
                $ext = '.png';
                break;
            case 'image/pjpeg':
                $ext = '.png';
                break;
            default:
                break;
        }
        return $ext;
    }

    /**
     * Get image or placeholder
     *
     * @resource string $resource
     */
    public static function getImageOrPlaceholder($image, $placeholder = 'app/img/icon-placeholder.png') {
        if (!is_file($image)) {
            return $placeholder;
        }
        return $image;
    }

    /**
     * Convert string to slug
     *
     * @param string $str
     * @return  string
     */
    public static function toSlug($str) {
        # special accents
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'Ð', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', 'Œ', 'œ', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'Š', 'š', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Ÿ', 'Z', 'z', 'Z', 'z', 'Ž', 'ž', '?', 'ƒ', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), str_replace($a, $b, $str)));


        //return strtr($string, $table);
    }

    /**
     * Cut text after (x) amount of characters
     *
     * @param string $str
     * @param int $chars
     * @param string $end
     * @return  string
     */
    public static function cutText($str, $chars, $end = '...') {
        if (strlen($str) <= $chars) {
            return $str;
        }
        $new = substr($str, 0, $chars + 1);
        return substr($new, 0, strrpos($new, ' ')) . $end;
    }
    /**
     * Build a server path
     * @return string
     */
    public static function serverPath() {
        $s = &$_SERVER;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        $uri = $protocol . '://' . $host . $s['REQUEST_URI'];
        $segments = explode('?', $uri, 2);
        $url = $segments[0];
        return $url;
    }

}
