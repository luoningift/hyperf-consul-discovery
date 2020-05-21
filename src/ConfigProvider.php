<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HKY\HyperfDiscovery;


use HKY\HyperfDiscovery\Listener\AfterWorkerStartListener;
use HKY\HyperfDiscovery\Listener\BeforeMainServerStartListener;
use HKY\HyperfDiscovery\Listener\MainWorkerStartListener;
use HKY\HyperfDiscovery\Listener\ManagerStopListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                AfterWorkerStartListener::class,
                BeforeMainServerStartListener::class,
                MainWorkerStartListener::class,
                ManagerStopListener::class,
            ],
            'dependencies' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
                'ignore_annotations' => [
                    'mixin',
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of discovery config.',
                    'source' => __DIR__ . '/publish/discovery.php',
                    'destination' => BASE_PATH . '/config/autoload/discovery.php',
                ],
            ],
        ];
    }
}
