<?php declare(strict_types = 1);

class InsertImage
{
    public $imageType;
    public $imagePass;
    public $imageData;
    public $newImagePass;

    public function __construct($imageType, $imagePass, $imageData)
    {
        $this->imageType = $imageType;
        $this->imagePass = $imagePass;
        $this->imageData = $imageData;
    }

    public function insertLibrary()
    {
        $this->newImagePass = self::changeImagePass();
        self::moveImage();
    }

    public function changeImagePass()
    {
        $this->imageType = preg_replace('/image\//', '', $this->imageType);
        $objDateTime = new DateTime('now');
        $newImageName = $objDateTime->format('Y.m.d.His'). '.'. $this->imageType;
        $newImagePass = 'library/'. $newImageName;
        return $newImagePass;
    }

    public function moveImage()
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT']. '/'. $this->newImagePass, $this->imageData);
    }
    public function getImagePass()
    {
        return $this->newImagePass;
    }
}