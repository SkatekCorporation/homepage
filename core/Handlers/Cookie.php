<?php

namespace Stephanie\Handlers;

use Stephanie\Interfaces\SessionInterface;

class Cookie implements SessionInterface, \Countable, \ArrayAccess {
    public static function get($key){
        return isset($_COOKIE[$key]) ? \unserialize($_COOKIE[$key]) : null;
    }

    public static function set($key, $value){
        setcookie($key, serialize($value));
    }

    public static function delete($key){
        if (isset($_COOKIE[$key])){
            unset($_COOKIE[$key]);
            setcookie($key, '', time() - 3600);
        }
    }

    public function count(){
        return count($_COOKIE);
    }

    public function offsetExists($offset){
        return isset($_COOKIE[$offset]);
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