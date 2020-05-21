<?php

declare(strict_types=1);

namespace HKY\HyperfDiscovery\Listener;

use HKY\HyperfDiscovery\Consul\ConsulRegisterAtomic;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Utils\ApplicationContext;

class BeforeMainServerStartListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
        ];
    }


    public function process(object $event)
    {
        $container = ApplicationContext::getContainer();
        $container->get(StdoutLoggerInterface::class)->info('consul: atomic init success!');
        $container->get(ConsulRegisterAtomic::class);
    }
}
