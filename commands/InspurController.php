<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;


use app\models\INSPUR\BaseCurl;
use yii\base\Exception;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InspurController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }


    //  /root/LWSetup/packages/php-5.6.30/sapi/cli/php  /usr/local/src/first/yii hello/options  ip username pwd
    public function actionOptions(){
        $get = $_SERVER['argv'];

        //print_r($get);die;


        if(isset($get[2])) $ip = $get[2];
        if(isset($get[3])) $user = $get[3];
        if(isset($get[4])) $pwd = $get[4];


//        $ip = '10.240.240.79';
//        $user = 'admin';
//        $pwd = 'admin';

        $curl = new BaseCurl($ip,$user,$pwd);
        $data = $curl->run();
        echo json_encode(['data'=>$data['options']]);

    }

    //  /root/LWSetup/packages/php-5.6.30/sapi/cli/php  /usr/local/src/first/yii hello/options  key ip username pwd

    //  key   key.other 或 key ; 如：{#SPName},或{#MemoryInfo}.Type
    public function actionData(){

        $get = $_SERVER['argv'];

        if(isset($get[2])) $key = $get[2];
        if(isset($get[3])) $ip = $get[3];
        if(isset($get[4])) $user = $get[4];
        if(isset($get[5])) $pwd = $get[5];
//        $ip = '10.240.240.79';
//        $user = 'admin';
//        $pwd = 'admin';
        $curl = new BaseCurl($ip,$user,$pwd);
        $datas = $curl->run();
        $data = $datas['data'];

        //print_r($datas);exit;

        $r = preg_replace('/{#|}/','',$key);

        $rs = explode(".",$r);

        try{
            if(count($rs)>1){
                foreach ($data as $vo){
                    if(is_array($vo)){
                        foreach ($vo as $k=>$v){
                            if(strtoupper($v)==$rs[0]){
                                foreach ($vo as  $j=>$l){
                                    if( strtoupper($j) == $rs[1]){
                                        echo $l;exit();
                                    }
                                }
                            }
                        }
                    }
                }

            }
            foreach ( $data as $k=>$vo){
                if(is_array($vo)){
                    foreach ($vo as $k1=>$v){
                        if(strtoupper($k1)==$rs[0]){
                            echo $v;exit();
                        }
                    }
                }
                if(!is_array($vo)){
                    if( strtoupper($k) == $rs[0] ) {
                        echo $vo;exit();
                    }
                }
            }

        }catch (Exception $e){
            echo "NULL";
        }

        echo "NULL";

    }







}