<?php

namespace EJS\Database;


class Conexao
{
    private static $dsn = 'mysql:host=localhost;dbname=apisilex';
    private static $usuario = 'root';
    private static $senha = 'root';
    private static $opcoes = [\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8' ];

    private function conectar()
    {
        try {
            $pdo = new \PDO(self::$dsn, self::$usuario, self::$senha, self::$opcoes);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            echo "Nao foi possivel conectar ao banco de dados ";
            die("{$e->getMessage()}");
        }
    }

    public function getDb()
    {
        return self::conectar();
    }
} 