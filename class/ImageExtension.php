<?php declare(strict_types = 1);
require_once(__DIR__. '/Check.php');
require_once(__DIR__. '/Message.php');
require_once(__DIR__. '/Make.php');
require_once(__DIR__. '/../configs/constApp.php');

class ImageExtension
{
    const MENU_IMTAGE = ConstApp::MENU_IMAGE;
    const FILE_NAME = constApp::FILE_NAME;
    const FILE_TYPE = ConstApp::FILE_TYPE;
    const FILE_TMP_NAME = ConstApp::FILE_TMP_NAME;
    const FILE_ERROR = ConstApp::FILE_ERROR;
    const FILE_SIZE = constApp::FILE_SIZE;

    public $imageName;
    public $imageType;
    public $imagePass;
    public $imageError;
    public $imageSize;
    public $check;
    public $make;
    public $message;
    public $existImage;
    public $fileExtension;
    public $fileSize;
    public $mime;

    public function __construct($image)
    {
        $this->imageName = $image[self::FILE_NAME];
        $this->imageType = $image[self::FILE_TYPE];
        $this->imagePass = $image[self::FILE_TMP_NAME];
        $this->imageError = $image[self::FILE_ERROR];
        $this->imageSize = $image[self::FILE_SIZE];
        $this->check = new Check();
        $this->make = new Make();
        $this->message = new Message();
    }

    public function check()
    {
        $this->existImage = $this->check->emptyImage($this->imagePass);
        if ($this->existImage === true)
        {
            $this->mime = $this->check->extensionType($this->imageType, $this->imagePass);
            $this->fileExtension = ($this->mime === false) ? false : (in_array($this->mime, ConstClass::MIMES, true));
            $this->fileSize = $this->check->fileSize($this->imageSize);
        }
    }

    public function setMessage()
    {
        switch ($this->imageError)
        {
            case 0:
            if ($this->existImage !== true)
            {
                $this->message->pushEmpty(self::MENU_IMTAGE);
            }
            if ($this->fileExtension !== true)
            {
                $this->message->impossibleExtension(self::MENU_IMTAGE);
            }
            if ($this->fileSize !== true)
            {
                $this->message->impossibleFileSize(self::MENU_IMTAGE);
            }
            break;
            default:
            $this->message->fileError(self::MENU_IMTAGE);
        }
    }

    public function getMessage()
    {
        return $this->message->getMessage();
    }

    public function getImage(): array
    {
        return $result = [
            self::FILE_NAME => $this->getImageName(),
            self::FILE_TYPE => $this->getImageType(),
            self::FILE_TMP_NAME => $this->getImagePass(),
            self::FILE_ERROR => $this->getImageError(),
            self::FILE_SIZE => $this->getImageSize()
        ];
    }
    public function getImageName(): string
    {
        return $this->imageName;
    }
    public function getImageType(): string
    {
        return $this->imageType;
    }
    public function getImagePass(): string
    {
        return $this->imagePass;
    }
    public function getImageError()
    {
        return $this->imageError;
    }
    public function getImageSize()
    {
        return $this->imageSize;
    }
    public function getImageMimeType()
    {
        return $this->mime;
    }
}