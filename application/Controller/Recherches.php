<?php
/**
* Recherches.php
* @author Souvenance <skavunga@gmail.com>
* @version 1.1
* @importance Controller des pages de l'application
*/
namespace App\Controller;

use Stephanie\Components\Files;
use Cake\Filesystem\Folder;

class Recherches extends AppController {
    /**
     * Page d'accueil des resultats
     */
    public function index(){
        $term = $this->request->getQuery('search');
        $rec = $this->request->getQuery('search_depth') == 'child' ? true : false;
        $results = $this->find($this->request->getQuery('search_folder'), $term, $rec);
        $nbre = count($results);

        $this->render('index', [
            'title' => 'Recherche',
            'search_term' => $term,
            'current' => $this->request->getQuery('search_folder'),
            'search_nbre' => $nbre,
            'resultats' => $results
        ]);
    }

    /**
     * Recherche dans les dossiers
     * @param string $dir Le repertoire a rechercher
     * @param string $term Le terme a rechercher
     * @return array
     */
    private function find($dir = null, $term = null, $recursive = false){
        $files = new Files();

        $contents = $files->setDirectory($dir)->getContents(); 

        if ($recursive) {
            $alls = [$contents];
            foreach ($contents as $key => $content) {
                if ($content->isDir()) {
                    $alls[] = $files->setDirectory($content->getLink())->getContents();
                }
            }
            $contents = [];
            for($i = 0, $n = count($alls); $i < $n; $i++) {
                $contents = array_merge($contents, $alls[$i]);
            }
        }        

        $names = [];
        foreach ($contents as $key => $value) $names[$key] = $value->getName();        
        $names = preg_grep('#'. $term . '#i', $names);
        foreach ($names as $key => $value) $names[$key] = $contents[$key]; 
        
        return array_values($names);
    }
    
}