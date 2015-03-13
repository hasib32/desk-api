<?php namespace Hasan\DeskApi\Providers\Laravel;

use Hasan\DeskApi\Exceptions\MissingKeyException;
use Hasan\DeskApi\Interfaces\StorageInterface;
use Session;
class LaravelSession implements StorageInterface {

    public function putToken($token){
        $encryptedToken = Crypt::encrypt($token);
        return Session::put('desk_api_resources', $encryptedToken);
    }

    public function put($key, $value){
        if(!Session::has($key)){
            Session::put($key, $value);
        }
    }
    public function get($key){
        $value = Session::get($key);
        if(isset($value)){
            return $value;
        }
        throw new MissingKeyException(sprintf("No value for requested key: %s",$key));
    }
}