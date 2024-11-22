<?php declare(strict_types=1);
require_once(__DIR__. '/SelectOrderTable.php');
require_once(__DIR__. '/PdoForm.php');
require_once(__DIR__. '/DeletePdo.php');
require_once(__DIR__. '/UpdatePdo.php');
require_once(__DIR__. '/../class/Message.php');
require_once(__DIR__. '/../class/Encrypt.php');
require_once(__DIR__. '/../class/Check.php');

class OrderModel{

    public $selectMenus;
    public $userId;
    public $message;
    public $select;
    public $delete;
    public $update;
    public $encrypt;
    private $check;
    public $beforeDay;
    private $noGroupBeforeDay = [];
    private $isGroupBeforeDay = [];
    private $noGroupToday = [];
    private $isGroupToday = [];
    private $noGroupAfterDay = [];
    private $isGroupAfterDay = [];
    public $today = [];
    public $afterDay = [];
    private $resetWeek;
    public $resetOrderOfPoint;
    private $objDateTime;


    use PdoForm;


    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->message = new Message();
        $this->select = new SelectOrderTable();
        $this->delete = new DeletePdo();
        $this->update = new UpdatePdo();
        $this->encrypt = new Encrypt();
        $this->check = new Check();
        $this->objDateTime = new DateTime();
    }

    public function selectMenu($admin, $startTime = null, $endTime = null)
    {
        try{
            if(isset($startTime) && is_null($endTime) or is_null($startTime) && isset($endTime)){
                throw new Exception('開始時刻、終了時刻が記載されていません');
            }elseif (is_null($startTime) && is_null($endTime)){
                $fromDay = $this->objDateTime->modify('-1 year')->format('Y-m-d');
                $untilDay = $this->objDateTime->modify('+1 year')->modify('+2 month')->format('Y-m-d');
                $this->objDateTime->modify('-2 month');
            }else {
                $startDate = new DateTime($startTime);
                $fromDay = $startDate->format('Y-m-d');
                $endDate = new DateTime($endTime);
                $untilDay = $endDate->format('Y-m-d');
            }
        }catch(Exception $e){
            file_put_contents(__DIR__. '/../errorLog/writeError.php', $this->objDateTime->format('Y-m-d H:i:s'). ', 異常名 '. $e->getLine(). $e->getMessage(). "\n", FILE_APPEND | LOCK_EX);
            header('Location: error.php');
            exit();
        }
        $currentDate = $this->objDateTime->format('Y-m-d');
        if ($admin === true){
            $this->selectMenus = $this->select->adminSelectColumnTimeLimit('order_info', $fromDay, $untilDay);
            if (is_null($this->selectMenus)){
                return;
            } else{
                foreach ($this->selectMenus as $key => $array){
                    $name = [$array['name'], $array['name_iv']];
                    $this->selectMenus[$key]['user_name'] = $this->encrypt->composite($name);
                    $address = [$array['address'], $array['address_iv']];
                    $this->selectMenus[$key]['user_address'] = $this->encrypt->composite($address);
                    $tel = [$array['tel'], $array['tel_iv']];
                    $this->selectMenus[$key]['user_tel'] = $this->encrypt->composite($tel);
                }
                $this->isAlreadyOrder(strtotime($currentDate));
                return;
            }
        } else {
            $this->selectMenus = $this->select->selectColumnTimeLimit('menu_info', $this->userId, $fromDay, $untilDay);
            if (is_null($this->selectMenus)){
                return;
            } else{
                $this->isAlreadyOrder(strtotime($currentDate));
                return;
            }
        }
    }

    private function isAlreadyOrder($currentDate)
    {
        foreach ($this->selectMenus as $orders){
            $deliverydateTime = strtotime($orders['delivery_date']);
            $instanceData = $this->changeInstance($orders);
            if ($currentDate < $deliverydateTime){
                $this->beforeDay[] = $instanceData;
            } elseif ($currentDate === $deliverydateTime){
                $this->today[] = $instanceData;
            } elseif ($currentDate > $deliverydateTime){
                $this->afterDay[] = $instanceData;
            }
        }
        return;
    }

    private function changeInstance($orders)
    {
        if ($orders['business_set'] === 0){
            $instanceData = new MenuMoney($orders);
            $instanceData->make();
            return $instanceData;
        }elseif ($orders['business_set'] === 1){
            $instanceData = new MenuPoint($orders);
            $instanceData->make();
            return $instanceData;
        }
    }

    private function orderIds($orderDays)
    {
        foreach ($orderDays as $orderMenu){
            $orderIds[] = $orderMenu->getOrderId();
        }
        return $orderIds;
    }

    private function isBeforeDeliverydate($resetWeek)
    {
        switch($resetWeek){
            case 'before':
                $orderIds = $this->orderIds($this->getBeforeDay());
                break;
            case 'today':
                $orderIds = $this->orderIds($this->getToday());
                break;
            case 'after':
                $orderIds = $this->orderIds($this->getAfterDay());
                break;
        }
        return $orderIds;
    }

    private function isnull($resetOrderId, $todayTimestamp, $deliverydateTimestamp, $orderIds, $admin){
        if ($admin === true && in_array($resetOrderId, $orderIds, true)){
            try{
                $pdo = self::connect();
                $pdo->beginTransaction();
                $this->unsetOrder($resetOrderId);
                $this->deleteOrderRecord($resetOrderId);
                $this->message->successDelete(ConstApp::MYPAGE);
            }catch(PDOException $e){
                $pdo->rollBack();
            }
        }elseif (in_array($resetOrderId, $orderIds, true) && $todayTimestamp < $deliverydateTimestamp){
            try{
                $pdo = self::connect();
                $pdo->beginTransaction();
                $this->unsetOrder($resetOrderId);
                $this->deleteOrderRecord($resetOrderId);
                $this->message->successDelete(ConstApp::MYPAGE);
            }catch(PDOException $e){
                $pdo->rollBack();
            }
        }else {
            $this->message->notDelete(ConstApp::MYPAGE);
            return;
        }
        return;
    }
    private function isInt($resetOrderId, $groupOrderIds, $todayTimestamp, $deliverydateTimestamp, $orderIds, $admin){
        if (in_array($resetOrderId, $groupOrderIds, true)){
            foreach($groupOrderIds as $groupOrderId){
                $this->isnull($groupOrderId, $todayTimestamp, $deliverydateTimestamp, $orderIds, $admin);
            }
        }else {
            $this->message->notDelete(ConstApp::MYPAGE);
            return;
        }
        return;
    }

    public function isProcessDelete($resetOrderId, $resetOrderLargeGroupNum, $deliverydate, $resetWeek, $admin){
        $this->resetWeek = $resetWeek;
        $orderIds = $this->isBeforeDeliverydate($this->resetWeek);
        $todayTimestamp = strtotime($this->objDateTime->format('Y-m'));
        $deliverydateTimestamp = strtotime($deliverydate);
        if ($this->check->allInt($resetOrderId)){
            if ($resetOrderLargeGroupNum === ''){
                $this->isnull(intval($resetOrderId), $todayTimestamp, $deliverydateTimestamp, $orderIds, $admin);
            } elseif($this->check->allInt($resetOrderLargeGroupNum)){
                $groupOrderIds = $this->select->selectColumnLargeOrderGroup('order_info', $resetOrderLargeGroupNum);
                $this->isInt(intval($resetOrderId), $groupOrderIds, $todayTimestamp, $deliverydateTimestamp, $orderIds, $admin);
            }else {
                $this->message->notDelete(ConstApp::MYPAGE);
            }
        }else{
            $this->message->notDelete(ConstApp::MYPAGE);
        }
        return;
    }

    private function deleteOrderRecord($id)
    {
        $this->delete->deleteColumn('order_info', 'order_id', $id);
    }
    private function unsetOrder($resetOrderId)
    {
        switch($this->resetWeek){
            case 'before':
                foreach ($this->beforeDay as $key => $object){
                    switch ($object->getOrderId()){
                        case $resetOrderId:
                            unset($this->beforeDay[$key]);
                            break;
                    }
                }
                break;
            case 'today':
                foreach ($this->today as $key => $object){
                    switch ($object->getOrderId()){
                        case $resetOrderId:
                            unset($this->today[$key]);
                            break;
                    }
                }
                break;
            case 'after':
                foreach ($this->afterDay as $key => $object){
                    switch ($object->getOrderId()){
                        case $resetOrderId:
                            unset($this->afterDay[$key]);
                            break;
                    }
                }
                break;
        }
        return;
    }

    public function noneSubtotal($orderSubTotalCost)
    {
        return ltrim($orderSubTotalCost, '小計：');
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }


    public function separateTheArray(){
        if (!empty($this->getBeforeDay())){
            list($this->noGroupBeforeDay, $this->isGroupBeforeDay) = $this->swappingTheArray(array_values($this->getBeforeDay()));
        }
        if (!empty($this->getToday())){
            list($this->noGroupToday, $this->isGroupToday) = $this->swappingTheArray(array_values($this->getToday()));
        }
        if (!empty($this->getAfterDay())){
            list($this->noGroupAfterDay, $this->isGroupAfterDay) = $this->swappingTheArray(array_values($this->getAfterDay()));
        }
    }

    private function swappingTheArray($array)
    {
        if (is_null($array)){
            return [null, null];
        }else {
            $count = count($array);
            for ($i = 0; $i < intval($count); $i++){
                if ($array[$i]->getLargeOrderGroup() !== null){
                    $largeOrder[] = $array[$i];
                    unset($array[$i]);
                }
            }
            if (isset($largeOrder) && !empty($largeOrder)){
                for ($i = 0; $i < count($largeOrder); $i++){
                    $currentGroupNum = $largeOrder[$i]->getLargeOrderGroup();
                    if (!isset($previousGroupNum) || $previousGroupNum === $currentGroupNum){
                        $largeOrderGroup[$currentGroupNum][] = $largeOrder[$i];
                    }else {
                        $largeOrderGroup[$currentGroupNum][] = $largeOrder[$i];
                    }
                    $previousGroupNum = $currentGroupNum;
                }
            }else {
                $largeOrderGroup = null;
            }
            return [$array,  $largeOrderGroup];
        }
    }

    public function getBeforeDay()
    {
        return $this->beforeDay;
    }
    public function getNoGroupBeforeDay()
    {
        return $this->noGroupBeforeDay;
    }
    public function getIsGroupBeforeDay()
    {
        return $this->isGroupBeforeDay;
    }

    public function getToday()
    {
        return $this->today;
    }
    public function getNoGroupToday()
    {
        return $this->noGroupToday;
    }
    public function getIsGroupToday()
    {
        return $this->isGroupToday;
    }

    public function getAfterDay()
    {
        return $this->afterDay;
    }
    public function getNoGroupAfterDay()
    {
        return $this->noGroupAfterDay;
    }
    public function getIsGroupAfterDay()
    {
        return $this->isGroupAfterDay;
    }

}