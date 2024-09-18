<?php declare(strict_types = 1);
require_once(__DIR__. '/../DB/InsertMenu.php');
require_once(__DIR__. '/../DB/MenuModel.php');
require_once(__DIR__. '/../class/Trait-Show.php');
require_once(__DIR__. '/../configs/constApp.php');
require_once(__DIR__. '/Check.php');
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/Make.php');
require_once(__DIR__. '/Trait-Show.php');


class Menu
{
    use Show;

    private const MENU_NAME = ConstApp::MENU_NAME;
    protected const MENU_COST = ConstApp::MENU_COST;
    private const MENU_WEIGHT = ConstApp::MENU_WEIGHT;
    private const MENU_CATEGORY = ConstApp::MENU_CATEGORY;
    private const MENU_UNIT = ConstApp::MENU_UNIT;
    private const MENU_COMMENT_TOP = ConstApp::MENU_COMMENT_TOP;
    private const MENU_COMMENT_BOTTOM = ConstApp::MENU_COMMENT_BOTTOM;
    private const MENU_NOTES = ConstApp::MENU_NOTES;
    private const TOTAL_COMMENT = ConstApp::TOTAL_COMMENT;
    private const DIGEST_COMMENT = ConstApp::DIGEST_COMMENT;
    private const DIGEST_NOTES = ConstApp::DIGEST_NOTES;

    public $datas;
    private $insert;
    private $check;
    private $message;
    private $make;
    private $menuModel;
    public $count;
    public $subTotalCost;
    private static $totalCost = 0;
    private $checkNameBlank;
    private $checkCostBlank;
    private $checkCostInt;
    private $checkWeightBlank;
    private $checkWeightInt;
    private $checkCategoryBlank;
    private $checkUnitBlank;
    private $checkCommentTopBlank;
    private $checkCommentBottomBlank;
    private $checkNotesBlank;
    private $alreadyItemNames;
    private $checkDuplicationName;

    use Show;

    public function __construct(?array $datas)
    {
        $this->datas = $datas;
        $this->insert = new InsertMenu();
        $this->check = new Check();
        $this->message = new Message();
        $this->make = new Make();
        $this->menuModel = new MenuModel();
    }

    public function deleteEmpty()
    {
        $this->datas[self::MENU_NAME]= $this->make->emptyDel($this->datas[self::MENU_NAME]);
        $this->datas[self::MENU_COST] = $this->make->emptyDel($this->datas[self::MENU_COST]);
        $this->datas[self::MENU_WEIGHT] = $this->make->emptyDel($this->datas[self::MENU_WEIGHT]);
        $this->datas[self::MENU_CATEGORY] = $this->make->emptyDel($this->datas[self::MENU_CATEGORY]);
        $this->datas[self::MENU_UNIT] = $this->make->emptyDel($this->datas[self::MENU_UNIT]);
        $this->datas[self::MENU_COMMENT_TOP] = $this->make->emptyDel($this->datas[self::MENU_COMMENT_TOP]);
        $this->datas[self::MENU_COMMENT_BOTTOM] = $this->make->emptyDel($this->datas[self::MENU_COMMENT_BOTTOM]);
        $this->datas[self::MENU_NOTES] = $this->make->emptyDel($this->datas[self::MENU_NOTES]);

        $this->datas[self::MENU_COMMENT_TOP] = $this->make->emptyIndention($this->datas[self::MENU_COMMENT_TOP]);
        $this->datas[self::MENU_COMMENT_BOTTOM] = $this->make->emptyIndention($this->datas[self::MENU_COMMENT_BOTTOM]);
        $this->datas[self::MENU_NOTES] = $this->make->emptyIndention($this->datas[self::MENU_NOTES]);
    }

    public function check()
    {
        $this->menuModel->menuNames();
        $this->alreadyItemNames = $this->menuModel->getNames();
        if ($this->alreadyItemNames === null){
            $this->checkDuplicationName = true;
        } else {
            $this->checkDuplicationName = $this->check->DuplicationName($this->datas[self::MENU_NAME], $this->alreadyItemNames);
        }
        $this->checkNameBlank = $this->check->blankCheck($this->datas[self::MENU_NAME]);
        $this->checkCostBlank = $this->check->blankCheck($this->datas[self::MENU_COST]);
        $this->checkCostInt = $this->check->onlyInteger($this->datas[self::MENU_COST]);
        $this->checkWeightBlank = $this->check->blankCheck($this->datas[self::MENU_WEIGHT]);
        $this->checkWeightInt = $this->check->onlyInteger($this->datas[self::MENU_WEIGHT]);
        $this->checkCategoryBlank = $this->check->blankCheck($this->datas[self::MENU_CATEGORY]);
        $this->checkUnitBlank = $this->check->blankCheck($this->datas[self::MENU_UNIT]);
        $this->checkCommentTopBlank = $this->check->blankCheck($this->datas[self::MENU_COMMENT_TOP]);
    }

