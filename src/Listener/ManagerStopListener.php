<?php
declare(strict_types=1);

namespace HKY\HyperfDiscovery\Listener;

use HKY\HyperfDiscovery\Consul\ConsulRegisterService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\OnManagerStop;
use Hyperf\Utils\ApplicationContext;

class ManagerStopListener implements ListenerInterface
{
   
    public function listen(): array
    {
        return [
            OnManagerStop::class,
        ];
    }

    public function process(object $event)
    {
        $container = ApplicationContext::getContainer();
        $logger = $container->get(StdoutLoggerInterface::class);
        try {
            $registerServer = $container->get(ConsulRegisterService::class);
            if (!$registerServer->del()) {
                $logger->error('consul: deregister to consul failed');
            }
        } catch (\Exception $throwable) {
            $logger->error(sprintf('%s[%s] in %s', 'consul: ' . $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
            $logger->error('consul: ' . $throwable->getTraceAsString());
        }
    }
}