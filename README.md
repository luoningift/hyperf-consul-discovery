#### Hyperf-discovery

##### 1.安装
在项目中 `composer.json` 的 `repositories` 项中增加
``` 
{
    ....
    "repositories":{
        "hky/hyperf-discovery":{
            "type":"vcs",
            "url":"http://icode.kaikeba.com/base/hky-packages-hyperf-discovery.git"
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
如果遇到错误信息为:
`Your configuration does not allow connections to http://icode.kaikeba.com/base/hky-packages-hyperf-http-client.git. See https://getcomposer.org/doc/06-config.md#secure-http for details` 
执行以下命令
```bash
$ composer config secure-http false
```
##### 2.在config/autoload/server.php增加onshutdown配置项
```php
 'callbacks' => [
      SwooleEvent::ON_SHUTDOWN => [Hyperf\Framework\Bootstrap\ShutdownCallback::class, 'onShutdown']
 ],
```
##### 3.配置文件说明config/autoload/discovery.php
```php
<?php
'consul' => [
     //服务发现地址，多个以英文;隔开 
     //多个地址是指consul一个集群中的多个ip 不要把测试的和正式的服务发现地址都写到里面，用env文件区分不同的环境注册发现地址
     //说明：env环境 (local dev test pre online) 都有各自的consul集群，需要填写各自consul集群ip地址
     'url' => env('DISCOVERY_CONSUL_URL', 'http://127.0.0.1:8500'),
     //是否关闭服务发现，0关闭 1开启 
     'enable' => (int) env('DISCOVERY_CONSUL_ENABLE', 0),
     //读取哪个网卡信息 ifconfig命令查看 比如：eth0 eth1 无特殊需要 留空就好
     'net_card' => env('DISCOVERY_CONSUL_NET_CARD', ''),
],
```
```$xslt
.env文件配置样式
CDISCOVERY_CONSUL_ENABLE=0
CDISCOVERY_CONSUL_URL=http://127.0.0.1:8500
CDISCOVERY_CONSUL_NET_CARD=
```
##### 4.实现健康检查接口
```php
<?php
// config/routes.php Router::addRoute(['GET', 'POST', 'HEAD'], '/health/check', 'App\Controller\IndexController@health');
public function health() {
   return "success";
}
```
##### 5.服务注册注意事项
```$xslt
1、项目的config/config.php 中的app_name项目名称一定要保证在整个项目组唯一 服务发现注册的名称为app_name
2、本地代码开发不进行consul注册，在env.dev和env.local中配置CONSUL_ENABLE=0, 在env.test, env.pre, env.online中配置CONSUL_ENABLE=1
3、注册服务未成功，检查四个方面(consul ip和端口是否可访问， 注册的ip地址是否外网可访问， 配置文件里面的enable的值是否为1， 是否在项目中增加了健康检查的接口(步骤3))
```
##### 6.服务发现本地测试
```@xslt
官网下载https://www.consul.io/downloads 软件后执行
启动consul: ./consul agent -dev -client 0.0.0.0 -ui
启动hyperf项目
打开consul http界面 根据你的配置 8500端口 比如http://127.0.0.1:8500
出现你第四步配置的app_name 表示注册成功
点进去出现两个绿点表示 注册成功 健康检测成功
测试环境配置kong网关 请找运维总管
```
### 版本改动:
```$xslt
v1.0.5   注册服务发现修改注销监听事件
v1.0.4   注册服务发现说明修改
v1.0.3   服务注册和注销逻辑修改, 修改使用说明
v1.0.2   增加服务注册注意事项
v1.0.1   增加 hyperf-discovery 注册发现
v1.0.0   增加 hyperf-discovery 注册发现
```
