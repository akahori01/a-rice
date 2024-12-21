<?php
declare(strict_types = 1);

require_once(__DIR__. '/PdoForm.php');

class SelectMenuTable
{
    use PdoForm;

    public function selectColumnAll($selectColumn)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT {$selectColumn} FROM menu_info");
        $statement->execute();
        $rows = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $rows[] = $row["{$selectColumn}"];
        }
        if ($rows === [])
        {
            return self::nothingElement();
        }
        return $rows;
    }

    public function selectItemBusinessSetAll($businessMoney)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM menu_info WHERE business_set = :business_set");
        $statement->bindValue(':business_set', $businessMoney);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
             // バイナリデータを取り出す
            $imageData = $row['menu_image_data'];
            // バイナリデータを適切に処理（例：保存、表示など）
            if ($imageData) {
                // ここでバイナリデータを利用する処理を行う
                // 例: メモリ内で処理
                $row['menu_image_data'] = $imageData;
            }
            $rows[] = $row;
        }
        if(isset($rows)){
            return $rows;
        }else{
            return null;
        }
    }


    public function selectItemAll()
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM menu_info");
        $statement->execute();
        $row = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }


    public function nothingElement()
    {
        return null;
    }
}