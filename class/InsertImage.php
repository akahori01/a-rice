<?php declare(strict_types = 1);

class InsertImage
{
    public $imageType;
    public $imagePass;
    public $newImagePass;
    public $imageData;

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

    private function changeImagePass()
    {
        $this->imageType = preg_replace('/image\//', '', $this->imageType);
        $objDateTime = new DateTime('now');
        $newImageName = $objDateTime->format('Y.m.d.His'). '.'. $this->imageType;
        $newImagePass = 'library/'. $newImageName;
        return $newImagePass;
    }

    public function moveImage()
    {
        file_put_contents(__DIR__. '/../public/'. $this->newImagePass, $this->imageData);
    }
    public function getImagePass()
    {
        return $this->newImagePass;
    }
}