    public function make()
    {
        $this->datas[self::MENU_COST] = $this->make->halfChar($this->datas[self::MENU_COST]);
        $this->datas[self::TOTAL_COMMENT] = $this->make->join($this->datas[self::MENU_COMMENT_TOP], $this->datas[self::MENU_COMMENT_BOTTOM]);
        $this->datas[self::DIGEST_COMMENT] = $this->make->digest($this->datas[self::TOTAL_COMMENT]);
        $this->datas[self::DIGEST_NOTES] = $this->make->digest($this->datas[self::MENU_NOTES]);
    }

    protected function convertSaveMenuDatabase()
    {
        if (!$this->check->blankCheck($this->datas[self::MENU_COMMENT_BOTTOM])){
            $this->datas[self::MENU_COMMENT_BOTTOM] = null;
        }
        if (!$this->checkNotesBlank = $this->check->blankCheck($this->datas[self::MENU_NOTES])){
            $this->datas[self::MENU_NOTES] = null;
        }
    }


    public function setMessage()
    {
        if ($this->checkDuplicationName !== true)
        {
            $this->message->duplicationItemName(self::MENU_NAME);
        }
        if ($this->checkNameBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_NAME);
        }
        if ($this->checkCostBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_COST);
        }
        if ($this->checkWeightBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_WEIGHT);
        }
        if ($this->checkCostInt !== true)
        {
            $this->message->notIntegerOnly(self::MENU_COST);
        }
        if ($this->checkWeightInt !== true)
        {
            $this->message->notIntegerOnly(self::MENU_WEIGHT);
        }
        if ($this->checkCategoryBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_CATEGORY);
        }
        if ($this->checkUnitBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_UNIT);
        }
        if ($this->checkCommentTopBlank !== true)
        {
            $this->message->pushEmpty(self::MENU_COMMENT_TOP);
        }
    }

    public function subTotalCost(){
        $this->datas['sub_total_cost'] = intval($this->datas[self::MENU_COST]) * intval($this->datas['order_count']);
        self::totalCost();
    }

    public function totalCost()
    {
        self::$totalCost += $this->datas['sub_total_cost'];
        $this->datas['total_cost'] = self::$totalCost;
        return;
    }

    public function getSubTotalCost()
    {
        return $this->datas['sub_total_cost'];
    }


    public function getTotalCost()
    {
        return self::$totalCost;
    }



    public function makeEscape()
    {
        foreach ($this->datas as $key => $value)
        {
            $this->datas[$key] = self::escape($value);
        }
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }

    public function getDatas()
    {
        return $this->datas;
    }
    public function getName(): string
    {
        return $this->datas[self::MENU_NAME];
    }
    public function getWeight(): int
    {
        return intval($this->datas[self::MENU_WEIGHT]);
    }
    public function getCategory(): string
    {
        return $this->datas[self::MENU_CATEGORY];
    }
    public function getUnit(): string
    {
        return $this->datas[self::MENU_UNIT];
    }
    public function getCommentTop(): string
    {
        return $this->datas[self::MENU_COMMENT_TOP];
    }
    public function getCommentBottom(): ?string
    {
        return $this->datas[self::MENU_COMMENT_BOTTOM];
    }
    public function getTotalComment()
    {
        return $this->datas[self::TOTAL_COMMENT];
    }
    public function getDigestComment(): string
    {
        return $this->datas[self::DIGEST_COMMENT];
    }
    public function getNotes(): ?string
    {
        return $this->datas[self::MENU_NOTES];
    }
    public function getDigestNotes(): ?string
    {
        return $this->datas[self::DIGEST_NOTES];
    }

    public function getMenuId()
    {
        return $this->datas['menu_id'];
    }

    public function getBusinessSet()
    {
        return $this->datas['business_set'];
    }
    public function getLargeOrderGroup()
    {
        return $this->datas['large_order_group'];
    }

    public function getOrderId()
    {
        return $this->datas['order_id'];
    }

    public function getDeliverydate()
    {
        return $this->datas['delivery_date'];
    }

    public function getUserName()
    {
        return $this->datas['user_name'];
    }

    public function getAddress()
    {
        return $this->datas['user_address'];
    }

    public function getImagePass()
    {
        return $this->datas['menu_image_pass'];
    }

    public function getTel()
    {
        $tel = self::telFormat($this->datas['user_tel']);
        return $tel;
    }

    public function getCount()
    {
        return $this->datas['order_count'];
    }

    public function deleteMenu($deleteId)
    {
        $this->menuModel->deleteMenu($deleteId);
    }
}