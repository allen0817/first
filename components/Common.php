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
    public static function findClass($class){
        $arr = ['inspur','ibm'];
        foreach ($arr as $vo){
            if (preg_match("/$vo/i",$class)){
                return strtoupper($vo);
            }
        }
    }

}

