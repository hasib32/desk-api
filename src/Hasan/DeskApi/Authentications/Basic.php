<?php namespace Hasan\DeskApi\Authentications;

use GuzzleHttp\ClientInterface;
use Hasan\DeskApi\Client;
use Hasan\DeskApi\Interfaces\AuthenticationInterface;
use Hasan\DeskApi\Interfaces\StorageInterface;

class Basic extends Client implements AuthenticationInterface{
    /**
     * @param ClientInterface $client
     * @param StorageInterface $storage
     * @param $configurations
     */
    public function __construct(ClientInterface $client, StorageInterface $storage, $configurations){
        $this->client = $client;
        $this->storage = $storage;
        $this->configurations = $configurations;
    }

    public function authenticate($loginUrl = null){
    }

}