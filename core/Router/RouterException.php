<?php

    namespace Stephanie\Router;

    use Stephanie\View\View;

    class RouterException extends \Exception {

        protected $view;

        public function __construct($message = null, $code = 404){
            parent::__construct($message, $code);
            $this->view = new View();
            $this->getContent();
        }

        public function getContent(){
            $content = $title = $this->message;
            $code    = $this->code;
            // $line    = $this->getLine();
            // $fichier = str_replace(substr(APPS_DIR, 0, -22), '', $this->getFile());

            $this->view->render('exception', compact('title', 'content', 'code', 'line', 'fichier'));
        }
    }