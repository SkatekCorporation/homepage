<?php

namespace Stephanie\Interfaces;

interface SessionInterface {
    public static function get($key);

    public static function set($key, $value);

    public static function delete($key);
}