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
  
```
git clone https://github.com/mirrorgdit/swooleChat.git
```
### 配置

 在 `server.php` 修改具体的配置
  ```
//websocket服务器 IP 端口
define("SERVER_IP",'192.168.2.99');
define("SERVER_PORT",'9501');

//开启Redis服务 IP 端口
define("REDIS_IP",'192.168.2.99');
define("REDIS_PORT",'6379');

  ```

 
 ### 使用
 
  ```
  # 启动 
  php server.php
   ```
   
### 测试

```
直接访问测试页面访问
http://path/index.html
```
