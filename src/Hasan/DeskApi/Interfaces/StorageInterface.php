<?php namespace Hasan\DeskApi\Interfaces;

interface StorageInterface {
    public function putToken($token);

    public function put($key, $value);

    public function get($key);
}