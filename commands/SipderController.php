<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:59
 */

namespace app\commands;


use app\components\Common;
use yii\base\Exception;
use yii\console\Controller;


class SipderController extends Controller
{
//UserParameter=sipderoptions[*],php_cli  /usr/local/src/first/yii sipder/options  $1 $2 $3 $4
//UserParameter=sipder[*],php_cli  /usr/local/src/first/yii sipder/data $1  $2 $3 $4 $5

//php_cli  /usr/local/src/first/yii sipder/options  172.16.253.181 USERID PASSW0RD ibmx3850
//php_cli  /usr/local/src/first/yii sipder/data CHANNELB.STATE 172.16.253.181 USERID PASSW0RD  ibmx3850


    /**
     * 所有监控项
     * php_cli  /usr/local/src/first/yii sipder/options  10.240.240.79  admin  admin Inspurnf8480m4
     *                                                  ip          user    pwd     型号
     */
    public function actionOptions(){
        $get = $_SERVER['argv'];

        if(isset($get[2])) $ip = $get[2];
        if(isset($get[3])) $user = $get[3];
        if(isset($get[4])) $pwd = $get[4];
        if(isset($get[5])) $class = $get[5];

        $class = ucfirst( strtolower($class) );

        $dir = Common::findClass($class);
        if (!$dir) echo json_encode(['data'=>[]]);
        $ch =  '\app\models\\'.$dir.'\\'.$class;

        $curl = new  $ch($ip,$user,$pwd);

        try{
            $data = $curl->run();
            echo json_encode(['data'=>$data['options']]);
        }catch (Exception $e){
            echo json_encode(['data'=>[]]);
        }
    }


    public function actionData(){
        $get = $_SERVER['argv'];
        if(isset($get[2])) $key = $get[2];
        if(isset($get[3])) $ip = $get[3];
        if(isset($get[4])) $user = $get[4];
        if(isset($get[5])) $pwd = $get[5];
        if(isset($get[6])) $class = $get[6];
        $class = ucfirst( strtolower($class) );

        $dir = Common::findClass($class);
        if (!$dir) echo json_encode(['data'=>[]]);
        $ch =  '\app\models\\'.$dir.'\\'.$class;
        $curl = new  $ch($ip,$user,$pwd);
        $curl->getVal($key);

    }






}