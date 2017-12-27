<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/25
 * Time: 12:12
 */
namespace app\admin\model;
use think\Model;

class Retail extends Model
{
    //自动写入创建及修改的时间戳
    protected $autoWriteTimestamp = true;

    //获取器自动处理state字段
    protected function getStateAttr($value)
    {
        $state = [0 => '未通过' , 1 => '通过'];
        return $state[$value];
    }

    protected function getCreateTimeAttr($value)
    {
        return date('Y-m-d' , $value);
    }
}