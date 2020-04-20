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

# 开放接口

## 1. Api / Qiniu / streamCreate  POST 创建流
    
### 必须参数
* streamKey string 流名称  名称必须满足 4-200个数字或字母

### 返回

``` 
    // 已经存在
    {
        "code":"614",
        "result":null,
        "msg":"stream already exists"
    }

    // 创建成功
    {
        "code":"200",
        "result":null,
        "msg":"stream create success"
    }

    // 条件出错
    {
        "code":"400",
        "result":null,
        "msg":"名称必须满足 4-200个数字或字母"
    }

```


## 2. Api / Qiniu / streamInfoGet  POST 获取流
    
### 必须参数
* streamKey string 流名称  名称必须满足 4-200个数字或字母

### 返回

``` 
    // 没有找到
    {
        "code":"612",
        "result":null,
        "msg":"stream not found"
    }

    // 成功
    {
        "code":"200",
        "result":{
            "hub":"testbg",
            "key":"test",
            "disabledTill":0,
            "converts":[]
            },
        "msg":"success"
    }

    // 条件出错
    {
        "code":"400",
        "result":null,
        "msg":"名称必须满足 4-200个数字或字母"
    }

```

## 3. Api / Qiniu / streamAllGet  POST 获取所有流信息
    
### 可选参数
* @param request prefix 流名的前缀.
* @param request limit  限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
* @param request marker 是上一次遍历得到的流标.

### 返回
* RETURN
* @return keys    流名的数组.
* @return omarker 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.

``` 
    // 没有找到
    {
        "code":"612",
        "result":null,
        "msg":"stream not found"
    }

    // 成功
    {
        "code":"200",
        "result":
        {
            "keys":["asd","asd2","asd3","php-sdk-test1587350841","php-sdk-test1587350869","test","test2","test3","test4"],"omarker":""
        },
        "msg":"success"
        }
```


## 4. Api / Qiniu / streamLiveAllGet  POST 列出正在直播的流
    
### 可选参数
* @param request prefix 流名的前缀.
* @param request limit  限定了一次最多可以返回的流个数, 实际返回的流个数可能小于这个 limit 值.
* @param request marker 是上一次遍历得到的流标.

### 返回
* RETURN
* @return keys    流名的数组.
* @return omarker 记录了此次遍历到的游标, 在下次请求时应该带上, 如果 omarker 为 "" 表示已经遍历完所有流.

``` 
    // 没有找到
    {
        "code":"612",
        "result":null,
        "msg":"stream not found"
    }

    // 成功
    {
        "code":"200",
        "result":
        {
            "keys":["asd","asd2","asd3","php-sdk-test1587350841","php-sdk-test1587350869","test","test2","test3","test4"],"omarker":""
        },
        "msg":"success"
        }
```


## 5. Api / Qiniu / streamLiveStatusGet  POST 获取直播状态
    
### 必须参数
* streamKey string 流名称  名称必须满足 4-200个数字或字母

### 返回
``` 
    200 {
    "startAt": <StartAt>,
        "clientIP": "<ClientIP>",
        "bps": <Bps>, // 当前码率
        "fps": {
            "audio": <Audio>,
            "video": <Video>,
            "data": <Data>
        }
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }


```

## 6. Api / Qiniu / streamLiveStatusBatchGet  POST 批量获取直播状态
    
### 必须参数
* streamKey jsonArray 流名称  名称必须满足 4-200个数字或字母 "["test","sfasf"]"

### 返回
``` 
    200 {
        "items": [
            {
                "key": "<StreamTitle>",
                "startAt": <StartAt>,
                "clientIP": "<ClientIP>",
                "bps": <Bps>, // 当前码率
                "fps": {
                    "audio": <Audio>,
                    "video": <Video>,
                    "data": <Data>
            },
            ...
        ]
    }

    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }


```


## 7. Api / Qiniu / streamDisabled  POST 禁用流
    
### 必须参数
* streamKey string 流名称  名称必须满足 4-200个数字或字母


### 可选参数
* disabledTill Unix 时间戳, 在这之前流均不可用。不填则默认为 -1（永久禁播）.

### 返回
``` 
    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }


```


## 8. Api / Qiniu / streamDisabled  POST 启用流
    
### 必须参数
* streamKey string 流名称  名称必须满足 4-200个数字或字母


### 可选参数
* disabledTill Unix 时间戳, 在这之前流均不可用。不填则默认为 -1（永久禁播）.

### 返回
``` 
    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }


```


## 9. Api / Qiniu / liveSave  POST 保存直播
    
### 必须参数
* @param request $streamKey 流名称.
* @param request $start:    Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
* @param request $end       Unix 时间戳, 结束时间, 0 值表示当前时间.

### 返回
``` 
    {
        "code":"200",
        "result":{
            "end":1587197168,
            "fname":"recordings/z1.testbg.test/0_1587373142.m3u8",
            "start":1586936811
        },
        "msg":"success"
    }

    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }


```


## 10. Api / Qiniu / liveSave  POST 保存直播
    
### 必须参数
* @param request $streamKey 流名称.
* @param request $start:    Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
* @param request $end       Unix 时间戳, 结束时间, 0 值表示当前时间.

### 返回
``` 
    {
        "code":"200",
        "result":{
            "end":1587197168,
            "fname":"recordings/z1.testbg.test/0_1587373142.m3u8",
            "start":1586936811
        },
        "msg":"success"
    }

    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }


```

## 11. Api / Qiniu / liveSaveAs  POST 灵活度更高的保存直播回放
    
