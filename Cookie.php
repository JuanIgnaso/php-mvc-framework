<?php

namespace app\core;

class Cookie
{
    /**
     * Crea una cookie en la aplicación pasando nombre, valor y tiempo.
     * @param $name
     * @param $value
     * @param $time
     */
    public function create($name, $value, $time)
    {
        if (!is_int($time)) {
            throw new \InvalidArgumentException('El valor especificado en el tiempo debe de ser de tipo int.');
        }
        setcookie($name, $value, $time);
    }

    public function get($name)
    {
        if (isset($_COOKIE[$name])) {
            echo $_COOKIE[$name];
        } else {
            return false;
        }
    }

    public function exists($name)
    {
        return isset($_COOKIE[$name]);
    }

    public function delete($name)
    {
        if (isset($_COOKIE[$name])) {
            setcookie($name, '', time() - 3600);
        }
    }
}