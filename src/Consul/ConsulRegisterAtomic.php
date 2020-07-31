<?php

namespace HKY\HyperfDiscovery\Consul;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Utils\ApplicationContext;
use Swoole\Atomic;
use Swoole\Process;

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
     * 注册服务
     */
    public function register()
    {

        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);
        $workerNum = intval($config->get('server.settings.worker_num'));
        if ($this->atomic->add() == $workerNum) {
            $container->get(ConsulRegisterService::class)->add();
        }
    }


    /**
     * 注销服务事件
     */
    public function shutdown()
    {
        $container = ApplicationContext::getContainer();
        $container->get(ConsulRegisterService::class)->del();
    }
}
