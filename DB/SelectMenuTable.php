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
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row["{$selectColumn}"];
        }
        if (isset($rows)){
            return $rows;
        }else {
            return self::nothingElement();
        }
    }

    public function selectItemBusinessSetAll($businessMoney)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM menu_info WHERE business_set = :business_set");
        $statement->bindValue(':business_set', $businessMoney);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        }else {
            return self::nothingElement();
        }
    }


    public function selectItemAll()
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM menu_info");
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        }else {
            return self::nothingElement();
        }
    }


    public function nothingElement()
    {
        return null;
    }
}