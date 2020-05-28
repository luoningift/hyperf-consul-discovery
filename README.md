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
     'url' => 'http://127.0.0.1:8500;http://192.168.100.141:8500',
     //是否关闭服务发现，0关闭 1开启
     'enable' => 0,
     //读取哪个网卡信息 ifconfig命令查看 比如：eth0 eth1 无特殊需要 留空就好
     'net_card' => '',
],
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
项目的config/config.php 中的app_name项目名称一定要保证在整个项目组唯一 服务发现注册的名称为app_name
本地测试，consul官网下载软件，执行
```
### 版本改动:
```$xslt
v1.0.3   服务注册和注销逻辑修改
v1.0.2   增加服务注册注意事项
v1.0.1   增加 hyperf-discovery 注册发现
v1.0.0   增加 hyperf-discovery 注册发现
```
