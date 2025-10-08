<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class DropDB extends Command
{
    protected $signature = 'db:drop';
    protected $description = 'Drop database from .env';

    public function handle()
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $databaseName = config('database.connections.mysql.database');

        try {
            $connection = new PDO("mysql:host=$host;port=$port", $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $connection->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbname");
            $stmt->execute(['dbname' => $databaseName]);

            if ($stmt->rowCount() > 0) {
                $connection->exec("DROP DATABASE `$databaseName`");
                $this->info("Database $databaseName dropped successfully");
            } else {
                $this->error("Database $databaseName does not exist");
            }
        } catch (PDOException $e) {
            $this->error("Database dropping error: " . $e->getMessage());
        }
    }
}
