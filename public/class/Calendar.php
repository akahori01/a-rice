<?php
declare(strict_types=1);
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/../DB/CalendarModel.php');

class Calendar{

    public $debugDate;
    public $message;
    public $today;
    public $todayStart;
    public $timestamps = [];
    public $week = '';
    public $weeks = [];
    public $calendars = [];
    public $db_tb_calendar;
    public $holidate;
    public $sessionDay;
    public $deliveryWeek;
    private $bigLotHoliday = [];

    public function __construct(?string $sessionDay, ?string $deliveryWeek = null)
    {
        if (isset($sessionDay))
        {
            $this->sessionDay = $sessionDay;
        }
        $this->deliveryWeek = $deliveryWeek;
        $this->today = date('Y-m-d');
        $this->todayStart = date('Y-m'). '-1';
        $todayStartTimestamp = strtotime($this->todayStart);
        $nextStartTimestamp = strtotime('+1 month', $todayStartTimestamp);
        $this->timestamps = [$todayStartTimestamp, $nextStartTimestamp];
        $this->message = new Message();
        $this->db_tb_calendar = new CalendarModel();
    }


    public function beginningWeek($timestamp)
    {
        return intval(date('w', $timestamp));
    }

    public function searchYM($timestamp)
    {
        return date('Y-m', $timestamp);
    }

    public function calendarTitle($timestamp)
    {
        return date('Y年n月', $timestamp);
    }

    public function countMonthDays($timestamp)
    {
        return intval(date('t', $timestamp));
    }

    /* 配達日指定用
    private function fromWeekToInt($deliveryWeek)
    {
        switch ($deliveryWeek)
        {
            case '日曜日';
                $deliveryWeek = '0';
                break;
            case '月曜日';
                $deliveryWeek = '1';
                break;
            case '火曜日';
                $deliveryWeek = '2';
                break;
            case '水曜日';
                $deliveryWeek = '3';
                break;
            case '木曜日';
                $deliveryWeek = '4';
                break;
            case '金曜日';
                $deliveryWeek = '5';
                break;
            case '土曜日';
                $deliveryWeek = '6';
                break;
        }
        return $deliveryWeek;
    }
    */
    public function insertholiday($holiday)
    {
        $this->db_tb_calendar->insert($holiday);
    }
    public function deleteholiday($deleteday)
    {
        $db_calendar = $this->db_tb_calendar->select();
        if (is_null($db_calendar)) {
            return;
        } else{
            foreach ($db_calendar as $index_num => $array){
                foreach ($array as $column_name => $value){
                    if ($column_name === 'holiday'){
                        if ($deleteday === $value){
                            $deleteid = $db_calendar[$index_num]['id'];
                        }
                    }
                }
            }
            if (isset($deleteid)){
                $this->db_tb_calendar->delete($deleteid);
            } else{
                return;
            }
        }
    }
    public function selectholiday()
    {
        $db_calendar = $this->db_tb_calendar->select();
        if (is_null($db_calendar)) {
            return;
        } else{
            foreach ($db_calendar as $array){
                foreach ($array as $column_name => $value){
                    if ($column_name === 'holiday'){
                        $holidays[] = $value;
                    }
                }
            }
            return $holidays;
        }
    }

    public function showCalendar()
    {
        foreach ($this->timestamps as $timestamp)
        {
            $beginningWeek = self::beginningWeek($timestamp);
            $YM = self::searchYM($timestamp);
            $title = self::calendarTitle($timestamp);
            $monthDays = self::countMonthDays($timestamp);
            $this->week .= str_repeat('<td></td>', $beginningWeek);
            /* 配達日指定用
            $this->deliveryWeek = self::fromWeekToInt($this->deliveryWeek);
            */
            $holidays = self::selectholiday();
            $this->calendars[] = self::makeCalendar($monthDays, $beginningWeek, $YM, $title, $this->week, $this->weeks, $holidays);
        }
    }

    private function makeCalendar(int $monthDays, int $beginningWeek, string $YM, string $title, string $week, array $weeks, ?array $holidays)
    {
        for($day = 1; $day <= $monthDays; $day++, $beginningWeek++){
            $d = str_pad(strval($day), 2, '0', STR_PAD_LEFT);
            $date = $YM . '-' . $d;
            /*
            配達日指定用
            if ($date > $this->today && date('w', strtotime($date)) === $this->deliveryWeek)
            */
            if ($holidays !== null && $date >= $this->today && in_array($date, $holidays, true)){
                self::holidate($date);
                $week .= '<td class="holiday">'. $day;
            } elseif ($date > $this->today){
                self::debugDate($date);
                if (isset($this->sessionDay) && $date === $this->sessionDay){
                    $week .= '<td class="deliveryDate"><input type="radio" name="checkDay" value="'. $date. '" id="'.$date. '" checked><label for="'. $date. '">'. $day. '</label>';
                } else {
                    $week .= '<td class="deliveryDate"><input type="radio" name="checkDay" value="'. $date. '" id="'.$date. '"><label for="'. $date. '">'. $day. '</label>';
                }
            } elseif ($this->today === $date){
                $week .= '<td class="today">'. $day;
            } else {
                $week .= '<td class="notDeliveryDate">'. $day;
            }
            $week .= '</td>';

            if($beginningWeek % 7 == 6 || $day === $monthDays){
                if($day === $monthDays){
                    $week .= str_repeat('<td></td>', 6 - ($beginningWeek % 7));
                }
                $weeks[] = '<tr>' . $week . '</tr>';

                $week = '';
            }
        }
        $this->week = '';
        return [
            'title' => $title,
            'weeks' => $weeks
        ];
    }



