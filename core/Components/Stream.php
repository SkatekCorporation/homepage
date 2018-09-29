<?php

/**
 * Pour un seul fichier
 * @author Souvenance <skavunga@gmail.com>
 */

namespace Stephanie\Components;

class Stream implements \ArrayAccess
{
    /**
     * Nom du fichier
     */
    private $_name;

    /**
     * Lien vers le fichier
     */
    private $_link;

    private $_linki;

    /**
     * Type du fichier
     */
    private $_type;

    /**
     * Chemin vers le fichier
     */
    private $_path;

    /**
     * Les permissions sur le fichier
     */
    private $_perms;

    /**
     * La taille du fichier
     */
    private $_size;

    /**
     * Date de derniere modification
     */
    private $_modified;

    private $_dirinfo;

    const DIR = 'dir';

    public function __construct($fullPath = null){
        $this->_path = $fullPath;
        $this->initialize();
    }

    /**
     * Si le fichier est un repertoire
     * @return boolean
     */
    public function isDir(){
        if ($this->_type === self::DIR) {
            return true;
        }
        return false;
    }

    /**
     * Initialisation des valeurs
     */
    private function initialize(){
        if(\file_exists($this->_path)){
            $this->_size     = filesize($this->_path);
            $this->_perms    = fileperms($this->_path);
            $this->_type     = filetype($this->_path);
            $this->_modified = filemtime($this->_path);
        } else {
            $this->_size  = null;
            $this->_perms = null;
            $this->_type  = null;
            $this->_modified = null;
        }
        return $this;
    }

    public function getPath(){
        return $this->_path;
    }

    public function path(){
        return $this->getPath();
    }

    public function setName($value){
        $this->_name = $value;
        return $this;
    }

    public function name(){
        return $this->getName();
    }

    public function getName(){
        return $this->_name;
    }

    /**
     * Definir un nouveau chemin du fichier
     */
    public function setPath($path = null){
        $this->_path = $path;
        return $this->initialize();
    }

    /**
     * Si c'est un fichier
     * @return boolean
     */
    public function isFile(){
        if ($this->_type !== self::DIR) {
            return true;
        }
        return false;
    }

    /**
     * Le type de fichier
     * @return string
     */
    public function getType(){
        return $this->_type;
    }

    /**
     * Le type de fichier
     * @return string
     */
    public function type(){
        return $this->getType();
    }

    /**
     * Obtenir les permissions sur le fichier
     */
    public function getPerms(){
        return $this->fileConvertPerms($this->_perms);
    }

    public function perms(){
        return $this->getPerms();
    }

    /**
     * Lien vers le fichier dans Web file explorer
     */
    public function getLinki(){
        if($this->isDir()){
            return $this->_linki;
        }
        return READ_FILE_LINK . $this->_link;
    }

    /**
     * Lien vers le fichier
     */
    public function getLink(){
        return $this->_link;
    }

    public function linki(){
        return $this->getLinki();
    }

    public function link(){
        return $this->getLink();
    }

    /**
     * Obtenir la taille du fichier
     * @param boolean $readable Si la taille doit etre comprehensible par les humains
     * @return string|int
     */
    public function getSize($readable = true){
        if($this->isDir())
            return null;
        if($readable)
            return $this->fileSizeConvert($this->_size);
        return $this->_size;
    }

    public function size(){
        return $this->getSize();
    }

    /**
     * Date de deniere modification
     * @return int
     */
    public function getModified(){
        return $this->_modified;
    }

    public function modified(){
        return $this->getModified();
    }

    public function setLinki($value){
        $this->_linki = $value;
        return $this;
    }

    public function setLink($value){
        $this->_link = $value;
        return $this;
    }

    public function setDirinfo($value = null){
        $this->_dirinfo = $value;
        return $this;
    }

    public function getDirinfo(){
        return $this->_dirinfo;
    }

    public function dirinfo(){
        return $this->getDirinfo();
    }

    /**
    * Converts bytes into human readable file size.
    *
    * @param string $bytes
    * @return string human readable file size (2,87 Мб)
    * @author Mogilev Arseny
    */
    private function fileSizeConvert($bytes = 0.0)
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
        $result = '0 B';
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
    * @return string 
    */
    private function fileConvertPerms($perms){
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

    public function offsetExists($offset) {
        $offset = '_' . $offset;
        return isset($this->$offset);
    }

    public function offsetGet($offset) {
        $offset = '_' . $offset;

        if(isset($this->$offset)){
            return $this->$offset;
        }
        return null;
    }

    public function offsetSet($offset, $value) {
        $offset = '_' . $offset;
        if(isset($this->$offset)){
            $this->$offset = $value;
            $this->initialize();
        }
    }

    public function offsetUnset($offset) {
        $offset = '_' . $offset;
        if(isset($this->$offset)){
            $this->$offset = null;
            $this->initialize();
        }
    }

    public function __toString(){
        return $this->_name . ' (' . $this->size() . ')';
    }
}
