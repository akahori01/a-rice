<?php declare(strict_types = 1);
require_once('Menu.php');

class MenuPoint extends Menu
{
    private const BUSINESS_SET = ConstApp::BUSINESS_SET;
    private const BUSINESS_POINT = ConstApp::BUSINESS_POINT;


    public function __construct($datas)
    {
        parent::__construct($datas);
    }

    public function convertSaveMenuDatabase()
    {
        parent::convertSaveMenuDatabase();
        $this->datas[self::BUSINESS_SET] = self::BUSINESS_POINT;
    }

    public function getAddPoint()
    {
        return $this->datas['add_point'] = 0;
    }

    public function calc()
    {
        $riceTypeArray = [
            '5kg' => $rice5num = $this->datas['order_count_5'],
            '10kg' => $rice10num = $this->datas['order_count_10'],
            '15kg' => $rice15num = $this->datas['order_count_15'],
            '30kg' => $rice30num = $this->datas['order_count_30']
        ];
        $rice5price = $rice5num * 1640;
        $rice10price =$rice10num * 3240;
        $rice15price =$rice15num * 4800;
        $rice30price =$rice30num * 9600;
        $this->datas['sub_total_cost'] = $rice5price + $rice10price + $rice15price + $rice30price;
        self::totalCost();
        foreach($riceTypeArray as $key => $value){
            if ($value === '0'){
                continue;
            }else {
                 $riceType[] = $key. ' , '. $value. '個';
            }
        }
        return $riceType;
    }
    public function getCostFormat()
    {
        return '消費ポイント：'. strval(self::numFormat($this->datas[self::MENU_COST])). '円';
    }
    public function getSubTotalCostFormat()
    {
        return '小計：'. strval(self::numFormat($this->datas['sub_total_cost'])). '円';
    }
    public function getTotalCostFormat()
    {
        return '合計：'. strval(self::numFormat($this->getTotalCost())). '円';
    }
}