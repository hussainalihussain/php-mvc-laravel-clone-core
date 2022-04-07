<?php

namespace app\core\database;

use app\core\Application;

abstract class QueryBuilder
{
    abstract public static function table(): string;
    abstract public static function primaryKey(): string;
    abstract public static function attributes(): array;

    public static function find(array $wheres = [])
    {
        try
        {
            $getKeyAndPlaceholders = function($field) {
                return "{$field}=:{$field}";
            };
            $tableName = static::table();
            $fields    = array_keys($wheres);
            $fields    = array_map($getKeyAndPlaceholders, $fields);
            $fields    = implode(' AND ', $fields);
            $sql       = "SELECT * FROM {$tableName} WHERE $fields";
            $statement = self::prepare($sql);

            foreach ($wheres as $key=> $value)
            {
                $statement->bindValue(":{$key}", $value);
            }

            $statement->execute();

            if(!$statement->rowCount())
            {
                return false;
            }

            return $statement->fetchObject(static::class);
        }
        catch (\PDOException $e)
        {
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
            return false;
        }
    }
    public function save(): bool
    {
        try
        {
            $tableName  = static::table();
            $attributes = static::attributes();
            $keys       = implode(",", $attributes);
            $values     = ':' . implode(",:", $attributes);
            $sql        = "INSERT INTO {$tableName} ({$keys}) VALUES({$values})";
            $statement  = self::prepare($sql);

            foreach ($attributes as $attribute)
            {
                $statement->bindValue(":{$attribute}", $this->{$attribute});
            }

            return $statement->execute();
        }
        catch (\PDOException $e)
        {
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
            return false;
        }
    }

    protected static function prepare(string $sql): \PDOStatement
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}