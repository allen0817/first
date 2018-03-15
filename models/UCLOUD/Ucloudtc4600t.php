<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:39
 */

namespace app\models\UCLOUD;


use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Ucloudtc4600t extends  \app\components\BaseCurl
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
//$arr = $this->re('WEBVAR_STRUCTNAME_WEB_SESSION',$res);

        $str1 = preg_replace('/((\/\*[\s\S]*?\*\/)|(\/\/.*)|(#.*))|(\\n)/', "", $res);
        preg_match('/WEBVAR_STRUCTNAME_WEB_SESSION :(.*?])/', $str1, $fpg);

        if(isset($fpg[1])){
            $arr3 = preg_replace('/\'/', '"', $fpg[1]);
            $arr4 = json_decode($arr3, true);
            $arr = array_filter($arr4);
            if(!empty($arr) && isset($arr[0])){
                $this->cookie = 'test=1;';
                $this->cookie .= ';SessionCookie='.$arr[0]['SESSION_COOKIE'];
                $this->cookie .= ';BMC_IP_ADDR='.$arr[0]['BMC_IP_ADDR'];
                $this->cookie .= ';Username='.$this->user;
                $this->cookie .= ';gMultiLAN='.true;

                $this->csrfToken = $arr[0]['CSRFTOKEN'];
                $this->auth = true;
            }
        }else{
            \Yii::error($this->ip.' login error');
        }
    }

    /**
     * @return mixed
     */
    protected function getData()
    {
        $this->login();
        $this->fru();  //fru信息
        $this->getAllSensors(); //硬件监控


        $this->cpu();
        //$this->pcie(); 这个值不知是什么意思先不要
        $this->memory();
        $this->hdd();
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


    /**资产信息 cpu
     * @param $str1
     */
    protected function cpu(){
        $url = 'http://'.$this->ip.'/rpc/sugon_get_cpu_info.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_SUGONGETCPUINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'CPU'.strtoupper($vo['CpuNo']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['CPU'=>$val]);
        }
    }

    /**资产信息 pcie
     * @param $str1
     */
    protected function pcie(){
        $url = 'http://'.$this->ip.'/rpc/sugon_get_pci_info.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_SUGONGETPCIEINFO';
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
    protected function memory(){
        $url = 'http://'.$this->ip.'/rpc/sugon_get_mem_info.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_SUGONGETMEMINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $k=>$vo){
                if($vo['memPN']){
                    $vo['{#NAME}'] = 'MEMORY'.$k;
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['MEMORY'=>$val]);
        }
    }

    protected function hdd(){
        $url = 'http://'.$this->ip.'/rpc/sugon_get_hdd_info.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_SUGONGETHDDINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'HDD'.strtoupper($vo['HddNo']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['HDD'=>$val]);
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
     * 曙光只有温度，电压，
     */



    public function hardWare($str1){
        $key = 'WEBVAR_STRUCTNAME_HL_GETALLSENSORS';
        $arr2=$this->re($key,$str1);

        if($arr2){
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
                        break;
                    case 2: $k = $word.'PV';break;
                    case 8:
                        $k = $word.'POWER';
                        break;
                }

                $vo['SensorState'] = $state;
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
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 5 );
        curl_setopt($this->getClient(), CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($this->getClient());

        if(curl_errno($this->getClient()) == CURLE_OPERATION_TIMEDOUT)
        {
            $msg = '获取：'.$url.' 超时';
            \Yii::error($msg);
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
            \Yii::error($e);
        }

    }






}