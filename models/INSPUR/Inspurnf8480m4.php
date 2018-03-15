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

class Inspurnf8480m4 extends  \app\components\BaseCurl
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
        $this->power();//资产电源
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
        $key = 'WEBVAR_STRUCTNAME_HL_GETALLFRUINFO';
        $fruArr=$this->re($key,$str1);
        if( !empty($fruArr) && isset($fruArr[0]) ){
            //只要部分数据
            $tmp = array(
                'FRUDeviceID','FRUDeviceName','PI_ProductInfoAreaFormatVersion','BI_MfgDateTime','BI_BoardMfr','BI_BoardProductName','PI_MfrName','PI_ProductName','PI_ProductVersion','PI_ProductSerialNum','PI_AssetTag'
            );
            $val=[];
            foreach ($fruArr[0] as $k=> $vo){
                if(in_array($k,$tmp)){
                    $val[] = ['{#NAME}'=>strtoupper($k),'VALUE'=>$vo];
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['FRU'=>$val]);
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

        if( !empty($fpgaArr) && isset($fpgaArr[0])){
            $fpgaVer  = '';
            foreach ($fpgaArr[0] as $vo){
                $fpgaVer .= $vo.'.';
            }
            if($fpgaVer) $fpgaVer = substr($fpgaVer,0,-1);
            $this->allData = ArrayHelper::merge($this->allData,['FPGAVERSION'=>[['{#NAME}'=>'FPGAVERSION','VALUE'=>$fpgaVer]]]);
        }

    }


    /**bios version
     * @param $str1
     */
    protected function biosVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_BIOS_VERSION';
        $arr2=$this->re($key,$str1);
        if(!empty($arr2[0])&& isset($arr2[0]) ){
            $this->allData = ArrayHelper::merge($this->allData,['BIOSVERSION'=>[['{#NAME}'=>'BIOSVERSION','VALUE'=>$arr2[0]['BiosVersion']]]]);
//            ["BIOSBUILDTIME"=>$arr2[0]['BiosBuildTime'] ]
        }


    }


    /**MEVERSION
     * @param $str1
     */
    protected function meVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_ME_VERSION';
        $arr2=$this->re($key,$str1);
        if( !empty($arr2[0]) && isset($arr2[0]) ){
            $this->allData = ArrayHelper::merge($this->allData,[
                'MEVERSION' =>[ ["{#NAME}"=>'MEVERSION','VALUE'=>$arr2[0]['MEVersion']],
                ]]);
        }

    }



    /**fanAndHddVersion
     * @param $str1
     */
    protected function fanAndHddVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_FANPSOC_VERSION';
        $arr2=$this->re($key,$str1);

        if( isset($arr2[0]) && !empty($arr2[0])   ){
            $fanVer  = $arr2[0]['fanPsocVer0'].'.'.$arr2[0]['fanPsocVer1'].'.'.$arr2[0]['fanPsocVer2'];
            $hddVer = $arr2[0]['hddPsocVer0'].'.'.$arr2[0]['hddPsocVer1'].'.'.$arr2[0]['hddPsocVer2'];

            $this->allData = ArrayHelper::merge($this->allData,[
                'FANPSOCVERSION' => [["{#NAME}"=>'FANPSOCVERSION','VALUE'=>$fanVer],
                ]]);
            $this->allData = ArrayHelper::merge($this->allData,[
                'HDDPSOCVERSION' => [["{#NAME}"=>'HDDPSOCVERSION','VALUE'=>$hddVer],
                ]]);
        }

    }

    /** mrb version
     * @param $str1
     */
    protected function mrbVersion($str1){
        $key = 'WEBVAR_STRUCTNAME_MRB_VERSION';
        $arr2=$this->re($key,$str1);
        if( $arr2){
            $val = [];
            foreach ($arr2 as $k=>$vo ){
                if(!empty($vo)){
                    $val[] = array(
                        '{#NAME}' => 'MRBVERSION'.$k,
                        'VALUE' => $vo['MRBVersion']
                    );
                }
            }

            $this->allData = ArrayHelper::merge($this->allData,[
                'MRBVERSION'=>$val
            ]);
        }
    }


    /**资产信息 cpu
     * @param $str1
     */
    protected function cpu($str1){
        $key = 'WEBVAR_STRUCTNAME_CPUINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = strtoupper($vo['CPUSocket']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['CPU'=>$val]);
        }
    }

    /**资产信息 pcie
     * @param $str1
     */
    protected function pcie($str1){
        $key = 'WEBVAR_STRUCTNAME_GETPCIEINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = strtoupper($vo['PCIESlot']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['PCIE'=>$val]);
        }
    }

    /**资产信息 内存
     * @param $str1
     */
    protected function memory($str1){
        $key = 'WEBVAR_STRUCTNAME_GETMEMINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = strtoupper($vo['MemDimm']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['MEMORY'=>$val]);
        }
    }

    /**资产信息 电源
     * @param $str1
     */
    protected function power(){
        $url = 'http://'.$this->ip.'/rpc/getallpsuinfo.asp';
        $str1 = $this->exec($url);

        $key = 'WEBVAR_STRUCTNAME_GETPSUINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'POWER'.strtoupper($vo['Id']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['POWER'=>$val]);
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

                $vo['{#NAME}'] = strtoupper($vo['SensorName']);
                $val[$k][] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,$val);
        }

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
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 10 );
        curl_setopt($this->getClient(), CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($this->getClient());

        if(curl_errno($this->getClient()) == CURLE_OPERATION_TIMEDOUT)
        {
            $msg = '获取：'.$url.' 超时';
            \Yii::error($msg);
            //exit();
            return null;
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


}