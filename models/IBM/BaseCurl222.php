<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 22:06
 */

namespace  app\models\IBM;

use Yii;
use yii\helpers\ArrayHelper;

class BaseCurl
{

    public $client;

    protected $id;
    protected $user;
    protected $pwd;


    protected $cookie;

    protected $auth;

    public $allOptions=[];

    public $allData=[];




    public function __construct($ip,$user,$pwd)
    {
        $this->ip = $ip;
        $this->user = $user;
        $this->pwd = $pwd;

        $this->createClient();
    }



    public function replaceKong($data,$options=false){
        foreach ($data as $k=>$vo){
            if(is_array($vo)){
                foreach ($vo as $i=>$v){
                    $data[$k][$i] = str_replace(' ','',$v);
                    if($options){
                        $data[$k][$i] = strtoupper($data[$k][$i]);
                    }

                }
            }else{
                $data[$k] = str_replace(' ','',$vo);
                if($options){
                    $data[$k] = strtoupper($data[$k]);
                }
            }
        }
        return $data;
    }





    public function run(){
        $cache = \Yii::$app->cache;
        //$cache->flush();
        $data = $cache->get($this->ip.'data');

        $this->state();

        if($data){
            return $data;
        }else{
            $this->login();
            $this->getName();
            $this->getMemory();
            $this->getSensor();
            $this->getVital();
            $this->getProcessor();
            $this->getHostMac();
            $this->getVirtualLightPath();
            $this->logout();

            $data =  [
                'options' => $this->replaceKong($this->allOptions,true),
                'data' => $this->allData,//$this->replaceKong($this->allData)
            ];
            $cache->set($this->ip.'data',$data,3600*24);
            return $data;
        }
    }

    public function state(){
        $cache = \Yii::$app->cache;
        $key = $this->ip.'state';
        $data = $cache->get($key);
        if(!$data){
            $this->login();
            $this->getVirtualLightPath();
            $cache->set($key,true,300);
        }
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
        $url = 'http://'.$this->ip.'/session/create';
        $cookie = "HideIPv6WhenDisabled=0; session_id=none";
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_setopt($this->client,CURLOPT_POST,1);
        curl_setopt($this->client, CURLOPT_POSTFIELDS, "USERNAME=".$this->user."PASSWORD=".$this->pwd);
        curl_setopt($this->client,CURLOPT_COOKIE,$cookie);

        $res = curl_exec($this->client);
        if($res){
            $c = explode(":",$res);
            if($c[0]=='ok'){
                $this->auth = true;
                $this->cookie = "HideIPv6WhenDisabled=0; session_id=".$c[1];
            }
        }
    }

    public function getName(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/iBMCControl</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/iBMCControl/GetSPNameSettings</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetSPNameSettings xmlns="http://www.ibm.com/iBMC/sp/iBMCControl" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetSPNameSettings>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);

