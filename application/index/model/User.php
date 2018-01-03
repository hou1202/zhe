<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2017/12/27
 * Time: 14:14
 */
namespace app\index\model;

use think\Model;

class User extends Model
{
    public static $tableName = 'think_user';

    //密码MD5自动加密
    protected function setPasswordAttr($value){
        return md5($value);
    }

    protected function getGradeAttr($value){
        $grade = [0 => '青铜小兵' , 1 => '白银连长', 2 => '黄金将军', 3 => '铂金元帅', 4 => '王者大帝'];
        return $grade[$value];
    }

    public function getUserInfoByMobile($phone){
        return $this->where(['phone' => $phone])->find();
    }
}