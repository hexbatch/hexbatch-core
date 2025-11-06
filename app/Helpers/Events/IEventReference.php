<?php

namespace App\Helpers\Events;

interface IEventReference
{
    public function  getSourceId() : int ;
    public function  getSourceRef() : string ;
    public function  setSourceRef(string $ref) : void ;
    public function  getReferences() : array ;
    public function  setReferences(array $whats) :void  ;

}
