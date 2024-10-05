<?php declare(strict_types = 1);

class InsertImage
{
    public $imageType;
    public $imagePass;
    public $newImagePass;

    public function __construct($imageType, $imagePass)
    {
        $this->imageType = $imageType;
        $this->imagePass = $imagePass;
    }

    public function insertLibrary()
    {
        $this->newImagePass = self::changeImagePass();
        // self::moveImage();
    }

    private function changeImagePass()
    {
        $this->imageType = preg_replace('/image\//', '', $this->imageType);
        $objDateTime = new DateTime('now');
        $newImageName = $objDateTime->format('Y.m.d.His'). '.'. $this->imageType;
        $newImagePass = 'library/'. $newImageName;
        return $newImagePass;
    }

    // public function moveImage()
    // {
    //     file_put_contents(__DIR__. '/../public/'. $this->newImagePass, $this->imageData);
    // }
    public function getImagePass()
    {
        return $this->newImagePass;
    }
}