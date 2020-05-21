#### Hyperf-http-client

##### 1.安装
在项目中 `composer.json` 的 `repositories` 项中增加
``` 
{
    ....
    "repositories":{
        "hky/hyperf-discovery":{
            "type":"vcs",
            "url":"git@192.168.100.11:base/hky-packages-hyperf-discovery.gi"
        }
        ....
    }
}
```
修改完成后执行 
```bash
$ composer require hky/hyperf-discovery
$ php bin/hyperf.php vendor:publish hky/hyperf-discovery
```
##### 2.修改配置文件config/autoload/server.php
```php
<?php
// callbacks 中增加
SwooleEvent::ON_MANAGER_STOP => [Hyperf\Framework\Bootstrap\ManagerStopCallback::class, 'onManagerStop'],
```
##### 3.配置文件说明config/autoload/discovery.php
```php
<?php
'consul' => [
     //服务发现地址，多个以英文;隔开
     'url' => 'http://127.0.0.1:8500;http://192.168.100.141:8500',
     //是否关闭服务发现，0关闭 1开启
     'enable' => 0,
     //读取哪个网卡信息 ifconfig命令查看 比如：eth0 eth1 无特殊需要 留空就好
     'net_card' => '',
],
```
### 版本改动:
v1.0.0   增加 http-discovery 注册发现
