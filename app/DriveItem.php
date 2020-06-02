<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriveItem 
{
    public $path;
    public $itemId;
    public $fileSize;

    public function __construct($itemId,$path,$fileSize){
        $this->path=$path;
        $this->itemId=$itemId;
        $this->fileSize=$fileSize;
    }
}
