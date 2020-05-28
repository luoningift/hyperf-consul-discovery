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
            AfterWorkerStart::class,
        ];
    }

    public function process(object $event)
    {
        $this->atomic->register();
    }
}
