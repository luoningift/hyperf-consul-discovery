<?php
declare(strict_types=1);

namespace HKY\HyperfDiscovery\Listener;

use HKY\HyperfDiscovery\Consul\ConsulRegisterAtomic;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\Utils\ApplicationContext;

class AfterWorkerStartListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            AfterWorkerStart::class,
        ];
    }

    public function process(object $event)
    {
        $container = ApplicationContext::getContainer();
        $container->get(StdoutLoggerInterface::class)->info('consul: atomic incr one success!');
        $container->get(ConsulRegisterAtomic::class)->add();
    }
}
