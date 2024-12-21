<?php declare(strict_types = 1);
require_once(__DIR__. '/PdoForm.php');

class InsertMenu
{
    const BUSINESS_SET = ConstApp::BUSINESS_SET;
    const MENU_NAME = ConstApp::MENU_NAME;
    const MENU_COST = ConstApp::MENU_COST;
    const MENU_WEIGHT = ConstApp::MENU_WEIGHT;
    const MENU_CATEGORY = ConstApp::MENU_CATEGORY;
    const MENU_UNIT = ConstApp::MENU_UNIT;
    const MENU_COMMENT_TOP = ConstApp::MENU_COMMENT_TOP;
    const MENU_COMMENT_BOTTOM = ConstApp::MENU_COMMENT_BOTTOM;
    const MENU_NOTES = ConstApp::MENU_NOTES;


    use PdoForm;

    public function insertMenuTable(array $datas, string $imagePass, $imageData, string $mimeType)
    {
        $pdo = self::connect();
        $statement = $pdo->prepare(
            "INSERT INTO menu_info (business_set, menu_image_pass, menu_image_data, image_mime_type, menu_name, menu_cost, menu_weight, menu_category, menu_unit, comment_top, comment_bottom, notes, created_at, updated_at)
            VALUES(:businessSet, :menuImagePass, :menuImageData, :imageMimeType, :menuName, :menuCost, :menuWeight, :menuCategory, :menuUnit, :commentTop, :commentBottom, :notes, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)"
        );
        foreach ($datas as $key => $value)
        {
            switch ($key)
            {
                case self::BUSINESS_SET:
                    $statement->bindValue(':businessSet', intval($value), PDO::PARAM_INT);
                    break;
                case self::MENU_NAME:
                    $statement->bindValue(':menuName', $value, PDO::PARAM_STR);
                    break;
                case self::MENU_COST:
                    $statement->bindValue(':menuCost', intval($value), PDO::PARAM_INT);
                    break;
                case self::MENU_WEIGHT:
                    $statement->bindValue(':menuWeight', intval($value), PDO::PARAM_INT);
                    break;
                case self::MENU_CATEGORY:
                    $statement->bindValue(':menuCategory', $value, PDO::PARAM_STR);
                    break;
                case self::MENU_UNIT:
                    $statement->bindValue(':menuUnit', $value, PDO::PARAM_STR);
                    break;
                case self::MENU_COMMENT_TOP:
                    $statement->bindValue(':commentTop', $value, PDO::PARAM_STR);
                    break;
                case self::MENU_COMMENT_BOTTOM:
                    $statement->bindValue(':commentBottom', $value, PDO::PARAM_STR);
                    break;
                case self::MENU_NOTES:
                    $statement->bindValue(':notes', $value, PDO::PARAM_STR);
                    break;
            }
        }
        $statement->bindValue(':menuImagePass', $imagePass, PDO::PARAM_STR);
        $statement->bindValue(':menuImageData', $imageData, PDO::PARAM_LOB);
        $statement->bindValue(':imageMimeType', $mimeType, PDO::PARAM_STR);
        $statement->execute();
    }
}