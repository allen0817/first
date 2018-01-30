<?php

namespace app\controllers;

use app\models\IBM\Common;
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
    public function actionIndex()
    {
        $o = '{"options":[{"{#SPNAME}":"SN#06AB654"},{"{#MEMORYINFO}":"DIMM1"},{"{#MEMORYINFO}":"DIMM3"},{"{#MEMORYINFO}":"DIMM6"},{"{#MEMORYINFO}":"DIMM8"},{"{#MEMORYINFO}":"DIMM17"},{"{#MEMORYINFO}":"DIMM19"},{"{#MEMORYINFO}":"DIMM22"},{"{#MEMORYINFO}":"DIMM24"},{"{#MEMORYINFO}":"DIMM33"},{"{#MEMORYINFO}":"DIMM35"},{"{#MEMORYINFO}":"DIMM38"},{"{#MEMORYINFO}":"DIMM40"},{"{#MEMORYINFO}":"DIMM49"},{"{#MEMORYINFO}":"DIMM51"},{"{#MEMORYINFO}":"DIMM54"},{"{#MEMORYINFO}":"DIMM56"},{"{#SERVERPOWER}":"SERVERPOWER"},{"{#SERVERSTATE}":"SERVERSTATE"},{"{#VOLTAGE}":"PLANAR3.3V"},{"{#VOLTAGE}":"PLANAR5V"},{"{#VOLTAGE}":"PLANAR12V"},{"{#VOLTAGE}":"CMOSVBAT"},{"{#FAN}":"FAN1TACH"},{"{#FAN}":"FAN2TACH"},{"{#FAN}":"FAN3ATACH"},{"{#FAN}":"FAN3BTACH"},{"{#FAN}":"FAN4TACH"},{"{#FAN}":"FAN5TACH"},{"{#PRODUCTNAME}":"SYSTEMX3850X5\/X3950X5"},{"{#MACHINETYPEANDMODEL}":"71435GR"},{"{#SERIALNUMBER}":"06AB654"},{"{#UUID}":"BE290FAA6C7611E3AD6340F2E963810C"},{"{#FRUNAME}":"SASBP1"},{"{#FRUNAME}":"MEMORYCARD1"},{"{#FRUNAME}":"MEMORYCARD3"},{"{#FRUNAME}":"MEMORYCARD5"},{"{#FRUNAME}":"MEMORYCARD7"},{"{#FRUNAME}":"CPUBOARD"},{"{#FRUNAME}":"POWERSUPPLY1"},{"{#FRUNAME}":"POWERSUPPLY2"},{"{#FRUNAME}":"SYSTEMBOARD"},{"{#FIRWARENAME}":"IMM"},{"{#FIRWARENAME}":"UEFI"},{"{#FIRWARENAME}":"DSA"},{"{#FIRWARENAME}":"FPGA"},{"{#PROCESSOR}":"PROCESSOR1"},{"{#PROCESSOR}":"PROCESSOR2"},{"{#PROCESSOR}":"PROCESSOR3"},{"{#PROCESSOR}":"PROCESSOR4"},{"{#HOSTMADDR}":"HOSTETHERNETMACADDRESS1"},{"{#HOSTMADDR}":"HOSTETHERNETMACADDRESS2"},{"{#VIRTUALLIGHPATH}":"POWER"},{"{#VIRTUALLIGHPATH}":"FAULT"},{"{#VIRTUALLIGHPATH}":"INFO"},{"{#VIRTUALLIGHPATH}":"CPU"},{"{#VIRTUALLIGHPATH}":"PS"},{"{#VIRTUALLIGHPATH}":"DASD"},{"{#VIRTUALLIGHPATH}":"FAN"},{"{#VIRTUALLIGHPATH}":"MEM"},{"{#VIRTUALLIGHPATH}":"NMI"},{"{#VIRTUALLIGHPATH}":"OVERSPEC"},{"{#VIRTUALLIGHPATH}":"TEMP"},{"{#VIRTUALLIGHPATH}":"SP"},{"{#VIRTUALLIGHPATH}":"IDENTIFY"},{"{#VIRTUALLIGHPATH}":"PCI"},{"{#VIRTUALLIGHPATH}":"CPU1"},{"{#VIRTUALLIGHPATH}":"CPU2"},{"{#VIRTUALLIGHPATH}":"CPU3"},{"{#VIRTUALLIGHPATH}":"CPU4"},{"{#VIRTUALLIGHPATH}":"FAN1"},{"{#VIRTUALLIGHPATH}":"FAN2"},{"{#VIRTUALLIGHPATH}":"FAN3"},{"{#VIRTUALLIGHPATH}":"PCI1"},{"{#VIRTUALLIGHPATH}":"PCI2"},{"{#VIRTUALLIGHPATH}":"PCI3"},{"{#VIRTUALLIGHPATH}":"PCI4"},{"{#VIRTUALLIGHPATH}":"PCI5"},{"{#VIRTUALLIGHPATH}":"PCI6"},{"{#VIRTUALLIGHPATH}":"PCI7"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD1"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD2"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD3"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD4"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD5"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD6"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD7"},{"{#VIRTUALLIGHPATH}":"MEMERRCARD8"},{"{#VIRTUALLIGHPATH}":"MEMCARD1"},{"{#VIRTUALLIGHPATH}":"MEMCARD2"},{"{#VIRTUALLIGHPATH}":"MEMCARD3"},{"{#VIRTUALLIGHPATH}":"MEMCARD4"},{"{#VIRTUALLIGHPATH}":"MEMCARD5"},{"{#VIRTUALLIGHPATH}":"MEMCARD6"},{"{#VIRTUALLIGHPATH}":"MEMCARD7"},{"{#VIRTUALLIGHPATH}":"MEMCARD8"},{"{#VIRTUALLIGHPATH}":"DIMM1"},{"{#VIRTUALLIGHPATH}":"DIMM2"},{"{#VIRTUALLIGHPATH}":"DIMM3"},{"{#VIRTUALLIGHPATH}":"DIMM4"},{"{#VIRTUALLIGHPATH}":"DIMM5"},{"{#VIRTUALLIGHPATH}":"DIMM6"},{"{#VIRTUALLIGHPATH}":"DIMM7"},{"{#VIRTUALLIGHPATH}":"DIMM8"},{"{#VIRTUALLIGHPATH}":"DIMM9"},{"{#VIRTUALLIGHPATH}":"DIMM10"},{"{#VIRTUALLIGHPATH}":"DIMM11"},{"{#VIRTUALLIGHPATH}":"DIMM12"},{"{#VIRTUALLIGHPATH}":"DIMM13"},{"{#VIRTUALLIGHPATH}":"DIMM14"},{"{#VIRTUALLIGHPATH}":"DIMM15"},{"{#VIRTUALLIGHPATH}":"DIMM16"},{"{#VIRTUALLIGHPATH}":"DIMM17"},{"{#VIRTUALLIGHPATH}":"DIMM18"},{"{#VIRTUALLIGHPATH}":"DIMM19"},{"{#VIRTUALLIGHPATH}":"DIMM20"},{"{#VIRTUALLIGHPATH}":"DIMM21"},{"{#VIRTUALLIGHPATH}":"DIMM22"},{"{#VIRTUALLIGHPATH}":"DIMM23"},{"{#VIRTUALLIGHPATH}":"DIMM24"},{"{#VIRTUALLIGHPATH}":"DIMM25"},{"{#VIRTUALLIGHPATH}":"DIMM26"},{"{#VIRTUALLIGHPATH}":"DIMM27"},{"{#VIRTUALLIGHPATH}":"DIMM28"},{"{#VIRTUALLIGHPATH}":"DIMM29"},{"{#VIRTUALLIGHPATH}":"DIMM30"},{"{#VIRTUALLIGHPATH}":"DIMM31"},{"{#VIRTUALLIGHPATH}":"DIMM32"},{"{#VIRTUALLIGHPATH}":"DIMM33"},{"{#VIRTUALLIGHPATH}":"DIMM34"},{"{#VIRTUALLIGHPATH}":"DIMM35"},{"{#VIRTUALLIGHPATH}":"DIMM36"},{"{#VIRTUALLIGHPATH}":"DIMM37"},{"{#VIRTUALLIGHPATH}":"DIMM38"},{"{#VIRTUALLIGHPATH}":"DIMM39"},{"{#VIRTUALLIGHPATH}":"DIMM40"},{"{#VIRTUALLIGHPATH}":"DIMM41"},{"{#VIRTUALLIGHPATH}":"DIMM42"},{"{#VIRTUALLIGHPATH}":"DIMM43"},{"{#VIRTUALLIGHPATH}":"DIMM44"},{"{#VIRTUALLIGHPATH}":"DIMM45"},{"{#VIRTUALLIGHPATH}":"DIMM46"},{"{#VIRTUALLIGHPATH}":"DIMM47"},{"{#VIRTUALLIGHPATH}":"DIMM48"},{"{#VIRTUALLIGHPATH}":"DIMM49"},{"{#VIRTUALLIGHPATH}":"DIMM50"},{"{#VIRTUALLIGHPATH}":"DIMM51"},{"{#VIRTUALLIGHPATH}":"DIMM52"},{"{#VIRTUALLIGHPATH}":"DIMM53"},{"{#VIRTUALLIGHPATH}":"DIMM54"},{"{#VIRTUALLIGHPATH}":"DIMM55"},{"{#VIRTUALLIGHPATH}":"DIMM56"},{"{#VIRTUALLIGHPATH}":"DIMM57"},{"{#VIRTUALLIGHPATH}":"DIMM58"},{"{#VIRTUALLIGHPATH}":"DIMM59"},{"{#VIRTUALLIGHPATH}":"DIMM60"},{"{#VIRTUALLIGHPATH}":"DIMM61"},{"{#VIRTUALLIGHPATH}":"DIMM62"},{"{#VIRTUALLIGHPATH}":"DIMM63"},{"{#VIRTUALLIGHPATH}":"DIMM64"},{"{#VIRTUALLIGHPATH}":"CHANNELA"},{"{#VIRTUALLIGHPATH}":"CHANNELB"},{"{#VIRTUALLIGHPATH}":"CHANNELC"},{"{#VIRTUALLIGHPATH}":"CHANNELD"},{"{#VIRTUALLIGHPATH}":"CHANNELE"},{"{#VIRTUALLIGHPATH}":"CHANNELF"},{"{#VIRTUALLIGHPATH}":"CHANNELG"},{"{#VIRTUALLIGHPATH}":"CHANNELH"},{"{#VIRTUALLIGHPATH}":"SCALE"},{"{#VIRTUALLIGHPATH}":"QPILINK1"},{"{#VIRTUALLIGHPATH}":"QPILINK2"},{"{#VIRTUALLIGHPATH}":"QPILINK3"},{"{#VIRTUALLIGHPATH}":"QPILINK4"},{"{#VIRTUALLIGHPATH}":"HEARTBEAT"},{"{#VIRTUALLIGHPATH}":"CNFG"},{"{#VIRTUALLIGHPATH}":"LOG"},{"{#VIRTUALLIGHPATH}":"RAID"},{"{#VIRTUALLIGHPATH}":"IOPLANAR"},{"{#VIRTUALLIGHPATH}":"CPUPLANAR"},{"{#VIRTUALLIGHPATH}":"BRD"},{"{#VIRTUALLIGHPATH}":"VRD"},{"{#VIRTUALLIGHPATH}":"LINK"}],"data":{"0":{"{#SPNAME}":"SN# 06AB654"},"1":{"Description":"DIMM 1","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"c1cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"2":{"Description":"DIMM 3","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"bfcb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"3":{"Description":"DIMM 6","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"14cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"4":{"Description":"DIMM 8","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"07cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"5":{"Description":"DIMM 17","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"48cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"6":{"Description":"DIMM 19","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cacb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"7":{"Description":"DIMM 22","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"02cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"8":{"Description":"DIMM 24","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cdcb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"9":{"Description":"DIMM 33","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"a2cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"10":{"Description":"DIMM 35","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"05cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"11":{"Description":"DIMM 38","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"dacb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"12":{"Description":"DIMM 40","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"12cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"13":{"Description":"DIMM 49","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"23cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"14":{"Description":"DIMM 51","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cecb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"15":{"Description":"DIMM 54","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"2ccb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"16":{"Description":"DIMM 56","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"bcca0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"ServerPower":"2","ServerState":"System on\/starting UEFI","17":{"Component":"Planar 3.3V","CurrentValue":"3.32 V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"3.560000","LowerThresholdCritical":"3.040000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"18":{"Component":"Planar 5V","CurrentValue":"5.02 V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"5.580000","LowerThresholdCritical":"4.470000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"19":{"Component":"Planar 12V","CurrentValue":"12.10 V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"13.450000","LowerThresholdCritical":"10.690000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"20":{"Component":"CMOS VBAT","CurrentValue":"3.18 V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"2.100000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"2.250000"},"21":{"Component":"Fan1 Tach","CurrentValue":"24 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"50.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"22":{"Component":"Fan2 Tach","CurrentValue":"24 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"50.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"23":{"Component":"Fan3A Tach","CurrentValue":"34 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"480.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"24":{"Component":"Fan3B Tach","CurrentValue":"34 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"480.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"25":{"Component":"Fan4 Tach","CurrentValue":"25 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"575.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"26":{"Component":"Fan5 Tach","CurrentValue":"24 %","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"575.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"ProductName":"System x3850 X5 \/ x3950 X5","MachineTypeAndModel":"71435GR","SerialNumber":"06AB654","UUID":"BE290FAA6C7611E3AD6340F2E963810C","27":{"FRUNumber":"43V7070","FRUName":"SAS BP 1","SerialNumber":"Y010RW3BP120","MfgID":"USIS"},"28":{"FRUNumber":"47C2450","FRUName":"Memory Card 1","SerialNumber":"Y010BG3BV0R3","MfgID":"CLCN"},"29":{"FRUNumber":"47C2450","FRUName":"Memory Card 3","SerialNumber":"Y010BG3BV057","MfgID":"CLCN"},"30":{"FRUNumber":"47C2450","FRUName":"Memory Card 5","SerialNumber":"Y010BG3BV0JB","MfgID":"CLCN"},"31":{"FRUNumber":"47C2450","FRUName":"Memory Card 7","SerialNumber":"Y010BG3BV0JA","MfgID":"CLCN"},"32":{"FRUNumber":"47C2444","FRUName":"CPU Board","SerialNumber":"Y011UF3BF07J","MfgID":"WIST"},"33":{"FRUNumber":"69Y5945","FRUName":"Power Supply 1","SerialNumber":"K10813C606K","MfgID":"EMER"},"34":{"FRUNumber":"69Y5945","FRUName":"Power Supply 2","SerialNumber":"K10813C606P","MfgID":"EMER"},"35":{"FRUNumber":"88Y5889","FRUName":"System Board","SerialNumber":"Y012BG3CH075","MfgID":"IBM"},"36":{"FirmwareName":"IMM","VersionString":"YUOOF7C-1.41","ReleaseDate":"07\/09\/2013"},"37":{"FirmwareName":"UEFI","VersionString":"G0E179BUS-1.79","ReleaseDate":"07\/28\/2013"},"38":{"FirmwareName":"DSA","VersionString":"DSYTA9C-9.32","ReleaseDate":"03\/21\/2013"},"39":{"FirmwareName":"FPGA","VersionString":"G0UD91A-5.01","ReleaseDate":"08\/09\/2013"},"40":{"Description":"Processor 1","Speed":"2133","Identifier":"3030373038394141","Type":"Central","Family":"Intel Xeon","Cores":"8","Threads":"16","Voltage":"1.087000","Datawidth":"64"},"41":{"Description":"Processor 2","Speed":"2133","Identifier":"3030373038394141","Type":"Central","Family":"Intel Xeon","Cores":"8","Threads":"16","Voltage":"1.087000","Datawidth":"64"},"42":{"Description":"Processor 3","Speed":"2133","Identifier":"3030373038394141","Type":"Central","Family":"Intel Xeon","Cores":"8","Threads":"16","Voltage":"1.087000","Datawidth":"64"},"43":{"Description":"Processor 4","Speed":"2133","Identifier":"3030373038394141","Type":"Central","Family":"Intel Xeon","Cores":"8","Threads":"16","Voltage":"1.087000","Datawidth":"64"},"44":{"Description":"Host Ethernet MAC Address 1","Address":"40:F2:E9:63:81:0C"},"45":{"Description":"Host Ethernet MAC Address 2","Address":"40:F2:E9:63:81:0E"},"46":{"Name":"Power","Color":"5","State":"2"},"47":{"Name":"Fault","Color":"2","State":"4"},"48":{"Name":"Info","Color":"2","State":"4"},"49":{"Name":"CPU","Color":"2","State":"4"},"50":{"Name":"PS","Color":"2","State":"4"},"51":{"Name":"DASD","Color":"2","State":"4"},"52":{"Name":"FAN","Color":"2","State":"4"},"53":{"Name":"MEM","Color":"2","State":"4"},"54":{"Name":"NMI","Color":"2","State":"4"},"55":{"Name":"OVER SPEC","Color":"2","State":"4"},"56":{"Name":"TEMP","Color":"2","State":"4"},"57":{"Name":"SP","Color":"2","State":"4"},"58":{"Name":"Identify","Color":"2","State":"4"},"59":{"Name":"PCI","Color":"2","State":"4"},"60":{"Name":"CPU1","Color":"2","State":"4"},"61":{"Name":"CPU2","Color":"2","State":"4"},"62":{"Name":"CPU3","Color":"2","State":"4"},"63":{"Name":"CPU4","Color":"2","State":"4"},"64":{"Name":"FAN1","Color":"2","State":"4"},"65":{"Name":"FAN2","Color":"2","State":"4"},"66":{"Name":"FAN3","Color":"2","State":"4"},"67":{"Name":"PCI 1","Color":"2","State":"4"},"68":{"Name":"PCI 2","Color":"2","State":"4"},"69":{"Name":"PCI 3","Color":"2","State":"4"},"70":{"Name":"PCI 4","Color":"2","State":"4"},"71":{"Name":"PCI 5","Color":"2","State":"4"},"72":{"Name":"PCI 6","Color":"2","State":"4"},"73":{"Name":"PCI 7","Color":"2","State":"4"},"74":{"Name":"Mem Err Card 1","Color":"2","State":"4"},"75":{"Name":"Mem Err Card 2","Color":"2","State":"4"},"76":{"Name":"Mem Err Card 3","Color":"2","State":"4"},"77":{"Name":"Mem Err Card 4","Color":"2","State":"4"},"78":{"Name":"Mem Err Card 5","Color":"2","State":"4"},"79":{"Name":"Mem Err Card 6","Color":"2","State":"4"},"80":{"Name":"Mem Err Card 7","Color":"2","State":"4"},"81":{"Name":"Mem Err Card 8","Color":"2","State":"4"},"82":{"Name":"Mem Card 1","Color":"2","State":"4"},"83":{"Name":"Mem Card 2","Color":"2","State":"4"},"84":{"Name":"Mem Card 3","Color":"2","State":"4"},"85":{"Name":"Mem Card 4","Color":"2","State":"4"},"86":{"Name":"Mem Card 5","Color":"2","State":"4"},"87":{"Name":"Mem Card 6","Color":"2","State":"4"},"88":{"Name":"Mem Card 7","Color":"2","State":"4"},"89":{"Name":"Mem Card 8","Color":"2","State":"4"},"90":{"Name":"DIMM1","Color":"2","State":"4"},"91":{"Name":"DIMM2","Color":"2","State":"4"},"92":{"Name":"DIMM3","Color":"2","State":"4"},"93":{"Name":"DIMM4","Color":"2","State":"4"},"94":{"Name":"DIMM5","Color":"2","State":"4"},"95":{"Name":"DIMM6","Color":"2","State":"4"},"96":{"Name":"DIMM7","Color":"2","State":"4"},"97":{"Name":"DIMM8","Color":"2","State":"4"},"98":{"Name":"DIMM9","Color":"2","State":"4"},"99":{"Name":"DIMM10","Color":"2","State":"4"},"100":{"Name":"DIMM11","Color":"2","State":"4"},"101":{"Name":"DIMM12","Color":"2","State":"4"},"102":{"Name":"DIMM13","Color":"2","State":"4"},"103":{"Name":"DIMM14","Color":"2","State":"4"},"104":{"Name":"DIMM15","Color":"2","State":"4"},"105":{"Name":"DIMM16","Color":"2","State":"4"},"106":{"Name":"DIMM17","Color":"2","State":"4"},"107":{"Name":"DIMM18","Color":"2","State":"4"},"108":{"Name":"DIMM19","Color":"2","State":"4"},"109":{"Name":"DIMM20","Color":"2","State":"4"},"110":{"Name":"DIMM21","Color":"2","State":"4"},"111":{"Name":"DIMM22","Color":"2","State":"4"},"112":{"Name":"DIMM23","Color":"2","State":"4"},"113":{"Name":"DIMM24","Color":"2","State":"4"},"114":{"Name":"DIMM25","Color":"2","State":"4"},"115":{"Name":"DIMM26","Color":"2","State":"4"},"116":{"Name":"DIMM27","Color":"2","State":"4"},"117":{"Name":"DIMM28","Color":"2","State":"4"},"118":{"Name":"DIMM29","Color":"2","State":"4"},"119":{"Name":"DIMM30","Color":"2","State":"4"},"120":{"Name":"DIMM31","Color":"2","State":"4"},"121":{"Name":"DIMM32","Color":"2","State":"4"},"122":{"Name":"DIMM33","Color":"2","State":"4"},"123":{"Name":"DIMM34","Color":"2","State":"4"},"124":{"Name":"DIMM35","Color":"2","State":"4"},"125":{"Name":"DIMM36","Color":"2","State":"4"},"126":{"Name":"DIMM37","Color":"2","State":"4"},"127":{"Name":"DIMM38","Color":"2","State":"4"},"128":{"Name":"DIMM39","Color":"2","State":"4"},"129":{"Name":"DIMM40","Color":"2","State":"4"},"130":{"Name":"DIMM41","Color":"2","State":"4"},"131":{"Name":"DIMM42","Color":"2","State":"4"},"132":{"Name":"DIMM43","Color":"2","State":"4"},"133":{"Name":"DIMM44","Color":"2","State":"4"},"134":{"Name":"DIMM45","Color":"2","State":"4"},"135":{"Name":"DIMM46","Color":"2","State":"4"},"136":{"Name":"DIMM47","Color":"2","State":"4"},"137":{"Name":"DIMM48","Color":"2","State":"4"},"138":{"Name":"DIMM49","Color":"2","State":"4"},"139":{"Name":"DIMM50","Color":"2","State":"4"},"140":{"Name":"DIMM51","Color":"2","State":"4"},"141":{"Name":"DIMM52","Color":"2","State":"4"},"142":{"Name":"DIMM53","Color":"2","State":"4"},"143":{"Name":"DIMM54","Color":"2","State":"4"},"144":{"Name":"DIMM55","Color":"2","State":"4"},"145":{"Name":"DIMM56","Color":"2","State":"4"},"146":{"Name":"DIMM57","Color":"2","State":"4"},"147":{"Name":"DIMM58","Color":"2","State":"4"},"148":{"Name":"DIMM59","Color":"2","State":"4"},"149":{"Name":"DIMM60","Color":"2","State":"4"},"150":{"Name":"DIMM61","Color":"2","State":"4"},"151":{"Name":"DIMM62","Color":"2","State":"4"},"152":{"Name":"DIMM63","Color":"2","State":"4"},"153":{"Name":"DIMM64","Color":"2","State":"4"},"154":{"Name":"Channel A","Color":"2","State":"4"},"155":{"Name":"Channel B","Color":"2","State":"4"},"156":{"Name":"Channel C","Color":"2","State":"4"},"157":{"Name":"Channel D","Color":"2","State":"4"},"158":{"Name":"Channel E","Color":"2","State":"4"},"159":{"Name":"Channel F","Color":"2","State":"4"},"160":{"Name":"Channel G","Color":"2","State":"4"},"161":{"Name":"Channel H","Color":"2","State":"4"},"162":{"Name":"Scale","Color":"2","State":"4"},"163":{"Name":"QPI Link 1","Color":"5","State":"2"},"164":{"Name":"QPI Link 2","Color":"5","State":"2"},"165":{"Name":"QPI Link 3","Color":"5","State":"2"},"166":{"Name":"QPI Link 4","Color":"5","State":"2"},"167":{"Name":"Heartbeat","Color":"5","State":"3"},"168":{"Name":"CNFG","Color":"2","State":"4"},"169":{"Name":"LOG","Color":"2","State":"4"},"170":{"Name":"RAID","Color":"2","State":"4"},"171":{"Name":"IO Planar","Color":"2","State":"4"},"172":{"Name":"CPU Planar","Color":"2","State":"4"},"173":{"Name":"BRD","Color":"2","State":"4"},"174":{"Name":"VRD","Color":"2","State":"4"},"175":{"Name":"LINK","Color":"2","State":"4"}}}';
        echo "<pre>";
        print_r(json_decode($o,true));die;




        $str = '
<?xml version="1.0" encoding="UTF-8" ?><s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:wxf="http://schemas.xmlsoap.org/ws/2004/09/transfer"><s:Header><wsa:To>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:To><wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetProcessorInfoResponse</wsa:Action><wsa:RelatesTo>dt:1516966358631</wsa:RelatesTo><wsa:From><wsa:Address>http://172.16.253.181/wsman</wsa:Address></wsa:From><wsa:MessageID>uuid:7d95f6c7-453c-47ee-9a46-9a8e759c961e</wsa:MessageID></s:Header><s:Body><GetProcessorInfoResponse><Processor><ProcessorInfo><Description>Processor 1</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 2</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 3</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 4</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo></Processor></GetProcessorInfoResponse></s:Body></s:Envelope>';



        $r = '/<s:Body>(.*)<\/s:Body>/i';

        preg_match($r,$str,$data);
        $xml = simplexml_load_string($data[1]);
        echo "<pre>";


        $res = json_decode(json_encode($xml),true);



        print_r($res);die;




        $j = '{"0":{"{#SPNAME}":"SN#06AB654"},"1":{"Description":"DIMM1","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"c1cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"2":{"Description":"DIMM3","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"bfcb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"3":{"Description":"DIMM6","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"14cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"4":{"Description":"DIMM8","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"07cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"5":{"Description":"DIMM17","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"48cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"6":{"Description":"DIMM19","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cacb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"7":{"Description":"DIMM22","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"02cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"8":{"Description":"DIMM24","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cdcb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"9":{"Description":"DIMM33","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"a2cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"10":{"Description":"DIMM35","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"05cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"11":{"Description":"DIMM38","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"dacb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"12":{"Description":"DIMM40","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"12cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"13":{"Description":"DIMM49","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"23cb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"14":{"Description":"DIMM51","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"cecb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"15":{"Description":"DIMM54","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"2ccb0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"16":{"Description":"DIMM56","PartNumber":"HMT31GR7EFR8A-G7","SerialNumber":"bcca0230","ManufactureDate":"5113","Type":"DDR3","Size":"8"},"ServerPower":"2","ServerState":"Systemon\/startingUEFI","17":{"Component":"Planar3.3V","CurrentValue":"3.32V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"3.560000","LowerThresholdCritical":"3.040000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"18":{"Component":"Planar5V","CurrentValue":"5.02V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"5.580000","LowerThresholdCritical":"4.470000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"19":{"Component":"Planar12V","CurrentValue":"12.10V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"13.450000","LowerThresholdCritical":"10.690000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"20":{"Component":"CMOSVBAT","CurrentValue":"3.18V","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"2.100000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"2.250000"},"21":{"Component":"Fan1Tach","CurrentValue":"24%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"50.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"22":{"Component":"Fan2Tach","CurrentValue":"24%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"50.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"23":{"Component":"Fan3ATach","CurrentValue":"34%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"480.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"24":{"Component":"Fan3BTach","CurrentValue":"34%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"480.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"25":{"Component":"Fan4Tach","CurrentValue":"25%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"575.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"26":{"Component":"Fan5Tach","CurrentValue":"24%","UpperThresholdFatal":"-1.000000","LowerThresholdFatal":"-1.000000","UpperThresholdCritical":"-1.000000","LowerThresholdCritical":"575.000000","UpperThresholdNonCritical":"-1.000000","LowerThresholdNonCritcal":"-1.000000"},"27":{"Description":"HostEthernetMACAddress1","Address":"40:F2:E9:63:81:0C"},"28":{"Description":"HostEthernetMACAddress2","Address":"40:F2:E9:63:81:0E"}}';



        $data = json_decode($j,true);

        echo "<pre>";
        print_r($data);
        die;



//        foreach ($data as $k=>$vo){
//            if(is_array($vo)){
//                foreach ($vo as $i=>$v){
//                    $data[$k][$i] = str_replace(' ','',$v);
//                }
//            }else{
//                $data[$k] = str_replace(' ','',$vo);
//            }
//        }
//
//        echo "<pre>";

//        die;

//
//        echo strtoupper('Processor&2.THREADS');die;
//
//        print_r($data);






        $key = 'PROCESSOR2.THREADS';
        $key = strtoupper("SN#06AB654");

        $r = preg_replace('/{#|}/','',$key);

        $rs = explode(".",$r);

        print_r($rs);



        if(count($rs)>1){
            foreach ($data as $vo){
                if(is_array($vo)){
                    foreach ($vo as $k=>$v){
                        $v = str_replace(' ','',$v);
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
                foreach ($vo as $k=>$v){
                    $v = str_replace(' ','',$v);
                    if(strtoupper($v)==$rs[0]){
                        echo $v;exit();
                    }
                }
            }


            if(!is_array($vo)){
                if( strtoupper($vo) == $rs[0] ) {
                    echo $vo;exit();
                }
            }
        }



        die;


        foreach ($data as $vo){
            if(is_array($vo)){
                foreach ($vo as $k=>$v){
                    if($v==$rs[0]){
                        foreach ($vo as  $j=>$l){
                            if( strtoupper($j) == $rs[1]){
                                echo $l;exit();
                            }
                        }
                    }
                }
            }
        }

        foreach ($data as $k=>$vo){
            if( $vo ==$rs[0]) {
                echo $vo;exit();
            }
        }









        die;

        $rs = explode(".",'DIMM 3.Type');

        foreach ($data as $k=>$vo){
            if(is_array($vo)){
                foreach ($vo as $k=>$v){
                    if($v==$rs[0]){
                        echo $vo[$rs[1]];
                        exit();
                    }
                }


            }


        }


        echo "<pre>";



//        print_r(json_decode($j,true));
        die;














        $str = '
<?xml version="1.0" encoding="UTF-8" ?><s:Envelope xmlns:s="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:wxf="http://schemas.xmlsoap.org/ws/2004/09/transfer"><s:Header><wsa:To>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:To><wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetProcessorInfoResponse</wsa:Action><wsa:RelatesTo>dt:1516966358631</wsa:RelatesTo><wsa:From><wsa:Address>http://172.16.253.181/wsman</wsa:Address></wsa:From><wsa:MessageID>uuid:7d95f6c7-453c-47ee-9a46-9a8e759c961e</wsa:MessageID></s:Header><s:Body><GetProcessorInfoResponse><Processor><ProcessorInfo><Description>Processor 1</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 2</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 3</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo><ProcessorInfo><Description>Processor 4</Description><Speed>2133</Speed><Identifier>3030373038394141</Identifier><Type>Central</Type><Family>Intel Xeon</Family><Cores>8</Cores><Threads>16</Threads><Voltage>1.087000</Voltage><Datawidth>64</Datawidth></ProcessorInfo></Processor></GetProcessorInfoResponse></s:Body></s:Envelope>';



        $r = '/<s:Body>(.*)<\/s:Body>/i';

        preg_match($r,$str,$data);
        $xml = simplexml_load_string($data[1]);
        echo "<pre>";


        $res = json_decode(json_encode($xml),true);



        print_r($res);



        $vpd= $res['GetVitalProductDataResponse']['VPD'];
        $vp = [];
        foreach ($vpd as $k=>$vo){
            $vp[] = array(
                "{#FirmwareName}" => $vo['FirmwareName']
            );
        }
        print_r($vp);





    }



}