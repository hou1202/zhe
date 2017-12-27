<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 16:10
 */
namespace app\admin\model;

use think\Model;

class Background  extends Model
{
    //自动写入创建及修改的时间戳
    protected $autoWriteTimestamp = true;

    //获取器自动处理state字段
    /*public function getStateAttr($value){
        $state = [0 => '未通过' , 1 => '通过'];
        return $state[$value];
    }*/


    public function getUpdateTimeAttr($value){
        return date('Y-m-d' , $value);
    }





}