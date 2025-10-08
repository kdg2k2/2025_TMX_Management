<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;
use PDOException;

class CreateDB extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Create database if not exists';

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

            if ($stmt->rowCount() == 0) {
                $connection->exec("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->info("Database created successfully");
            } else {
                $this->info("Database already exists");
            }
        } catch (PDOException $e) {
            $this->error("Database creation error: " . $e->getMessage());
        }
    }
}
