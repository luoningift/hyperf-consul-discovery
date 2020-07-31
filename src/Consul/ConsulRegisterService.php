<?php

namespace HKY\HyperfDiscovery\Consul;

use Hyperf\Consul\Agent;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Hyperf\Guzzle\ClientFactory;

/**
 * register self to consul
 * Class ConsulRegisterService
 * @package HKY\HyperfDiscovery\Consul
 */
class ConsulRegisterService
{

    private $container;

    private $consulConfig = [
        'url' => 'http://127.0.0.1:8500',
        'enable' => 1,
        'net_card' => '',
    ];

    private $registerIp = '127.0.0.1';

    private $registerPort = 0;

    private $consulId = '';

    private $consulName = '';

    private $consulUrl = '';

    private $logger;

    public function __construct(ContainerInterface $container, LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('consul-discovery');
        $this->container = $container;
        $config = $container->get(ConfigInterface::class);
        $poolName = 'consul';
        $consulKey = 'discovery.' . $poolName;
        if (!$config->has($consulKey)) {
            throw new \InvalidArgumentException('config[' . $consulKey . '] is not exist!');
        }
        $this->consulConfig = array_replace($this->consulConfig, $config->get($consulKey));
        //consul服务地址
        $this->consulUrl = explode(';', $this->consulConfig['url']);
        //获取项目名称
        $this->consulName = strval($config->get('app_name'));
        if (!$this->consulName) {
            throw new \InvalidArgumentException('config[config.app_name] is null');
        }
        //注册端口和ip
        $this->registerPort = intval($config->get('server.servers')[0]['port']);
        $clientIp = swoole_get_local_ip();
        $netCard = $this->consulConfig['net_card'];
        $this->registerIp = $netCard && isset($clientIp[$netCard]) ? $clientIp[$netCard] : array_pop($clientIp);
        $this->consulId = $this->consulName . '-' . $this->registerIp . ':' . $this->registerPort;
    }

    /**
     */
    public function add()
    {

        if (!$this->consulConfig['enable']) {
            return;
        }

        $registerService = [
            'ID' => $this->consulId,
            'Name' => $this->consulName,
            'Tags' => [
               $this->consulName
            ],
            'Address' => $this->registerIp,
            'Port' => $this->registerPort,
            'Meta' => [
                'version' => '1.0'
            ],
            'EnableTagOverride' => false,
            'Weights' => [
                'Passing' => 10,
                'Warning' => 1
            ],
            'Checks' => [
                [
                    'name' => $this->consulId . '-check',
                    'http' => 'http://' . $this->registerIp . ':' . $this->registerPort . '/health/check',
                    'interval' => "5s",
                    'timeout' => "2s",
                ]
            ]
        ];
        foreach ($this->consulUrl as $consulUrl) {
            try {
                $agent = new Agent(function () use ($consulUrl) {
                    return $this->container->get(ClientFactory::class)->create([
                        'base_uri' => $consulUrl,
                    ]);
                });
                $response = $agent->registerService($registerService);
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    $this->logger->info(date("Y-m-d H:i:s") . " consul: register to " . $consulUrl . ' succcess !');
                } else {
                    $this->logger->error(date("Y-m-d H:i:s") . " consul: register to " . $consulUrl . ' failed !', ['status' => $statusCode, 'body' => $response->getBody()]);
                }
            } catch (\Exception $throwable) {
                $this->logger->error('register failed', [
                    'url' => $consulUrl,
                    'message' => $throwable->getMessage(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => $throwable->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * kill -15 服务停止事件
     */
    public function del()
    {

        if (!$this->consulConfig['enable']) {
            return;
        }
        foreach ($this->consulUrl as $consulUrl) {
            try {
                $agent = new Agent(function () use ($consulUrl) {
                    return $this->container->get(ClientFactory::class)->create([
                        'base_uri' => $consulUrl,
                    ]);
                });
                $response = $agent->deregisterService(urlencode($this->consulId));
                $statusCode = $response->getStatusCode();
                if ($statusCode == 200) {
                    $this->logger->info(date("Y-m-d H:i:s") . " consul: deregister to " . $consulUrl . ' succcess !');
                } else {
                    $this->logger->error(date("Y-m-d H:i:s") . " consul: deregister to " . $consulUrl . ' failed !', ['status' => $statusCode, 'body' => $response->getBody()]);
                }
            } catch (\Throwable $throwable) {
                $this->logger->error('deregister failed', [
                    'url' => $consulUrl,
                    'message' => $throwable->getMessage(),
                    'file' => $throwable->getFile(),
                    'line' => $throwable->getLine(),
                    'trace' => $throwable->getTraceAsString(),
                ]);
            }
        }
    }
}
