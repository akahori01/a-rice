<?php declare(strict_types = 1);
require_once('Menu.php');

class MenuMoney extends Menu
{
    private const BUSINESS_SET = ConstApp::BUSINESS_SET;
    private const BUSINESS_MONEY = ConstApp::BUSINESS_MONEY;


    public function __construct($datas)
    {
        parent::__construct($datas);
    }


    public function convertSaveMenuDatabase()
    {
        parent::convertSaveMenuDatabase();
        $this->datas[self::BUSINESS_SET] = self::BUSINESS_MONEY;
    }


    private function calcPoint()
    {
        return intval(floatval($this->datas['sub_total_cost']) / ConstClass::DIVISION_NUM) * ConstClass::MULTIPLY_NUM;
    }

    public function getAddPoint()
    {
        return $this->datas['add_point'] = self::calcPoint();
    }

    public function getCostFormat()
    {
        return '金額：'. strval(self::numFormat($this->datas[self::MENU_COST])). '円';
    }
    public function getSubTotalCostFormat()
    {
        return '小計：'. strval(self::numFormat($this->getSubTotalCost())). '円';
    }
    public function getTotalCostFormat()
    {
        return '合計：'. strval(self::numFormat($this->getTotalCost())). '円';
    }


}