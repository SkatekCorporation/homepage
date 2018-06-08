<?php
/**
* Route.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Parametres d'une route en particulier
*/
namespace Stephanie\Router;

class Route {
    private $originalPath;
    private $path;
    private $callable;
    private $matches = [];
    private $params = [];

    public function __construct($path, $callable){
        $this->path         = trim($path, '/');
        $this->originalPath = $path;
        $this->callable     = $callable;
    }

    public function with($param, $regex){
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this;
    }

    public function match($url){
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);

        $regex = "#^$path$#i";

        if (! preg_match($regex, $url, $matches)){
            return false;
        }

        array_shift($matches);

        $this->matches = $matches;

        return true;
    }

    private function paramMatch($match){
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function getUrl($params){
        $path = $this->path;
        foreach($params as $k => $v){
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }

    public function getCallable(){
        return $this->callable;
    }

    public function getMatches(){
        return $this->matches;
    }

    public function getPath()
    {
        return $this->originalPath;
    }
}