<?php
/**
* AppTwig.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Extension suplementaire pour le moteur de template Twig que va utiliser l'Application
* Vers les fichiers de templates
*/

    namespace Stephanie\View;

    use Stephanie\Router\Router;
    use Stephanie\Handlers\Flash;
    use Stephanie\Auth\Auth;

    class Twig extends \Twig_Extension {

        /**
         * Insertion des filters qui seront accesible aux view des tempaltes
         */
        public function getFilters()
        {
            return [
                new \Twig_SimpleFilter('icon', [$this, 'setIcon'], self::isSafe('html'))
            ];
        }

        /**
         * Filtre pour ajouter les icons devant le texte
         */
        public function setIcon($value = null, $icon = null, $options = null)
        {
            if ($options == null){
                $class = 'glyphicon';
            } else if (! empty($options['class'])) {
                $class = $options['class'];
            } else {
                $class = $options;
            }

            if ($icon == null) {
                $icon = 'home';
            }
            
            return '<i class = "'. $class . ' ' . $class . '-' . $icon . '"></i> ' . $value;
        }

        public function setIconFunc($icon = 'home', $class = 'glyphicon')
        {            
            return '<i class = "'. $class . ' ' . $class . '-' . $icon . '"></i> ';
        }

        /**
         * Insertion des fonctions accessible via le moteur de templates Twig
         */
        public function getFunctions()
        {
            return [
                new \Twig_SimpleFunction('cssHtml',     [$this, 'cssHtml'],     self::isSafe('html')),
                new \Twig_SimpleFunction('jsHtml',      [$this, 'jsHtml'],      self::isSafe('html')),
                new \Twig_SimpleFunction('imgHtml',     [$this, 'imgHtml'],     self::isSafe('html')),
                new \Twig_SimpleFunction('linkHtml',    [$this, 'linkHtml'],    self::isSafe('html')),
                new \Twig_SimpleFunction('renderFlash', [$this, 'renderFlash'], self::isSafe('html', 'js')),
                new \Twig_SimpleFunction('icon',        [$this, 'setIconFunc'], self::isSafe('html', 'js')),
                new \Twig_SimpleFunction('activeClass', [$this, 'activeClass']),
                new \Twig_SimpleFunction('url',         [$this, 'buildUrl']),
                new \Twig_SimpleFunction('imgUrl',      [$this, 'imgUrl']),
                new \Twig_SimpleFunction('currentUser', [$this, 'currentUser']),
                new \Twig_SimpleFunction('currentEnt',  [$this, 'currentEnt'])
            ];
        }

        /**
         * Construction de l'adresse URL a partir des methodes
         * valable dans l'application
         * ['controller' => 'Cont', 'action' => 'method', 'q' => 'queries']
         * App#debut
         * users/profil
         */
        public static function buildUrl($params = null)
        {
            return Router::buildUrl($params);
        }

        /**
         * On recupere le chemin URL du fichier CSS que l'on demande
         * @param string $value le fichier que l'on demande
         */
        public static function cssUrl($value = null)
        {
            if ($value == null) 
                return null;
            
            if (\strtolower(substr($value, -4, 4)) == '.css')
                return CSS_DIR . \strtolower($value);

            return CSS_DIR . $value . '.css';
        }

        /**
         * On recupere le chemin URL du fichier JAVASCRIPT que l'on demande
         * @param string $value le fichier que l'on demande
         */
        public static function jsUrl($value = null)
        {
            if ($value == null)
                return null;
            
            if (strtolower(substr($value, -3, 3))== '.js') 
                return JS_DIR . strtolower($value);
            
            return JS_DIR . strtolower($value) . '.js' ;
        }

        /**
         * On recupere le chemin URL de l'image que l'on demande
         * @param string $value l'image que l'on demande l'URL
         * @param string $dir Le repertoire pour chercher
         */
        public static function imgUrl($value = null, $dir = null)
        {
            if ($value == null)
                return null;
            if ($dir != null){
                return IMAGES_DIR . trim($dir, DS) . DS . $value;
            }
            return IMAGES_DIR . $value;
        }

        /**
         * Pour avoir la balise HTML du fichier CCS demander
         * @param string $value Le nom du fichier CSS
         * @param array $params Liste des attributs ainsi que leurs valeurs
         */
        public function cssHtml($value = null, array $params = [])
        {
            if ($value == null) return null;         
            $rel = empty($params['rel']) ? 'rel = "stylesheet" ' : '';
            return '<link ' . $rel . ' href = "' . $this->cssUrl($value) . '" ' . $this->parseParams($params) .' />';
        }

        /**
         * Pour avoir la balise HTML du script demander
         * @param string $value Le nom du script
         * @param array $params Liste des attributs ainsi que leurs valeurs
         */
        public function jsHtml($value = null, array $params = []){
            return '<script src = "' . $this->jsUrl($value) . '" ' . $this->parseParams($params) .'></script>';
        }

        /**
         * Pour avoir la balise HTML d'une image
         * @param string $value Le nom du fichier avec son extension
         * @param array $params Liste des attributs ainsi que leurs valeurs
         */
        public function imgHtml($value = null, array $params = [], $dir = null)
        {
            if ($value == null)
                return null;
            return '<img src = "' . $this->imgUrl($value, $dir) . '" '. $this->parseParams($params) . ' />';
        }

        /**
         * Creation des liens HTML
         */
        public function linkHtml($params = null)
        {
            if (\is_array($params)){
                $name     = empty($params['name']) ? null : $params['name'];
                $value    = empty($params['href']) ? null : $params['href'];
                $options  = empty($params['params']) ? [] : $params['params'];
            } else {
                $params   = func_get_args();
                $value    = empty($params[0]) ? null : $params[0];
                $name     = empty($params[1]) ? null : $params[1];
                $options  = empty($params[2]) ? []   : $params[2];
            }
            
            
            if (! is_string($name)) {
                $name = $value;
            }
            return '<a href = "' . $this->buildUrl($value) . '" ' . $this->parseParams($options) . '>' . $name . '</a>';
        }

        /**
         * Parseur des attributs passer sous forme de tableau
         * @param array $params Liste des attributs
         * @return string La chaine des attributs et leurs valeurs en methode de HTML
         */
        private static function parseParams(array $params = [])
        {
            $parametres = "";

            foreach($params as $key => $param){
                if (is_string($key)) {
                    $parametres .= $key . ' = "' . $param . '" ';
                } else {
                    $parametres .= $param . ' = "" ';
                }
            }
            return trim($parametres);
        }
        
        /**
         * Renvoi active si vous etes sur la page actuel
         */
        public function activeClass($value = null)
        {
            if (self::buildUrl($value) == $_SESSION['current_page']){
                return ' active ';
            }
        }
        
        /**
         * Rendre le flash dans la vue
         * Nous avons utiliser le systeme de notification TOASTR
         */
        public function renderFlash(){
            $flash   = (new Flash())->get();

            if(count($flash) < 1){
                return null;
            }

            $chaine = '';

            $chaine .= $this->cssHtml('toastr.min');
            $chaine .= $this->jsHtml('toastr.min');

            $chaine .= '<script>toastr.' . $flash['type'];
            $chaine .= "(\"" . $flash['message'] . "\", ";
            $chaine .= "'" . $flash['title'] . "')";
            $chaine .= '</script>';
            return $chaine;
        }

        private static function isSafe()
        {
            return [
                'is_safe' => \func_get_args()
            ];
        }
    }
