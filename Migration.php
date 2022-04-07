<?php

namespace app\core;

use app\core\database\Database;

class Migration
{
    public static $migrationTableName = 'migration';
    /**
     * @var array
     */
    protected $migrations;

    /**
     * @var Database
     */
    public static $db;

    /**
     * @var string
     */
    public static $ROOT_PATH;

    /**
     * @var string
     */
    public static $MIGRATION_PATH;

    /**
     * @var bool
     */
    public static $saveMigrationInDb = true;

    /**
     * @var int
     */
    protected $batch         = 1;
    protected $batchMigrated = 0;

    /**
     * @param string $root_path
     * @param string $migration_path
     * @param array $config
     * @return void
     */
    public function setup(string $root_path, string $migration_path, array $config = [])
    {
        static::$db           = new Database($config ?? []);
        self::$ROOT_PATH      = $root_path;
        self::$MIGRATION_PATH = $migration_path;
    }

    /**
     * @return string
     */
    public function getMigrationsPath(): string
    {
        return self::$ROOT_PATH . '/' . self::$MIGRATION_PATH;
    }

    /**
     * @return array
     */
    public function getMigrationFiles(): array
    {
        $this->migrations = array_filter(scandir($this->getMigrationsPath()), function ($migration) {
            return !($migration === '.' || $migration === '..');
        });

        return $this->migrations;
    }

    /**
     * @param $message
     * @return void
     */
    public function message($message)
    {
        echo $message, ' ', static::class, PHP_EOL;
    }

    /**
     * @return void
     */
    public function migrate()
    {
        $migrationPath = $this->getMigrationsPath();
        $this->batch   = $this->getNextBatchNumber();

        foreach ($this->getMigrationFiles() as $migration)
        {
            if(!$this->isMigrated($migration))
            {
                $this->batchMigrated++;
                $this->migrateSingleFile($migrationPath, $migration);
            }
        }

        $this->showIfNothingIsMigrated();
    }

    public function showIfNothingIsMigrated()
    {
        if(!$this->batchMigrated)
        {
            echo 'Nothing to migrate!', PHP_EOL;
        }
    }

    public function getNextBatchNumber(): int
    {
        try {
            $sql       = 'SELECT batch FROM ' . self::$migrationTableName . ' ORDER BY batch DESC LIMIT 1';
            $statement = self::$db->pdo->prepare($sql);
            $statement->execute();
            $batch     = $statement->fetch(\PDO::FETCH_OBJ);

            return ++$batch->batch;
        } catch (\PDOException $e)
        {
            return 1;
        }
    }

    /**
     * @param string $migration
     * @return bool
     */
    public function isMigrated(string $migration): bool
    {
        try {
            $sql  = "SELECT * FROM " . self::$migrationTableName. " WHERE migration = '{$migration}' LIMIT 1";
            $stmt = self::$db->pdo->prepare($sql);
            $stmt->execute();
        }
        catch (\PDOException $e) {
            return false;
        }

        return $stmt->rowCount() > 0;
    }

    /**
     * @param string $migrationPath
     * @param string $migration
     * @return void
     */
    public function migrateSingleFile(string $migrationPath, string $migration)
    {
        require $migrationPath . '/' . $migration;
        $class  = 'app\\migrations\\' . pathinfo($migration, PATHINFO_FILENAME);
        $object = new $class();
        $object->up();

        if($class::$saveMigrationInDb)
        {
            $this->saveMigration($migration);
        }
    }

    public function saveMigration(string $migration)
    {
        $sql  = "INSERT INTO " . self::$migrationTableName . "(migration, batch) VALUES (:migration, :batch)";
        $stmt = self::$db->pdo->prepare($sql);
        $stmt->bindValue(':migration', $migration);
        $stmt->bindValue(':batch', $this->batch);
        $stmt->execute();
    }
}