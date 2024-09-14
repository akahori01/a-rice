<?php declare(strict_types=1);
require_once(__DIR__. '/PdoForm.php');

class InsertCalendarTable{
    use PdoForm;
    public function insert($holiday){
        $pdo = self::connect();
        $statement = $pdo->prepare("INSERT INTO calendar_info (holiday) VALUES (:holiday)");
        $statement->bindValue(':holiday', $holiday, PDO::PARAM_STR);
        $statement->execute();
    }
}
