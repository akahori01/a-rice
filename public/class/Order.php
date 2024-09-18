<?php declare(strict_types=1);
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/../instance/menu-instance.php');
require_once(__DIR__. '/../DB/InsertOrderTable.php');
require_once(__DIR__. '/../DB/SelectOrderTable.php');


class order
{
    public $menus;
    private $counts;
    public $menu;
    private $message;
    public $subTotalCost;
    private static $totalCost = 0;
    public $insert;
    public $datas;
    private $selectOrder;


    public function __construct()
    {
        $this->menu = new MenuInstance();
        $this->message = new Message();
        $this->insert = new InsertOrderTable();
        $this->selectOrder = new SelectOrderTable();
    }

    public function setCounts($counts)
    {
        $this->counts = $counts;
    }

    public function getCounts()
    {
        return $this->counts;
    }

    public function setDatas($datas)
    {
        $this->datas[] = $datas;
    }

    public function getDatas()
    {
        return $this->datas;
    }

    public function noneMenu()
    {
        $elementNum = count($this->counts);
        // $this->counts の value から NONE_MENU=0 を含む key を $noneMenuNumへ
        $noneMenuNum = count(array_keys($this->counts, ConstClass::NONE_MENU, true));
        if ($elementNum !== $noneMenuNum){
            return;
        } else{
            $this->setMessage(__FUNCTION__);
            return;
        }
    }

    public function countError()
    {
        for ($i = 0; $i < count($this->counts); $i++){
            if (preg_match('/\A[0-5]\z/u', strval($this->counts[$i])) !== 1){
                $this->counts[$i] = '0';
                $this->setMessage(__FUNCTION__);
            }
        }
    }
    public function largelotCountError()
    {
        for ($i = 0; $i < count($this->counts); $i++){
            if ($i === 0){
                if (preg_match('/\A[0-9]{3}\z/u', strval($this->counts[$i])) !== 1 || intval($this->counts[$i]) < 150 || $this->counts[$i] % 5 !== 0 ){
                    $this->counts[$i] = '';
                    $this->setMessage(__FUNCTION__);
                }else {
                    continue;
                }
            }else {
                if (preg_match('/\A[0-9]{1,2}\z/u', strval($this->counts[$i])) !== 1){
                    $this->counts[$i] = '';
                    $this->setMessage(__FUNCTION__);
                }else {
                    continue;
                }
            }
        }
    }

    public function variousCalcMiss()
    {
        if ($this->counts[0] === ($this->counts[1] * 5 + $this->counts[2] * 10 + $this->counts[3] * 15 + $this->counts[4] * 30)){
            return;
        } else{
            $this->setMessage(__FUNCTION__);
        }
    }

    public function accessWay(string $HTMLFreeRice, $databaseGiveRice)
    {
        if ($HTMLFreeRice === 'money-free' && is_null($databaseGiveRice))
        {
            return true;
        } elseif ($HTMLFreeRice === 'money-buy' )
        {
            return false;
        } else
        {
            $this->setMessage(__FUNCTION__);
            return null;
        }
    }
    public function setCountMenu($choiceMenu)
    {
        if ($choiceMenu === 'money'){
            $this->menus = $this->menu->moneyMenu();
        } elseif ($choiceMenu === 'point'){
            $this->menus = $this->menu->pointMenu();
        }
        foreach ($this->menus as $menu){
            $menu->datas['order_count'] = $this->setCount();
        }
    }

    public function setCount()
    {
        return array_shift($this->counts);
    }

    public function unsetMenu()
    {
        foreach ($this->menus as $key => $object)
        {
            if ($object->datas['order_count'] === 0)
            {
                unset($this->menus[$key]);
            }
        }
    }

    private function setMessage($functionName)
    {
        switch ($functionName)
        {
            case 'noneMenu':
                $this->message->noBuy(ConstApp::ORDER);
                break;
            case 'countError':
                $this->message->countError(ConstApp::ORDER);
                break;
            case 'largelotCountError':
                $this->message->countError(ConstApp::ORDER);
                break;
            case 'variousCalcMiss':
                $this->message->calcMiss(ConstApp::ORDER);
                break;
            case 'accessWay':
                $this->message->unauthorizedAccess(ConstApp::ORDER);
                break;
            case 'lackPoint';
                $this->message->lackPoint(ConstApp::ORDER);
        }
    }

    public function getAddPoint()
    {
        return $this->menus['add_point'];
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }
    public function lackPoint($haveUserPoint)
    {
        foreach (self::getMenus() as $menu){
            $menu->subTotalCost();
        }
        if ($haveUserPoint < $menu->getTotalCost()){
            $this->setMessage(__FUNCTION__);
            return;
        }else {
            return $menu->getTotalCost();
        }
    }


    public function getMenus()
    {
        return $this->menus;
    }

    public function selectColumnLargeOrderGroup()
    {
        $order_goup = $this->selectOrder->selectColumnMaxLargeOrderGroup('order_info');
        return $order_goup;
    }

    public function insertOrderTable($userId, $deliverydate, $business_set, $large_order_group)
    {
        if ($business_set === 1){
            if($large_order_group === null){
                $large_order_group = 0;
            }else{
                $large_order_group += 1;
            }
        }else {
            $large_order_group = null;
        }
        $this->insert->insertOrderTable($this->getDatas(), $userId, $deliverydate, $large_order_group);
    }
}