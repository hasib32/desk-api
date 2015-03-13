<?php
namespace Hasan\DeskApi\Resources;

class BaseDeskResource {

    public function __construct(Array $properties){
        foreach($properties as $key => $value){
            if(is_array($value)){
                $value = json_decode(json_encode($value), false);
            }
            $this->{$key} = $value;
        }
    }
    public function link($param){
        try {
            $url = $this->_links->$param->href;
        } catch(\Exception $e){
            return $e->getMessage();
        }
        $resourceObject = \DeskApi::get($url);
        return $resourceObject;
    }
}