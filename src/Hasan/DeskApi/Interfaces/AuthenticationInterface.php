<?php namespace Hasan\DeskApi\Interfaces;

interface AuthenticationInterface {
    /**
     * Begin Authentication
     * @param $url
     * @return mixed
     */
    public function authenticate($url);
}