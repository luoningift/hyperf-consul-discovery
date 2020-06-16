#### Hyperf-discovery

##### 1.安装
在项目中 `composer.json` 的 `repositories` 项中增加
``` 
{
    ....
    "repositories":{
        "hky/hyperf-discovery":{
            "type":"vcs",
            "url":"git@192.168.100.11:base/hky-packages-hyperf-discovery.git"
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
##### 2.配置文件说明config/autoload/discovery.php
```php
<?php
'consul' => [
     //服务发现地址，多个以英文;隔开
     'url' => env('CONSUL_URL', 'http://127.0.0.1:8500'),
     //是否关闭服务发现，0关闭 1开启 
     'enable' => (int) env('CONSUL_ENABLE', 1),
     //读取哪个网卡信息 ifconfig命令查看 比如：eth0 eth1 无特殊需要 留空就好
     'net_card' => '',
],
```
```$xslt
.env文件配置样式
CONSUL_ENABLE=0
CONSUL_URL=http://127.0.0.1:8500
CONSUL_NET_CARD=
```
##### 3.实现健康检查接口
```php
<?php
// config/routes.php Router::addRoute(['GET', 'POST', 'HEAD'], '/health/check', 'App\Controller\IndexController@health');
public function health() {
   return "success";
}
```
##### 4.服务注册注意事项
```$xslt
1、项目的config/config.php 中的app_name项目名称一定要保证在整个项目组唯一 服务发现注册的名称为app_name
2、本地测试，consul官网下载软件，执行
3、注册服务未成功，检查三个方面(consul ip和端口是否可访问， 注册的ip地址是否外网可访问， 配置文件里面的enable的值是否为1)
```
##### 5.服务发现本地测试
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
v1.0.3   服务注册和注销逻辑修改, 修改使用说明
v1.0.2   增加服务注册注意事项
v1.0.1   增加 hyperf-discovery 注册发现
v1.0.0   增加 hyperf-discovery 注册发现
```