### 必须参数
* @param string $streamKey 流名称.

### 可选参数

* @param jsonArray $option 选项.   "["format" => "mp4"]"


#### $option 选项

* @param fname: 保存的文件名, 不指定会随机生成.
* @param start: Unix 时间戳, 起始时间, 0 值表示不指定, 则不限制起始时间.
* @param end: Unix 时间戳, 结束时间, 0 值表示当前时间.
* @param format: 保存的文件格式, 默认为m3u8.
* @param pipeline: dora 的私有队列, 不指定则用默认队列.
* @param notify: 保存成功后的回调地址.
* @param expireDays: 对应ts文件的过期时间.
*   -1 表示不修改ts文件的expire属性.
*   0  表示修改ts文件生命周期为永久保存.
*   >0 表示修改ts文件的的生命周期为ExpireDays.
* 

### 返回
* @return fname: 保存到bucket里的文件名.
* @return persistentID: 异步模式时，持久化异步处理任务ID，通常用不到该字段.


``` 
    {
        "code":"200",
        "result":{
            "end":1587197168,
            "fname":"recordings/z1.testbg.test/0_1587373142.m3u8",
            "start":1586936811
        },
        "msg":"success"
    }

    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 11. Api / Qiniu / liveSaveAs  POST 查询推流历史
    
### 必须参数
* @param string $streamKey 流名称.

### 可选参数

* @param request $start     Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.
* @param request $end       Unix 时间戳, 限定了查询的时间范围, 0 值表示不限定, 系统会返回所有时间的直播历史.


### 返回
* RETURN
* @return items: 数组. 每个item包含一次推流的开始及结束时间.
*   @start: Unix 时间戳, 直播开始时间.
*   @end: Unix 时间戳, 直播结束时间.


``` 
    
    {
        "code":"200",
        "result":
        {
        "items":[
            {"start":1587197088,"end":1587197168},
            {"start":1586937477,"end":1586937530},
            ....
            ]
        }
        "msg":"success"
    }

    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 12. Api / Qiniu / liveSnapShot  POST 保存直播截图
    
### 必须参数
* @param string $streamKey 流名称.

### 可选参数

* @param jsonArray $option 选项.   "["format" => "mp4"]"

* $option 选项 
* @param fname: 保存的文件名, 不指定会随机生成.
* @param time: Unix 时间戳, 保存的时间点, 默认为当前时间.
* @param format: 保存的文件格式, 默认为jpg.
     
### 返回
* RETURN
    * @param fname: 保存到bucket里的文件名.


``` 
    
    {
        "code":"200",
        "result":{"fname":"test-8604592773339657603.jpg"},
        "msg":"success"
    }

    200 {
        
    }
    404 {
        "error": "stream not found"
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 13. Api / Qiniu / liveUpdateConverts  POST 更改流的实时转码规格
    
### 必须参数
* @param string $streamKey 流名称.

### 可选参数

* @param jsonArray $option 选项.   array("480p", "720p")

* $option 选项 
*  数组，转码配置，如果提交的 ProfileName 为空数组，那么之前的转码配置将被取消
     
### 返回
* RETURN
    * 

``` 
    
    {
        "code":"200",
        "result":null,
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 13. Api / Qiniu / rtmpPushUrlCreate  POST 生成 RTMP 推流地址
    
### 必须参数
* @param string $streamKey 流名称.
* @param string $expire    表示 URL 在多久之后失效 (秒).

### 返回
* RETURN
    * 

``` 
    
    
    {
        "code":"200",
        "result":"rtmp://publish-rtmp.520bg.com/testbg/test?e=1587394353&token=Wd9vQSPN-ogPEwuxJfdPLurfIHIyaB2mQ79EKUqo:y3awIJBToRCsGs6njSPyWyv-Pjk=",   
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 14. Api / Qiniu / hlsPlayUrlGet   HLS 直播放址
    
### 必须参数
* @param string $streamKey 流名称.

### 返回
* RETURN
    * 

``` 
    
    
    {
        "code":"200",
        "result":"http://live-hls.test.com/PiliSDKTest/streamkey.m3u8",   
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```

Î

## 14. Api / Qiniu / rtmpPlayUrlGet   RTMP 直播放址
    
### 必须参数
* @param string $streamKey 流名称.

### 返回
* RETURN
    * 

``` 
    
    
    {
        "code":"200",
        "result":"rtmp://live-rtmp.test.com/PiliSDKTest/streamkey",   
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```

Î
## 15. Api / Qiniu / hdlPlayUrlGet   HDL 直播放址
    
### 必须参数
* @param string $streamKey 流名称.

### 返回
* RETURN
    * 

``` 
    
    
    {
        "code":"200",
        "result":"http://live-hdl.test.com/PiliSDKTest/streamkey.flv",   
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```


## 15. Api / Qiniu / snapShotPlayUrlGet   截图直播地址
    
### 必须参数
* @param string $streamKey 流名称.

### 返回
* RETURN
    * 

``` 
    
    
    {
        "code":"200",
        "result":"http://live-snapshot.test.com/PiliSDKTest/streamkey.jp",   
        "msg":"success"
    }

    200 {
        
    }
    400 {
    "error": "invalid stream key" // 只能修改原始流，包含@的流不允许
    }
    404 {
        "error": "stream not found"
    }
    400 {
    "error": "invalid args" // 转码配置不存在
    }
    619 {
        "error": "no live" // 流不在直播
    }

    612 {
    "error": "stream not found"
    }

```

Î