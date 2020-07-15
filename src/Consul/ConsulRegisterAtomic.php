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
        if (intval($this->atomic->get()) <= $workerNum) {
            if ($this->atomic->add() == $workerNum) {
                $logger = $container->get(StdoutLoggerInterface::class);
                try {
                    $container->get(ConsulRegisterService::class)->add();
                } catch (\Exception $throwable) {
                    $logger->error(sprintf('%s[%s] in %s', 'consul: ' . $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
                    $logger->error('consul: ' . $throwable->getTraceAsString());
                }
            }
        }
    }


    /**
     * 注销服务事件
     */
    public function shutdown()
    {

        $container = ApplicationContext::getContainer();
        $logger = $container->get(StdoutLoggerInterface::class);
        try {
            $container->get(ConsulRegisterService::class)->del();
        } catch (\Exception $throwable) {
            $logger->error(sprintf('%s[%s] in %s', 'consul: ' . $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
            $logger->error('consul: ' . $throwable->getTraceAsString());
        }
    }
}
