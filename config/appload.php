<?php

/**
* Appload.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Les fonctions globales de l'application ainsi que les importantes constantes
*/
    
    /**
     * Si tu veut activer le cache ou le mode developpement/production
     * DEBUG => TRUE   Pour le developpement
     * DEBUG => FALSE  Pour la production
     */
    define('DEBUG', TRUE);

    verification_interface();
    session_start();
    require dirname(__DIR__) . "/vendor/autoload.php";


    /**
    * Nom du domaine de l'application
    * Si l'application se trouve dans un sous dossier, veuillez taper le chemin relatif vers le sous dossier 
    * contenant l'application avec tout ses fichiers
    */
    define('DOMAIN', '/homepage/');

    /**
    * Configuration base de données
    * Remplacer correctement les valeurs selon la configuration de votre BDD
    * HOST        => Hote de la BDD, laisser localhost par defaut, si vous etes en local
    * DB_NAME     => Nom de la base de donnees
    * DB_USERNAME => Nom d'utilisateur de la BDD, root par defaut
    * DB_PASSWORD => Mot de passe de la base de donnees
    */
    define('HOST',        'localhost');
    define('DB_NAME',     'none');
    define('DB_USERNAME', 'none');
    define('DB_PASSWORD', 'none');

    define('DS', DIRECTORY_SEPARATOR);
        


    /**
    * Affichage des erreurs
    * 
    * Ceci est geré avec la valeur de la constante DEBUG
    */
    error_reporting(DEBUG ? E_ALL : FALSE);
    
    /**
    * Les constantes des emplacements des fichiers de l'application
    * Il ne pas important de modifier les valeurs par defaut
    */
    define("ROOT",          dirname(__DIR__));
    define("APPS_DIR",      ROOT     . DS . "application");
    define("TEMPLATES_DIR", APPS_DIR . DS . "Templates");
    define("CACHES_DIR",    ROOT     . DS . "tmp");

    /**
     * Definitions des constantes du coeur de l'application
     */
    define("CORE_DIR",      ROOT     . DS . "core");
    define("CORE_CONFIG",   ROOT     . DS . "config");
    define("CORE_TEMPLATES",CORE_DIR . DS . "Templates");
    define("FLASH_KEY",     "StephanieFlash");

    /**
    * Constantes des fichiers pour la vue
    */
    define('ASSETS_DIR',    DOMAIN     . 'assets'. DS);
    define('CSS_DIR',       ASSETS_DIR . 'css'   . DS);
    define('JS_DIR',        ASSETS_DIR . 'js'    . DS);
    define('IMAGES_DIR',    ASSETS_DIR . 'img'   . DS);
    define('FILES_DIR',     ASSETS_DIR . 'files' . DS);
    define('FONTS_DIR',     ASSETS_DIR . 'fonts' . DS);

    /**
     * Constatntes pour IMG
     */
    define('IMAGES_ROOT', ROOT . DS . 'public' . DS . 'assets' . DS . 'img' . DS);
    define('IMAGES_TMP', IMAGES_DIR . 'temp' . DS);

     /**
     * Pour l'affichage des variables en mode debugger,
     * Accessible partout dans l'application
     */
    function debug($var = null){
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
    
    /**
     * Fonction globale pour afficher les erreurs
     * En attendant l'implementation de la class pour le FLASH
     * @param array|string $messages Le message a afficher
     * @param string $class La classe pour decorer l'affichage
     */
    function debugErrors($messages = null, $class = 'danger'){
        echo "<br><div class=\"container\">";
        echo "<div class=\"alert alert-$class\">";
        echo "<h4>Veuillez corriger";
        if (count($messages) > 1){ echo " les erreurs suivantes"; } 
        else { echo " l'erreur suivante"; }
        echo " :</h4>";

        if (is_array($messages)) {
            echo "<ul>";
            foreach($messages as $message){ echo "<li><strong>$message</strong></li>"; }
            echo "</ul>";
        } else { echo $messages; }
        echo "</div></div>";
    }

    function debugSuccess($message = null, $class = 'success'){
        echo '<div class = "container">';
        echo '<div class = "alert alert-' . $class . '">';
        echo $message;
        echo '</div></div>';
    }

    function verification_interface(){
        if (php_sapi_name() == 'cli') {
            print("\n\tSalut!\n\tNous ne prenons pas encore en charge ce type d'interface\n\n");
            print("\tCopyright 2018, Skatek Corporation <https://skatek.net>\n\n");
            exit();
        }
    }
    
