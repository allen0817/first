<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 11:20
 */




$id = pcntl_fork();

$pid= pcntl_fork();


if ($id == -1) {
    die('could not fork');
}elseif (!$id) {

    sleep(5);
    echo "sleep 5s over\n";


    if ($pid == -1) {
        die('could not fork');
    }elseif (!$pid) {
        //这里是子进程
        sleep(10);
        echo "sleep 10s over\n";
        exit();
    }



}





echo "hello world\n";