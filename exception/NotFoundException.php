<?php

namespace app\core\exception;

class NotFoundException extends \Exception
{
    protected $message = "La Página a la que intentas acceder no existe!";
    protected $code = 404;
}