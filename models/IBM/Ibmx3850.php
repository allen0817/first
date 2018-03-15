<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 17:42
 */

namespace app\models\IBM;


use yii\base\Exception;
use yii\helpers\ArrayHelper;


/** ibm 有空格 有大小写，key 先去掉空隔，再转大写。  匹配时 也这样做
 * Class Ibmx3850
 * @package app\models\IBM
 */


class Ibmx3850 extends \app\components\BaseCurl
{
    /**
     * @return mixed
     */
    protected function login()
    {
        $url = 'http://'.$this->ip.'/session/create';
        $cookie = "HideIPv6WhenDisabled=0; session_id=none";
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,1);
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, "USERNAME=".$this->user."PASSWORD=".$this->pwd);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$cookie);

        $res = curl_exec($this->getClient());
        if($res){
            $c = explode(":",$res);
            if($c[0]=='ok'){
                $this->auth = true;
                $this->cookie = "HideIPv6WhenDisabled=0; session_id=".$c[1];
            }
        }
    }


    protected function getUrl($key,$method='Monitors'){
        return '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/'.$method.'</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/'.$method.'/'.$key.'</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <'.$key.' xmlns="http://www.ibm.com/iBMC/sp/'.$method.'" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></'.$key.'>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
    }


    /** key 去掉空隔并转大写
     * @param $key
     * @return string
     */
    public function removeKongAndUp($key){
        return strtoupper( str_replace(' ','',$key)  );
    }

    /**
     * @return mixed
     */
    protected function logout()
    {
        $url = 'http://'.$this->ip.'/session/deactivate';
        curl_setopt($this->getClient(),CURLOPT_POST,0);
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_exec($this->getClient());
    }

    /**
     * @return mixed
     */
    protected function getData()
    {
        $this->login();
        $this->getName();
        $this->getMemory();
        $this->getSensor();
        $this->getVital();
        $this->getProcessor();
        $this->getHostMac();
        $this->getVirtualLightPath();
        $this->logout();
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function exec($param)
    {
        if(!$this->auth){
            $this->login();
        }
        $url = 'http://'.$this->ip.'/wsman';
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, $param);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 5 );
        $res = curl_exec($this->getClient());
        $r = '/<s:Body>(.*)<\/s:Body>/i';
        if($res){
            preg_match($r,$res,$data);

            $xml = simplexml_load_string($data[1]);
            return json_decode(json_encode($xml),true);

        }
        return null;
    }

    /**
     * ibm spname
     */
    public function getName(){
        $param = $this->getUrl('GetSPNameSettings','iBMCControl');
        $res = $this->exec($param);
        if(isset($res['SPName'])){
            $val[] = ['{#NAME}'=>strtoupper('SPName'),'VALUE'=>$res['SPName']];
            $this->allData = ArrayHelper::merge($this->allData,['SPNAME'=>$val]);
        }
    }


    public function getMemory(){
        $param = $this->getUrl('GetMemoryInfo');
        $res = $this->exec($param);
        if(isset($res['Memory']['MemoryInfo'])){
            $val = [];
            foreach ($res['Memory']['MemoryInfo'] as $vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Description']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['MEMORY' => $val]);
        }
    }



    public function getSensor(){
        $param = $this->getUrl('GetSensorValues');
        $res = $this->exec($param);

        if(!empty($res)){
            $health = $res['SystemHealthInfo'];
            $val = [];
            foreach ($health as $k=>$vo){
                $val[] = array(
                    '{#NAME}' => $this->removeKongAndUp($k),
                    'VALUE' => $vo
                );
            }
            $this->allData = ArrayHelper::merge($this->allData,['HEALTH'=>$val]);

            $voltage= $res['SensorInfo']['Voltage'];
            $val = [];
            foreach ($voltage as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Component']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['VOLTAGE'=>$val]);


            $fan= $res['SensorInfo']['Fan'];
            $val = [];
            foreach ($fan as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Component']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['FAN' => $val]);
        }

    }

    public function getVital(){
        $param = $this->getUrl('GetVitalProductData');
        $res = $this->exec($param);

        //MACHINE
        if(!empty($res)){
            $mlv= $res['GetVitalProductDataResponse']['MachineLevelVPD'];
            $val = [];

            foreach ($mlv as $k=>$vo){
                $val[] = array(
                    '{#NAME}' => $this->removeKongAndUp($k),
                    'VALUE' => $vo
                );
            }
            $this->allData = ArrayHelper::merge($this->allData,['MACHINE'=>$val]);


            $clv= $res['GetVitalProductDataResponse']['ComponentLevelVPD'];
            $val = [];
            foreach ($clv as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['FRUName']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['FRU'=>$val]);


            $vpd= $res['GetVitalProductDataResponse']['VPD'];
            $val = [];
            foreach ($vpd as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['FirmwareName']);
                $val[] = $vo;

            }
            $this->allData = ArrayHelper::merge($this->allData,['FIRMWARE'=>$val]);
        }


    }

    public function getProcessor(){
        $param = $this->getUrl('GetProcessorInfo');
        $res = $this->exec($param);
        if(!empty($res)){
            $process= $res['Processor']['ProcessorInfo'];
            $val = [];
            foreach ($process as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Description']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['PROCESSOR'=>$val]);
        }
    }

    public function getHostMac(){
        $param = $this->getUrl('GetHostMacAddresses');
        $res = $this->exec($param);
        if(!empty($res)){
            $HostMaddr= $res['HostMACaddress']['HostMaddr'];
            $val = [];
            foreach ($HostMaddr as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Description']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['MAC'=>$val]);
        }

    }

    public function getVirtualLightPath(){
        $param = $this->getUrl('GetVirtualLightPath');
        $res = $this->exec($param);
        if(!empty($res)){
            $vlh= $res['VirtualLightPathArray']['VirtualLightPath'];
            $val = [];
            foreach ($vlh as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['Name']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['LIGHTPATH'=>$val]);
        }
    }



    public  function run(){
        if(file_exists($this->path)){
            $file_json = file_get_contents($this->path);
            $file_arr = json_decode($file_json,true);
            if($file_arr['time'] + self::$timeOut < time() ){ //超时
                $pid= pcntl_fork();
                if ($pid == -1) {
                    die('could not fork');
                }elseif (!$pid) {
                    //这里是子进程
                    $params = ' '.$this->ip .' '.$this->user .' '.$this->pwd .' '. $this->class;
                    shell_exec("php  /usr/local/src/first/yii sipder/process   $params  > /dev/null 2>&1 & ");
                    exit();
                }
            }
            return $file_arr['data'];
        }else{//第一次
            $this->getData();
            if (!empty($this->allData)){
                $data =  [
                    'data' => $this->allData,
                    'time' => time(),
                ];
                $this->save($data);
                return $this->allData;
            }
            return [];
        }
    }


    public function getVal($key)
    {
        $data = $this->run();
        try{
            if(!empty($data)){
                $keys = explode('.',$key);
                foreach ($data as $vo){
                    foreach ( $vo as $v){
                        if($v['{#NAME}'] == $this->removeKongAndUp($keys[0]) ){
                            $tmp = $v;
                            break;
                        }
                    }
                }
                if (isset($tmp)){
                    if (isset($keys[1])){
                        $tmp = array_change_key_case($tmp,CASE_UPPER);
                        echo $tmp[$keys[1]];exit();
                    }else{
                        if(isset($tmp['SensorName'])) echo $tmp['SensorName'];
                        else    echo $tmp['{#NAME}'];
                        exit();
                    }
                }
            }
        }catch (Exception $e){
            echo 'null';exit();
        }
        echo 'null';exit();
    }



}