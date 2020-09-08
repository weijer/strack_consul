<?php

namespace strack\consul;

use strack\consul\service\Service;
use Yurun\Util\HttpRequest;

class ConsulClient
{
    /**
     * @var HttpRequest
     */
    private $http;
    private $baseUrl = '';
    private $service = [];

    public function __construct($option = [])
    {
        $default = ['host' => '127.0.0.1:8500', 'url' => '/v1/'];
        $default = array_replace($default, $option);
        $this->baseUrl = $default['host'] . $default['url'];
        $this->http = new HttpRequest;
    }

    /**
     * 用魔术属性获取服务
     *
     * @param string $name 服务名
     *
     * @return Service 服务
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * 获取服务
     *
     * @param string $name 服务名
     *
     * @return Service 服务
     */
    public function get($name)
    {
        $name = ucfirst($name);
        if (empty($this->service[$name])) {
            $class_name = "strack\\consul\\service\\$name";
            $this->service[$name] = new $class_name($this->baseUrl, $this->http);
        }
        return $this->service[$name];
    }
}

;