<?php

/**
 * Pour un groupe des fichiers a traiter
 * @author Souvenance <skavunga@gmail.com>
 */

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

    /**
     * Initialisation
     * @param string $dir Le repertoire a rechercher
     * @param string $absolutePath Le chemin absolu
     */
    public function __construct($directory = null, $absolutePath = null)
    {
        if($absolutePath == null)
            $this->_absolutePath = Session::get('absolutePath');
        $this->_relativePath = DOMAIN;
        if($directory == null)
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
     * @param bool $sort Si le tableau doit etre trie
     * @return array Les contenus
     */
    public function getContents($dir = null, $sort = true)
    {
        if($dir == null)
        {
            $dir = $this->_absolutePath . $this->_directory;
        }

        if (! is_dir($dir)) return null;
        $index     = 0;
        $repertory = opendir($dir);
        $contents = [];

        for ($index = 0; ($content = readdir($repertory)); $index++) {
            if (in_array($content, $this->_exceptions)) {
                $index--; continue;
            }

			if ($this->isWindowsPath($dir)) {
				$file = trim($dir, '/') . DS . $content;
			} else {
				$file = DS . trim($dir, '/'). DS . $content;
			}

            $contents[$index] = new Stream($this->correctSlash($file));
            $contents[$index]->setName($content);
            $contents[$index]->setDirinfo($this->_directory);
            $contents[$index]->setLinki($this->correctSlash(str_replace($this->_absolutePath, $this->_relativePath, $file)));
            $contents[$index]->setLink($this->correctSlash(str_replace($this->_absolutePath, '/', $file)));
            $contents[$index]['index']    = $this->checkIndex($file . DS);
        }

        if ($sort) array_multisort($contents);

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

    public function correctSlash($value){
        return \preg_replace('#\/\/#', '/', $value);
    }

    /**
     * Obtenir la liste des dossiers et fichiers des parametres courants
     * @return array
     */
    public function getLists()
    {
        # code...
    }

    /**
     * Returns true if given $path is a Windows path.
     *
     * @param string $path Path to check
     * @return bool true if windows path, false otherwise
     */
    public static function isWindowsPath($path)
    {
        return (preg_match('/^[A-Z]:\\\\/i', $path) || substr($path, 0, 2) === '\\\\');
    }

    public function getDirectory()
    {
      return $this->_directory;
    }

}
