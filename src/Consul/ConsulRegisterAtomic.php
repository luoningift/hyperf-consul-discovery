<?php

namespace HKY\HyperfDiscovery\Consul;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Swoole\Atomic;

/**
 * register self to consul
 * Class ConsulRegisterAtomic
 * @package HKY\HyperfDiscovery\Consul
 */
class ConsulRegisterAtomic
{

    private $atomic;

    public function __construct()
    {
       $this->atomic = new Atomic();
    }

    /**
     * 计数启动的worker数量
     */
    public function add()
    {
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);
        $workerNum = $config->get('server.settings.worker_num');
        if (intval($this->atomic->get()) <= intval($workerNum)) {
            $this->atomic->add();
        }
    }

    /**
     * 判断worker是否启动
     * @return bool
     */
    public function isReady() {
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);
        $workerNum = $config->get('server.settings.worker_num'); 
        return intval($this->atomic->get()) == intval($workerNum);
    }
}
