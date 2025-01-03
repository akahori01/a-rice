<?php declare(strict_types=1);
require_once(__DIR__. '/PdoForm.php');

class SelectOrderTable{

    use PdoForm;

    public function selectColumnTimeLimit($tableName, $userId, $fromDay, $untilDay)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT * FROM {$tableName} INNER JOIN order_info ON menu_info.menu_id = order_info.menu_id
            WHERE user_id = :user_id
            AND order_info.delivery_date BETWEEN :fromDay AND :untilDay
            ORDER BY order_info.delivery_date"
            );
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':fromDay', $fromDay, PDO::PARAM_STR);
        $statement->bindValue(':untilDay', $untilDay, PDO::PARAM_STR);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        } else {
            return;
        }
    }
    public function selectColumn($tableName, $userId)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT * FROM {$tableName} INNER JOIN order_info ON menu_info.menu_id = order_info.menu_id
            WHERE user_id = :user_id ORDER BY order_info.delivery_date"
            );
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        } else {
            return;
        }
    }
    public function selectColumnMaxLargeOrderGroup($tableName)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT MAX(large_order_group) FROM {$tableName}"
            );
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row['MAX(large_order_group)'];
    }

    public function selectColumnLargeOrderGroup($tableName, $large_order_group)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT order_id FROM {$tableName} WHERE large_order_group = :large_order_group"
            );
        $statement->bindValue(':large_order_group', $large_order_group, PDO::PARAM_INT);
        $statement->execute();
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $rows[] = $row['order_id'];
        }
        return $rows;
    }

    public function adminSelectColumnTimeLimit($tableName, $fromDay, $untilDay)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT * FROM {$tableName}
            INNER JOIN menu_info ON order_info.menu_id = menu_info.menu_id
            INNER JOIN user_info ON order_info.user_id = user_info.id
            WHERE order_info.delivery_date BETWEEN :fromDay AND :untilDay
            ORDER BY order_info.delivery_date"
            );
        $statement->bindValue(':fromDay', $fromDay, PDO::PARAM_STR);
        $statement->bindValue(':untilDay', $untilDay, PDO::PARAM_STR);
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        } else {
            return;
        }
    }
    public function adminSelectColumn($tableName)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "SELECT * FROM {$tableName}
            INNER JOIN menu_info ON order_info.menu_id = menu_info.menu_id
            INNER JOIN user_info ON order_info.user_id = user_info.id
            ORDER BY order_info.delivery_date"
            );
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if ($row['menu_image_data']) {
                $row['menu_image_data'] = stream_get_contents($row['menu_image_data']);
            }
            $rows[] = $row;
        }
        if (isset($rows)){
            return $rows;
        } else {
            return;
        }
    }
}