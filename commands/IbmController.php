<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:59
 */

namespace app\commands;


use app\components\Common;
use app\models\IBM\Ibmstoragev7000_back;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\ArrayHelper;


class IbmController extends Controller
{
    /**
     * 这个是不规范的做法，这里做补充V7000 采集情况
     * php7  yii ibm/index superuser Cloud@123.com 10.240.240.127 BJ-2-YZ06-F06-DA-IBMV7K-01
     */
    public function actionIndex(){
        $get = $_SERVER['argv'];

        $user = $get[2];
        $pwd = $get[3];
        $ip = $get[4];
        $hostname = $get[5];

        $ibm = new Ibmstoragev7000_back($ip,$user,$pwd,$hostname,$port=80);

        $ibm->send();
    }

    public function actionTest()
    {
        $hostname = 'BJ-2-YZ06-F06-DA-IBMV7K-01';
        $key = 'timezone';
        $value = 'hello allen';
        $command = "/usr/local/zabbix_proxy/bin/zabbix_sender  -z  $hostname  -p 10051 -s '172.16.86.105' -k $key -o  $value ";
        exec($command);
    }


}