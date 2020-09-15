<?php
/**
 * Class Clinet
 * @package strack\consul
 */

namespace strack\consul;

use Yurun\Util\HttpRequest;
use strack\consul\exception\ClientException;
use strack\consul\exception\ServerException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Client
{
    // consul 默认注册地址
    protected $consulHost = "http://127.0.0.1:8500";

    // http 请求发起对象
    protected $httpRequest;

    // 参数
    protected $options = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($host = "", LoggerInterface $logger = null)
    {
        if (!empty($host)) {
            $this->consulHost = $host;
        }

        $this->httpRequest = new HttpRequest;

        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Send a HTTP request.
     * @param string $method
     * @param string $url
     * @param array $options
     * @return ConsulResponse
     */
    protected function request(string $method, string $url, array $options = []): ConsulResponse
    {
        $requestUrl = $this->consulHost.$url;

        $this->logger->debug(sprintf('Consul Request [%s] %s', strtoupper($method), $requestUrl));
        try {
            $response =  $this->httpRequest->send($requestUrl, $options, $method, "json");
        } catch (TransferException $e) {
            $message = sprintf('Something went wrong when calling consul (%s).', $e->getMessage());
            $this->logger->error($message);
            throw new ServerException($e->getMessage(), $e->getCode(), $e);
        }

        if ($response->getStatusCode() >= 400) {
            $message = sprintf('Something went wrong when calling consul (%s - %s).', $response->getStatusCode(), $response->getReasonPhrase());
            $this->logger->error($message);
            $message .= PHP_EOL . (string) $response->getBody();
            if ($response->getStatusCode() >= 500) {
                throw new ServerException($message, $response->getStatusCode());
            }
            throw new ClientException($message, $response->getStatusCode());
        }

        return new ConsulResponse($response);
    }
}


