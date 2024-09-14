<?php
declare(strict_types=1);
require_once(__DIR__. '/../class/MenuMoney.php');
require_once(__DIR__. '/../class/MenuPoint.php');
require_once(__DIR__. '/../DB/MenuModel.php');

class MenuInstance
{
    public $menu;
    public $menus;

    public function __construct()
    {
        $this->menu = new menuModel();
        $this->menus = [];
    }

    public function moneyMenu()
    {
        $this->menu->menuMoneyAll();
        $menus = $this->menu->getMoneyAll();
        foreach ($menus as $menu){
            unset($menu['created_at']);
            unset($menu['updated_at']);
            $databasemenu = new MenuMoney($menu);
            $databasemenu->make();
            $this->menus[] = $databasemenu;
        }
        return $this->menus;
    }
    public function pointMenu()
    {
        $this->menu->menuPointAll();
        $menus = $this->menu->getPointAll();
        foreach ($menus as $menu){
            unset($menu['created_at']);
            unset($menu['updated_at']);
            $databasemenu = new MenuPoint($menu);
            $databasemenu->make();
            $this->menus[] = $databasemenu;
        }
        return $this->menus;
    }
}