    public function showCalendarBigLot()
    {
        // $monthDays に今月の日数取得
        $monthDays = self::countMonthDays($this->timestamps[0]);
        // 今日の 年 月 日 を分け代入
        list($y, $m, $d) = explode('-', $this->today);
        // $i によって準備期間を調整できる 現在であれば7日間注文できない仕様になっている
        for($i = 0; $i < 7; $i++){
            // 1日~9日であれば、01日 02日 ... 09日 へ変換
            if (strlen(strval($d)) !== 2){
                $d = str_pad(strval($d), 2, '0', STR_PAD_LEFT);
            }
            if ($d <= $monthDays){
                $this->bigLotHoliday[] = strval($d++);
            }else {
                $d = str_pad(strval(1), 2, '0', STR_PAD_LEFT);
            }
        }
        foreach ($this->timestamps as $timestamp)
        {
            $beginningWeek = self::beginningWeek($timestamp);
            $YM = self::searchYM($timestamp);
            $title = self::calendarTitle($timestamp);
            $monthDays = self::countMonthDays($timestamp);
            $this->week .= str_repeat('<td></td>', $beginningWeek);
            /* 配達日指定用
            $this->deliveryWeek = self::fromWeekToInt($this->deliveryWeek);
            */
            $holidays = self::selectholiday();
            $this->calendars[] = self::makeCalendarBigLot($monthDays, $beginningWeek, $YM, $title, $this->week, $this->weeks, $holidays);
        }
    }

    private function makeCalendarBigLot(int $monthDays, int $beginningWeek, string $YM, string $title, string $week, array $weeks, ?array $holidays)
    {

        for($day = 1; $day <= $monthDays; $day++, $beginningWeek++){
            $d = str_pad(strval($day), 2, '0', STR_PAD_LEFT);
            $date = $YM . '-' . $d;
            /*
            配達日指定用
            if ($date > $this->today && date('w', strtotime($date)) === $this->deliveryWeek)
            */
            if (!empty($this->bigLotHoliday) && $d == $this->bigLotHoliday[0]){
                self::holidate($date);
                $week .= '<td class="waitingTimeDay">'. $day;
                array_shift($this->bigLotHoliday);
            }elseif ($holidays !== null && $date >= $this->today && in_array($date, $holidays, true)){
                self::holidate($date);
                $week .= '<td class="holiday">'. $day;
            }elseif ($date > $this->today){
                self::debugDate($date);
                if (isset($this->sessionDay) && $date === $this->sessionDay){
                    $week .= '<td class="deliveryDate"><input type="radio" name="checkDay" value="'. $date. '" id="'.$date. '" checked><label for="'. $date. '">'. $day. '</label>';
                } else {
                    $week .= '<td class="deliveryDate"><input type="radio" name="checkDay" value="'. $date. '" id="'.$date. '"><label for="'. $date. '">'. $day. '</label>';
                }
            }elseif ($this->today === $date){
                $week .= '<td class="today">'. $day;
            }else {
                $week .= '<td class="notDeliveryDate">'. $day;
            }
            $week .= '</td>';

            if($beginningWeek % 7 == 6 || $day === $monthDays){
                if($day === $monthDays){
                    $week .= str_repeat('<td></td>', 6 - ($beginningWeek % 7));
                }
                $weeks[] = '<tr>' . $week . '</tr>';

                $week = '';
            }
        }
        $this->week = '';
        return [
            'title' => $title,
            'weeks' => $weeks
        ];
    }

    private function holidate($date)
    {
        $this->holidate[] = $date;
    }
    public function getholidate()
    {
        return $this->holidate;
    }

    private function debugDate($date)
    {
        $this->debugDate[] = $date;
    }

    private function getDebugDate()
    {
        return $this->debugDate;
    }

    public function error()
    {
        self::funnyValue();
    }

    public function funnyValue()
    {
        if (in_array($this->sessionDay, self::getDebugDate(), true)){
            return;
        } else {
            self::setMessage(__FUNCTION__);
        }
    }

    public function setMessage($functionName)
    {
        switch ($functionName)
        {
            case 'funnyValue';
                $this->message->funnyDay(ConstApp::CALENDAR);
                break;
        }
    }

    public function getCheckDay()
    {
        return $this->sessionDay;
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }

    public function getCalendars()
    {
        return $this->calendars;
    }
}