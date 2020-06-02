<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response 
{
    //
    public $responseCode;
    public $body;

    public function __construct($responseCode,$body){
        $this->responseCode = $responseCode;
        $this->body = $body;
    }
}
