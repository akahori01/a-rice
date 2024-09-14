<?php declare(strict_types = 1);

require_once(__DIR__. '/PdoForm.php');


class SelectPdo
{
    use PdoForm;

    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function select($tb_name){
        $pdo = self::connect();
        $statement= $pdo->prepare("SELECT * FROM {$tb_name}");
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row;
        };
        if (!isset($rows))
        {
            return self::nothingElement();
        }
        return $rows;
    }

    public function selectColumn($selectColumn, $uniqueColumn)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT {$selectColumn} FROM user_info WHERE {$uniqueColumn} = :uniqueValue");
        $statement->bindValue(':uniqueValue', $this->userId, PDO::PARAM_STR);
        $statement->execute();
        return $row["{$selectColumn}"] = $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function selectColumnAll($selectColumn)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT {$selectColumn} FROM user_info");
        $statement->execute();
        $rows = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $rows[] = $row["{$selectColumn}"];
        }
        return $rows;
    }

    public function selectSomeColumn($selectColumn, $selectColumnIv)
    {
        $pdo = self::connect();
        $statement1 = $pdo->prepare("SELECT {$selectColumn} FROM user_info WHERE user_id = :userId");
        $statement2 = $pdo->prepare("SELECT {$selectColumnIv} FROM user_info WHERE user_id = :userId");
        $statement1->bindValue('userId', $this->userId, PDO::PARAM_STR);
        $statement2->bindValue('userId', $this->userId, PDO::PARAM_STR);
        $statement1->execute();
        $statement2->execute();
        $row1 = $statement1->fetch(PDO::FETCH_ASSOC);
        $row2 = $statement2->fetch(PDO::FETCH_ASSOC);
        $rows = [
            "{$selectColumn}" => $row1["{$selectColumn}"],
            "{$selectColumnIv}" => $row2["{$selectColumnIv}"]
        ];
        return $rows;
    }

    public function selectSomeColumnAll($selectColumn, $selectColumnIv)
    {
        $pdo = self::connect();
        $statement1 = $pdo->prepare("SELECT {$selectColumn} FROM user_info");
        $statement2 = $pdo->prepare("SELECT {$selectColumnIv} FROM user_info");
        $statement1->execute();
        $statement2->execute();
        $rows = [];
        $i = 0;
        while ($row1 = $statement1->fetch(PDO::FETCH_ASSOC))
        {
            $row2 = $statement2->fetch(PDO::FETCH_ASSOC);
            $rows[$i] = [
                "{$selectColumn}" => $row1["{$selectColumn}"],
                "{$selectColumnIv}" => $row2["{$selectColumnIv}"]
            ];
            $i++;
        }
        return $rows;
    }

    public function selectPurchaseHistory()
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM order_info WHERE user_id = :userId LIMIT 1");
        $statement->bindValue('userId', $this->userId, PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $rows = [
            'itemId' => $row['item_id'],
            'orderCount' => $row['order_count'],
            'order_date' => $row['order_date']
        ];
        return $rows;
    }

    public function selectPurchaseHistorys()
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("SELECT * FROM order_info WHERE user_id = :userId");
        $statement->bindValue('userId', $this->userId, PDO::PARAM_STR);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $rows[] = [
                'itemId' => $row['item_id'],
                'orderCount' => $row['order_count'],
                'order_date' => $row['order_date']
            ];
        }
        return $rows;
    }

    public function nothingElement()
    {
        return null;
    }

}