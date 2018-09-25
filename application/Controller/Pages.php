<?php
/**
* Pages.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Controller des pages de l'application
*/
namespace App\Controller;

use Stephanie\Components\Files;

class Pages extends AppController {

    public function index()
    {
        $files = new Files();

        // debug($files->getContents()); exit;

        $this->render('index', [
            'title' => 'Accueil',
            'files' => $files->getContents()
        ]);
    }

    public function skatek()
    {
        $this->render('skatek', [
            'title' => 'Skatek Corporation'
        ]);
    }

    public function apropos()
    {
        $this->render('apropos', [
            'title' => "A propos"
        ]);
    }

    public function manuel()
    {
        $this->render('manuel', [
            'title' => "Manuel d'utilisation"
        ]);
    }
}
