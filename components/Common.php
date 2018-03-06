<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 17:27
 */

namespace app\components;


class Common
{
    /**查找机器型号
     * @param $class
     * @return string
     */
    public static function findClass($class){
        $arr = \Yii::$app->params['versions'];
        foreach ($arr as $vo){
            if (preg_match("/$vo/i",$class)){
                return strtoupper($vo);
            }
        }
    }

}

