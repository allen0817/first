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
use yii\helpers\ArrayHelper;


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

    public function actionComoptions(){
        $get = $_SERVER['argv'];
        if(isset($get[2])) $ip = $get[2];
        if(isset($get[3])) $user = $get[3];
        if(isset($get[4])) $pwd = $get[4];
        if(isset($get[5])) $class = $get[5];
        if(isset($get[6])) $options = $get[6];

        $class = ucfirst( strtolower($class) );

        $dir = Common::findClass($class);
        if (!$dir) echo json_encode(['data'=>[]]);
        $ch =  '\app\models\\'.$dir.'\\'.$class;
        $curl = new  $ch($ip,$user,$pwd,$class);
        try{
            $data = $curl->run();
            if(!empty($data)){
                if(isset($data[$options])){
                    $val = [];
                    foreach ($data[$options] as $vo){
                        $val[] = array(
                            '{#NAME}' => $vo['{#NAME}']
                        );
                    }
                    echo json_encode( ['data'=>$val] );exit();
                }
            }
        }catch (Exception $e){

        }
        echo json_encode( ['data'=>[]] );exit();
    }

    public function actionComdatas(){
        $get = $_SERVER['argv'];
        if(isset($get[2])) $key = $get[2];
        if(isset($get[3])) $ip = $get[3];
        if(isset($get[4])) $user = $get[4];
        if(isset($get[5])) $pwd = $get[5];
        if(isset($get[6])) $class = $get[6];
        if(isset($get[7])) $options = $get[7];
        $class = ucfirst( strtolower($class) );

        $dir = Common::findClass($class);
        if (!$dir) echo json_encode(['data'=>[]]);
        $ch =  '\app\models\\'.$dir.'\\'.$class;
        $curl = new  $ch($ip,$user,$pwd,$class);
        $curl->getVal($key);
    }


    public function actionProcess($ip,$user,$pwd,$class){
        //file_put_contents('/usr/local/src/first/web/curl_data/a.txt','allen');
        $get = $_SERVER['argv'];
        if(isset($get[2])) $ip = $get[2];
        if(isset($get[3])) $user = $get[3];
        if(isset($get[4])) $pwd = $get[4];
        if(isset($get[5])) $class = $get[5];

        $dir = Common::findClass($class);
        $ch =  '\app\models\\'.$dir.'\\'.$class;
        $curl = new  $ch($ip,$user,$pwd,$class);
        $curl->childProcess();
    }

    public function actionTest(){
        $get = $_SERVER['argv'];
        if(isset($get[2])) $ip = $get[2];
        if(isset($get[3])) $user = $get[3];
        if(isset($get[4])) $pwd = $get[4];
        if(isset($get[5])) $class = $get[5];
        if(isset($get[6])) $options = $get[6];

        $class = ucfirst( strtolower($class) );

        $dir = Common::findClass($class);
        if (!$dir) echo json_encode(['data'=>[]]);
        $ch =  '\app\models\\'.$dir.'\\'.$class;
        $curl = new  $ch($ip,$user,$pwd,$class);
        try{
            $curl->getData();;

        }catch (Exception $e){
        }
    }



}