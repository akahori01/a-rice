<?php declare(strict_types=1);
require_once(__DIR__. '/PdoForm.php');

class InsertOrderTable
{
    private const DB_COLUMN_MENU_ID = 'menu_id';
    private const DB_COLUMN_ORDER_COUNT = 'order_count';
    private const DB_COLUMN_SUB_TOTAL_COST = 'sub_total_cost';
    private const DB_COLUMN_LARGE_ORDER_GROUP = 'large_order_group';
    private const DB_COLUMN_ADD_POINT = 'add_point';

    use PdoForm;

    public function insertOrderTable($menuDatas, $userId, $deliverydate, $large_order_group){
        $pdo = self::connect();
        $statement = $pdo->prepare("INSERT INTO order_info (user_id, menu_id, order_count, sub_total_cost, delivery_date, order_date, large_order_group)
                        VALUES(:user_id, :menu_id, :order_count, :sub_total_cost, :delivery_date, CURRENT_TIMESTAMP, :large_order_group)");
        foreach ($menuDatas as $data){
            foreach ($data as $key => $value)
            {
                switch ($key){
                    case self::DB_COLUMN_MENU_ID:
                        $statement->bindValue(':menu_id', $value, PDO::PARAM_INT);
                        break;
                    case self::DB_COLUMN_ORDER_COUNT:
                        $statement->bindValue(':order_count', $value, PDO::PARAM_INT);
                        break;
                    case self::DB_COLUMN_SUB_TOTAL_COST:
                        $statement->bindValue(':sub_total_cost', $value, PDO::PARAM_INT);
                        break;
                }
            }
            $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $statement->bindValue(':delivery_date', $deliverydate, PDO::PARAM_STR);
            if (is_null($large_order_group)){
                $statement->bindValue(':large_order_group', $large_order_group, PDO::PARAM_NULL);
            }else {
                $statement->bindValue(':large_order_group', $large_order_group, PDO::PARAM_INT);
            }
            $statement->execute();
        }
    }
}