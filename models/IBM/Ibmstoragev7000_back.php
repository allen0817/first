<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29
 * Time: 11:06
 */

namespace app\models\IBM;


use app\components\BaseCurl;
use Yii;
use yii\helpers\ArrayHelper;


class Ibmstoragev7000_back extends  BaseCurl
{
    /**登录
     * @return mixed
     */

    public $hostname;




    protected function login()
    {
        // TODO: Implement login() method.
        $url = "https://$this->ip";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this->getHeaderSet());
        $res = curl_exec($ch);
        curl_close($ch);

        preg_match('/Set-Cookie: (.*?;)/',$res,$cookie1);
        preg_match('/SET-COOKIE: (.*?;)/',$res,$cookie2);

        if(isset($cookie2[1])){
            sleep(1); // 做了个简单访问限制，不能请求太快
            $url = $url.'/login';
            $loinInfo = array(
                'login' => $this->user,
                'password' => $this->pwd,
                'challenge' => '',
                'tzoffset' => -480
            );
            $header = array(
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
            );

            $str = 'login='.$this->user.'&password='.$this->pwd.'&challenge=&tzoffset=-480';
            //echo $this->user;die;
            $ch2 = curl_init();
            curl_setopt($ch2,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch2,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch2,CURLOPT_URL,$url);
            curl_setopt($ch2,CURLOPT_HEADER,1);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true );
            curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, 'POST');

            curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch2, CURLOPT_AUTOREFERER, 1);
            curl_setopt($ch2, CURLOPT_COOKIESESSION, 1);
            curl_setopt($ch2,CURLOPT_HTTPHEADER,$header);

            curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($loinInfo));
            curl_setopt($ch2,CURLOPT_COOKIE,$cookie1[1].$cookie2[1] );
            $res2 = curl_exec($ch2);
            //curl_close($this->getClient());
            preg_match('/Set-Cookie:(.+?;)/',$res2,$cookie3);
            preg_match('/SET-COOKIE:(.+?;)/',$res2,$cookie4);

            if(isset($cookie4[1]))  $this->cookie = $cookie4[1] . $cookie3[1].$cookie2[1];
            else {
                Yii::error($this->ip.'登录失败');
                exit();
            }
            echo $this->cookie;
        }else{
            Yii::error($this->ip.'登录失败');
            exit();
        }

    }


    /**
     * 总概
     */
    protected function getClusterSystem(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.ClusterRPC';
        $methodName = 'getClusterSystem';
        $result = $this->ownExec($methodClazz,$methodName);
        if ($result){
            $val = [
                'cluster.id' => $result['id'], //集群标识
                'product.version' => $result['codeLevel'],
                'product.name' => $result['name'],
                'timezone' => preg_replace('/\d|\s/','',$result['timeZone']),
                'dev.model' => $result['productName'],
                'product.ip' => $result['consoleIp'],
                //物理容量
                'total.mdisk.capacity' => $result['totalMdiskCapacity'],//总物理容量
                'total.used.capacity' => $result['totalMdiskCapacity'],//已使用存储容量
                'total.free.space' => $result['totalFreeSpace'],//可用容量
                //卷容量
                'total.vdisk.copacity' => $result['totalAllocatedExtentCapacity'], //已配置的总容量
                'total.vdisk.writed.copacity' => $result['totalVdiskCapacity'], //已写入的总容量
            ];
            $this->sendToZabbix('dev.model',$result['productName']);
        }
        $this->allData = ArrayHelper::merge($this->allData,['local' => $val]);
    }

    /**
     * 系统属性
     */
    protected function getSystemProps(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.ClusterRPC';
        $methodName = 'getSystemProps';
        $result = $this->ownExec($methodClazz,$methodName);
        if ($result){
            $val = [
                'io.group.count' => $result['ioGrpCount'], //i/o组
                'control.enc.count' => $result['controlEncCount'], //控制机柜
                'exp.enc.count' => $result['expEncCount'], //扩展机柜
                'internal.capacity' => $result['internalCapacity'],//内部容量
                'dev.temp' => $result['tempC'], //温度 C
                'power.usage' => $result['powerUsage'], //用电量 W
            ];
            $this->sendToZabbix('dev.storage.iogroup.number',$result['ioGrpCount']);
            $this->sendToZabbix('dev.storage.enc.control.number',$result['controlEncCount']);
            $this->sendToZabbix('dev.storage.enc.ext.number',$result['expEncCount']);
            $this->sendToZabbix('dev.storage.internal.size.total',$result['internalCapacity']);
            $this->sendToZabbix('dev.temp.value',$result['tempC']);
            $this->sendToZabbix('dev.power.usage',$result['powerUsage']);

        }
        $val = ArrayHelper::merge($this->allData['local'],$val);
        $this->allData = ArrayHelper::merge($this->allData,['local' => $val]);
    }

    /**
     * 序列号，MTM
     */
    protected function getAboutData(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.BaseRPC';
        $methodName = 'getAboutData';
        $result = $this->ownExec($methodClazz,$methodName);

        if ($result){
            $val = [
                'product.serial' => $result['sn'], //
                'mtm' => $result['mtm'], //MTM
            ];
            $this->sendToZabbix('product.serial',$result['sn']);
            $this->sendToZabbix('dev.mtm',$result['mtm']);
        }
        $val = ArrayHelper::merge($this->allData['local'],$val);
        $this->allData = ArrayHelper::merge($this->allData,['local' => $val]);
    }

    /**
     * 所有池
     */
    protected function getPoolsExtended(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.PoolsRPC';
        $methodName = 'getPoolsExtended';
        $resultArray = $this->ownExec($methodClazz,$methodName);

        if ($resultArray){
            foreach ($resultArray as $result ){
                if(!$result['parentMdiskGrpName']) continue;
                $val[] = [
                    'pool.name' => $result['parentMdiskGrpName'], //
                    'pool.id' => $result['id'], //
                    'pool.status' => $result['status'], //  return string online
                    'pool.raid.number' => $result['mdiskCount'], //磁盘数量
                    'pool.lun.number' => $result['vdiskCount'], //卷数量
                    'pool.capacity.size.total' => $result['capacity'], //总容量
                    'pool.ext.blocks.size' => $result['extentSize'], //块大小 MB
                    'pool.capacity.size.free' => $result['freeCapacity'], //可使用物理容量
                    'pool.lun.size.total' => $result['virtualCapacity'], //卷的空量
                    'pool.capacity.size.used' => $result['usedCapacity'], //已用空间
                    '{#NAME}' => $result['parentMdiskGrpName']
                ];
            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['pool' => $val]);
    }

    /**
     * 所有Mdisk
     */
    protected function getMDisksByPool(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.PhysicalRPC';
        $methodName = 'getMDisksByPool';
        $resultArray = $this->ownExec($methodClazz,$methodName);
        if ($resultArray) {
            foreach ($resultArray as $result){
                foreach ($result as $vo){
                    if(!$vo['name']) continue;
                    $val[] = [
                        'mdisk.id' => $vo['id'], //标识
                        'raid.name' => $vo['name'], //
                        'raid.pool.id' => $vo['mdiskGrpId'], // 所属池ID
                        'raid.pool.name' => $vo['mdiskGrpName'], //所属池名称
                        'raid.size.total' => $vo['capacity'], // 容量
                        'raid.size.free' => $vo['physicalFreeCapacity'], // 可用物量容量
                        'raid.status' => $vo['status'], //状态 （已译）
                        'raid.raid.status' => $vo['raidStatus'], //raid status
                        'raid.type' => $vo['raidLevel'], // raid 类型
                        'raid.tier' => $vo['tier'], // 层 ： 近线磁盘（返回是字符串：tier_nearline，要看js 翻译）
                        '{#NAME}' => $vo['name'],
                    ];
                }
            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['raid' => $val]);
    }

    //卷
    protected function vdisk(){
        $url = "https://$this->ip:443/VDiskGridDataHandler";
        $requestStr = 'count=9999';
        $result = $this->otherExec($url,$requestStr);
        $val = array();
        if (isset($result['items'])){
            foreach ($result['items'] as $vo ){
                if(!$vo['label']) continue;
                $val[] = array(
                    'lun.id' => $vo['volumeId'],//
                    'lun.name' => $vo['label'],//
                    'lun.status' => $vo['itemStatus'],// online
                    'lun.vir.type' => $vo['virtualizationType'],// 虚拟化类型
                    'lun.size.total' => $vo['capacity'],//容量
                    'lun.size.used' => $vo['usedCapacity'],//使用容量
                    'lun.size.total.real' => $vo['realCapacity'],//实际容量
                    'lun.pool.id' => $vo['mdiskGrpId'],// 池ID
                    'lun.pool.name' => $vo['mdiskGrpName'],// 池名称
                    'lun.node' => $vo['preferredNodeId'],// 主机节点
                    'lun.host.mapped' => $vo['isMapped']=='true' ? '是' : '否'  ,//主机映射
                    'lun.io.group.id' => $vo['ioGroupId'],//
                    'lun.io.group.name' => $vo['ioGroupName'],//
                    'lun.uid' => $vo['vdiskUid'],//唯一标识
                    '{#NAME}' => $vo['label'],
                );
            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['lun' => $val]);

    }

    //所有硬盘
    protected function disk(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.PhysicalRPC';
        $methodName = 'getInternalDriveInfo';
        $resultArray = $this->ownExec($methodClazz,$methodName);
        if (isset($resultArray['drives'])) {
            foreach ($resultArray['drives'] as $vo){
                if(!$vo['mdiskName']) continue;
                $val[] = array(
                    'io.group' => $vo['ioGrp'],//
                   //'drive.class' => $vo['driveClass'],//
                    'disk.id' => $vo['id'],//
                    //'disk.uid' => $vo['uid'],//
                    'disk.size' => $vo['capacity'],//
                    'disk.blocks.size' => $vo['blockSize'],// 512byte
                    'disk.mfc' => $vo['vendorId'],// 厂商
                    'disk.model' => $vo['productId'],//产品型号
                    'disk.fru' => $vo['fruPartNumber'],//部件号
                    'disk.fru.id' => $vo['fruIdentity'],//部件标识
                    'disk.speed' => $vo['rpm'],//str_replace('Gb','', $vo['rpm']),//Gb
                    'disl.firmware.level' => $vo['firmwareLevel'],//固件级别
                    'disk.raid.id' => $vo['mdiskId'],//
                    'disk.raid.name' => $vo['mdiskName'],//
                    'member.id' => $vo['memberId'],//
                    'enclosure.id' => $vo['enclosureId'],//  机柜id
                    'slot.id' => $vo['slotId'],// 插槽标识
                    'disk.interface.speed' =>  str_replace('Gb','',$vo['interfaceSpeed']),//  $vo['interfaceSpeed'],//接口速度 Gb
                    'disk.status' => $vo['status'],//
                    'tech.type' => $vo['techType'],//技术类型:近线磁盘
                    'port1.status' => $vo['port1Status'],//
                    'port2.status' => $vo['port2Status'],//
                    '{#NAME}' => 'disk'.$vo['id'],
                );
            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['disk' => $val]);
    }
    protected function getFc(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.ConfigRPC';
        $methodName = 'getFCPorts';
        $resultArray = $this->ownExec($methodClazz,$methodName);
        if($resultArray){
            foreach ($resultArray as $vo){
                if(!$vo['wwpn']) continue;
                $val[] = [
                    'id' => $vo['id'],// 1-1-2-1-0 未明白什么意思
                    'node.id' => $vo['nodeId'],//
                    'node.name' => $vo['nodeName'],//
                    'canister.id' => $vo['canisterId'],//
                    'fc.port.id' => $vo['portId'],// 那一组吧
                    'ifOperStatus' => $vo['status'],//
                    'ifType' => $vo['portType'],//
                    'fc.cluster.use' => $vo['clusterUse'],//不知什么意思
                    'ifSpeed' => str_replace('Gb','',$vo['portSpeed']),//单位Gb
                    'if.fc.wwpn' => $vo['wwpn'],//
                    'fc.nport.id' => $vo['nportid'],//
                    'if.fc.virtua' => $vo['virtualized'],// 虚拟化 yes or no
                    'if.fc.host.io.permitted' => $vo['hostIoPermitted'],//是否允许主机的IO yes or no
                    'if.fc.fabric.wwn' => $vo['fabricWwn'],//
                    //'fc.' => $vo['isLeftRight'],//
                    'fc.owning.node' => $vo['owningNodeId'],// 拥有节点
                    'fc.current.node' => $vo['currentNodeId'],//当前节点
                    'fc.adapter.id' => $vo['adapterId'],//不知什么意思
                    'fc.adapter.port.id' => $vo['adapterPortId'],//不知什么意思
                    '{#NAME}' => 'wwpn_'.$vo['wwpn'],
                ];

            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['fc' => $val]);
    }

    /**
     * 系统信息
     */
    protected function getPerformance(){
        $val = array();
        $methodClazz = 'com.ibm.svc.gui.logic.ClusterRPC';
        $methodName = 'getClusterStats';
        $resultArray = $this->ownExec($methodClazz,$methodName);
        if($resultArray){
            foreach ($resultArray as $vo){
                $vo['{#NAME}'] = $vo['statName'];
                $vo['value'] = $vo['statCurrent'];
                $val[] = $vo;
            }
        }
        $this->allData = ArrayHelper::merge($this->allData,['system' => $val]);
    }

    //容器
    //控制机柜
    //扩展机柜
    //节点


    /**登出
     * @return mixed
     */
    protected function logout()
    {
        // TODO: Implement logout() method.
        $url = "https://$this->ip:443/logout";
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_HTTPHEADER,$this->getHeaderSet());
        curl_setopt($this->getClient(), CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie );
        curl_exec($this->getClient());
        curl_close($this->getClient());
    }

    /**获取数据
     * @return mixed
     */
    protected function getData()
    {
        // TODO: Implement getData() method.
        $this->login();
        $this->getClusterSystem();// 基本信息
        $this->getSystemProps();// 基本信息
        $this->getAboutData();//基本信息
        $this->getPoolsExtended();//池
        $this->getMDisksByPool();// mdisk
        $this->getPerformance();//系统总体性能
        $this->getFc();// 光纤端口
        $this->disk();//硬盘
        $this->vdisk();//卷
        $this->logout();
    }

    /**把获取的数据发送给 zabbix
     * @param $hostname
     */
    public function send(){
        $this->login();
        $this->getClusterSystem();// 基本信息
        $this->getSystemProps();// 基本信息
        $this->getAboutData();//基本信息
        $this->logout();
    }

    public function sendToZabbix($key,$value){
        $command = "/usr/local/zabbix_proxy/bin/zabbix_sender  -z '172.16.86.105' -s  '".$this->class."'  -k $key  -o  '".$value."' ";
        exec($command);
    }

    /**curl_exec
     * @param $url
     * @return mixed
     */
    protected function exec($url)
    {
        // TODO: Implement exec() method.
    }
    /**
     * @param $methodClazz
     * @param $methodName
     * @param $methodArgs
     * @return null
     */
    protected function ownExec($methodClazz,$methodName,$methodArgs=[])
    {
        // TODO: Implement exec() method.
        $url = 'https://'.$this->ip.':443/RPCAdapter';
        //请求参数
        $requestParamsArray = array(
            'clazz' => 'com.ibm.evo.rpc.RPCRequest',
            'methodClazz' => $methodClazz,
            'methodName' => $methodName,
            'methodArgs' => $methodArgs,
        );
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_HTTPHEADER,$this->getHeaderSet());
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, json_encode($requestParamsArray) );
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie );
        $result = curl_exec($this->getClient());
        //curl_close($this->getClient());
        if(curl_error($this->getClient()) === false){
            Yii::error(curl_error($this->getClient()));
            exit();
        }else{
            $arr = json_decode($result,true);
            if(empty($arr['result'])) Yii::error($result);
            return $arr['result'];
        }
        return null;
    }

    protected function otherExec($url,$requestStr='')
    {
        curl_setopt($this->getClient(),CURLOPT_URL,$url);
        curl_setopt($this->getClient(),CURLOPT_HTTPHEADER,$this->getHeaderSet());
        curl_setopt($this->getClient(), CURLOPT_POSTFIELDS, $requestStr );
        curl_setopt($this->getClient(),CURLOPT_COOKIE,$this->cookie );
        $result = curl_exec($this->getClient());
        //curl_close($this->getClient());
        if(curl_error($this->getClient()) === false){
            Yii::error(curl_error($this->getClient()));
        }else{
            return json_decode($result,true);
        }
        return null;
    }

    /**
     * @return array
     */
    protected function getHeaderSet(){
        return  array(
            'Accept: */*',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            //'Content-Length: 128',
            'Content-Type: application/json-rpc',
            "Host: $this->ip:443",
            "Origin: https://$this->ip:443",
            'Pragma: no-cache',
            "Referer: https://$this->ip:443",
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.79 Safari/537.36',
            'X-Requested-With: XMLHttpRequest',
            'Referrer Policy: no-referrer-when-downgrade',
            "Remote Address: $this->ip:443",
        );
    }

}