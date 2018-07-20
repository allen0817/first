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


class Ibmx3650m4 extends \app\components\BaseCurl
{
    /**
     * @return mixed
     */
    protected function login()
    {
        $url = 'https://'.$this->ip.'/data/login';
        $cookie = "";
        $info = array(
            'user' => $this->user,
            'password' => $this->pwd,
            'SessionTimeout' => 1200,
        );
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,1);
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, http_build_query($info) );
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$cookie);
        curl_setopt($this->getClient(), CURLOPT_HEADER,1);
        $res = curl_exec($this->getClient());
        if($res){
            preg_match('/_appwebSessionId_=.+?;/',$res,$c);
            if($c){
                $this->auth = true;
                $this->cookie = $c[0];
            }
        }else{
            \Yii::error($this->ip.' login error');
            $this->resetBmc();//重启BMC
            exit();
        }
    }


    /** key 去掉空隔并转大写
     * @param $key
     * @return string
     */
    public function removeKongAndUp($key){
        //return strtoupper( str_replace(' ','',$key)  );
        return str_replace(' ','',$key);
    }

    /**
     * @return mixed
     */
    protected function logout()
    {
        $url = ' https://'.$this->ip.'//designs/imm/dataproviders/imm_trespass_sesstimeout.php?dojo.preventCache='.time();
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
        $this->getMemory();
        $this->getFan();
        $this->getDisk();

        $this->getPower();
        $this->getVital();
        $this->getProcessor();
        $this->logout();
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function exec($url)
    {
        if(!$this->auth){
            $this->login();
        }
        $head = array(
            'Content-Type:application/json;charset=utf-8',
            'Connection:keep-alive',
        );
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,0);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 5 );
        curl_setopt($this->getClient(),CURLOPT_HTTPHEADER,$head);
        curl_setopt($this->getClient(),CURLOPT_HEADER,0);
        $res = curl_exec($this->getClient());
        if ($res){
            return json_decode($res,true);
        }
        return null;
    }
    public function getMemory(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_memory.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        if(isset($res['items'][0]['memory'])){
            $val = [];
            foreach ($res['items'][0]['memory'] as $vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['memory.name']);
                $vo['memory.serial'] = $vo['memory.serial_number'];
                //$vo['memory.type'] 这个有可以用他的
                $vo['memory.name'] = $this->removeKongAndUp($vo['memory.name']);
                $vo['memory.status']  = $vo['memory.status'] == 'Normal'? 1 : 0;
                $vo['memory.size'] = $vo['memory.capacity']; // G
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['memory' => $val]);
        }
    }
    public function getFan(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_cooling.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        if(isset($res['items'][0]['cooling'])){
            $val = [];
            foreach ($res['items'][0]['cooling'] as $vo){
                if(empty($vo)) continue;
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['cooling.fan']);
                $vo['fan.name'] = $this->removeKongAndUp($vo['cooling.fan']);
                $vo['fan.status'] = $vo['cooling.status'] == 'Normal' ? 1:0;
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['fan' => $val]);
        }
    }
    public function getDisk(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_disks.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        if(isset($res['items'][0]['disks'])){
            $val = [];
            foreach ($res['items'][0]['disk'] as $vo){
                if(empty($vo)) continue;
                $vo['{#NAME}'] = 'Disk'.$this->removeKongAndUp($vo['disks.name']);
                $vo['disk.name'] = 'Disk'.$this->removeKongAndUp($vo['disks.name']);
                $vo['disk.status'] = $vo['disks.status'] == 'Normal' ? 1:0;
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['disk' => $val]);
        }
    }
    public function getPower(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_power_supplies.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        if(isset($res['items'][0]['power'])){
            $val = [];
            foreach ($res['items'][0]['power'] as $k=>$vo){
                $vo['{#NAME}'] = 'power'.$this->removeKongAndUp($vo['power.name']);
                $vo['power.name'] = 'power'.$this->removeKongAndUp($vo['power.name']);
                $vo['power.state'] = $vo['power.status'] == 'Normal' ? 1:0;
                $vo['power.serial'] = $vo['power.serial_number'];
                $vo['power.local'] = $vo['power.fru_number'];
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['power' => $val]);
        }
    }
    //系统信息 local
    public function getVital(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_autopromo.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        //MACHINE
        $val = [];
        if(!empty($res['items'])){
            $val['product.version'] = $res['items']['PrimaryBankVersion'];
        }

        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_properties.php?dojo.preventCache='.time();
        $res = $this->exec($url);

        if(!empty($res['items'])){
            $val['dev.timezone'] = $res['items']['date_gmt'];
        }

        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_properties.php?dojo.preventCache='.time();
        $res = $this->exec($url);

        if(!empty($res['items'])){
            $val['product.name'] = $res['items']['machine_name'];
            $val['product.serial'] = $res['items']['serial_number'];
            $val['product.model'] = $res['items']['machine_typemodel'];
            $val['uuid'] = $res['items']['UUID'];
            $val['sys.up.time'] = $res['items']['power_on_hours'];
        }
        $val['bmc'] = 1; //能采集数据，bmc一定能登录
        $this->allData = ArrayHelper::merge($this->allData,['local'=>$val]);

    }
    public function getProcessor(){
        $url = 'https://'.$this->ip.'/designs/imm/dataproviders/imm_processors.php?dojo.preventCache='.time();
        $res = $this->exec($url);
        if(isset($res['items'][0]['processors'])){
            $process= $res['items'][0]['processors'];
            $val = [];
            foreach ($process as $k=>$vo){
                $vo['{#NAME}'] = $this->removeKongAndUp($vo['processors.fru_name']);
                $vo['cpu.name'] = $this->removeKongAndUp($vo['processors.fru_name']);
                $vo['cpu.mfc'] = $vo['processors.manuf_id'];
                $vo['cpu.type'] = $vo['processors.family'];

                $vo['cpu.frequency'] = $vo['processors.clock_speed'];
                $vo['cpu.serial'] = $vo['Identifier'];
                $vo['cpu.core'] = $vo['processors.cores'];
                $vo['cpu.thread'] = $vo['processors.threads'];
                $vo['cpu.qpi.width'] = $vo['processors.max_data_width'];

                $vo['cpu.l1.cache'] =  str_replace('k','', $vo['processors.l1instrcache']);
                $vo[ 'cpu.l2.cache'] = str_replace('k','', $vo['processors.l2cache']);
                $vo[ 'cpu.l3.cache'] = str_replace('k','', $vo['processors.l3cache']);
                $val[] = $vo;
            }
            $this->allData = ArrayHelper::merge($this->allData,['cpu'=>$val]);
        }
    }



}