<?php


namespace juanignaso\phpmvc\framework;

use juanignaso\phpmvc\framework\db\DBmodel;

abstract class UserModel extends DBmodel
{
    abstract public function getUserName(): string;
}