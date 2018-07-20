<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/26
 * Time: 11:06
 */

namespace app\models\IBM;


use app\components\IbmStorage;

class Ibmstoragev7000 extends  IbmStorage
{
    protected function login()
    {
        // TODO: Implement login() method.
        $url = "https://$this->ip";
        $response = $this->send($url,[],[],'GET');
        sleep(1);
        if ($response){
            $this->cookie = array(
                [
                    'name' => 'JSESSIONID',
                    'value' => $response->getCookies()->get('JSESSIONID')->value
                ],
                [
                    'name' => '_sync',
                    'value' => $response->getCookies()->get('_sync')->value
                ],
            );
            $this->cookie = $response->cookies;
            $url = "https://$this->ip/login";
            $params = [
                'login' =>$this->user,
                'password' => $this->pwd,
                'tzoffset' => -480,
                'challenge' => '',
            ];
            $options = [
                'Accept: */*',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Content-Length: 65',
                'Content-Type: application/x-www-form-urlencoded',
                "Host: $this->ip:443",
                "Origin: https://$this->ip:443",
                'Pragma: no-cache',
                "Referer: https://$this->ip:443",
                'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.79 Safari/537.36',
                'X-Requested-With: XMLHttpRequest',
                'Referrer Policy: no-referrer-when-downgrade',
                "Remote Address: $this->ip:443",
            ];
            $response = $this->send($url,$params,$options,'POST');
            print_r($response);
        }

    }

    /**登出
     * @return mixed
     */
    protected function logout()
    {
        // TODO: Implement logout() method.
    }

    /**获取数据
     * @return mixed
     */
    protected function getData()
    {
        // TODO: Implement getData() method.
        $this->login();
    }

    /**curl_exec
     * @param $url
     * @return mixed
     */
    protected function exec($url)
    {
        // TODO: Implement exec() method.
    }


}