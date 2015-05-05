<?php
namespace Hasan\DeskApi\Resources;

class DeskSearchResponse extends BaseDeskResource{

    /**
     * Convert array to object
     * convert embedded array collection to corresponding class
     * @param array $response
     * @param $resourceClass
     */
    public function __construct($response, $resourceClass){
        parent::__construct($response);
        $this->_embedded = $this->_embedded['entries'];

        foreach($this->_embedded as $key => $value){
            $this->_embedded[$key] = new $resourceClass($value);
        }
    }

}