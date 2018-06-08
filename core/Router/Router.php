<?php
/**
* Router.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Creer des routes puis aller sur le bon chemin [Controller, Action] puis extraire 
* les parametres
*/
namespace Stephanie\Router;

use Stephanie\Request;

define('CTRL_NS', "App\\Controller\\");
define('CTRL_EXT', "");

class Router {
    private static $url;
    private $routes = [];
    private $namedRoutes = [];


    public function __construct($url = null){
        $url == null ? $url = $_GET['url'] : $url ;
        self::$url = $url;
        $this->parseRoutes();
    }
    /**
    * Create des routes pour la methode GET
    * @param string $path Le chemin du route
    * @param mixed $callable Controller et Action a rediriger vers
    * @param string $name Nom du route
    * @return App\Router\Router()
    */
    public function get($path, $callable, $name = null){
        return $this->add($path, $callable, $name, 'GET');
    }

    /**
    * Creation des routes avec la methode post
    * @param string $path Le chemin du route
    * @param mixed $callable Controller et Action a rediriger vers
    * @param string $name Nom du route
    * @return App\Router\Router()
    */
    public function post($path, $callable, $name = null){
        return $this->add($path, $callable, $name, 'POST');
    }

    /**
     * Creation d'une route avec les methodes GET et POST
     * @param string $path Le chemin du route
     * @param mixed $callable Controller et Action a rediriger vers
     * @param string $name Nom du route
     * @return App\Router\Router()
     */
    public function set($path, $callable, $name = null)
    {
        return $this->add($path, $callable, $name, ['GET', 'POST']);
    }

    /**
    * Ajoute une route dans l'application
    */
    private function add($path, $callable, $name, $method){
        $route = new Route($path, $callable);

        if (\is_array($method)) {
            foreach($method as $type) {
                $this->routes[$type][] = $route;
            }
        } else {
            $this->routes[$method][] = $route;
        }

        if ($name){
            $this->namedRoutes[$name] = $route;
        }

        return $this;
    }
    /**
    * Demarre l'application avec les bonnes routes'
    */
    public function run(){
        if (! isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new RouterException("REQUEST METHOD does not exist", 405);
        }

        $_SESSION['router'] = $this->buildUrl(self::$url, 'array');
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match(self::$url)){
                $_SESSION['router'] = $this->buildUrl($route->getCallable(), 'array');
                return $this->call($route->getCallable(), $route->getMatches(), $route->getPath());
            }
        } 
        if (($output = $this->parseUrl())) {
            return $this->call($output);
        }
        throw new RouterException("L'URL demandé n'existe pas ou a été mal taper.");
    }

    public function url($name, $params = []) {
        if (! isset($this->namedRoutes[$name])){
            throw new RouterException("No route matches this name");
        }
        return $this->namedRoutes[$name]->getUrl($params);
    }
    /**
    * Decomposition de l'url pour en faire un controller, une action et extraire les parametres (args)
    * @param string $url L'url a parser
    * @param boolean $exists Si on doit seulement renvoyer la route si le controller et l'action existe
    * @return array|false On retourne un array s'il existe laction demander ou false si absent' 
    */
    public static function parseUrl($url = null, $exists = false) {
        $params = explode('/', $url == null ? self::$url : $url);
        $url = ['controller' => ucfirst($params[0]), 'action' => ! empty($params[1]) ? $params[1] : null];
        array_shift($params); array_shift($params);
        $url['matches'] = $params;
        $url['class'] = CTRL_NS . $url['controller'] . CTRL_EXT;

        if (class_exists($url['class']) && method_exists($url['class'], $url['action']) || $exists) { return $url; }
        return false;

    }
    /**
    * Appel d'un controller, d'une action avec les parametres passees
    * @param $callable array|string|object Peut etre soit un string (Controller#action), soit un array (['controller' => 'value','action' => 'value', 'matches' => array]) ou un objet (function(){})
    * @param $matches array La liste des matches (Parametres a passer comme args dans la methode)
    */
    public function call($callable, $matches = [], $path = null) {
        if($path == null){
            $_SESSION['current_page'] = self::buildUrl($callable);
        } else {
            $_SESSION['current_page'] = self::buildUrl($path);
        }
        if (is_string($callable)){
            $params = explode("#", $callable);
            $controller = CTRL_NS . $params[0] . CTRL_EXT;
            $controller = new $controller();
            return call_user_func_array([$controller, $params[1]], $matches);
        } else if(is_array($callable)) {
            if(! empty($callable['matches']) && is_array($callable['matches'])) {
                $matches = $callable['matches'];
            }
            $controller = CTRL_NS . $callable['controller'] . CTRL_EXT;
            $controller = new $controller();
            return call_user_func_array([$controller, $callable['action']], $matches);
        } else { return call_user_func_array($callable, $matches); }
    }


    /**
     * Construction de l'adresse URL a partir des methodes
     * valable dans l'application
     * ['controller' => 'Cont', 'action' => 'method', 'q' => 'queries']
     * App#debut
     * users/profil
     * @param mixed $params L'adresse a construire
     * @param string $type Type de construction, array ou string
     */
    public static function buildUrl($params = null, $type = 'string')
    {
        if ($params === 'hback'){
            return Request::getReferer();
        }

        if (! is_array($params) && preg_match('#(((https?|ftp)://(w{3}\.)?)(?<!www)(\w+-?)*\.([a-z]{2,4}))#', $params)){
            return $params;
        }

        $controller = ''; 
        $action     = '';
        $parametres = [];
        if(\is_array($params)) {
            $controller = empty($params['controller']) ? '': '/' . $params['controller'];
            $action     = empty($params['action']) ? '': '/' . $params['action'];
            $action    .= ! empty($params['q']) ? \trim($params['q']) : '';
        } else if (\is_string($params)) {
            $param = explode('#', $params);

            if (count($param) > 1){
                $controller = '/' . $param[0];
                $action     = '/' . $param[1];
            } else if (count($param) == 1) {
                $controller = $param[0];
            }
        }

        if($type == 'array') {
            $building = self::parseUrl(trim($controller, '/') . $action, true);
            return array_merge($building, ['domain' => DOMAIN]);
        }

        return strtolower(substr(DOMAIN, 0, -1) . $controller . $action);
    }

    /**
     * Construire les routes a partir du fichier qui leurs ai destinnee
     * config/routes.json
     * @return App\Router\Router
     */
    public function parseRoutes()
    {
        $routes = json_decode(file_get_contents(CORE_CONFIG . DS . "routes.json")) ?: [];
        foreach($routes as $method => $route){
            foreach ($route as $path => $callable) {
                $name = ! empty($callable[1]) ? $callable[1] : null;
                if (strtoupper($method) == 'SET'){
                    $this->add($path, $callable[0], $name, ['GET', 'POST']);
                } else {
                    $this->add($path, $callable[0], $name, strtoupper($method));
                } 
            }
        }
        return $this;
    }

    public static function getUrl()
    {
        return self::$url;
    }
}