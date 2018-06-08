<?php


namespace Stephanie;

class Request {
    private static $datas    = [];
    private static $queries  = [];
    private static $sessions = [];
    private static $files = [];
    private static $method;
    private static $server;

    public function __construct()
    {
        self::$datas    = $_POST;
        self::$queries  = $_GET;
        self::$method   = $_SERVER['REQUEST_METHOD'];
        self::$sessions = $_SESSION;
        self::$server   = $_SERVER;
        self::$files   = $_FILES;
    }

    public static function getData($name = null)
    {
        if ($name == null){
            return self::$datas;
        }
        else if (! empty(self::$datas) && ! empty(self::$datas[$name])){
            return self::$datas[$name];
        }
        return NULL;
    }

    public static function getFile($name = null)
    {
        if ($name == null){
            return self::$files;
        }
        else if (! empty(self::$files) && ! empty(self::$files[$name])){
            return self::$files[$name];
        }
        return NULL;
    }

    /**
     * Obtenir les requetes passes dans l'URL
     * @return mixed Requetes 'sils eistent ou null
     */
    public static function getQuery($name = null){
        if ($name == null) {
            return self::$queries;
        } else if (! empty(self::$queries[$name])){
            return self::$queries[$name];
        }
        return NULL;
    }

    /**
     * Recuperer la valeur de l'URL en cours d'execution
     * @return string Valeur de l'URL
     */
    public static function getUrl(){
        return self::$sessions['current_url'];
    }

    public static function getSession($name = null){
        if ($name == null){
            return self::$sessions;
        } else if (! empty(self::$sessions[$name])) {
            return self::$sessions[$name];
        }
        return NULL;
    }

    public static function setSession($key, $value){
        self::$sessions[$key] = $value;
    }
    
    /**
     * Verifie si la methode HTTP est de cet type
     * @param string $method Methode a comparer
     * @return string|FALSE False, si la methode ne correspond pas au HTTP METHOD
     */
    public static function is($method = null){
        if($method == null || \strtoupper($method) === self::$method) {
            return self::$method;
        }
        return FALSE;
    }

    public static function getReferer(){
        return self::$server['HTTP_REFERER'];
    }
}