<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 22:06
 */

namespace  app\models\INSPUR;

use Yii;
use yii\helpers\ArrayHelper;

class BaseCurl
{
    //yii cli 模式下这个缓存找不到路径，故缓存会失败

    public $client;

    protected $id;
    protected $user;
    protected $pwd;


    protected $cookie;

    protected $auth;

    protected $csrfToken;

    public $allOptions=[];

    public $allData=[];

    static $timeOut = 300;//缓存时间 s

    static $BASE_PATH = '/usr/local/src/first/web/curl_data/';

    public $path;



    public function __construct($ip,$user,$pwd)
    {
        $this->ip = $ip;
        $this->user = $user;
        $this->pwd = $pwd;

        $this->path = self::$BASE_PATH.$ip;

        $this->createClient();
    }



    public function getClient(){
        if (!$this->client) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

    private  function createClient(){
        $this->client=curl_init();
        curl_setopt($this->client,CURLOPT_POST,1);
        curl_setopt($this->client,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($this->client,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($this->client,CURLOPT_HEADER,0);
        curl_setopt($this->client,CURLOPT_RETURNTRANSFER,1);
        return $this->client;
    }


    public function login(){
        //$this->logout();
        $url = 'http://'.$this->ip.'/rpc/WEBSES/create.asp';
        $cookie = "test=1; ";
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_setopt($this->client,CURLOPT_POST,1);
        curl_setopt($this->client, CURLOPT_POSTFIELDS, "WEBVAR_USERNAME=".$this->user."&WEBVAR_PASSWORD=".$this->pwd);
        curl_setopt($this->client,CURLOPT_COOKIE,$cookie);

        $res = curl_exec($this->client);
        $re = '/\[([\s\S]*)\]/';

        preg_match($re,$res,$json);
        if(!empty($json)){
            $this->cookie = 'test=1;';
            $n = preg_replace('/\'/','"',$json[1]);
            $arr = json_decode('['.$n.']',true);

            $this->cookie .= ';SessionCookie='.$arr[0]['SESSION_COOKIE'];
            $this->cookie .= ';SESSION_ID='.$arr[0]['SESSION_ID'];
            $this->cookie .= ';BMC_IP_ADDR='.$arr[0]['BMC_IP_ADDR'];
            $this->cookie .= ';CSRFtoken='.$arr[0]['CSRFTOKEN'];

            $this->cookie .= ';Username='.$this->user;
            $this->cookie .= ';gMultiLAN='.true;

            $this->csrfToken = $arr[0]['CSRFTOKEN'];
            $this->auth = true;
        }
    }

    //硬件监控
    public function getAllSensors(){
        $url = 'http://'.$this->ip.'/rpc/getallsensors.asp';
        $str1 = $this->exec($url);
        $this->hardWare($str1);
    }

    //fur信息
    public function fur(){
        $url = 'http://'.$this->ip.'/rpc/getfruinfo.asp';
        $str1 = $this->exec($url);

        $re = '/\[([\s\S]*)\]/';
        preg_match($re,$str1,$arr1);
        if(!empty($arr1)){
            preg_match('/{.*?}/',$arr1[1],$arr2);
            if(!empty($arr2)){
                $json = preg_replace('/\'/','"',$arr2[0]);
                $arr = json_decode($json,true);
                $fruOptions = [];
                foreach ($arr as $k=>$vo){
                    $k = strtoupper($k);
                    $fruOptions[] = array(
                        "{#{$k}}" => $k
                    );
                }
                $this->allOptions =  ArrayHelper::merge($this->allOptions,$fruOptions);
                $this->allData = ArrayHelper::merge($this->allData,$arr);
            }
        }
    }

    //版本信息
    public function HwVersion(){
        $url = 'http://'.$this->ip.'/rpc/HwVersion.asp';
        $str1 = $this->exec($url);
        //fpgaVersion  start
        $this->fpgaVersion($str1);

        //Bios  version start
        $this->biosVersion($str1);

        //me version
        $this->meVersion($str1);

        //mrb
        $this->mrbVersion($str1);

        $this->fanAndHddVersion($str1);


    }


    //资产信息
    public function getHWInfo(){
        $url = 'http://'.$this->ip.'/rpc/getHWInfo.asp';
        $str1 = $this->exec($url);
        $this->cpu($str1);
        $this->pcie($str1);
        $this->memory($str1);
    }


    /** FPGAVersion
     * @param $str1
     */
    protected function fpgaVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_FPGA_VERSION';
        $fpgaArr=$this->re($key,$str1);
        $this->allOptions =  ArrayHelper::merge($this->allOptions,[["{#FPGAVERSION}"=>"FPGAVERSION"]]);
        if($fpgaArr){
            if(isset($fpgaArr[0]) && !empty($fpgaArr) ){
                $fpgaVer  = '';
                foreach ($fpgaArr[0] as $vo){
                    $fpgaVer .= $vo.'.';
                }
                if($fpgaVer) $fpgaVer = substr($fpgaVer,0,-1);
                $this->allData = ArrayHelper::merge($this->allData,[['FPGAVERSION'=>$fpgaVer]]);
            }
        }
    }


    /**bios version
     * @param $str1
     */
    protected function biosVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_BIOS_VERSION';
        $arr2=$this->re($key,$str1);

        $this->allOptions =  ArrayHelper::merge($this->allOptions,[         ["{#BIOSVERSION}"=>"BIOSVERSION"],["{#BIOSBUILDTIME}"=>"BIOSBUILDTIME"]
        ]);
        if($arr2){
            if( isset($arr2[0]) && !empty($arr2[0])   ){
                $this->allData = ArrayHelper::merge($this->allData,[
                    ["BIOSVERSION"=> $arr2[0]['BiosVersion'] ],["BIOSBUILDTIME"=>$arr2[0]['BiosBuildTime'] ]
                ]);
            }
        }

    }


    /**MEVERSION
     * @param $str1
     */
    protected function meVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_ME_VERSION';
        $arr2=$this->re($key,$str1);

        $this->allOptions =  ArrayHelper::merge($this->allOptions,[["{#MEVERSION}"=>"MEVERSION"]
        ]);
        if($arr2){
            if( isset($arr2[0]) && !empty($arr2[0])){
                $this->allData = ArrayHelper::merge($this->allData,[
                    ["MEVERSION"=> $arr2[0]['MEVersion'] ],
                ]);
            }
        }
    }

