<?php
namespace Hasan\DeskApi\Resources;

class BaseDeskResource {

    /**
     * convert array to object
     * @param array $properties
     */
    public function __construct(Array $properties){
        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }

    /**
     * Return the first array link href
     * @param $param
     * @return string
     */
    public function link($param){
        try {
            if(isset($this->_embedded)){
                $response = $this->_embedded[0]->_links[$param]['href'];
            } else {
                $response = $this->_links[$param]['href'];
            }
        } catch(\Exception $e){
            return $e->getMessage();
        }
        return $response;
    }
}