<?php

namespace Stephanie\Handlers;

use Stephanie\Interfaces\SessionInterface;

/**
 * Gestion des sessions
 */

class Session implements SessionInterface, \Countable, \ArrayAccess {

    public static function get($key){
        if(! empty($_SESSION[$key])){
            return $_SESSION[$key];
        } else {
            return [];
        }
    }

    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    public static function delete($key){
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
            return TRUE;
        }
        return FALSE;
    }

    public function count(){
        return count($_SESSION);
    }

    public function offsetExists($offset){
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset){
        return $this->get($offset);
    }

    public function offsetSet($offset, $value){
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset){
        return $this->delete($offset);
    }
}
