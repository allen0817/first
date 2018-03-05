<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 14:29
 */
namespace  app\components;



abstract class BaseCurl
{
    private  $_client;

    protected $id;
    protected $user;
    protected $pwd;


    protected $cookie;

    protected $auth;

    protected $csrfToken;

    protected $allOptions=[];

    protected $allData=[];

    static $timeOut = 300;//缓存时间 s

    //文件缓存目录
    static $BASE_PATH = '/usr/local/src/first/web/curl_data/';

    protected $path;

    /**
     * @param $options
     * @return $this
     */

    public function __construct($ip,$user,$pwd)
    {
        $this->ip = $ip;
        $this->user = $user;
        $this->pwd = $pwd;

        $this->path = self::$BASE_PATH.$ip;

        $this->getClient();
    }

    public function getClient(){
        if (!$this->_client) {
            $this->_client = $this->createClient();
        }
        return $this->_client;
    }

    private  function createClient(){
        $this->_client = curl_init();
        curl_setopt($this->_client,CURLOPT_POST,1);
        curl_setopt($this->_client,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($this->_client,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($this->_client,CURLOPT_HEADER,0);
        curl_setopt($this->_client,CURLOPT_RETURNTRANSFER,1);
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
    abstract public function getVal($key);

    /**执行
     * 先返回缓存数据，同时只新开一个进程获取新数据，直到成功新数据就覆盖旧数据
     * @return mixed|string
     */
    public  function run(){
        if(file_exists($this->path)){
            $file_json = file_get_contents($this->path);
            $file_arr = json_decode($file_json,true);
            //检查超时
            if($file_arr['time'] + self::$timeOut < time() ){ //超时
                $pid= pcntl_fork();
                if ($pid == -1) {
                    die('could not fork');
                }elseif (!$pid) {
                    //这里是子进程
                    $this->childProcess($file_arr);
                    exit();
                }
            }
            return $file_arr;
        }else{//第一次
            $this->getData();
            $data =  [
                'options' => $this->allOptions,
                'data' => $this->allData,
                'time' => time(),
            ];
            $this->save($data);
            return json_encode($data);
        }
    }

    /** 子进程获取数据
     * @param $file_arr
     */
    protected function childProcess($file_arr){
        if(!isset($file_arr['hand'])){
            $file_arr['hand'] = true;
            $this->save($file_arr);
            $this->getData();
            $this->logout();
            if(!empty($this->allOptions)){
                $data =  [
                    'options' => $this->allOptions,
                    'data' => $this->allData,//$this->replaceKong($this->allData)
                    'time' => time()
                ];
                $this->save($data);
            }
        }
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


}