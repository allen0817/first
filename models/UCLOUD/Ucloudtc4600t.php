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
        $this->fru();  //fru信息
       // $this->getAllSensors(); //硬件监控
        $this->cpu();
        $this->pcie(); //这个值不知是什么意思
        $this->nic();
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
        $val = [];
        if( !empty($fruArr) && isset($fruArr[0]) ){
            //只要部分数据
            $val = array(
                'board.mfc' => $fruArr[0]['BI_BoardMfr'],
                'board.name' => $fruArr[0]['BI_BoardProductName'],

                'product.mfc' => $fruArr[0]['PI_MfrName'],
                'product.name' => $fruArr[0]['PI_ProductName'],
                'product.serial' => $fruArr[0]['PI_ProductSerialNum'],
                'product.version' => $fruArr[0]['PI_ProductVersion'],
            );
        }
        $val['bmc'] = 1; //能采集数据，bmc一定能登录
        $val['dev.timezone'] = $this->getTimeZone();
        $this->allData = ArrayHelper::merge($this->allData,['local'=>$val]);
    }


    protected function getTimeZone(){
        $url = 'http://'.$this->ip.'/rpc/getdatetime.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_GETDATETIME';
        $fruArr=$this->re($key,$str1);
        if( !empty($fruArr) && isset($fruArr[0]) ){
            return $fruArr[0]['TIMEZONE'];
        }
        return '';
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
                    $tmp = explode(" ",$vo['cpuBrandName']);
                    $vo['cpu.type'] = isset($tmp[0]) ? $tmp[0] : '';
                    $vo['cpu.frequency'] = isset($tmp[6]) ? $tmp[6] : ''; //主频
                    $vo['cpu.present']  = $vo['cpuPresent']; // == 1 ? '可用':'不可用';
                    $vo['cpu.qpi.width']  = $this->getCpuQpiWidth($vo['cpuQpiWidth']);
                    $vo['cpu.status']  = $vo['cpuState']; // == 0? '可用':'不可用';
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['cpu'=>$val]);
        }
    }

    private function getCpuQpiWidth($cpuqpiwidth){
        switch ($cpuqpiwidth){
            case 1 : $actionstr = "Q3Q2Q1Q0";break;
            case 2 : $actionstr = "Q1Q0";break;
            case 3 : $actionstr = "Q2Q0";break;
            case 4 : $actionstr = "Q3Q0";break;
            case 5 : $actionstr = "Q2Q1";break;
            case 6 : $actionstr = "Q3Q1";break;
            case 7 : $actionstr = "Q3Q2";break;
            case 8 : $actionstr = "Q0";break;
            case 9 : $actionstr = "Q1";break;
            case 10 : $actionstr = "Q2";break;
            case 11 : $actionstr = "Q3";break;
            default :  $actionstr = '未知';break;
        }
        return $actionstr;
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
                    $vo['{#NAME}'] = 'PCIE'.$vo['pcieNo'];
                    $vo['pcie.present'] = $vo['pcieDevPresent']; //==1 ? 'present':'absent';
                    $vo['pcie.speed'] = $this->getPcieLinkspeed( $vo['PcieLinkSpeed'] );
                    $vo['pcie.rated.width'] = $vo['PcieLinkWidth'];
                    $vo['pcie.type'] = $this->getPcieType($vo['pcieBaseClass'],$vo['pcieSubClass']);
                    $vo['pcie.mfc'] = $this->getPcieMfc($vo['pcieVendorID0']);
                    $vo['pcie.status'] = $vo['pcieState']; //  0 normal
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['pcie'=>$val]);
        }
    }


    private function getPcieType($pcieclass,$pciesubclass){
        $actionstr = '';
        switch($pcieclass) {
            case(0x0):
                if($pciesubclass == 0x01) { $actionstr = 'VGA Card';break;}
                else {  $actionstr = 'Undefined Device';break;}
            case(0x1):
                if($pciesubclass == 0x00) { $actionstr = 'SCSI Card';break;}
                else if($pciesubclass == 0x04) {    $actionstr = 'RAID Card';break;}
                else if($pciesubclass == 0x07) {$actionstr = 'SAS Card';break;}
                else {$actionstr = 'Mass Storage Controller';break;}
            case(0x2):  $actionstr = 'Network Card';break;
            case(0x3):  $actionstr = 'VGA Card';break;
            case(0x4):
                if($pciesubclass == 0x00) {$actionstr = 'Video Card';break;}
                else if($pciesubclass == 0x01) {$actionstr = 'Audio Card';break;}
                else {$actionstr = 'Multimedia Device Card';break;}
            case(0x5):$actionstr = 'Memory Controller Card';break;
            case(0x6):$actionstr = 'Bridge Device Card';break;
            case(0x7):$actionstr = 'Simple Communication Controller';break;
            case(0x8):$actionstr = 'Base System Peripheral';break;
            case(0x0c):
                if($pciesubclass == 0x04)
                {$actionstr = 'Fibre Channel Card';break;}
                else{$actionstr = 'Serial Bus Controller';break;}
            case(0x0d):$actionstr = 'Wireless Controller';break;
            case(0x0e):$actionstr = 'Intelligent I/O Controller';break;
            case(0x0f):$actionstr = 'Satellite Communication Controller';break;
            default:$actionstr = 'Undefined Device';break;
        }
        return $actionstr;
    }

   private function getPcieLinkspeed($PcieLinkspeed){
        switch($PcieLinkspeed)
        {
            case(0x1):
                $actionstr = '2.5 Gbps';
                break;
            case(0x2):
                $actionstr =  '5.0 Gbps';
                break;
            case(0x3):
                $actionstr = '8.0 Gbps';
                break;
            default:
                $actionstr = 'Unknown';
                break;
        }
        return $actionstr;
    }

   private function getPcieMfc($PcieVendorID){
        $actionstr= '';
        switch($PcieVendorID)
        {
            case(0x0A5C):
                $actionstr = 'Broadcom';
                break;
            case(0x0AC8):
                $actionstr = 'ASUS';
                break;
            case(0x0E11):
                $actionstr = 'Compaq';
                break;
            case(0x1000):
                $actionstr = 'LSI Logic';
                break;
            case(0x1002):
                $actionstr = 'ATI';
                break;
            case(0x1008):
                $actionstr = 'Epson';
                break;
            case(0x100A):
                $actionstr = 'Phoenix';
                break;
            case(0x100D):
                $actionstr = 'AST';
                break;
            case(0x1010):
                $actionstr = 'Video Logic';
                break;
            case(0x1011):
                $actionstr = 'Digital Equipment';
                break;
            case(0x1016):
                $actionstr = 'Fujitsu';
                break;
            case(0x101E):
                $actionstr = 'AMI';
                break;
            case(0x1025):
                $actionstr = 'Acer';
                break;
            case(0x1028):
                $actionstr = 'Dell';
                break;
            case(0x102A):
                $actionstr = 'LSI Logic';
                break;
            case(0x102B):
                $actionstr = 'Matrox Electronic Systems';
                break;
            case(0x1077):
                $actionstr = 'Qlogic';
                break;
            case(0x107A):
                $actionstr = 'Networth';
                break;
            case(0x107B):
                $actionstr = 'Gateway 2000';
                break;
            case(0x107D):
                $actionstr = 'Leadtek';
                break;
            case(0x108E):
                $actionstr = 'Sun Microsystems';
                break;
            case(0x108F):
                $actionstr = 'Systemsoft';
                break;
            case(0x1095):
                $actionstr = 'Silicon Image';
                break;
            case(0x1099):
                $actionstr = 'Samsung ';
                break;
            case(0x10A9):
                $actionstr = 'Silicon Graphics ';
                break;
            case(0x10B7):
                $actionstr = '3Com';
                break;
            case(0x10DE):
                $actionstr = 'NVIDIA';
                break;
            case(0x10DF):
                $actionstr = 'Emulex';
                break;
            case(0x10EC):
                $actionstr = 'Realtek';
                break;
            case(0x10F1):
                $actionstr = 'Tyan';
                break;
            case(0x1106):
                $actionstr = 'VIA';
                break;
            case(0x1109):
                $actionstr = 'Adaptec/Cogent Data';
                break;
            case(0x1116):
                $actionstr = 'Data Translation';
                break;
            case(0x113B):
                $actionstr = 'Network Computing';
                break;
            case(0x1166):
                $actionstr = 'Broadcom';
                break;
            case(0x1177):
                $actionstr = 'Silicon Engineering ';
                break;
            case(0x11C1):
                $actionstr = 'LSI';
                break;
            case(0x11CA):
                $actionstr = 'LSI Systems';
                break;
            case(0x126F):
                $actionstr = 'Silicon Motion';
                break;
            case(0x12B9):
                $actionstr = '3Com';
                break;
            case(0x13FF):
                $actionstr = 'Silicon Spice';
                break;
            case(0x144d):
                $actionstr = 'Samsung';
                break;
            case(0x1462):
                $actionstr = 'Micro-Star International';
                break;
            case(0x14A4):
                $actionstr = 'Liteon';
                break;
            case(0x14E4):
                $actionstr = 'Broadcom';
                break;
            case(0x1543):
                $actionstr = 'Silicon Laboratories';
                break;
            case(0x15B3):
                $actionstr = 'Mellanox';
                break;
            case(0x15D9):
                $actionstr = 'Super Micro';
                break;
            case(0x163C):
                $actionstr = 'intel';
                break;
            case(0x17D3):
                $actionstr = 'Areca';
                break;
            case(0x19A2):
                $actionstr = 'Emulex';
                break;
            case(0x1CB8):
                $actionstr = 'Sugon';
                break;
            case(0x2646):
                $actionstr = 'Kingston';
                break;
            case(0x6409):
                $actionstr = 'Logitec';
                break;
            case(0x8086):
                $actionstr = 'Intel';
                break;
            case(0x8087):
                $actionstr = 'Intel';
                break;
            case(0x8888):
                $actionstr = 'Silicon Magic';
                break;
            case(0x9004):
                $actionstr = 'Adaptec';
                break;
            case(0x9005):
                $actionstr = 'Adaptec';
                break;
            case(0xA200):
                $actionstr = 'NEC';
                break;
            case(0xA727):
                $actionstr = '3Com';
                break;
            default:
                $actionstr = 'Undefined Device';
                break;
        }

        return $actionstr;
    }


    protected function nic(){
        $url = 'http://'.$this->ip.'/rpc/sugon_get_offboardnic_info.asp';
        $str1 = $this->exec($url);
        $key = 'WEBVAR_STRUCTNAME_SUGONGETOFFBNICINFO';
        $arr2=$this->re($key,$str1);
        if($arr2){
            $val = [];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'eth'.$vo['offBNicNo'];
                    $vo['net.ifPhysAddress'] = strtoupper( dechex($vo['offBNicMac0'])).':'.strtoupper( dechex($vo['offBNicMac1'])).':'.strtoupper( dechex($vo['offBNicMac2'])).':'.strtoupper( dechex($vo['offBNicMac3'])).':'.strtoupper( dechex($vo['offBNicMac4'])).':'.strtoupper( dechex($vo['offBNicMac5']));
                    $vo['net.if.mfc'] = $this->getoffboardNicVenderString($vo['offBNicVedDevID1']);
                    $vo['net.ifOperStatus'] = $vo['offBNicState']; // 0 normal
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['nic'=>$val]);
        }
    }
    private  function getoffboardNicVenderString($VendorID){
        $bin = decbin($VendorID);
        $bin = '10000000'.strval($bin);
        $dec = bindec($bin);
        $str = '0x'. dechex($dec) ;
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
                    $vo['{#NAME}'] = $this->getMemoryName($vo['nodeNo'],$vo['channelNo'],$vo['dimmNo']);
                    $vo['memory.present'] =  $vo['memPresent'];//1 ? 'present' : 'absent';
                    $vo['memory.type'] = 'DDR'.$vo['memType'];
                    $vo['memory.speed'] = $this->translateMemFre($vo['memType'],$vo['memFreq']);
                    $vo['memory.size'] =  $vo['memSize'];
                    $vo['memory.mfc'] =  $vo['memManufact'];
                    $vo['memory.model'] =  $vo['memPN'];
                    $vo['ecc.number'] =  $vo['memEccNum'];
                    $vo['memory.status'] =  $vo['memState']; // 0 normal
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['memory'=>$val]);
        }
    }

    private function getMemoryName($nodeNo,$channelNo,$dimmNo){
        //CPU0 DIMMA0
        $chan = '';
        switch ($channelNo){
            case 0: $chan = 'A';break;
            case 1: $chan = 'B';break;
            case 2: $chan = 'C';break;
            case 3: $chan = 'D';break;
            default :$chan = '';break;
        }
        return 'CPU'.$nodeNo.'DIMM'.$chan.$dimmNo;
    }

	
    //根据管理页面JS ，翻译内存主频
	protected function translateMemFre($MemTypeNo,$MemFreNo){
        $memfrequency = 0;
		if($MemTypeNo == 3)
        {
            switch ($MemFreNo) {
                case 1: $memfrequency = 800; break;
                case 2: $memfrequency = 4066; break;
                case 3: $memfrequency = 1333; break;
                case 4: $memfrequency = 1600; break;
                case 5: $memfrequency = 1866; break;
                case 6: $memfrequency = 2133; break;
            }
        }
        if($MemTypeNo == 4)
        {
            switch ($MemFreNo) {
                case 0: $memfrequency = 800; break;
                case 1: $memfrequency = 1000; break;
                case 2: $memfrequency = 1067; break;
                case 3: $memfrequency = 1200; break;
                case 4: $memfrequency = 1333; break;
                case 5: $memfrequency = 1400; break;
                case 6: $memfrequency = 1600; break;
                case 7: $memfrequency = 1800; break;
                case 8: $memfrequency = 1867; break;
                case 9: $memfrequency = 2000; break;
                case 10: $memfrequency = 2133; break;
                case 11: $memfrequency = 2200; break;
                case 12: $memfrequency = 2400; break;
                case 13: $memfrequency = 2600; break;
                case 14: $memfrequency = 2667; break;
                case 15: $memfrequency = 2800; break;
                case 16: $memfrequency = 2933; break;
                case 17: $memfrequency = 3000; break;
                case 18: $memfrequency = 3200; break;        
            }
        }
        return $memfrequency;
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
                    $vo['disk.present'] = $vo['HddPresent'];
                    $vo['disk.status'] = $vo['HddState'];// == 1 ?  'normal' : 'abnormal' ;
                    //$vo['disk.size'] = $vo['HddSize']; //不知道单位 当是TB,不要这个监控项，验证不对
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['disk'=>$val]);
        }
    }

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
            curl_setopt($this->getClient(),CURLOPT_POST,0);
			curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie);
            curl_setopt($this->getClient(),CURLOPT_URL,$url);
            curl_exec($this->getClient());
            curl_close($this->getClient());
        }catch (Exception $e){
            \Yii::error($e);
        }

    }






}