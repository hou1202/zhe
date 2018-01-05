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

    protected $autoWriteTimestamp = true;

    //密码MD5自动加密
    protected function setPasswordAttr($value){
        return md5($value);
    }

    protected function getGradeAttr($value){
        $grade = [0 => '青铜小兵' , 1 => '白银连长', 2 => '黄金将军', 3 => '铂金元帅', 4 => '王者大帝'];
        return $grade[$value];
    }

    /*
     * @ getUserInfoByMobile    通过手机号码获取用户完整信息
     * $phone   用户手机号码
     * @return 用户完整信息
     * */
    public function getUserInfoByMobile($phone){
        return $this->where(['phone' => $phone])->find();
    }

    /*
     * @ getParentUserIdByInvite    通过邀请码，获取父级用户ID
     * $invite 父级用户邀请码
     * @return id
     * */
    public function getParentUserIdByInvite($invite){
        return $this -> field('id') -> where('invite',$invite) -> find();
    }

    /*
     * @ getLoginUserInfoByPhone 通过手机号码/密码，获取登录用户信息
     * $phone   登录用户手机号码
     * $password    登录用户密码
     * @return  id,phone,state
     * */
    public function getLoginUserInfoByPhone($phone,$password){
        return $this -> field('id,phone,state')
                     -> where('phone',$phone)
                     -> where('password',md5($password))
                     -> find();
    }



}