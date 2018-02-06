<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/26
 * Time: 15:22
 */

namespace app\index\controller;
use app\common\controller\CommController;
use app\common\controller\ReturnJson;
use app\index\model\User;
use think\Hook;
use think\Cookie;

class Personal extends CommController {

    public function personal(){

        //检测用户登录状态
        Hook::listen('CheckAuth',$params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user -> field('id,user_name,portrait,phone,invite,balance,grade,state')
                            -> where('phone',$uid)
                            -> find();
        //var_dump($result);die;
        if(!$result){
            //return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
            return $this ->jsonFail('未查找到你们账户信息，请重新确认账户，或注册...');
        }
        if($result['state'] == 0){
            //return ReturnJson::ReturnA("您的帐号处于异常状态，无法登录，请联系管理员...");
            return $this ->jsonFail('您的帐号处于异常状态，无法登录，请联系管理员...');
        }
        return $this -> fetch('personal/personal',['User'=>$result]);
    }

}