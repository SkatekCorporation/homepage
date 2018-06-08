<?php

namespace Stephanie\Components;

use Stephanie\Handlers\Session;

class Files {
    /**
     * Repertoire actuel
     * @var string
     */
    private $_directory = '';

    /**
     * Chemin absolu
     * @var string
     */
    private $_absolutePath = '';

    /**
     * Chemin relatif
     */
    private $_relativePath = '';

    /**
     * Liste des exceptions
     * @var array
     */
    private $_exceptions = ['.', '..'];

    /**
     * Repertoire referer
     * @var string
     */
    private $_referer = '';

    /**
     * Les fichiers reconnus comme index
     * @var array
     */
    private $_indexes = ['index.html', 'index.php', 'default.php', 'index.cgi'];

    public function __construct()
    {
        $this->_absolutePath = Session::get('absolutePath');
        $this->_relativePath = DOMAIN;
        $this->_directory = Session::get('r_chemin');
        $this->setExceptions(trim(DOMAIN, '/'));
        $this->setExceptions($this->_indexes);
    }

    /**
     * Definir le nom de l'absolute path
     * @param string $path Le chemin
     * @return void
     */
    public function setAbsolutePath($path = null)
    {
        $this->_absolutePath = $path;
        return $this;
    }

    /**
     * Definir le repertoire en cours
     * @param string $dir Le repertoire
     * @return object 
     */
    public function setDirectory($dir = null)
    {
        $this->_directory = $dir;
        return $this;
    }

    /**
     * Definir la liste des exceptions
     * @param array|string $options Liste des exceptions
     */
    public function setExceptions($options = null)
    {
        if (is_string($options)){
            $this->_exceptions[] = $options;
        } else {
            $this->_exceptions = array_merge($this->_exceptions, $options);
        }
        return $this;
    }

    /**
     * Retourne le chemin absolu
     * @return string
     */
    public function getAbsolutePath()
    {
        return $this->_absolutePath;
    }

    /**
     * Obtenir toutes les exceptions
     * @return array Liste des trucs a ignorer
     */
    public function getExceptions()
    {
        return $this->_exceptions;
    }

    /**
     * Obtenir le contenu d'un dossier
     * @param string $dir Nom du dossier
     * @return array Les contenus
     */
    public function getContents($dir = null)
    {
        if($dir == null) 
        {
            $dir = $this->_absolutePath . $this->_directory;
        } 

        if (! is_dir($dir)) return null;
        $index     = 0;
        $repertory = opendir($dir);

        for ($index = 0; ($content = readdir($repertory)); $index++) {
            if (in_array($content, $this->_exceptions)) {
                $index--; continue;
            }            
			
			if ( isset($_SERVER['SystemRoot']) && strrchr($_SERVER['SystemRoot'], 'Windows')) {
				$file = trim($dir, '/') . DS . $content;
			} else {
				$file = DS . trim($dir, '/'). DS . $content;
			}
            
            $contents[$index]['name']     = $content;
            $contents[$index]['path']     = $file;
            $contents[$index]['linki']     = str_replace($this->_absolutePath, $this->_relativePath, $file);
            $contents[$index]['link']     = str_replace($this->_absolutePath, '/', $file);
            $contents[$index]['type']     = filetype($file);
            $contents[$index]['perms']    = fileperms($file);
            $contents[$index]['size']     = filesize($file);
            $contents[$index]['modified'] = filemtime($file);
            $contents[$index]['index']    = $this->checkIndex($file . DS); 
        }

        return $contents;
    }

    /**
     * Verifie s'il existe un fichier index dans le directory
     * @param string $dir Le nom du dossier terminer par /
     * @return boolean
     */
    public function checkIndex($dir = null)
    {
        if (! is_dir($dir)) {
            return false;
        }
        
        foreach ($this->_indexes as $index) {
            if (is_file($dir . $index)) {
                return $index;
            }
        }
        
        return false;
    }

    /**
     * Obtenir la liste des dossiers et fichiers des parametres courants
     * @return array
     */
    public function getLists()
    {
        # code...
    }

}
