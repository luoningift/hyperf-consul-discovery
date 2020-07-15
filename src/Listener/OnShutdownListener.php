<?php
declare(strict_types=1);

namespace HKY\HyperfDiscovery\Listener;

use HKY\HyperfDiscovery\Consul\ConsulRegisterAtomic;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\Framework\Event\OnShutdown;
use Hyperf\Framework\Event\OnWorkerStop;
use Hyperf\Utils\ApplicationContext;

class OnShutdownListener implements ListenerInterface
{

    /**
     * @var ConsulRegisterAtomic
     */
    private $atomic;

    public function __construct()
    {
        $this->atomic = new ConsulRegisterAtomic();
    }

    public function listen(): array
    {
        return [
            OnShutdown::class,
        ];
    }

    public function process(object $event)
    {
        $this->atomic->shutdown();
    }
}
