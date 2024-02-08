<?php


namespace juanignaso\phpmvc\framework\db;

use juanignaso\phpmvc\framework\Model;
use juanignaso\phpmvc\framework\Application;

#[\AllowDynamicProperties]

abstract class DBmodel extends Model
{
    abstract public function tableName(): string;

    //Debería traer todos los nombre de las columnas de la tabla
    abstract public function attributes(): array;
    abstract public function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ") 
        VALUES(" . implode(',', $params) . ")");
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    /**
     * Devuelve una lista de los valores de un atributo que coincidan
     * con el valor cargado en ese atributo(el atributo en cuestión debe de estar previamente
     * cargado en el modelo mediante $modelo->loadData()).
     * 
     * @param $attr
     */
    public function getAttrList($attr)
    {
        $tableName = $this->tableName();
        $statement = self::prepare("SELECT $attr  FROM  $tableName WHERE $attr LIKE :search ORDER BY 1");
        $statement->bindValue(":search", "%" . $this->{$attr} . "%");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $sql = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("DELETE FROM $tableName WHERE $sql");
        //bind values
        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();

        return $statement->rowCount() != 0;
    }

    public function getAll()
    {
        $tableName = $this->tableName();
        return self::query("SELECT * FROM $tableName ORDER BY 1")->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function query($sql)
    {
        return Application::$app->db->pdo->query($sql);
    }

    public function findOne($where) //[email => 'email', username => 'name']
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode(' AND ', array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
        //SELECT * FROM $tableName WHERE email=:email AND username = :username
    }
}