<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27
 * Time: 17:46
 */
namespace app\admin\model;

use think\Model;

class Video extends Model{

    protected $autoWriteTimestamp = true;

    //获取器自动处理state字段
    public function getStateAttr($value){
        $state = [0 => '未通过' , 1 => '通过'];
        return $state[$value];
    }


    public function getUpdateTimeAttr($value){
        return date('Y-m-d' , $value);
    }


}