        if(isset($res['SPName'])){
            $n= array( array('{#SPNAME}' => $res['SPName'] ));
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$n);
            $this->allData = ArrayHelper::merge($this->allData,$n );
        }
    }


    public function getMemory(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetMemoryInfo</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetMemoryInfo xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetMemoryInfo>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
';
        $res = $this->exec($param);

        if(isset($res['Memory']['MemoryInfo'])){
            $t = [];
            foreach ($res['Memory']['MemoryInfo'] as $vo){
                $t[] = array(
                    '{#MEMORYINFO}' => $vo['Description']
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$t);
            $this->allData = ArrayHelper::merge($this->allData,$res['Memory']['MemoryInfo']);
        }
    }

    public function getSensor(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetSensorValues</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetSensorValues xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetSensorValues>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);

        if(!empty($res)){
            $health = $res['SystemHealthInfo'];
            $h = [];
            foreach ($health as $k=>$vo){
                $k = strtoupper($k);
                $h[] = array(
                    "{#{$k}}" => $k
                );
            }

            $this->allOptions =  ArrayHelper::merge($this->allOptions,$h);
            $this->allData = ArrayHelper::merge($this->allData,$health);

            $voltage= $res['SensorInfo']['Voltage'];
            $v = [];
            foreach ($voltage as $k=>$vo){
                $v[] = array(
                    "{#VOLTAGE}" => $vo['Component']
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$v);
            $this->allData = ArrayHelper::merge($this->allData,$voltage);


            $fan= $res['SensorInfo']['Fan'];
            $f = [];
            foreach ($fan as $k=>$vo){
                $f[] = array(
                    "{#FAN}" => $vo['Component']
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$f);
            $this->allData = ArrayHelper::merge($this->allData,$fan);
        }

    }

    public function getVital(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetVitalProductData</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetVitalProductData xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetVitalProductData>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);

        if(!empty($res)){
            $mlv= $res['GetVitalProductDataResponse']['MachineLevelVPD'];
            $m = [];
            foreach ($mlv as $k=>$vo){
                $k = strtoupper($k);
                $m[] = array(
                    "{#$k}" => $k
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$m);
            $this->allData = ArrayHelper::merge($this->allData,$mlv);

            $clv= $res['GetVitalProductDataResponse']['ComponentLevelVPD'];
            $c = [];
            foreach ($clv as $k=>$vo){
                $c[] = array(
                    "{#FRUNAME}" => $vo['FRUName']
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$c);
            $this->allData = ArrayHelper::merge($this->allData,$clv);


            $vpd= $res['GetVitalProductDataResponse']['VPD'];
            $vp = [];
            foreach ($vpd as $k=>$vo){
                $vp[] = array(
                    "{#FIRMWARENAME}" => $vo['FirmwareName']
                );
            }

            $this->allOptions =  ArrayHelper::merge($this->allOptions,$vp);
            $this->allData = ArrayHelper::merge($this->allData,$vpd);
        }


    }

    public function getProcessor(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetProcessorInfo</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetProcessorInfo xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetProcessorInfo>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);
        if(!empty($res)){
            $process= $res['Processor']['ProcessorInfo'];
            $p = [];
            foreach ($process as $k=>$vo){
                $p[] = array(
                    "{#PROCESSOR}" => $vo['Description']
                );
            }

            $this->allOptions =  ArrayHelper::merge($this->allOptions,$p);
            $this->allData = ArrayHelper::merge($this->allData,$process);
        }
    }

    public function getHostMac(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetHostMacAddresses</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetHostMacAddresses xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetHostMacAddresses>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);

        if(!empty($res)){
            $HostMaddr= $res['HostMACaddress']['HostMaddr'];
            $hm = [];
            foreach ($HostMaddr as $k=>$vo){
                $hm[] = array(
                    "{#HOSTMADDR}" => $vo['Description']
                );
            }
            $this->allOptions =  ArrayHelper::merge($this->allOptions,$hm);
            $this->allData = ArrayHelper::merge($this->allData,$HostMaddr);
        }

    }

    public function getVirtualLightPath(){
        $param = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://www.w3.org/2003/05/soap-envelope" xmlns:wsa="http://schemas.xmlsoap.org/ws/2004/08/addressing" xmlns:wsman="http://schemas.dmtf.org/wbem/wsman/1/wsman.xsd">
  <SOAP-ENV:Header>
    <wsa:To>http://'.$this->ip.'/wsman</wsa:To>
    <wsa:ReplyTo>
      <wsa:Address>http://schemas.xmlsoap.org/ws/2004/08/addressing/role/anonymous</wsa:Address>
    </wsa:ReplyTo>
    <wsman:ResourceURI>http://www.ibm.com/iBMC/sp/Monitors</wsman:ResourceURI>
    <wsa:Action>http://www.ibm.com/iBMC/sp/Monitors/GetVirtualLightPath</wsa:Action>
    <wsa:MessageID>dt:'.time().'</wsa:MessageID>
  </SOAP-ENV:Header>
  <SOAP-ENV:Body>
    <GetVirtualLightPath xmlns="http://www.ibm.com/iBMC/sp/Monitors" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"></GetVirtualLightPath>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        $res = $this->exec($param);
        if(!empty($res)){
            $vlh= $res['VirtualLightPathArray']['VirtualLightPath'];
            $vl = [];
            foreach ($vlh as $k=>$vo){
                $vl[] = array(
                    "{#VIRTUALLIGHTPATN}" => $vo['Name']
                );
            }

            //先这样
            $optionBool=false;
            foreach ($this->allOptions as $vo){
                foreach ($vo as $k=>$v){
                    if($k=='{#VIRTUALLIGHPATH}'){
                        $optionBool = true;
                        break;
                    }
                }
            }
            if(!$optionBool) $this->allOptions =  ArrayHelper::merge($this->allOptions,$vl);

            if(!empty($vlh)){
              foreach ($vlh as $vo){
                  $bool = true;
                  foreach ($this->allData as $k=>$v){
                      if($v['name']==$vo['name']){
                          $this->allData[$k] = $vo;
                          $bool=false;
                          break;
                      }
                  }
                  if($bool) ArrayHelper::merge($this->allData,$vo);
              }
            }

//            $this->allOptions =  ArrayHelper::merge($this->allOptions,$vl);
            //$this->allData = ArrayHelper::merge($this->allData,$vlh);
        }

    }


    public function exec($param){
        if(!$this->auth){
            $this->login();
        }
        $url = 'http://'.$this->ip.'/wsman';
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_setopt($this->client, CURLOPT_POSTFIELDS, $param);
        curl_setopt($this->client,CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->client, CURLOPT_TIMEOUT, 5 );
        $res = curl_exec($this->client);
        $r = '/<s:Body>(.*)<\/s:Body>/i';
        if($res){
            preg_match($r,$res,$data);
            $xml = simplexml_load_string($data[1]);

            return json_decode(json_encode($xml),true);

        }
        return null;
    }



    public function logout(){
        $url = 'http://'.$this->ip.'/session/deactivate';
        curl_setopt($this->client,CURLOPT_POST,0);
        curl_setopt($this->client,CURLOPT_URL,$url);
        curl_exec($this->client);
    }


    public function test(){

        $cache = \Yii::$app->cache;
        $cache->flush();



        $this->run();

        $data = $this->ip.'data';
        return  json_encode($cache->get($data));

        return json_encode([$this->allOptions,$this->allData]);


        return [
            $this->allOptions,
            $this->allData
        ];
    }


}