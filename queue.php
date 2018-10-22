<?php
require 'mongodb.php';

//封装queue快捷操作
function queue(){
    static $_queue;
    if(!$_queue){
        $_queue=new queue();
    }
    return $_queue;
}

//消息队列
class queue{

	public $table='queue';
	public $log_table='queue_log';
    
    /**
     * 添加队列
     * @param $url 消费者接口url
     * @param array $data 参数
     * @param int $exec_at 延迟执行时间/秒
     * @return mixed
     */
    public function add($url,$data=[],$exec_at=0){
		$queue['url']=$url;
		$queue['data']=$data;
		$queue['exec_at']=time()+$exec_at;
		$queue['created_at']=date('Y-m-d H:i:s');
 		return mongodb($this->table)->insert($queue);
	}

	//获取队列
	public function get(){
		$queue=mongodb($this->table)->where(['exec_at'=>['$lte'=>time()]])->findAndRemove();
		if($queue){
			$data['_id']=$queue->_id;
			$data['url']=$queue->url;
			$data['data']=$queue->data;
			$data['exec_at']=$queue->exec_at;
			$data['created_at']=date('Y-m-d H:i:s');
			return $data;
		}
	}

    //执行队列
    public function exec($data){
        $opts[CURLOPT_URL] = $data['url'];
        $opts[CURLOPT_POST] = 1;
        $opts[CURLOPT_POSTFIELDS] = $data['data'];
        $opts[CURLOPT_TIMEOUT] = 2;
        $opts[CURLOPT_RETURNTRANSFER] = 1;

        $ch=curl_init();
        curl_setopt_array($ch, $opts);
        $res=curl_exec($ch);
        curl_close($ch);
        
        //记录日志
        $data['res']=$res;
        $data['executed_at']=date('Y-m-d H:i:s');
		mongodb($this->log_table)->insert($data);
        return $res;
    }
}