    /**MRBVERSION
     * @param $str1
     */
//    protected function mrbVersion($str1){
//        $key = 'WEBVAR_STRUCTNAME_MRB_VERSION';
//        $arr2=$this->re($key,$str1);
//
//        $this->allOptions =  ArrayHelper::merge($this->allOptions,[["{#MEVERSION}"=>"MEVERSION"]
//        ]);
//        if($arr2){
//            $opt = [] ;
//            $val = [];
//            foreach ($arr2 as $k=>$vo ){
//                $key = 'MRBVERSION'.$k;
//                $opt[] = [
//                    'MRBVERSION' => $key
//                ];
//                $val[] = [ $key=> $vo['MRBVersion'] ];
//            }
//
//            $this->allOptions =  ArrayHelper::merge($this->allOptions,$opt);
//            $this->allData = ArrayHelper::merge($this->allData,$val);
//
//        }
//    }

    /**fanAndHddVersion
     * @param $str1
     */
    protected function fanAndHddVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_FANPSOC_VERSION';
        $arr2=$this->re($key,$str1);

        $this->allOptions =  ArrayHelper::merge($this->allOptions,[         ["{#FANPSOCVERSION}"=>"FANPSOCVERSION"],["{#HDDPSOCVERSION}"=>"HDDPSOCVERSION"]
        ]);
        if($arr2){
            if( isset($arr2[0]) && !empty($arr2[0])   ){
                $fanVer  = $arr2[0]['fanPsocVer0'].'.'.$arr2[0]['fanPsocVer1'].'.'.$arr2[0]['fanPsocVer2'];
                $hddVer = $arr2[0]['hddPsocVer0'].'.'.$arr2[0]['hddPsocVer1'].'.'.$arr2[0]['hddPsocVer2'];
                $this->allData = ArrayHelper::merge($this->allData,[
                    ["FANPSOCVERSION"=> $fanVer ],["HDDPSOCVERSION"=> $hddVer ]
                ]);
            }
        }
    }

    /** mrb version
     * @param $str1
     */
    protected function mrbVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_MRB_VERSION';
        $arr2=$this->re($key,$str1);
        if( $arr2){
            $mrbOptions = [];
            $mrbValues = [];
            foreach ($arr2 as $k=>$vo ){
                if(!empty($vo)){
                    $key = 'MRBVERSION'.$k;
                    $mrbOptions[] = [
                        '{#MRBVERSION}' => $key
                    ];
                    $mrbValues[] = [
                        $key => $vo['MRBVersion']
                    ];
                }
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$mrbOptions);
            $this->allData = ArrayHelper::merge($this->allData,$mrbValues);
        }
    }




    /**资产信息 cpu
     * @param $str1
     */
    protected function cpu($str1){
        $key = 'WEBVAR_STRUCTNAME_CPUINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $options = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $options[] = [ '{#CPUSOCKET}'=> strtoupper($vo['CPUSocket'])  ];
                }
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$options);
            $this->allData = ArrayHelper::merge($this->allData,$arr2);
        }
    }

    /**资产信息 pcie
     * @param $str1
     */
    protected function pcie($str1){
        $key = 'WEBVAR_STRUCTNAME_GETPCIEINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $options = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $options[] = [ '{#PCIESLOT}'=> strtoupper($vo['PCIESlot']) ];
                }
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$options);
            $this->allData = ArrayHelper::merge($this->allData,$arr2);
        }
    }

    /**资产信息 内存
     * @param $str1
     */
    protected function memory($str1){
        $key = 'WEBVAR_STRUCTNAME_GETMEMINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $options = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $options[] = [ '{#MEMDIMM}'=> strtoupper($vo['MemDimm']) ];
                }
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$options);
            $this->allData = ArrayHelper::merge($this->allData,$arr2);
        }
    }


    /** SensorState（关闭）
     * 电压 2
     * 温度 1
     * 电流 3
     * 风扇 4
     * 电源供电 8
     *  驱动器草 13
     * 微控制器 22
     * 从建键值是为了zabbix模板可以区分开就用集和中文说明
     * @param $key
     * @param $str
     * @return array|bool
     */

    public function hardWare($str1){
        $key = 'WEBVAR_STRUCTNAME_HL_GETALLSENSORS';
        $arr2=$this->re($key,$str1);

        if($arr2){
            $opt =  [];
            $val = [];
            $word = 'HARDWARE_';
            foreach ($arr2 as $vo) {
                $type = $vo['SensorType'];
                $k = '';
                switch ($type){
                    case 1: $k = $word.'TEMP';break;
                    case 2: $k = $word.'PV';break;
                    case 3: $k = $word.'AMP';break;
                    case 4: $k = $word.'FAN';break;
                    case 8: $k = $word.'POWER';break;
                    case 13: $k = $word.'DRIVE';break;
                    case 22: $k = $word.'CONTROL';break;
                }
                $opt[] = [ '{#'.$k.'}' => strtoupper($vo['SensorName'])];
                $vo[$k] = $vo['SensorName'];
                $val[] =  $vo;
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$opt);
            $this->allData = ArrayHelper::merge($this->allData,$val);
        }

    }





    public function re($key,$str){
        $re = "/$key :(.*?])/";
        preg_match($re,$str,$arr);
        if(!empty($arr)) {
            $str2 = preg_replace('/\'/', '"', $arr[1]);
            $arr2 = json_decode($str2, true);
            return $arr2 = array_filter($arr2);
        }
        return false;
    }

    public function removeMsg($str){
        return preg_replace('/((\/\*[\s\S]*?\*\/)|(\/\/.*)|(#.*))|(\\n)/', "", $str);
    }





    public function exec($url){
        if(!$this->auth){
            $this->login();
        }
        $headers = array(
            'CSRFTOKEN:'.$this->csrfToken
        );
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_setopt($this->client,CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->client, CURLOPT_TIMEOUT, 20 );
        curl_setopt($this->client, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($this->client);
        return $this->removeMsg($res);
    }





    public function logout(){
        $url = 'http://'.$this->ip.'/rpc/WEBSES/logout.asp';
        curl_setopt($this->client,CURLOPT_POST,0);
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_exec($this->client);
        curl_close($this->client);
    }



//    public function run(){
//        $cache = \Yii::$app->cache;
//        $cache->flush();
//        $data = $cache->get($this->ip.'data');
//        if(!$data){
//            $this->login();
//            $this->getAllSensors();
//            $this->fur();
//            $this->HWVersion();
//            $this->getHWInfo();
//            $this->logout();
//            $data =  [
//                'options' => $this->allOptions,
//                'data' => $this->allData,//$this->replaceKong($this->allData)
//            ];
//            $cache->set($this->ip.'data',$data,self::$timeOut);
//        }
//        return $data;
//    }


    public function run(){
        if(file_exists($this->path)){
            $file_json = file_get_contents($this->path);
            $file_arr = json_decode($file_json,true);

            //检查超时
            if($file_arr['time'] + self::$timeOut < time() ){ //超时
                $pid= pcntl_fork();
                if ($pid == -1) {
                    die('could not fork');
                }elseif (!$pid) {
                    //这里是子进程
                    $this->hand($file_arr);
                    exit();
                }

            }
            return $file_arr;

        }else{//第一次
            $this->get();
            $data =  [
                'options' => $this->allOptions,
                'data' => $this->allData,
                'time' => time(),
            ];
            $this->save($data);
            return json_encode($data);
        }
    }


    public function get(){
        $this->login();
        $this->getAllSensors();
        $this->fur();
        $this->HWVersion();
        $this->getHWInfo();
        $this->logout();
    }

    public function hand($file_arr){
        if(!isset($file_arr['hand'])){
            $file_arr['hand'] = true;
            $this->save($file_arr);

            $this->get();
            $this->logout();
            if(!empty($this->allOptions)){
                $data =  [
                    'options' => $this->allOptions,
                    'data' => $this->allData,//$this->replaceKong($this->allData)
                    'time' => time()
                ];
                $this->save($data);
            }
        }
    }


    public function save($data){
        file_put_contents($this->path,json_encode($data));
    }



}