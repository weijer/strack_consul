<?php

namespace strack\consul\service;

use Yurun\Util\HttpRequest;

class Service
{
    /**
     * @var HttpRequest
     */
    protected $http;
    protected $baseUrl = '';
    public $name = 'agent';

    public function __construct($baseUrl, $http)
    {
        $this->baseUrl = $baseUrl;
        $this->http = $http;
    }

    public function __call($methodName, $args)
    {
        $url = $this->name . '/' . $methodName;
        if (empty($args)) {
            $resp = $this->http->get($this->baseUrl . $url);
        } else {
            $param = array();
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    $param = array_merge($param, $arg);
                } else {
                    $url .= '/' . $arg;
                }
            }
            $resp = $this->http->get($this->baseUrl . $url, $param);
        }
        return $this->response($resp);
    }

    /**
     * 返回数据
     * @param $str
     * @return bool|mixed|string
     */
    public function response($str)
    {
        if ($str === false) {
            return false;
        }
        if (empty($str)) {
            return '';
        }
        return json_decode($str, true);
    }

    /**
     * 设置服务header头
     * @return bool|HttpRequest
     */
    public function header()
    {
        $funcArgs = func_get_args();
        if (count($funcArgs) == 0) {
            return false;
        }

        $methodName = $funcArgs[0];
        $param = array();
        $url = $this->name . '/' . $methodName;

        for ($i = 1; $i < count($funcArgs); $i++) {
            if (is_array($funcArgs[$i])) {
                $param = array_merge($param, $funcArgs[$i]);
            } else {
                $url .= '/' . $funcArgs[$i];
            }
        }

        return $this->http->headers($param);
    }

    /**
     * put 发送数据
     * @return bool|mixed|string
     */
    public function put()
    {
        $args = func_get_args();
        $url = $this->name;
        if (empty($args)) {
            $resp = $this->http->get($this->baseUrl . $url);
        } else {
            $param = [];
            $buildUrl = "";
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    $param = array_merge($param, $arg);
                } else {
                    if ($arg == 'build_url') {
                        $buildUrl = $arg;
                        continue;
                    }
                    $url .= '/' . $arg;
                }
            }
            if (empty($param)) {
                $resp = $this->http->put($this->baseUrl . $url, []);
            } else {
                if ($buildUrl) {
                    $resp = $this->http->put($this->baseUrl . $url, $param);
                } else {
                    $resp = $this->http->put($this->baseUrl . $url, $param);
                }
            }
        }
        return $this->response($resp);
    }
}
