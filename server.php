<?php
require 'queue.php';
echo "Queue server start success \r\n";
//循环读取队列
while(1){
    while($data=queue()->get()){
        queue()->exec($data);
        echo date('Y-m-d H:i:s')." Queue ".$data['_id']." exec success! \r\n";
    }
    sleep(1);
}