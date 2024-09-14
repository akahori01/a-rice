<?php declare(strict_types = 1);
require_once('../configs/constClass.php');
require_once('../DB/UserModel.php');

class DeliveryDate
{
    private const CITY1 = ConstClass::CITY1;
    private const CITY2 = ConstClass::CITY2;
    private const CITY1_DAY = ConstClass::CITY1_DAY;
    private const CITY2_DAY = ConstClass::CITY2_DAY;

    public $userId;
    public $userModel;
    public $city;
    public $possibleDay;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->userModel = new SelectUserModel($this->userId);
    }

    public function setPossibleDate()
    {
        $this->userModel->selectCity();
        $this->city = $this->userModel->getCity();
        switch ($this->city)
        {
            case self::CITY1:
                $this->possibleDay = self::CITY1_DAY;
                break;
            case self::CITY2:
                $this->possibleDay = self::CITY2_DAY;
                break;
        }
    }

    public function getPossibleDate()
    {
        return $this->possibleDay;
    }
}