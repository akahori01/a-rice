<?php declare(strict_types = 1);
require_once(__DIR__. '/SelectMenuTable.php');
require_once(__DIR__. '/DeletePdo.php');
require_once(__DIR__. '/../configs/constApp.php');

class MenuModel
{
    private const MENU_NAME = ConstApp::MENU_NAME;
    private const MENU_MONEY = ConstApp::BUSINESS_MONEY;
    private const MENU_POINT = ConstApp::BUSINESS_POINT;
    public $select;
    public $delete;
    public $menuNames;
    public $menuAll;
    public $menuMoneyAll;
    public $menuPointAll;

    public function __construct()
    {
        $this->select = new SelectMenuTable();
        $this->delete = new DeletePdo();
    }
    public function menuNames()
    {
        $this->menuNames = $this->select->selectColumnAll(self::MENU_NAME);
    }

    public function menuMoneyAll()
    {
        $this->menuMoneyAll = $this->select->selectItemBusinessSetAll(self::MENU_MONEY);
    }
    public function menuPointAll()
    {
        $this->menuPointAll = $this->select->selectItemBusinessSetAll(self::MENU_POINT);
    }

    public function deleteMenu($deleteId)
    {
        $this->delete->deleteColumn('menu_info', 'menu_id', $deleteId);
    }

    public function menuAll()
    {
        $this->menuAll = $this->select->selectItemAll();
        return $this;
    }
    public function getAll()
    {
        return $this->menuAll;
    }
    public function getNames()
    {
        return $this->menuNames;
    }
    public function getMoneyAll()
    {
        return $this->menuMoneyAll;
    }
    public function getPointAll()
    {
        return $this->menuPointAll;
    }
}