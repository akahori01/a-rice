<?php declare(strict_types=1);
require_once(__DIR__. '/SelectPdo.php');
require_once(__DIR__. '/InsertCalendar.php');
require_once(__DIR__. '/DeletePdo.php');

class CalendarModel{
    public $select;
    public $insert;
    public $delete;
    public function __construct()
    {
        $this->select = new SelectPdo(null);
        $this->insert = new InsertCalendarTable();
        $this->delete = new DeletePdo();
    }

    public function select()
    {
        $db_tb_calendar = $this->select->select('calendar_info');
        return $db_tb_calendar;
    }
    public function insert($holiday)
    {
        $this->insert->insert($holiday);
    }
    public function delete($deleteid)
    {
        $this->delete->deleteColumn('calendar_info', 'id', $deleteid);
    }

}