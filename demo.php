<?php
require 'queue.php';

$data['email']='ninvfeng@gmail.com';
$data['content']='test queue';
$res=queue()->add('http://localhost/phpqueue/sendmail.php',$data,0);
var_dump($res);