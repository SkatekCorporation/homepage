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

			if ( $this->isWindowsPath($dir)) {
				$file = trim($dir, '/') . DS . $content;
			} else {
				$file = DS . trim($dir, '/'). DS . $content;
			}

            $contents[$index]['name']     = $content;
            $contents[$index]['path']     = $file;
            $contents[$index]['linki']     = str_replace($this->_absolutePath, $this->_relativePath, $file);
            $contents[$index]['link']     = str_replace($this->_absolutePath, '/', $file);
            $contents[$index]['type']     = filetype($file);
            $contents[$index]['perms']    = $this->fileConvertPerms(fileperms($file));// substr(sprintf('%o', fileperms($file)), -4);
            $contents[$index]['size']     = $this->FileSizeConvert(filesize($file));
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

    /**
    * Converts bytes into human readable file size.
    *
    * @param string $bytes
    * @return string human readable file size (2,87 Мб)
    * @author Mogilev Arseny
    */
    public function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    /**
    * Converti les permissions en format texte
    */
    public function fileConvertPerms($perms){
      switch ($perms & 0xF000) {
          case 0xC000: // socket
              $info = 's';
              break;
          case 0xA000: // symbolic link
              $info = 'l';
              break;
          case 0x8000: // regular
              $info = 'r';
              break;
          case 0x6000: // block special
              $info = 'b';
              break;
          case 0x4000: // directory
              $info = 'd';
              break;
          case 0x2000: // character special
              $info = 'c';
              break;
          case 0x1000: // FIFO pipe
              $info = 'p';
              break;
          default: // unknown
              $info = 'u';
      }

      // Owner
      $info .= (($perms & 0x0100) ? 'r' : '-');
      $info .= (($perms & 0x0080) ? 'w' : '-');
      $info .= (($perms & 0x0040) ?
                  (($perms & 0x0800) ? 's' : 'x' ) :
                  (($perms & 0x0800) ? 'S' : '-'));

      // Group
      $info .= (($perms & 0x0020) ? 'r' : '-');
      $info .= (($perms & 0x0010) ? 'w' : '-');
      $info .= (($perms & 0x0008) ?
                  (($perms & 0x0400) ? 's' : 'x' ) :
                  (($perms & 0x0400) ? 'S' : '-'));

      // World
      $info .= (($perms & 0x0004) ? 'r' : '-');
      $info .= (($perms & 0x0002) ? 'w' : '-');
      $info .= (($perms & 0x0001) ?
                  (($perms & 0x0200) ? 't' : 'x' ) :
                  (($perms & 0x0200) ? 'T' : '-'));

      return substr(sprintf('%o', $perms), -4) . ' (' . $info . ')';
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

}
