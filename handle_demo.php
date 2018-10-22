<?php
$res=file_put_contents('mail'.date('His').rand(10,99).'.txt',$_POST);
var_dump($res);
echo 'success';