<?php

namespace app\controllers;


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
     * @return string
     */

    static $PATH = './curl_data/';

    public function actionIndex()
    {


        $str = "WEBVAR_JSONVAR_FPGA_VERSION = 
 { 
 WEBVAR_STRUCTNAME_FPGA_VERSION : 
 [ 
 { 'FPGAVersion1' : 2,'FPGAVersion2' : 0,'FPGAVersion3' : 7 },  {} ],  
 HAPI_STATUS:0 }; 
//Dynamic data end
//Dynamic Data Begin
 WEBVAR_JSONVAR_BIOS_VERSION = 
 { 
 WEBVAR_STRUCTNAME_BIOS_VERSION : 
 [ 
 { 'BiosVersion' : '4.0.8','BiosBuildTime' : '04/25/2017' },  {} ],  
 HAPI_STATUS:0 }; 
//Dynamic data end
//Dynamic Data Begin
 WEBVAR_JSONVAR_ME_VERSION = 
 { 
 WEBVAR_STRUCTNAME_ME_VERSION : 
 [ 
 { 'MEVersion' : '2.4.0.043' },  {} ],  
 HAPI_STATUS:0 }; 
//Dynamic data end
//Dynamic Data Begin
 WEBVAR_JSONVAR_FANPSOC_VERSION = 
 { 
 WEBVAR_STRUCTNAME_FANPSOC_VERSION : 
 [ 
 { 'fanPsocVer0' : 1,'fanPsocVer1' : 2,'fanPsocVer2' : 0,'hddPsocVer0' : 1,'hddPsocVer1' : 7,'hddPsocVer2' : 0 },  {} ],  
 HAPI_STATUS:0 }; 
//Dynamic data end
//Dynamic Data Begin
 WEBVAR_JSONVAR_MRB_VERSION = 
 { 
 WEBVAR_STRUCTNAME_MRB_VERSION : 
 [ 
 { 'MRBVersion' : 102 },  { 'MRBVersion' : 255 },  { 'MRBVersion' : 102 },  { 'MRBVersion' : 255 },  { 'MRBVersion' : 102 },  { 'MRBVersion' : 255 },  { 'MRBVersion' : 102 },  { 'MRBVersion' : 255 },  {} ],  
 HAPI_STATUS:0 }; 
//Dynamic data end";


//        $str1 = preg_replace('/((\/\*[\s\S]*?\*\/)|(\/\/.*)|(#.*))|(\\n)/', "", $str);
//        preg_match('/WEBVAR_STRUCTNAME_MRB_VERSION :(.*?])/', $str1, $fpg);
//
//
//        $arr3 = preg_replace('/\'/', '"', $fpg[1]);
//        $arr4 = json_decode($arr3, true);
//
//        echo "<pre>";
//        print_r($arr4);die;


//        $str1 = preg_replace('/((\/\*[\s\S]*?\*\/)|(\/\/.*)|(#.*))|(\\n)/', "", $str);
//        $re = '/\[([\s\S]*)\]/';
//        preg_match($re,$str1,$arr1);
//        preg_match('/{.*?}/',$arr1[1],$arr2);
//        $arr3 = preg_replace('/\'/','"',$arr2[0]);
//        $arr4 = json_decode($arr3,true);
//

//        $arr4 = array_filter($arr4);
//        $opt =  [];
//        $val = [];
//        $word = 'HARDWARE_';
//        foreach ($arr4 as $vo) {
//            $type = $vo['SensorType'];
//            $k = '';
//            switch ($type){
//                case 1: $k = $word.'TEMP';break;
//                case 2: $k = $word.'PV';break;
//                case 3: $k = $word.'AMP';break;
//                case 4: $k = $word.'AMP';break;
//                case 8: $k = $word.'FAN';break;
//                case 13: $k = $word.'DRIVE';break;
//                case 22: $k = $word.'CONTROL';break;
//            }
//            $opt[] = [ $k =>$vo['SensorName']];
//            $vo[$k] = $vo['SensorName'];
//            $val[] =  $vo;
//        }

        $json = '{"0":{"SensorNumber":1,"SensorName":"P1V1_PCH_SENSE","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1110,"RawReading":111,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1050,"HighNCThresh":1160,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"P1V1_PCH_SENSE"},"1":{"SensorNumber":5,"SensorName":"P5V_SENSE_R","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":5040,"RawReading":168,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":3990,"HighNCThresh":6000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"P5V_SENSE_R"},"2":{"SensorNumber":6,"SensorName":"P12V_SENSE_R","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":12000,"RawReading":200,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":9599,"HighNCThresh":14400,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"P12V_SENSE_R"},"3":{"SensorNumber":7,"SensorName":"THERMISTOR3","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":22000,"RawReading":22,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":105000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2056,"HARDWARE_TEMP":"THERMISTOR3"},"4":{"SensorNumber":9,"SensorName":"PSU0_AMP","OwnerID":32,"OwnerLUN":0,"SensorType":3,"SensorUnit1":0,"SensorUnit2":5,"SensorUnit3":0,"SensorReading":7000,"RawReading":7,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":255000,"HighNRThresh":255000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_AMP":"PSU0_AMP"},"5":{"SensorNumber":10,"SensorName":"PSU0_VOLT","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":12000,"RawReading":200,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_PV":"PSU0_VOLT"},"6":{"SensorNumber":11,"SensorName":"PSU1_AMP","OwnerID":32,"OwnerLUN":0,"SensorType":3,"SensorUnit1":0,"SensorUnit2":5,"SensorUnit3":0,"SensorReading":6000,"RawReading":6,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":255000,"HighNRThresh":255000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_AMP":"PSU1_AMP"},"7":{"SensorNumber":12,"SensorName":"PSU1_VOLT","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":12000,"RawReading":200,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_PV":"PSU1_VOLT"},"8":{"SensorNumber":13,"SensorName":"PSU2_AMP","OwnerID":32,"OwnerLUN":0,"SensorType":3,"SensorUnit1":0,"SensorUnit2":5,"SensorUnit3":0,"SensorReading":7000,"RawReading":7,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":255000,"HighNRThresh":255000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_AMP":"PSU2_AMP"},"9":{"SensorNumber":14,"SensorName":"PSU2_VOLT","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":12000,"RawReading":200,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_PV":"PSU2_VOLT"},"10":{"SensorNumber":15,"SensorName":"PSU3_AMP","OwnerID":32,"OwnerLUN":0,"SensorType":3,"SensorUnit1":0,"SensorUnit2":5,"SensorUnit3":0,"SensorReading":6000,"RawReading":6,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":255000,"HighNRThresh":255000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_AMP":"PSU3_AMP"},"11":{"SensorNumber":16,"SensorName":"PSU3_VOLT","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":10980,"RawReading":183,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_PV":"PSU3_VOLT"},"12":{"SensorNumber":17,"SensorName":"FAN_0","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_0"},"13":{"SensorNumber":18,"SensorName":"FAN_1","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_1"},"14":{"SensorNumber":19,"SensorName":"FAN_2","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_2"},"15":{"SensorNumber":20,"SensorName":"FAN_3","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_3"},"16":{"SensorNumber":21,"SensorName":"FAN_4","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_4"},"17":{"SensorNumber":22,"SensorName":"FAN_5","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_5"},"18":{"SensorNumber":23,"SensorName":"FAN_6","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2720000,"RawReading":34,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_6"},"19":{"SensorNumber":24,"SensorName":"FAN_7","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":18,"SensorUnit3":0,"SensorReading":2640000,"RawReading":33,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":160000,"HighNCThresh":12000000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_FAN":"FAN_7"},"20":{"SensorNumber":33,"SensorName":"INLET1_1","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":16000,"RawReading":16,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":50000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2056,"HARDWARE_TEMP":"INLET1_1"},"21":{"SensorNumber":36,"SensorName":"PVCC_CPU1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1839,"RawReading":184,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1439,"HighNCThresh":2000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"PVCC_CPU1"},"22":{"SensorNumber":37,"SensorName":"PVCC_CPU0","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1839,"RawReading":184,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1439,"HighNCThresh":2000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"PVCC_CPU0"},"23":{"SensorNumber":38,"SensorName":"PVCC_CPU2","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1839,"RawReading":184,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1439,"HighNCThresh":2000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"PVCC_CPU2"},"24":{"SensorNumber":39,"SensorName":"PVCC_CPU3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1839,"RawReading":184,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1439,"HighNCThresh":2000,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"PVCC_CPU3"},"25":{"SensorNumber":55,"SensorName":"MRB0_PVDDQ_CH0_1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1220,"RawReading":122,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB0_PVDDQ_CH0_1"},"26":{"SensorNumber":56,"SensorName":"MRB0_PVDDQ_CH2_3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB0_PVDDQ_CH2_3"},"27":{"SensorNumber":77,"SensorName":"MRB2_PVDDQ_CH0_1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1220,"RawReading":122,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB2_PVDDQ_CH0_1"},"28":{"SensorNumber":78,"SensorName":"MRB2_PVDDQ_CH2_3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB2_PVDDQ_CH2_3"},"29":{"SensorNumber":99,"SensorName":"MRB4_PVDDQ_CH0_1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB4_PVDDQ_CH0_1"},"30":{"SensorNumber":100,"SensorName":"MRB4_PVDDQ_CH2_3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB4_PVDDQ_CH2_3"},"31":{"SensorNumber":121,"SensorName":"MRB6_PVDDQ_CH0_1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB6_PVDDQ_CH0_1"},"32":{"SensorNumber":122,"SensorName":"MRB6_PVDDQ_CH2_3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1230,"RawReading":123,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":960,"HighNCThresh":1339,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"MRB6_PVDDQ_CH2_3"},"33":{"SensorNumber":136,"SensorName":"V_CH1","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1860,"RawReading":186,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1709,"HighNCThresh":1990,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"V_CH1"},"34":{"SensorNumber":137,"SensorName":"V_CH2","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1050,"RawReading":105,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":950,"HighNCThresh":1100,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"V_CH2"},"35":{"SensorNumber":138,"SensorName":"V_CH3","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":1120,"RawReading":112,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":1020,"HighNCThresh":1209,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"V_CH3"},"36":{"SensorNumber":139,"SensorName":"V_CH4","OwnerID":32,"OwnerLUN":0,"SensorType":2,"SensorUnit1":0,"SensorUnit2":4,"SensorUnit3":0,"SensorReading":5100,"RawReading":170,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":4230,"HighNCThresh":5460,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":2313,"HARDWARE_PV":"V_CH4"},"37":{"SensorNumber":146,"SensorName":"CPU0_TEMP","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":38000,"RawReading":38,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":75000,"HighNRThresh":75000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_TEMP":"CPU0_TEMP"},"38":{"SensorNumber":147,"SensorName":"CPU1_TEMP","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":39000,"RawReading":39,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":75000,"HighNRThresh":75000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_TEMP":"CPU1_TEMP"},"39":{"SensorNumber":148,"SensorName":"CPU2_TEMP","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":36000,"RawReading":36,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":75000,"HighNRThresh":75000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_TEMP":"CPU2_TEMP"},"40":{"SensorNumber":149,"SensorName":"CPU3_TEMP","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":37000,"RawReading":37,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":75000,"HighCTThresh":75000,"HighNRThresh":75000,"SensorAccessibleFlags":0,"SettableReadableThreshMask":14392,"HARDWARE_TEMP":"CPU3_TEMP"},"41":{"SensorNumber":152,"SensorName":"PMBPower_0","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":6,"SensorUnit3":0,"SensorReading":87000,"RawReading":29,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PMBPower_0"},"42":{"SensorNumber":153,"SensorName":"PMBPower_1","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":6,"SensorUnit3":0,"SensorReading":84000,"RawReading":28,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PMBPower_1"},"43":{"SensorNumber":154,"SensorName":"PMBPower_2","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":6,"SensorUnit3":0,"SensorReading":81000,"RawReading":27,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PMBPower_2"},"44":{"SensorNumber":155,"SensorName":"PMBPower_3","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":6,"SensorUnit3":0,"SensorReading":81000,"RawReading":27,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PMBPower_3"},"45":{"SensorNumber":160,"SensorName":"HDDFAN0","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":0,"SensorUnit3":0,"SensorReading":32770000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_FAN":"HDDFAN0"},"46":{"SensorNumber":161,"SensorName":"HDDFAN1","OwnerID":32,"OwnerLUN":0,"SensorType":4,"SensorUnit1":0,"SensorUnit2":0,"SensorUnit3":0,"SensorReading":32770000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_FAN":"HDDFAN1"},"47":{"SensorNumber":162,"SensorName":"HDD0_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD0_Status"},"48":{"SensorNumber":163,"SensorName":"HDD1_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD1_Status"},"49":{"SensorNumber":164,"SensorName":"HDD2_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD2_Status"},"50":{"SensorNumber":165,"SensorName":"HDD3_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD3_Status"},"51":{"SensorNumber":166,"SensorName":"HDD4_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD4_Status"},"52":{"SensorNumber":167,"SensorName":"HDD5_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD5_Status"},"53":{"SensorNumber":168,"SensorName":"HDD6_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD6_Status"},"54":{"SensorNumber":169,"SensorName":"HDD7_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"HDD7_Status"},"55":{"SensorNumber":170,"SensorName":"PSU0_Supply","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PSU0_Supply"},"56":{"SensorNumber":171,"SensorName":"PSU1_Supply","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PSU1_Supply"},"57":{"SensorNumber":172,"SensorName":"PSU2_Supply","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PSU2_Supply"},"58":{"SensorNumber":173,"SensorName":"PSU3_Supply","OwnerID":32,"OwnerLUN":0,"SensorType":8,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32769000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_POWER":"PSU3_Supply"},"59":{"SensorNumber":183,"SensorName":"NVME_HDD0_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"NVME_HDD0_Status"},"60":{"SensorNumber":184,"SensorName":"NVME_HDD1_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"NVME_HDD1_Status"},"61":{"SensorNumber":185,"SensorName":"NVME_HDD2_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"NVME_HDD2_Status"},"62":{"SensorNumber":186,"SensorName":"NVME_HDD3_Status","OwnerID":32,"OwnerLUN":0,"SensorType":13,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32768000,"RawReading":0,"SensorState":0,"DiscreteState":111,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_DRIVE":"NVME_HDD3_Status"},"63":{"SensorNumber":188,"SensorName":"NVME_HDD0_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":213,"SettableReadableThreshMask":0,"HARDWARE_TEMP":"NVME_HDD0_Temp"},"64":{"SensorNumber":189,"SensorName":"NVME_HDD1_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":213,"SettableReadableThreshMask":0,"HARDWARE_TEMP":"NVME_HDD1_Temp"},"65":{"SensorNumber":190,"SensorName":"NVME_HDD2_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":213,"SettableReadableThreshMask":0,"HARDWARE_TEMP":"NVME_HDD2_Temp"},"66":{"SensorNumber":191,"SensorName":"NVME_HDD3_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":213,"SettableReadableThreshMask":0,"HARDWARE_TEMP":"NVME_HDD3_Temp"},"67":{"SensorNumber":193,"SensorName":"BMC_Boot_Up","OwnerID":32,"OwnerLUN":0,"SensorType":22,"SensorUnit1":0,"SensorUnit2":59,"SensorUnit3":0,"SensorReading":32770000,"RawReading":0,"SensorState":0,"DiscreteState":9,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":0,"HighCTThresh":0,"HighNRThresh":0,"SensorAccessibleFlags":0,"SettableReadableThreshMask":0,"HARDWARE_CONTROL":"BMC_Boot_Up"},"68":{"SensorNumber":201,"SensorName":"GPU0_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":86000,"HighCTThresh":92000,"HighNRThresh":108000,"SensorAccessibleFlags":213,"SettableReadableThreshMask":16191,"HARDWARE_TEMP":"GPU0_Temp"},"69":{"SensorNumber":202,"SensorName":"MIC0_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":102000,"HighCTThresh":114000,"HighNRThresh":130000,"SensorAccessibleFlags":213,"SettableReadableThreshMask":16191,"HARDWARE_TEMP":"MIC0_Temp"},"70":{"SensorNumber":203,"SensorName":"GPU1_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":86000,"HighCTThresh":92000,"HighNRThresh":108000,"SensorAccessibleFlags":213,"SettableReadableThreshMask":16191,"HARDWARE_TEMP":"GPU1_Temp"},"71":{"SensorNumber":204,"SensorName":"MIC1_Temp","OwnerID":32,"OwnerLUN":0,"SensorType":1,"SensorUnit1":0,"SensorUnit2":1,"SensorUnit3":0,"SensorReading":0,"RawReading":0,"SensorState":1,"DiscreteState":0,"LowNRThresh":0,"LowCTThresh":0,"LowNCThresh":0,"HighNCThresh":102000,"HighCTThresh":114000,"HighNRThresh":130000,"SensorAccessibleFlags":213,"SettableReadableThreshMask":16191,"HARDWARE_TEMP":"MIC1_Temp"},"FRUDeviceID":0,"FRUDeviceName":"BMC_FRU","CH_CommonHeaderFormatVersion":1,"CH_InternalUseAreaStartOffset":0,"CH_ChassisInfoAreaStartOffset":1,"CH_BoardInfoAreaStartOffset":5,"CH_ProductInfoAreaStartOffset":11,"CH_MultiRecordAreaStartOffset":0,"CI_ChassisInfoAreaFormatVersion":1,"CI_ChassisInfoAreaLength":4,"CI_ChassisType":"Main Server Chassis","CI_ChassisPartNum":"YZCP-00265-102","CI_ChassisSerialNum":"0","CI_CustomFields":"inspur\n","BI_BoardInfoAreaFormatVersion":1,"BI_BoardInfoAreaLength":6,"BI_Language":0,"BI_MfgDateTime":"Fri Nov  7 15:50:00 2014\n","BI_BoardMfr":"Inspur","BI_BoardProductName":"NF8480M4","BI_BoardSerialNum":"0","BI_BoardPartNum":"YZCP-00265-102","BI_FRUFileID":"","BI_CustomFields":"(null)","PI_ProductInfoAreaFormatVersion":1,"PI_ProductInfoAreaLength":8,"PI_Language":0,"PI_MfrName":"Inspur","PI_ProductName":"NF8480M4","PI_ProductPartNum":"YZCP-00265-102","PI_ProductVersion":"01","PI_ProductSerialNum":"217436299","PI_AssetTag":"217436299","PI_FRUFileID":"","PI_CustomFields":"(null)","72":{"FPGAVERSION":"2.0.7"},"73":{"BIOSVERSION":"4.0.8"},"74":{"BIOSBUILDTIME":"04\/25\/2017"},"75":{"MEVERSION":"2.4.0.043"},"76":{"MRBVERSION0":102},"77":{"MRBVERSION1":255},"78":{"MRBVERSION2":102},"79":{"MRBVERSION3":255},"80":{"MRBVERSION4":102},"81":{"MRBVERSION5":255},"82":{"MRBVERSION6":102},"83":{"MRBVERSION7":255},"84":{"FANPSOCVERSION":"1.2.0"},"85":{"HDDPSOCVERSION":"1.7.0"},"86":{"CPUSocket":"CPU0","CPUType":3,"L1Cache":64,"L2Cache":256,"L3Cache":25600,"CPUVersion":"Intel(R) Xeon(R) CPU E7-4820 v3 @ 1.90GHz"},"87":{"CPUSocket":"CPU1","CPUType":3,"L1Cache":64,"L2Cache":256,"L3Cache":25600,"CPUVersion":"Intel(R) Xeon(R) CPU E7-4820 v3 @ 1.90GHz"},"88":{"CPUSocket":"CPU2","CPUType":3,"L1Cache":64,"L2Cache":256,"L3Cache":25600,"CPUVersion":"Intel(R) Xeon(R) CPU E7-4820 v3 @ 1.90GHz"},"89":{"CPUSocket":"CPU3","CPUType":3,"L1Cache":64,"L2Cache":256,"L3Cache":25600,"CPUVersion":"Intel(R) Xeon(R) CPU E7-4820 v3 @ 1.90GHz"},"90":{"PCIESlot":"Slot8","PCIEClass":12,"PCIESubClass":4,"PCIEVendorId":4215,"PCIEDeviceId":8801,"PCIELinkSpeed":"8.0 GT\/s(Gen 3)","PCIELinkWidth":"x8"},"91":{"PCIESlot":"Slot10","PCIEClass":2,"PCIESubClass":0,"PCIEVendorId":32902,"PCIEDeviceId":4347,"PCIELinkSpeed":"5.0 GT\/s(Gen 2)","PCIELinkWidth":"x8"},"92":{"PCIESlot":"Slot11","PCIEClass":2,"PCIESubClass":0,"PCIEVendorId":32902,"PCIEDeviceId":4347,"PCIELinkSpeed":"5.0 GT\/s(Gen 2)","PCIELinkWidth":"x8"},"93":{"PCIESlot":"Slot12","PCIEClass":1,"PCIESubClass":4,"PCIEVendorId":36869,"PCIEDeviceId":653,"PCIELinkSpeed":"8.0 GT\/s(Gen 3)","PCIELinkWidth":"x8"},"94":{"MemDimm":"MEM0_C0D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"95":{"MemDimm":"MEM0_C0D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"96":{"MemDimm":"MEM0_C1D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"97":{"MemDimm":"MEM0_C1D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"98":{"MemDimm":"MEM0_C2D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"99":{"MemDimm":"MEM0_C2D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"100":{"MemDimm":"MEM0_C3D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"101":{"MemDimm":"MEM0_C3D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"102":{"MemDimm":"MEM2_C0D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"103":{"MemDimm":"MEM2_C0D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"104":{"MemDimm":"MEM2_C1D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"105":{"MemDimm":"MEM2_C1D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"106":{"MemDimm":"MEM2_C2D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"107":{"MemDimm":"MEM2_C2D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"108":{"MemDimm":"MEM2_C3D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"109":{"MemDimm":"MEM2_C3D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"110":{"MemDimm":"MEM4_C0D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"111":{"MemDimm":"MEM4_C0D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"112":{"MemDimm":"MEM4_C1D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"113":{"MemDimm":"MEM4_C1D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"114":{"MemDimm":"MEM4_C2D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"115":{"MemDimm":"MEM4_C2D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"116":{"MemDimm":"MEM4_C3D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"117":{"MemDimm":"MEM4_C3D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"118":{"MemDimm":"MEM6_C0D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"119":{"MemDimm":"MEM6_C0D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"120":{"MemDimm":"MEM6_C1D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"121":{"MemDimm":"MEM6_C1D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"122":{"MemDimm":"MEM6_C2D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"123":{"MemDimm":"MEM6_C2D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"124":{"MemDimm":"MEM6_C3D0","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333},"125":{"MemDimm":"MEM6_C3D1","MemType":26,"MemManufacturer":"Hynix","memSize":16,"MemSpeed":2400,"MemCurrentSpeed":1333}}';

        echo "<pre>";
        //print_r(json_decode($json,true));
        $data = json_decode($json,true);
        $key = 'PVCC_CPU3.RAWREADING';

        $r = preg_replace('/{#|}/','',$key);

        $rs = explode(".",$r);

        print_r($data);

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
        //echo $rs[0];
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




        die;

    }


    public function actionA(){
        $str = '{"data":[{"{#HARDWARE_PV}":"P1V1_PCH_SENSE"},{"{#HARDWARE_PV}":"P5V_SENSE_R"},{"{#HARDWARE_PV}":"P12V_SENSE_R"},{"{#HARDWARE_TEMP}":"THERMISTOR3"},{"{#HARDWARE_AMP}":"PSU0_AMP"},{"{#HARDWARE_PV}":"PSU0_VOLT"},{"{#HARDWARE_AMP}":"PSU1_AMP"},{"{#HARDWARE_PV}":"PSU1_VOLT"},{"{#HARDWARE_AMP}":"PSU2_AMP"},{"{#HARDWARE_PV}":"PSU2_VOLT"},{"{#HARDWARE_AMP}":"PSU3_AMP"},{"{#HARDWARE_PV}":"PSU3_VOLT"},{"{#HARDWARE_FAN}":"FAN_0"},{"{#HARDWARE_FAN}":"FAN_1"},{"{#HARDWARE_FAN}":"FAN_2"},{"{#HARDWARE_FAN}":"FAN_3"},{"{#HARDWARE_FAN}":"FAN_4"},{"{#HARDWARE_FAN}":"FAN_5"},{"{#HARDWARE_FAN}":"FAN_6"},{"{#HARDWARE_FAN}":"FAN_7"},{"{#HARDWARE_TEMP}":"INLET1_1"},{"{#HARDWARE_PV}":"PVCC_CPU1"},{"{#HARDWARE_PV}":"PVCC_CPU0"},{"{#HARDWARE_PV}":"PVCC_CPU2"},{"{#HARDWARE_PV}":"PVCC_CPU3"},{"{#HARDWARE_PV}":"MRB0_PVDDQ_CH0_1"},{"{#HARDWARE_PV}":"MRB0_PVDDQ_CH2_3"},{"{#HARDWARE_PV}":"MRB2_PVDDQ_CH0_1"},{"{#HARDWARE_PV}":"MRB2_PVDDQ_CH2_3"},{"{#HARDWARE_PV}":"MRB4_PVDDQ_CH0_1"},{"{#HARDWARE_PV}":"MRB4_PVDDQ_CH2_3"},{"{#HARDWARE_PV}":"MRB6_PVDDQ_CH0_1"},{"{#HARDWARE_PV}":"MRB6_PVDDQ_CH2_3"},{"{#HARDWARE_PV}":"V_CH1"},{"{#HARDWARE_PV}":"V_CH2"},{"{#HARDWARE_PV}":"V_CH3"},{"{#HARDWARE_PV}":"V_CH4"},{"{#HARDWARE_TEMP}":"CPU0_TEMP"},{"{#HARDWARE_TEMP}":"CPU1_TEMP"},{"{#HARDWARE_TEMP}":"CPU2_TEMP"},{"{#HARDWARE_TEMP}":"CPU3_TEMP"},{"{#HARDWARE_POWER}":"PMBPOWER_0"},{"{#HARDWARE_POWER}":"PMBPOWER_1"},{"{#HARDWARE_POWER}":"PMBPOWER_2"},{"{#HARDWARE_POWER}":"PMBPOWER_3"},{"{#HARDWARE_FAN}":"HDDFAN0"},{"{#HARDWARE_FAN}":"HDDFAN1"},{"{#HARDWARE_DRIVE}":"HDD0_STATUS"},{"{#HARDWARE_DRIVE}":"HDD1_STATUS"},{"{#HARDWARE_DRIVE}":"HDD2_STATUS"},{"{#HARDWARE_DRIVE}":"HDD3_STATUS"},{"{#HARDWARE_DRIVE}":"HDD4_STATUS"},{"{#HARDWARE_DRIVE}":"HDD5_STATUS"},{"{#HARDWARE_DRIVE}":"HDD6_STATUS"},{"{#HARDWARE_DRIVE}":"HDD7_STATUS"},{"{#HARDWARE_POWER}":"PSU0_SUPPLY"},{"{#HARDWARE_POWER}":"PSU1_SUPPLY"},{"{#HARDWARE_POWER}":"PSU2_SUPPLY"},{"{#HARDWARE_POWER}":"PSU3_SUPPLY"},{"{#HARDWARE_DRIVE}":"NVME_HDD0_STATUS"},{"{#HARDWARE_DRIVE}":"NVME_HDD1_STATUS"},{"{#HARDWARE_DRIVE}":"NVME_HDD2_STATUS"},{"{#HARDWARE_DRIVE}":"NVME_HDD3_STATUS"},{"{#HARDWARE_TEMP}":"NVME_HDD0_TEMP"},{"{#HARDWARE_TEMP}":"NVME_HDD1_TEMP"},{"{#HARDWARE_TEMP}":"NVME_HDD2_TEMP"},{"{#HARDWARE_TEMP}":"NVME_HDD3_TEMP"},{"{#HARDWARE_CONTROL}":"BMC_BOOT_UP"},{"{#HARDWARE_TEMP}":"GPU0_TEMP"},{"{#HARDWARE_TEMP}":"MIC0_TEMP"},{"{#HARDWARE_TEMP}":"GPU1_TEMP"},{"{#HARDWARE_TEMP}":"MIC1_TEMP"},{"{#FRUDEVICEID}":"FRUDEVICEID"},{"{#FRUDEVICENAME}":"FRUDEVICENAME"},{"{#CH_COMMONHEADERFORMATVERSION}":"CH_COMMONHEADERFORMATVERSION"},{"{#CH_INTERNALUSEAREASTARTOFFSET}":"CH_INTERNALUSEAREASTARTOFFSET"},{"{#CH_CHASSISINFOAREASTARTOFFSET}":"CH_CHASSISINFOAREASTARTOFFSET"},{"{#CH_BOARDINFOAREASTARTOFFSET}":"CH_BOARDINFOAREASTARTOFFSET"},{"{#CH_PRODUCTINFOAREASTARTOFFSET}":"CH_PRODUCTINFOAREASTARTOFFSET"},{"{#CH_MULTIRECORDAREASTARTOFFSET}":"CH_MULTIRECORDAREASTARTOFFSET"},{"{#CI_CHASSISINFOAREAFORMATVERSION}":"CI_CHASSISINFOAREAFORMATVERSION"},{"{#CI_CHASSISINFOAREALENGTH}":"CI_CHASSISINFOAREALENGTH"},{"{#CI_CHASSISTYPE}":"CI_CHASSISTYPE"},{"{#CI_CHASSISPARTNUM}":"CI_CHASSISPARTNUM"},{"{#CI_CHASSISSERIALNUM}":"CI_CHASSISSERIALNUM"},{"{#CI_CUSTOMFIELDS}":"CI_CUSTOMFIELDS"},{"{#BI_BOARDINFOAREAFORMATVERSION}":"BI_BOARDINFOAREAFORMATVERSION"},{"{#BI_BOARDINFOAREALENGTH}":"BI_BOARDINFOAREALENGTH"},{"{#BI_LANGUAGE}":"BI_LANGUAGE"},{"{#BI_MFGDATETIME}":"BI_MFGDATETIME"},{"{#BI_BOARDMFR}":"BI_BOARDMFR"},{"{#BI_BOARDPRODUCTNAME}":"BI_BOARDPRODUCTNAME"},{"{#BI_BOARDSERIALNUM}":"BI_BOARDSERIALNUM"},{"{#BI_BOARDPARTNUM}":"BI_BOARDPARTNUM"},{"{#BI_FRUFILEID}":"BI_FRUFILEID"},{"{#BI_CUSTOMFIELDS}":"BI_CUSTOMFIELDS"},{"{#PI_PRODUCTINFOAREAFORMATVERSION}":"PI_PRODUCTINFOAREAFORMATVERSION"},{"{#PI_PRODUCTINFOAREALENGTH}":"PI_PRODUCTINFOAREALENGTH"},{"{#PI_LANGUAGE}":"PI_LANGUAGE"},{"{#PI_MFRNAME}":"PI_MFRNAME"},{"{#PI_PRODUCTNAME}":"PI_PRODUCTNAME"},{"{#PI_PRODUCTPARTNUM}":"PI_PRODUCTPARTNUM"},{"{#PI_PRODUCTVERSION}":"PI_PRODUCTVERSION"},{"{#PI_PRODUCTSERIALNUM}":"PI_PRODUCTSERIALNUM"},{"{#PI_ASSETTAG}":"PI_ASSETTAG"},{"{#PI_FRUFILEID}":"PI_FRUFILEID"},{"{#PI_CUSTOMFIELDS}":"PI_CUSTOMFIELDS"},{"{#FPGAVERSION}":"FPGAVERSION"},{"{#BIOSVERSION}":"BIOSVERSION"},{"{#BIOSBUILDTIME}":"BIOSBUILDTIME"},{"{#MEVERSION}":"MEVERSION"},{"{#MRBVERSION}":"MRBVERSION0"},{"{#MRBVERSION}":"MRBVERSION1"},{"{#MRBVERSION}":"MRBVERSION2"},{"{#MRBVERSION}":"MRBVERSION3"},{"{#MRBVERSION}":"MRBVERSION4"},{"{#MRBVERSION}":"MRBVERSION5"},{"{#MRBVERSION}":"MRBVERSION6"},{"{#MRBVERSION}":"MRBVERSION7"},{"{#FANPSOCVERSION}":"FANPSOCVERSION"},{"{#HDDPSOCVERSION}":"HDDPSOCVERSION"},{"{#CPUSOCKET}":"CPU0"},{"{#CPUSOCKET}":"CPU1"},{"{#CPUSOCKET}":"CPU2"},{"{#CPUSOCKET}":"CPU3"},{"{#PCIESLOT}":"SLOT8"},{"{#PCIESLOT}":"SLOT10"},{"{#PCIESLOT}":"SLOT11"},{"{#PCIESLOT}":"SLOT12"},{"{#MEMDIMM}":"MEM0_C0D0"},{"{#MEMDIMM}":"MEM0_C0D1"},{"{#MEMDIMM}":"MEM0_C1D0"},{"{#MEMDIMM}":"MEM0_C1D1"},{"{#MEMDIMM}":"MEM0_C2D0"},{"{#MEMDIMM}":"MEM0_C2D1"},{"{#MEMDIMM}":"MEM0_C3D0"},{"{#MEMDIMM}":"MEM0_C3D1"},{"{#MEMDIMM}":"MEM2_C0D0"},{"{#MEMDIMM}":"MEM2_C0D1"},{"{#MEMDIMM}":"MEM2_C1D0"},{"{#MEMDIMM}":"MEM2_C1D1"},{"{#MEMDIMM}":"MEM2_C2D0"},{"{#MEMDIMM}":"MEM2_C2D1"},{"{#MEMDIMM}":"MEM2_C3D0"},{"{#MEMDIMM}":"MEM2_C3D1"},{"{#MEMDIMM}":"MEM4_C0D0"},{"{#MEMDIMM}":"MEM4_C0D1"},{"{#MEMDIMM}":"MEM4_C1D0"},{"{#MEMDIMM}":"MEM4_C1D1"},{"{#MEMDIMM}":"MEM4_C2D0"},{"{#MEMDIMM}":"MEM4_C2D1"},{"{#MEMDIMM}":"MEM4_C3D0"},{"{#MEMDIMM}":"MEM4_C3D1"},{"{#MEMDIMM}":"MEM6_C0D0"},{"{#MEMDIMM}":"MEM6_C0D1"},{"{#MEMDIMM}":"MEM6_C1D0"},{"{#MEMDIMM}":"MEM6_C1D1"},{"{#MEMDIMM}":"MEM6_C2D0"},{"{#MEMDIMM}":"MEM6_C2D1"},{"{#MEMDIMM}":"MEM6_C3D0"},{"{#MEMDIMM}":"MEM6_C3D1"}]}';

        echo "<pre>";
        $arr = json_decode($str,true);
        print_r($arr);

    }


    public function actionB(){
        $arr = ['inspur','ibm'];
        $str = 'ibm';

        foreach ($arr as $vo){
            if (preg_match("/$vo/i",$str)){
                return strtoupper($vo);
            }
        }






    }




}