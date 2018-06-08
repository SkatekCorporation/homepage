<?php

namespace Stephanie\Router;

class FRouter extends Router {

    private $_absolutePath;

    /**
     * Initialisation
     * @param string $dir Le chemin par defaut a explorer comme ROOT
     */
    public function __construct($dir = null)
    {
        parent::__construct();
        $this->_absolutePath = $_SERVER['DOCUMENT_ROOT'] . DS ;
        $_SESSION['absolutePath'] = $this->_absolutePath;
    }

    public function run()
    {
        $url = trim(self::getUrl(), '/') . '/';
        $chemin = $this->_absolutePath . $url;

        $_SESSION['r_chemin'] = $url;

        if (! is_dir($chemin)){
            return parent::run();
        }
        return $this->call('Pages#index');        
    }
}
