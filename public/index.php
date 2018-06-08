<?php
/**
* Index.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Page d'entrer de l'application. Se charge aussi d'executer les differentes routes de l'application. 
* Les routes ne peuvent etre que du type get ou post. Pour definir une route:
*      $router->get('/url', ['controller' => 'AppController', 'action' => 'methode']); ou
*      $router->post('/url', 'Controller#methode');
*/
    require dirname(__DIR__) . "/config/appload.php";

    // use Stephanie\Router\Router;
    use Stephanie\Router\FRouter;
    use Stephanie\Router\RouterException;

    /**
    * Declaration de la variable $router qui contiendra l'instance de la classe App\Router\Router
    * C'est cette variable qui demarera l'application et qui permettra l'ajout des differentes route
    * Uiliser dans l'application
    * Si aucune route n'est defini, la regle reste simple
    * http://domain.cd/controller/methode/variables/qui/doivent/etre/passes/dans/la/methode
    * controller => Le nom de la classe qui servira de Controller
    * method     => Le nom de la methode a appeler
    */

    $router = new FRouter();

    /**
    * Definitions des routes de l'application
    *
    * Nous ne pouvons jusque la, ajouter seulement les methodes HTTP GET et POST
    * Pour ajouter une route qui utilisera la REQUEST_METHOD GET,
    *   $router->get('/nom_du_route', [
    *       'controller' => 'ClassDuController',
    *       'action'     => 'methode_a_appeler'
    *   ]);
    * Il existe une raccourcis dans tout ca
    *   $router->get('/nom_du_route', 'ClassController#methode_a_appeler');
    * Pour ajouter une route qui utilisera la REQUEST_METHOD POST
    *   $router->post('/nom_du_route', 'ClassController#methode_a_appeler');
    * Pour ajouter une route qui soit a la fois de type GET et POST
    *   $router->set('/nom_du_route', 'ClassController#methode');
    * Pour definir la page d'accueil
    *   $router->get('/', 'Controller#method_a_appeler');
    */
    

    /**
     * Nous essayons de lancer l'application d'une maniere sur et de capturer les erreurs 
     * de routage s'ils existent
     */
    
    try {
        $router->run();
    } catch (RouterException $e){ }
    