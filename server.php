<?php
//websocket服务器 IP 端口
define("SERVER_IP",'192.168.2.99');
define("SERVER_PORT",'9501');

//开启Redis服务 IP 端口
define("REDIS_IP",'192.168.2.99');
define("REDIS_PORT",'6379');


//创建websocket服务器对象,监听0.0.0.0:9555端口
$ws = new swoole_websocket_server(SERVER_IP, SERVER_PORT);

//监听websocket连接打开事件
$ws->on('open', function ($ws, $request) {
    //var_dump($request -> fd,$request ->get, $request ->server);
});

//监听websocket消息事件
$ws->on("message", function ($ws, $frame) {
    $message = json_decode($frame->data, true);
    //初始化操作
    if ($message["type"] == 'origin') {

        //开启Redis服务
        $redis = new Redis();
        $redis->connect(REDIS_IP, REDIS_PORT);
        $mem_peoples_str = $redis->get("peoplesList");
        $mem_peoples = json_decode($mem_peoples_str,true);
        $mem_peoples[$frame->fd] = array(
            'id' => $frame->fd,
            'name' => $message['name']
        );
        //暂且内存保存一天
        $redis->set("peoplesList", json_encode($mem_peoples), 1 * 24 * 60 * 60);
        //需要发送的数据
        $data_arr = array(
            'type' => 'system',
            'times' => date("Y-m-d H:i:s", time()),
            'peoples_list' => $mem_peoples
        );
        //向所有用户广播上线消息
        foreach ($ws->connections as $val) {
            if ($val != $frame->fd) {
                $data_arr['message'] = "<b style='color:#F43A1A'>[系统消息]</b>" . $message['name'] . "上线啦";
                //$ws -> push($val,'{"type":"system","message":"'.$message['name'].'上线啦","times":"'.date("Y-m-d H:i:s",time()).'","peoples_list":"'.json_encode($mem_peoples).'"}');
            } else {
                $data_arr['message'] = "<b style='color:#F43A1A'>[系统消息]</b>欢迎你来到聊天室";
                //$ws -> push($frame ->fd , '{"type":"system","message":"<b>[系统消息]</b>欢迎你来到聊天室","times":"'.date("Y-m-d H:i:s",time()).'","peoples_list":"'.json_encode($mem_peoples).'"}');
            }
            $ws->push($val, json_encode($data_arr));
        }
    } elseif ($message["type"] == 'message') {//正常对话操作
        //从memcached中取出peoples

        //Redis中取出peoples
        $redis = new Redis();
        $redis->connect(REDIS_IP, REDIS_PORT);
        $mem_peoples = $redis->get("peoplesList");
        $mem_peoples = json_decode($mem_peoples,true);
        //需要发送的数据
        $data_arr = array(
            'type' => 'message',
            'times' => date("Y-m-d H:i:s", time()),
            'message' => $message['message']
        );

        if ($message['sendto'] == 0) {//表示向所有人发送消息
            foreach ($ws->connections as $val) {
                if ($val == $frame->fd) {
                    $data_arr['name'] = '你说';
                } else {
                    $data_arr['name'] = $mem_peoples[$frame->fd]["name"];
                }
                $ws->push($val, json_encode($data_arr));
            }
        } else {//表示想指定用户发送消息
            //推送给别人
            $data_arr['name'] = $mem_peoples[$frame->fd]["name"];
            $ws->push($message['sendto'], json_encode($data_arr));
            //推送给自己
            $data_arr['name'] = "你说";
            $ws->push($frame->fd, json_encode($data_arr));
        }
    }
});

//监听websocket连接关闭事件

$ws->on("close", function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();
