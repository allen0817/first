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
        }else{
            \Yii::error($this->ip.' login error');
            $this->resetBmc();//重启BMC
            exit();
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
        //$this->HWVersion(); //版本信息
        $this->getHWInfo(); //资产信息
        $this->power();//资产电源
        $this->nic();//网卡
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
        $val = [];
        if( !empty($fruArr) && isset($fruArr[0]) ){
            //只要部分数据
            $val = array(
                'board.mfc' => $fruArr[0]['BI_BoardMfr'],
                'board.name' => $fruArr[0]['BI_BoardProductName'],
                'board.date' => $fruArr[0]['BI_MfgDateTime'],
                'product.mfc' => $fruArr[0]['PI_MfrName'],
                'product.name' => $fruArr[0]['PI_ProductName'],
                'product.serial' => $fruArr[0]['PI_ProductSerialNum'],
                'product.version' => $fruArr[0]['PI_ProductVersion'],
            );
        }
        $val['bmc'] = 1; //能采集数据，bmc一定能登录
        $val['dev.timezone'] = $this->timezone();
        $this->allData = ArrayHelper::merge($this->allData,['local'=>$val]);
    }

    //时区
    public function timezone(){
        $url = 'http://'.$this->ip.'/rpc/getdatetime.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_GETDATETIME';
        $time=$this->re($key,$str1);
        $str = '';
        if( !empty($time) && isset($time[0]) ){
            $hour = floor( $time[0]['UTCMINUTES']/ 60 ); //返回的是分钟
            if ($hour > 0) $str = 'GMT + '.$hour.' : 00';
            else $str = 'GMT - '.$hour.' : 00';
        }
        return $str;
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

    //网卡信息
    public function nic(){
        $url = 'http://'.$this->ip.'/rpc/getalllancfg.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_GETALLNETWORKCFG';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'eth'.$vo['channelNum'];
                    $vo['net.ifPhysAddress'] = $vo['macAddress'];//mac 
                    $vo['net.if.ip.addr'] = $vo['v4IPAddr']; //v4IPAddr
                    $vo['net.if.mask'] = $vo['v4Subnet']; //v4Subnet
                    $vo['net.if.gateway'] = $vo['v4Gateway']; //v4Gateway
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['nic'=>$val]);
        }
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
                    $vo['{#NAME}'] = $vo['CPUSocket'];
                    //$vo['cpu.type'] = $vo['CPUType'];
                    $vo['cpu.l1.cache'] = $vo['L1Cache'] * 1024 ;//'KB';
                    $vo['cpu.l2.cache'] = $vo['L2Cache'] * 1024;
                    $vo['cpu.l3.cache'] = $vo['L3Cache'] * 1024; //
                    $tmp = explode(' ',$vo['CPUVersion']);
                    $vo['cpu.frequency'] = end($tmp);
                    $vo['cpu.type'] = isset($tmp[0]) ? $tmp[0] : '';
                    $vo['cpu.version'] = $vo['CPUVersion'];
                    $vo['cpu.name'] = $vo['CPUSocket'];
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['cpu'=>$val]);
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
                    $vo['{#NAME}'] = $vo['PCIESlot'];
                    $vo['pcie.speed'] = $vo['PCIELinkSpeed'];
                    $vo['pcie.rated.width'] = $vo['PCIELinkWidth'];
                    $vo['pcie.type'] = $this->getPcieType( $vo['PCIEClass']);
                    $vo['pcie.mfc'] = $this->getPcieMfc( $vo['PCIEVendorId'] );
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['pcie'=>$val]);
        }
    }

    /**返回pcie 类型，没有找到他们的映射关系，用页面几个值的
     * @param $class
     * @return string
     */
    private function getPcieType($class){
        switch ($class){
            case  1 : $str = '大容量存储控制器';break;
            case  2 : $str = '网络控制器';break;
            case  12 : $str = '串行总线控制器';break;
            default : $str = '未知';break;
        }
        return $str;
    }
    private function getPcieMfc($vendor){
        switch ($vendor){
            case 4215 : return 'QLogic Corp.';break;
            case 32902 : return 'Intel Corporation';break;
            case 36869 : return '0x9005';break;
            default : return '未知';break;
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
                    $vo['{#NAME}'] = $vo['MemDimm'];
                    $vo['memory.type'] = $vo['MemType'] == 26 ? 'DDR4':'DDR3';
                    $vo['memory.mfc'] = $vo['MemManufacturer'];
                    $vo['memory.size.total'] = $vo['memSize'];
                    $vo['memory.frequency'] = $vo['MemSpeed'];
                    $vo['memory.frequency.current'] = $vo['MemCurrentSpeed'];
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['memory'=>$val]);
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
                    $vo['{#NAME}'] = 'PSU'.$vo['Id'];
                    $vo['power.mfc'] = $vo['MFRID'];
                    $vo['power.local'] = 'PSU'.$vo['Id'];
                    $vo['power.serial'] = $vo['SN'];
                    $vo['power.version'] = $vo['FWVersion'];
                    $vo['power.status'] = $vo['ErrStatus'] == 65535 ? 1:0;   //他这个0是正常，1是异常
                    $vo['power.rating'] = $vo['PwrInWatts'];
                    $vo['power.in'] = $vo['InputPower'];

                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['power'=>$val]);
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
            $temp = [];
            $voltage= [];
            $amps = [];
            $fan = [];
            $power = [];
            $drive = [];
            $controller = [];
            foreach ($arr2 as $vo) {
                $type = $vo['SensorType'];
                switch ($type){
                    case  1 : //temp
                        $temp[] = array(
                            'temp.name' => $vo['SensorName'],
                            'temp.lower' => $vo['LowNCThresh']/1000,
                            'temp.up' => $vo['HighNCThresh']/1000, // ° C
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;

                    case 2 : // voltage
                        $voltage[] = array(
                            'voltage.name' => $vo['SensorName'],
                            'voltage.lower' => $vo['LowNCThresh']/1000, //V
                            'voltage.up' => $vo['HighNCThresh']/1000,
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;
                    case 3 : // amps
                        $amps[] = array(
                            'amps.name' => $vo['SensorName'],
                            'amps.lower' => $vo['LowNCThresh']/1000, // Amps
                            'amps.up' => $vo['HighNCThresh']/1000,
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;
                    case 4: // fan
                        $fan[] = array(
                            'fan.name' => $vo['SensorName'],
                            'fan.lower' => $vo['LowNCThresh']/1000, //RPM
                            'fan.up' => $vo['HighNCThresh']/1000,
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;

                    case  8 : //power
                        $power[] =  array(
                            'power.name' => $vo['SensorName'],
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;
                    case  13 : //drive
                        $hex = $this->decToHex($vo['SensorReading']);
                        if($hex==8001)  $status = 1;
                        else $status = 0;
                        $drive[] =  array(
                            'drive.name' => $vo['SensorName'],
                            '{#NAME}' => $vo['SensorName'],
                            'drive.status' =>$status ,
                            'drive.value' => '0x'.$hex,
                        );
                        break;
                    case  22 : //controller
                        $controller[] =  array(
                            'controller.name' => $vo['SensorName'],
                            '{#NAME}' => $vo['SensorName'],
                        );
                        break;

                }
            }
            $this->allData = ArrayHelper::merge($this->allData,
                ['temp' => $temp ],
                [ 'voltages' => $voltage ],
                [ 'amps' => $amps ],
                [ 'fan' => $fan ],
                //[ 'power'=> $power ],
                [ 'drive' => $drive ],
                ['controller' => $controller]
            );
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