<?php
/**
 * Class Agent
 * @package Reepu\Consul\Models
 */

namespace strack\consul;


class Agent extends Client
{

    /**
     * 注册服务
     * @param $consulServiceId
     * @param $consulServiceName
     * @param $address
     * @param int $port
     * @param bool $isHttps
     * @param array $tags
     * @param string $interval
     */
    public function register(
        $consulServiceId,
        $consulServiceName,
        $address,
        $port = 80,
        $tags = ["primary"],
        $isHttps = false,
        $interval = '10s'
    )
    {
        // 判断访问链接的http连接类型
        $urlHttp = $isHttps ? "https" : "http";

        $param = [
            "ID" => $consulServiceId, // 注册服务ID
            "Name" => $consulServiceName, // 注册服务名称
            "Address" => $address,
            "Port" => $port, // 端口
            "Tags" => $tags, // 标签
            "EnableTagOverride" => false, // 启用标签覆盖
            "Check" => [
                "DeregisterCriticalServiceAfter" => "90m", // 在之后注销关键服务多少秒
                "Http" => "{$urlHttp}://{$address}:{$port}/consul/health", // 健康检测地址
                "Interval" => "10s" // 检测时间间隔
            ]
        ];

        return $this->request('put', '/v1/agent/service/register', $param);
    }

    /**
     * @param $service_id
     * @param $enable
     * @param string $reason
     */
    public function maintenance($service_id, $enable, $reason = '')
    {

    }

    /**
     * @param $serviceId
     */
    public function deregister($serviceId)
    {

    }
}
