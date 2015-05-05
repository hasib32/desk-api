<?php namespace Hasan\DeskApi;

use Hasan\DeskApi\Exceptions\MissingKeyException;
use GuzzleHttp\Exception\RequestException;
use Hasan\DeskApi\Resources\BaseDeskResource;
use Hasan\DeskApi\Resources\DeskSearchResponse;

abstract class Client {
    /**
     * HTTP client
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $storage;

    /**
     * Configuration for the Desk Api
     * @var
     */
    protected $configurations;

    /**
     * Desk Api Headers
     * @var
     */
    protected $headers;

    /**
     * This method can handle both GET
     * and Query search Request
     * @param $resource
     * @param null $param
     * @return mixed
     */
    public function get($resource, $param = null){
        $url = $this->getInstanceUrl($resource);
        if(is_int($param)){
            $url .="/".$param;
        }
        elseif(is_array($param)){
            $url .= "/search?".http_build_query($param);
        }
        // for Desk APi q search
        elseif(is_string($param)){
            $url .= "/search?q=".$param;
        }
        return $this->request('GET', urldecode($url));
    }

    /**
     * POST Request method
     * @param $resource
     * @param $params
     * @return mixed
     */
    public function post($resource, $params){
        $url = $this->getInstanceUrl($resource);
        return $this->request('POST', $url, $params);
    }

    /**
     * Update Request method
     * @param $resource
     * @param $params
     * @return mixed
     */
    public function patch($resource, $params){
        $url = $this->getInstanceUrl($resource);
        return $this->request('PATCH', $url, $params);
    }

    /**
     * create request and
     * send it to the HTTP client
     * @param $method
     * @param $url
     * @param null $params
     * @return mixed
     */
    private function request($method, $url, $params = null){
        $this->setHeaders();
        $options = $this->headers;
        if($params){
            $options['json'] = $params;
        }
        $request = $this->client->createRequest($method, $url, $options);
        try{
            $response = $this->client->send($request);
        } catch(RequestException $e){
            if ($e->hasResponse()) {
                $response =  $e->getResponse()->getBody(true);
                \Log::info($response);
                return $response;
            }
        }
        $response = $response->json();
        return $this->formatResponse($response, $url);
    }

    /**
     * Get all the Desk Api Resources
     * @return mixed
     */
    public function resources(){
        try{
            $resources = $this->storage->get('desk_api_resources');
        } catch (MissingKeyException $e){
            $this->setHeaders();
            $resources = $this->client->get($this->client->getBaseUrl(), $this->headers)->json()['_links'];
            $this->storage->put('desk_api_resources', $resources);
        }

        return $resources;
    }

    /**
     * Return full url from resource
     * @param $resource
     * @return string
     */
    protected function getInstanceUrl($resource){
        $baseUrl = $this->configurations['credentials']['url'];
        $urlPath = substr(parse_url($baseUrl)['path'], 1);
        // check if the resource contains api version part of the url
        // if contains. remove the version part from base url
        if(strpos($resource, $urlPath) !== false) {
            $resource = substr($resource, strlen($urlPath) + 1);
        }
        $url = $baseUrl.$resource;
        return $url;
    }

    /**
     * Desk APi Headers
     */
    public function setHeaders(){
        $this->headers['headers']['Accept'] = 'application/json';
        $this->headers['headers']['content-type'] = 'application/json';
        $this->headers['auth'] = [$this->configurations['credentials']['username'], $this->configurations['credentials']['password']];
    }

    /**
     * Return resource class
     * @param $response
     * @param
     * @return mixed
     */
    private function getResourceClass($response, $url){
        if(isset($response['_embedded'])){
            // if the response does not have entries
            // we get the class from url
            if($response['total_entries'] < 1 ){
                $class = $this->getResourceClassFromUrl($url);
                return $class;
            }
            $class = $response['_embedded']['entries'][0]['_links']['self']['class'];
        } else {
            $class  = $response['_links']['self']['class'];
        }
        return $class;
    }

    /**
     * Convert json response to corresponding object class
     * @param $response
     * @param $url
     * @return DeskSearchResponse
     */
    private function formatResponse($response, $url){
        $resourceClass = $this->getResourceClass($response, $url);
        $deskResourceClass = "\\Hasan\\DeskApi\\Resources\\Desk".$resourceClass;
        if(isset($response['_embedded'])){
            $response = new DeskSearchResponse($response, $deskResourceClass);
        } else {
            $response  = new $deskResourceClass($response);
        }
        return $response;
    }

    /**
     * @param $url
     * @return string
     */
    private function getResourceClassFromUrl($url){
        // get base url from configurations
        $baseUrl = $this->configurations['credentials']['url'];
        // remove baseUrl from url
        $removeBaseUrl = substr($url, strlen($baseUrl));
        if(substr_count($removeBaseUrl, "/") == 2){
            $positionOfLastSlash = strrpos($removeBaseUrl, "/") + 1;
            $class = substr($removeBaseUrl, $positionOfLastSlash, strlen($removeBaseUrl));
        } else {
            // find first / and get string
            $class = substr($removeBaseUrl, 0, strpos($removeBaseUrl, "/"));
        }
        $class = str_singular($class);
        return $class;
    }
}
