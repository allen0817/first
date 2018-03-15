<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:39
 */

namespace app\models\INSPUR;


use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Inspurnf8480m4_back0312 extends  \app\components\BaseCurl
{
    protected function login()
    {
        $url = 'http://'.$this->ip.'/rpc/WEBSES/create.asp';
        $cookie = "test=1; ";
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,1);
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, "WEBVAR_USERNAME=".$this->user."&WEBVAR_PASSWORD=".$this->pwd);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$cookie);

        $res = curl_exec($this->getClient());
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

    /**
     * @return mixed
     */
    protected function getData()
    {

        $this->login();
        $this->getAllSensors(); //硬件监控
        $this->fru();  //fru信息
        $this->HWVersion(); //版本信息
        $this->getHWInfo(); //资产信息
        $this->logout();
    }



    //硬件监控
    public function getAllSensors(){
        $url = 'http://'.$this->ip.'/rpc/getallsensors.asp';
        $str1 = $this->exec($url);
        $this->hardWare($str1);
    }

    //fru信息
    public function fru(){
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
     * SensorState
     * 0 关机
     * 1 正常
     *
     * 0x8000
     */

    public function decToHex($n){
        return dechex($n/1000);
    }

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
                $state = 0;
                if($vo['SensorState'] && $vo['SensorReading'] ){
                    $state = 1;
                }
                switch ($type){
                    case 1:
                        $k = $word.'TEMP';
                        if(!$state){
                            if(!$vo['SensorReading'])$state = 0;
                        }
                        break;
                    case 2: $k = $word.'PV';break;
                    case 3: $k = $word.'AMP';break;
                    case 4:
                        $k = $word.'FAN';
                        if(!$state){
                            if($this->decToHex($vo['SensorReading'])==8001)$state = 0;
                        }
                        break;
                    case 8:
                        $k = $word.'POWER';
                        if(!$state){
                            $hex = $this->decToHex($vo['SensorReading']);
                            if($hex==8001 || $hex==8009 || $hex==8081 || $hex==8089 )$state = 1;
                        }
                        break;
                    case 13:
                        $k = $word.'DRIVE';
                        if(!$state){
                            if($this->decToHex($vo['SensorReading'])==8001)$state = 1;
                        }
                        break;
                    case 22:
                        $k = $word.'CONTROL';
                        if(!$state){
                            if($this->decToHex($vo['SensorReading'])==8002)$state = 1;
                            elseif($this->decToHex($vo['SensorReading'])==8001)$state = 0;
                        }
                        break;
                }
                $vo['SensorState'] =  $state;
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


    protected function exec($url){
        if(!$this->auth){
            $this->login();
        }
        $headers = array(
            'CSRFTOKEN:'.$this->csrfToken
        );
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 20 );
        curl_setopt($this->getClient(), CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($this->getClient());
        if($res === false)
        {
            if(curl_errno($this->getClient()) == CURLE_OPERATION_TIMEDOUT)
            {
                return null;
            }
        }
        return $this->removeMsg($res);
    }




    protected function logout(){
        try{
            $url = 'http://'.$this->ip.'/rpc/WEBSES/logout.asp';
            //curl_setopt($this->getClient(),CURLOPT_POST,0);
            curl_setopt($this->getClient(),CURLOPT_URL,$url);
            curl_exec($this->getClient());
            curl_close($this->getClient());
        }catch (Exception $e){

        }

    }

    /** 获取单个监控项的值
     * @return mixed
     */
    public function getVal($key)
    {
        $arr = $this->run();
        if (!isset($arr['data'])) {
            echo "NULL";exit();
        }


        $data = $arr['data'];
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