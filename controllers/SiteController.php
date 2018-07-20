<?php

namespace app\controllers;


use app\components\Common;
use app\models\IBM\Ibmx3650m4;
use app\models\INSPUR\BaseCurl;
use mongosoft\soapclient\Client;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }





    /**
     * Displays homepage.
     *
     * @return string  /etc/init.d/zabbix_proxy restart
     */

    static $PATH = './curl_data/';

    public function actionIndex()
    {
        $str = '{"clazz":"com.ibm.evo.rpc.RPCResponse","messages":null,"result":[{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"sas_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":129,"statPeakTime":"180701155615","statPeakEpoch":1530431775,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_w_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"fc_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_r_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_w_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"iplink_comp_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_r_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":11,"statPeakTime":"180701155625","statPeakEpoch":1530431785,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"cloud_up_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_w_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_r_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_w_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"compression_cpu_pc","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"cpu_pc","sampleEpoch":1530431996,"statCurrent":1,"statPeak":2,"statPeakTime":"180701155951","statPeakEpoch":1530431991,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"power_w","sampleEpoch":1530431996,"statCurrent":1696,"statPeak":1697,"statPeakTime":"180701155540","statPeakEpoch":1530431740,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"total_cache_pc","sampleEpoch":1530431996,"statCurrent":79,"statPeak":79,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"sas_io","sampleEpoch":1530431996,"statCurrent":298,"statPeak":810,"statPeakTime":"180701155921","statPeakEpoch":1530431961,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"cloud_down_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_r_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":129,"statPeakTime":"180701155615","statPeakEpoch":1530431775,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_w_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_w_ms","sampleEpoch":1530431996,"statCurrent":19,"statPeak":25,"statPeakTime":"180701155856","statPeakEpoch":1530431936,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"iplink_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"iscsi_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_w_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"temp_f","sampleEpoch":1530431996,"statCurrent":73,"statPeak":73,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_r_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_w_io","sampleEpoch":1530431996,"statCurrent":5,"statPeak":9,"statPeakTime":"180701155545","statPeakEpoch":1530431745,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"drive_r_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":520,"statPeakTime":"180701155615","statPeakEpoch":1530431775,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_r_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"cloud_up_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"mdisk_r_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_io","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_w_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"temp_c","sampleEpoch":1530431996,"statCurrent":23,"statPeak":23,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"iscsi_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"fc_io","sampleEpoch":1530431996,"statCurrent":5,"statPeak":5,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"write_cache_pc","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"cloud_down_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"iplink_mb","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null},{"clazz":"com.ibm.svc.devicelayer.api.output.ClusterStatsBean","statName":"vdisk_r_ms","sampleEpoch":1530431996,"statCurrent":0,"statPeak":0,"statPeakTime":"180701155956","statPeakEpoch":1530431996,"statValue":null}]}
';

        $arr = json_decode($str, true);
        echo count($arr['result']);
        echo "<pre>";
        print_r($arr['result']);


    }

    private  function getoffboardNicVenderString($VendorID){

        $bin = decbin($VendorID);

        $bin = '10000000'.strval($bin);
        $dec = bindec($bin);
        $str = '0x'. dechex($dec) ;
echo $str;die;
        switch($str)
        {
            case '0x0A5C' : $actionstr = 'Broadcom';break;
            case '0x0AC8' : $actionstr = 'ASUS'; break;
            case '0x0E11' : $actionstr = 'Compaq'; break;
            case '0x1000' : $actionstr = 'LSI Logic'; break;
            case '0x1002' : $actionstr = 'ATI'; break;
            case '0x1008' : $actionstr = 'Epson'; break;
            case '0x100A':  $actionstr = 'Phoenix'; break;
            case '0x100D':  $actionstr = 'AST'; break;
            case '0x1010':  $actionstr = 'Video Logic'; break;
            case '0x1011' : $actionstr = 'Digital Equipment';  break;
            case '0x1016':  $actionstr = 'Fujitsu'; break;
            case '0x101E':  $actionstr = 'AMI'; break;
            case '0x1025':  $actionstr = 'Acer';break;
            case '0x1028':  $actionstr = 'Dell'; break;
            case '0x102A' : $actionstr = 'LSI Logic';break;
            case '0x102B' : $actionstr = 'Matrox Electronic Systems';break;
            case '0x1077' : $actionstr = 'Qlogic';break;
            case '0x107A' : $actionstr = 'Networth';break;
            case '0x107B' : $actionstr = 'Gateway 2000';break;
            case '0x107D' : $actionstr = 'Leadtek';break;
            case '0x108E' : $actionstr = 'Sun Microsystems';break;
            case '0x108F' : $actionstr = 'Systemsoft';break;
            case '0x1095' : $actionstr = 'Silicon Image';break;
            case '0x1099' : $actionstr = 'Samsung ';break;
            case '0x10A9' : $actionstr = 'Silicon Graphics ';break;
            case '0x10B7' : $actionstr = '3Com';break;
            case '0x10DE' : $actionstr = 'NVIDIA';break;
            case '0x10DF' : $actionstr = 'Emulex';break;
            case '0x10F1' : $actionstr = 'Tyan';break;
            case '0x1106' : $actionstr = 'VIA';break;
            case '0x1109' : $actionstr = 'Adaptec/Cogent Data';break;
            case '0x1116' : $actionstr = 'Data Translation';break;
            case '0x113B' : $actionstr = 'Network Computing';break;
            case '0x1166' : $actionstr = 'Broadcom';break;
            case '0x1177' : $actionstr = 'Silicon Engineering ';break;
            case '0x11C1' : $actionstr = 'LSI';break;
            case '0x11CA' : $actionstr = 'LSI Systems';break;
            case '0x126F' : $actionstr = 'Silicon Motion';break;
            case '0x12B9' : $actionstr = '3Com';break;
            case '0x13FF' : $actionstr = 'Silicon Spice';break;
            case '0x1462' : $actionstr = 'Micro-Star International';break;
            case '0x14E4' : $actionstr = 'Broadcom';break;
            case '0x1543 ': $actionstr = 'Silicon Laboratories';break;
            case '0x15B3' : $actionstr = 'Mellanox';break;
            case '0x15D9' : $actionstr = 'Super Micro';break;
            case '0x163C' : $actionstr = 'intel';break;
            case '0x17D3' : $actionstr = 'Areca';break;
            case '0x19A2' : $actionstr = 'Emulex';break;
            case '0x1CB8' : $actionstr = 'Sugon';break;
            case '0x6409' : $actionstr = 'Logitec';break;
            case '0x8086' : $actionstr = 'Intel';break;
            case '0x8087' : $actionstr = 'Intel';break;
            case '0x8888' : $actionstr = 'Silicon Magic';break;
            case '0x9004' : $actionstr = 'Adaptec';break;
            case '0x9005' : $actionstr = 'Adaptec';break;
            case '0xA200' : $actionstr = 'NEC';break;
            case '0xA727' : $actionstr = '3Com';break;
            default : $actionstr = 'Undefined Device';break;
        }
        return $actionstr;
    }










}