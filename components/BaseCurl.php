<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 14:29
 */
namespace  app\components;



use yii\base\Exception;

abstract class BaseCurl
{
    private  $_client;

    protected $id;
    protected $user;
    protected $pwd;

    protected $port;

    protected $class;

    protected $cookie;

    protected $auth;

    protected $csrfToken;

    protected $allOptions=[];

    protected $allData=[];

    static $timeOut = 300;//缓存时间 s

    //文件缓存目录
    static $BASE_PATH = '/usr/local/src/first/web/curl_data/';

    //上海 根目录 /usr/local/src/php_script/first
    //北京 根目录  /usr/local/src/first/

    protected $path;
    protected $dir;

    /**
     * @param $options
     * @return $this
     */

    public function __construct($ip,$user,$pwd,$class,$port=80)
    {
        $this->ip = $ip;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->port = $port;

        $this->class = $class;

        $this->dir = $this->getCachePath();
        $this->path = $this->dir . $this->ip;

        $this->getClient();
    }

    public function getClient(){
        if (!$this->_client) {
            $this->_client = $this->createClient();
        }
        return $this->_client;
    }

    private  function createClient(){
        $head = array(
            'Connection:keep-alive',
        );


        $this->_client = curl_init();
        curl_setopt($this->_client,CURLOPT_POST,1);
        curl_setopt($this->_client,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($this->_client,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($this->_client,CURLOPT_HEADER,0);
        curl_setopt($this->_client,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($this->_client, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->_client, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->_client, CURLOPT_AUTOREFERER, 1);
        curl_setopt($this->_client, CURLOPT_COOKIESESSION, 1);

        return $this->_client;
    }


    /**登录
     * @return mixed
     */
    abstract protected function login();

    /**登出
     * @return mixed
     */
    abstract protected function logout();

    /**获取数据
     * @return mixed
     */
    abstract protected function getData();

    /**curl_exec
     * @param $url
     * @return mixed
     */
    abstract protected function exec($url);

    /**获取单个监控项的值
     * @return mixed
     */

    /** 获取单个监控项的值
     * @return mixed
     */
    public function getVal($key)
    {
        $data = $this->run();
        try{
            if(!empty($data)){
                $keys = explode('.',$key);
                foreach ($data as $vo){
                    foreach ( $vo as $v){
                        if($v['{#NAME}'] == $keys[0] ){
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

    public  function run(){
        if(file_exists($this->path){
            $file_json = file_get_contents($this->path);
            if ($file_json) {
                $file_arr = json_decode($file_json,true);
                if($file_arr['time'] + self::$timeOut < time() ){ //超时
                    $pid= pcntl_fork();
                    if ($pid == -1) {
                        die('could not fork');
                    }elseif (!$pid) {
                        //手动释放内存
                        $file_json = null;
                        $file_arr = null;
                        $params = ' '.$this->ip .' '.$this->user .' '.$this->pwd .' '. $this->class;
                        shell_exec("php  /usr/local/src/first/yii sipder/process   $params  > /dev/null 2>&1 & ");
                        exit();
                    }
                }
                return $file_arr['data'];
            } 
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
        }
        return [];
    }






    /** 子进程获取数据
     * @param $file_arr
     */
    public function childProcess(){
        $this->check();
        $this->getData();
        if(!empty($this->allData)){
            $file_json = file_get_contents($this->path);
            $file_arr = json_decode($file_json,true);
            if(empty($file_arr)) $new = $this->allData;
            else $new = $this->checkData($file_arr['data'],$this->allData);
            $data =  [
                'data' => $new,
                'time' => time(),
            ];
            $this->save($data);
        }
        exit();
    }

    protected function check(){
        $file = $this->dir . 'check' ;
        if(!file_exists($file)){
            $fd = fopen($file, 'w');
            fclose($fd);
        }

        $json = file_get_contents($file);
        $arr = json_decode($json,true);

        $k = $this->ip;

        if (!isset($arr[$k])){
            $arr[$k] = time();
        }
        elseif(isset($arr[$k]) && $arr[$k] + 300 < time()  ){
            $arr[$k] = time();
        }else{
            exit();
        }
        file_put_contents($file,json_encode($arr));
    }




    /** 缓存数据到文件
     * @param $data
     */
    public function save($data){
        file_put_contents($this->path,json_encode($data));
    }

    /** 清除注解
     * @param $str
     * @return null|string|string[]
     */

    public function removeMsg($str){
        return preg_replace('/((\/\*[\s\S]*?\*\/)|(\/\/.*)|(#.*))|(\\n)/', "", $str);
    }

    public function checkData($old,$new){
        foreach ($new as $k=>$vo){
            if(!empty($vo)){
                $old[$k] = $vo;
            }
            if(!$old[$k]){
                $old[$k] = $vo;
            }
        }

        return $old;
    }


    /**   正则过渡，浪潮和曙光可用
     * @param $key
     * @param $str
     * @return array
     */

    public function re($key,$str){
        $re = "/$key :(.*?])/";
        preg_match($re,$str,$arr);
        if(!empty($arr)) {
            $str2 = preg_replace('/\'/', '"', $arr[1]);
            $arr2 = json_decode($str2, true);
            if($arr2) return array_filter($arr2);

        }
        return [];
    }

    /**
     * 重启BMC
     */
    public function resetBmc()
    {
        if(file_exists($this->path)){
            $file_json = file_get_contents($this->path);
            $file_arr = json_decode($file_json,true);
            @$file_arr['data']['local']['bmc'] = 0;
            @$this->save($file_arr);
        }
        $command = "ipmitool -I lan -H $this->ip -U $this->user -P $this->pwd mc reset warm";
        exec($command);  
    }


    public  function getCachePath(){
        $this->class = $class;
        $dir = self::$BASE_PATH . $this->class ;
        is_dir($dir) OR mkdir($dir,0777,true);

        $path =  $dir . DIRECTORY_SEPARATOR . $this->ip;
        
        if(!file_exists($path)){
            $fd = fopen($path, 'w');
            fclose($fd);
        }
        return  $dir . DIRECTORY_SEPARATOR;
    }


}