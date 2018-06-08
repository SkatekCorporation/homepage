<?php

    namespace Stephanie\View;

    use \Twig_Loader_Filesystem;
    use \Twig_Environment;
    use Stephanie\Request;

    class View
    {
        protected $view;
        private $dir;
        private $globals;

        public function __construct($dir = null, $build = true, $absolute = false)
        {
            if($build)
                $this->setDir(substr($dir, 15, 30));
            else
                $this->setDir($dir); 

            if($absolute){
                $chemin = $dir;
            } else {
                $chemin = TEMPLATES_DIR . DS . $this->dir;
            }

            $loader        = new Twig_Loader_Filesystem($chemin);
            $this->globals = json_decode(file_get_contents(CORE_CONFIG . DS . 'global_view.json')) ?: [];
            
            $loader->addPath(CORE_TEMPLATES . DS . 'layouts');
            $loader->addPath(TEMPLATES_DIR  . DS . 'layouts');

			$this->setView(new Twig_Environment($loader, [
				'cache' => ! DEBUG ? CACHES_DIR . '/twig' : FALSE
            ]));
            $this->globalVars();
            $this->view->addExtension(new Twig());
        }
        
        /**
        * @uses $this->render('template', ['var1' => 'value1']); Sert a rendre une vue avec les variables
        * @param $template String La vue a rendre. Le template doit se trouver dans le dossier du controller. Sans l'extension html
        * @param $options Array Liste des variables qui doivent etre rendus au template
        */

        public function render($template = null, $options = [])
        {
            if (($tmp = \strtolower(substr($template, -5, 5))) == '.html')
                echo $this->getView()->render($template, $options);
            else
                echo $this->getView()->render($template . '.html', $options);
            exit();
        }

        public function getView(){
            return $this->view;
        }

        protected function setView($loader = null){
            $this->view = $loader;
        }

        public function setDir($dir)
        {
            $this->dir = strtolower($dir);
        }
        
        /**
        * Ajout des variables globales accessible aux fichiers de vues (templates)
        */
        private function globalVars() {
            foreach($this->globals as $key => $value){
                if (is_array($value)) { $value = $value[0]; }
                $this->view->addGlobal($key, $value);
            }
            $this->view->addGlobal('rootLink'    , DOMAIN);
            $this->view->addGlobal('current_page', Request::getSession('current_page'));            
        }

        public function addGlobal($key = null, $value = null){
            if ($key == null || $value == null){
                return false;
            }
            $this->view->addGlobal($key, $value);
            return true;
        }
    }