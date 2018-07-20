<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:39
 */

namespace app\models\HP;


use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class Hpdl380g8 extends  \app\components\BaseCurl
{
    protected function login()
    {

        $url = 'https://'.$this->ip.'/json/login_session';
        $cookie = " ";
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,1);

        $info =  array(
            'method' => 'login',
            'user_login' => $this->user,
            'password' => $this->pwd
        );
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS,json_encode($info));
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$cookie);

        $res = curl_exec($this->getClient());
        $arr = json_decode($res,true);
        if(isset($arr['session_key'])){
            $this->cookie .= 'sessionKey='.$arr['session_key'];
            $this->cookie .=';sessionUrl=https://172.16.253.71/';
            $this->cookie .=';sessionLang=en';

            $this->auth = true;
            $this->csrfToken = $arr['session_key'];
        }
        else{
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
        //$this->healthSummary();
        $this->sysInfo();
        //$this->healthFans();
        //$this->temp();
        //$this->sup(); //电池
        //$this->powerReading(); //总功率
        $this->proc();
        $this->mem();
        $this->nic();
        //$this->drive();
        //$this->firmware();
        $this->logout();

    }


    //health_summary 总概
    protected function healthSummary(){
        $url = 'https://'.$this->ip.'/json/health_summary?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            foreach ($arr as $k=>$vo){
                $t = [];
                if(!empty($vo)){
                    $t['{#NAME}'] = $this->replaceToUpper($k);
                    $t['VALUE'] = $vo;
                    $val[] = $t;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['SUMMARY'=>$val]);
        }
    }

    //系统信息
    protected function sysInfo(){
        $url = 'https://'.$this->ip.'/json/overview?_='.time().'&null';
        $arr = $this->exec($url);
        $get = [];
        if ($arr){
            $get = array(
                'product.serial'  => $arr['serial_num'],
                'product.name' => $arr['product_name'],
                'uuid'=>$arr['uuid'],
                'product.version' => $arr['ilo_fw_version'],
                'dev.ip' => $arr['ip_address'],
            );
        }
        $get['bmc'] = 1; //能采集数据，bmc一定能登录
        $get['dev.timezone'] = $this->getTimeZone();
        $this->allData = ArrayHelper::merge($this->allData,['local'=>$get]);
    }

    protected function getTimeZone(){
        $url = 'https://'.$this->ip.'/json/network_sntp/interface/0?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $id =  $arr['our_zone'];
            $path =  \Yii::getAlias('@app').'/models/HP/timezone.txt';
            $file = file_get_contents($path);
            $arr = json_decode($file,true);
            foreach ($arr['zones'] as $vo){
                if($vo['index'] == $id){
                    return $vo['name'];
                }
            }
        }
        return '';
    }


    protected function replaceToUpper($str){
        $str = preg_replace("/\s*|\(|\)/",'',$str);
        //return strtoupper ( $str );
        return $str;
    }


    //风扇
    protected function healthFans(){
        $url = 'https://'.$this->ip.'/json/health_fans?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['fans'];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['label']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['fan'=>$val]);
        }
    }

    //温度
    protected function temp(){
        $url = 'https://'.$this->ip.'/json/health_temperature?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['temperature'];
            foreach ($arr2 as $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['location'].$vo['label']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['temp'=>$val]);

        }
    }

    //电池
    protected function sup(){
        $url = 'https://'.$this->ip.'/json/power_supplies?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['supplies'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'POWERSUPPLIES'.$k;
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['SUP'=>$val]);
        }
    }

    //总功率
    protected function powerReading(){
        $url = 'https://'.$this->ip.'/json/power_readings?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $arr['{#NAME}'] = 'POWERREADIN';
            $val[] = $arr;
            $this->allData = ArrayHelper::merge($this->allData,['POWERING'=>$val]);
        }
    }


    //cpu
    protected function proc(){
        $url = 'https://'.$this->ip.'/json/proc_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['processors'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $tmp = explode(' ',$vo['proc_name']);

                    $val[] = ArrayHelper::merge($vo,[
                        'cpu.frequency' => $vo['proc_speed'],
                        'cpu.name' => $this->replaceToUpper($vo['proc_socket']),
                        'cpu.core' => $vo['proc_num_cores'],
                        'cpu.thread' =>  $vo['proc_num_threads'],
                        'cpu.qpi.width' => $vo['proc_mem_technology'],
                        'cpu.l1.cache' => $vo['proc_num_l1cache'],
                        'cpu.l2.cache' => $vo['proc_num_l2cache'],
                        'cpu.l3.cache' => $vo['proc_num_l3cache'],
                        'cpu.version' => $vo['proc_name'],
                        'cpu.type' => $tmp[0],
                        '{#NAME}' => 'CPU'.$k,
                    ]);
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['cpu'=>$val]);
        }
    }

    //Memory Details:mem_modules
    protected function mem(){
        $url = 'https://'.$this->ip.'/json/mem_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['memory'];
            foreach ($arr2 as $k=> $vo){
                if($vo){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['mem_dev_loc']);
                    $vo['memory.frequency'] = $vo['mem_speed'];
                    $vo['memory.size.total'] = $vo['mem_size'];
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['memory'=>$val]);
        }
    }

    //网卡
    protected function nic(){
        $url = 'https://'.$this->ip.'/json/nic_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['nics'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['dev_type']).$vo['port_num'];    //'nic'.$k;
                    $vo['net.ifPhysAddress'] = $vo['mac_addr'];
                    $vo['net.ifType'] = $vo['dev_type'];
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['nic'=>$val]);
        }
    }

    //固件版本
    protected function firmware(){
        $url = 'https://'.$this->ip.'/json/fw_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['firmware'];
            foreach ($arr2 as $k=> $vo){
                $t = [];
                if(!empty($vo)){
                    $t['{#NAME}'] = $this->replaceToUpper($vo['fw_name']);
                    $t['VALUE'] = $vo['fw_version'];
                    $val[] = $t;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['FIRMWARE'=>$val]);
        }
    }

    protected function drive(){
        $url = 'https://'.$this->ip.'/json/drives_status?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['backplanes'];
            foreach ($arr2 as $g=> $vo){
                if(!empty($vo['bays'])){
                    foreach ($vo['bays'] as $k=>$v){
                        $v['{#NAME}'] =  'GROUP'.$g.'BAY'.$v['bay_number'];
                        $val[] = $v;
                    }
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['DRIVE'=>$val]);
        }
    }

    protected function exec($url){
        //\Yii::$app->response->format = Response::FORMAT_JSON;
        if(!$this->auth){
            $this->login();
        }
        $headers = array(
            "Host: $this->ip",
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36',
            'X-Requested-With: XMLHttpRequest'
        );
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie);
        curl_setopt($this->getClient(), CURLOPT_TIMEOUT, 5 );
        curl_setopt($this->getClient(), CURLOPT_HTTPHEADER, $headers);

        curl_setopt($this->getClient(),CURLOPT_POST,0);
        $res = curl_exec($this->getClient());
        if(curl_errno($this->getClient()) == CURLE_OPERATION_TIMEDOUT)
        {
            $msg = '获取：'.$url.' 超时';
            \Yii::error($msg);
            return null;
        }
        $arr = json_decode($res,true);
        if(!isset($arr['message'])) return $arr;
        else return null;
    }



    protected function logout(){
        try{
            $url = 'https://'.$this->ip.'/json/login_session';
            curl_setopt($this->getClient(), CURLOPT_POSTFIELDS,'{"method":"logout","session_key":"'.$this->csrfToken.'"}');
            curl_setopt($this->getClient(),CURLOPT_URL,$url);
            curl_exec($this->getClient());
            curl_close($this->getClient());
        }catch (Exception $e){
            \Yii::error($e);
        }

    }






}