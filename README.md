# easyswoole api服务器

## 使用环境
* easyswoole
* swooleTable
* redisQueue
* mysql
* rpc


## Composer 安装
按下面的步骤进行手动安装
(建议使用)

``` 
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install



或者(可能出错)
composer install
php vendor/bin/easyswoole install

```



## 配置
```
    dev 开发环境
    cp dev.eample.php dev.php

    online 线上环境
    product.php
```

## 启动
```
    php easyswoole start [dev.php | produce.php] 默认dev.php

```
