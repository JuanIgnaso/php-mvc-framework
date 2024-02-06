<?php
namespace juanignaso\phpmvc\framework;

class Response
{
    public function setStatusCode($code)
    {
        http_response_code((int) $code);
    }

    /**
     * Redirigir al usuario a la ubicación pasada como parámetro
     * 
     * @param string $url - ruta de destino, ej: '/' o '/info'
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}
?>