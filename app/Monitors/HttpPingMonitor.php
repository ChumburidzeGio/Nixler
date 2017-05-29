<?php

namespace App\Monitors;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class HttpPingMonitor extends BaseMonitor
{
    /**  @var int */
    protected $responseCode;

    protected $responseContent;

    /**
     * @param array $config
     */
    public function __construct()
    {
        try {

            $guzzle = new Guzzle([
                'timeout' => 5,
                'allow_redirects' => true,
            ]);

            $response = $guzzle->get(config('app.url'));

            $this->responseCode = $response->getStatusCode();

        } catch (RequestException $e) {

            $this->setResponseCodeAndContentOnException($e);

        } catch (\Exception $e) {

            $this->setResponseCodeAndContentOnException($e);

        }
    }

    /**
     * @return boolean
     */
    public function setResponseCodeAndContentOnException($e)
    {
        if(method_exists($e, 'getResponse') && $response = $e->getResponse() && $response instanceof ResponseInterface) {
            return $this->responseCode = $response->getStatusCode();
        }

        $this->responseCode = null;

        $this->responseContent = $e->getMessage();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Http Ping';
    }

    /**
     * @return boolean
     */
    public function hasErrors()
    {
        return ($this->responseCode != '200');
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->hasErrors() ? 
            ($this->responseCode ? $this->responseCode.' error' : $this->responseContent) : 
            'The request is OK';
    }
}