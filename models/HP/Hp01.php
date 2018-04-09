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

class Hp01 extends  \app\components\BaseCurl
{
    protected function login()
    {

        $url = 'https://'.$this->ip.'/json/login_session';
        $cookie = " ";
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_POST,1);

        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS,'{"method":"login","user_login":"admin","password":"ctsicloud123"}');
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
        }
    }

    /**
     * @return mixed
     */
    protected function getData()
    {
        $this->login();

        $this->healthSummary();

/*        $this->healthFans();

        $this->temp();
        $this->sup();

        $this->powerReading();

        $this->proc();

        $this->mem();

        $this->nic();

        $this->drive();*/

        $this->logout();

    }






    //health_summary 总概
    public function healthSummary(){
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
    public function sysInfo(){
        $url = 'https://'.$this->ip.'/json/overview?_='.time().'&null';
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
            $this->allData = ArrayHelper::merge($this->allData,['SYS'=>$val]);
        }
    }


    public function replaceToUpper($str){
        return strtoupper ( str_replace(' ','',$str) );
    }


    //风扇
    public function healthFans(){
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
            $this->allData = ArrayHelper::merge($this->allData,['FAN'=>$val]);
        }
    }

    //温度
    public function temp(){
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
            $this->allData = ArrayHelper::merge($this->allData,['TEMP'=>$val]);

        }
    }

    public function sup(){
        $url = 'https://'.$this->ip.'/json/power_supplies?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['supplies'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'SUP'.$k;
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['SUP'=>$val]);
        }
    }

    public function powerReading(){
        $url = 'https://'.$this->ip.'/json/power_readings?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $arr['{#NAME}'] = 'POWERREADIN';
            $val[] = $arr;
            $this->allData = ArrayHelper::merge($this->allData,['POWERING'=>$val]);
        }
    }


    public function proc(){
        $url = 'https://'.$this->ip.'/json/proc_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['processors'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['proc_socket']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['PROC'=>$val]);
        }
    }

    public function mem(){
        $url = 'https://'.$this->ip.'/json/mem_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['memory'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = $this->replaceToUpper($vo['mem_dev_loc']);
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['MEM'=>$val]);
        }
    }

    public function nic(){
        $url = 'https://'.$this->ip.'/json/nic_info?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['nics'];
            foreach ($arr2 as $k=> $vo){
                if(!empty($vo)){
                    $vo['{#NAME}'] = 'NIC'.$vo['port_num'];
                    $val[] = $vo;
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['NIC'=>$val]);
        }
    }

    public function drive(){
        $url = 'https://'.$this->ip.'/json/drives_status?_='.time().'&null';
        $arr = $this->exec($url);
        if ($arr){
            $val = [];
            $arr2 = $arr['backplanes'];
            foreach ($arr2 as $g=> $vo){
                if(!empty($vo['bays'])){
                    foreach ($vo['bays'] as $k=>$v){
                        $v['{#NAME}'] = 'GROUP'.$g.'bay'.$v['bay_number'];
                        $val[] = $v;
                    }
                }
            }
            $this->allData = ArrayHelper::merge($this->allData,['DRIVE'=>$val]);
            print_r($val);
        }
    }


    protected function exec($url){
        //\Yii::$app->response->format = Response::FORMAT_JSON;
        if(!$this->auth){
            $this->login();
        }
        $headers = array(
            'Host: 172.16.253.71',
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
        return json_decode($res,true);
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