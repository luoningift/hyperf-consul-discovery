<?php
declare(strict_types=1);
namespace HKY\HyperfDiscovery\Listener;

use HKY\HyperfDiscovery\Consul\ConsulRegisterAtomic;
use HKY\HyperfDiscovery\Consul\ConsulRegisterService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\Utils\ApplicationContext;

class MainWorkerStartListener implements ListenerInterface
{
    
    public function listen(): array
    {
        return [
            MainWorkerStart::class,
        ];
    }

    public function process(object $event)
    {
        go(function() {
            $container = ApplicationContext::getContainer();
            $logger = $container->get(StdoutLoggerInterface::class);
            try {
                while (!$container->get(ConsulRegisterAtomic::class)->isReady()) {
                    $logger->info('consul: wait worker to ready !');
                    sleep(1);
                }
                $logger->info('consul: begin to register !');
                $consul = $container->get(ConsulRegisterService::class);
                if (!$consul->add()) {
                    $logger->error('consul: register to consul failed');
                }
            } catch (\Exception $throwable) {
                $logger->error(sprintf('%s[%s] in %s', 'consul: ' . $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
                $logger->error('consul: ' . $throwable->getTraceAsString());
            }    
        });
    }
}