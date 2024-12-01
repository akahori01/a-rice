<?php
declare(strict_types = 1);

trait PdoForm
{
    private $objDateTime;

    public function __construct()
    {
        $this->objDateTime = new DateTime('now');
    }
    public function connect()
    {
        try {
            // サーバー用
            $pdo = new PDO('mysql:host=us-cluster-east-01.k8s.cleardb.net;dbname=heroku_65df047e8aa26c8;charset=utf8mb4', 'bd0defb290d916', '5a2e514f');
            // ローカル用
            // $pdo = new PDO('mysql:host=localhost;dbname=rice_app;charset=utf8mb4', 'root', 'root');

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, false);
        } catch(PDOException $e){
            file_put_contents(__DIR__. '/../errorLog/DBError.php', $this->objDateTime->format('Y-m-d H:i:s'). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
            header('Location: error.php');
            exit();
        }
        return $pdo;
    }
}