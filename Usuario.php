<?php

namespace juanignaso\phpmvc\framework;

use juanignaso\phpmvc\framework\db\DBModel;

class Usuario extends DBModel
{
    const STATUS_INACTIVO = 0;
    const STATUS_ACTIVO = 1;
    const STATUS_BORRADO = 2;
    public string $nombre = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public string $id;
    public string $created_at = '';
    public string $lastlog = '';

    public function save()
    {
        $this->status = self::STATUS_INACTIVO;
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return parent::save();
    }

    public function rules(): array
    {
        return [
            'nombre' => [[self::RULE_REQUIRED, 'campo' => $this->getLabel('nombre')], [self::RULE_UNIQUE, 'class' => self::class]],
            'email' => [[self::RULE_REQUIRED, 'campo' => $this->getLabel('email')], self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
            'password' => [[self::RULE_REQUIRED, 'campo' => $this->getLabel('password')], [self::RULE_REGEX, 'regex' => '/^[a-zA-Z0-9\*\?\-\_\@#\+]{8,20}$/', 'text' => 'la contraseña debe contener de 8 a 20 caracteres, caracteres especiales permitidos: * ? - _ @ # +']],
            'passwordConfirm' => [[self::RULE_REQUIRED, 'campo' => $this->getLabel('passwordConfirm')], [self::RULE_MATCH, 'match' => 'password']]
        ];
    }

    public function attributes(): array
    {
        return ['nombre', 'email', 'password', 'status'];
    }

    public function tableName(): string
    {
        return 'usuarios';
    }

    public function primaryKey(): string
    {
        return 'id';
    }

    public function getUserName(): string
    {
        return $this->nombre;
    }

    public function labels(): array
    {
        return [
            'nombre' => 'Nombre',
            'email' => 'Email',
            'password' => 'Contraseña',
            'passwordConfirm' => 'Confirmar Contraseña'
        ];
    }
}