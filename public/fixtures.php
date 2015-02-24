<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Sao_Paulo');

/* Variáveis para o PDO */
$dsn     = 'mysql:host=localhost';
$usuario = 'root';
$senha   = 'root';
$opcoes  = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'];

/*Variáveis auxiliares*/
$dbname  = 'apisilex';
$tabela  = 'produtos';

try{
    //Conexão PDO
    $pdo = new \PDO($dsn, $usuario, $senha, $opcoes);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Criando a Base de Dados
    $pdo->exec("DROP DATABASE IF EXISTS {$dbname}");
    $pdo->exec("CREATE DATABASE IF NOT EXISTS {$dbname}");
    $pdo->exec("use $dbname");
    print("Criado o banco de dados [{$dbname}]<br/>");

    //Criando a Tabela
    $pdo->exec("DROP TABLE IF EXISTS {$tabela}");
    $comando ="CREATE table {$tabela}(
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `nome` longtext COLLATE utf8_unicode_ci NOT NULL,
          `descricao` longtext COLLATE utf8_unicode_ci NOT NULL,
          `valor` decimal(5,2) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    $pdo->exec($comando);
    print("Criada a tabela [{$tabela}]<br/>");

    //Preenchendo a tabela com dados
    $comando = "INSERT INTO `produtos` (`id`, `nome`, `descricao`, `valor`) VALUES
    (1, 'Caneta', 'Caneta Bic', '1.50'),
    (2, 'Lapis', 'Lapis de Cor', '3.90'),
    (3, 'Borracha', 'Borracha Branca', '1.00');";
    $pdo->exec($comando);

    print("Tabela [{$tabela}] preenchida com dados<br/>");

} catch (PDOException $e) {
    die("Erro: Código: {$e->getCode()}: Mensagem: {$e->getMessage()}");
}