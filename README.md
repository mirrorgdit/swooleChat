## swooleChat

基于swoole写的websocket简易聊天室


### 前提 

服务器安装swoole

```
 git clone https://github.com/swoole/swoole-src.git
 cd swoole-src
 phpize
 ./configure --enable-openssl -with-php-config=[PATH] #注意[PATH]为你的php地址 开启ssl用
 make && make install
 ```
### 安装
  
composer执行

```
composer require "jianyan74/yii2-websocket"
```

或者在 `composer.json` 加入

```
"jianyan74/yii2-websocket": "^1.0"
```
### 配置

 在 `common/config/main.php` 加入以下配置
  ```
     'redis' => [
         'class' => 'yii\redis\Connection',
         'hostname' => 'localhost',
         'port' => 6379,
         'database' => 0,
     ],
  ```

 
 ### 使用
 
  ```
  # 启动 
  php ./yii websocket/start
  # 停止 
  php ./yii websocket/stop
  # 重启 
  php ./yii websocket/restart
   ```
   
### 测试

```

```
