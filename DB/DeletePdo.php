<?php declare(strict_types = 1);

require_once(__DIR__. '/PdoForm.php');


class DeletePdo
{
    use PdoForm;

    public function deleteColumn($tableName, $columnName, $id)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare("DELETE FROM {$tableName} WHERE {$columnName} = :id");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
}