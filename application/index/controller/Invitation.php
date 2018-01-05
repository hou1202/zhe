<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/5
 * Time: 17:50
 */
namespace app\index\controller;


use app\common\controller\CommController;
use think\Hook;
use think\Cookie;
use app\index\model\User;
use app\common\controller\ReturnJson;

class Invitation extends CommController
{
    public function invitation(){
        Hook::listen('CheckAuth', $params);
        $uid = Cookie::get('user');
        $user = new User();
        $result = $user-> getUserInfoByMobile($uid);
        if (!$result) {
            return ReturnJson::ReturnA("未查找到你们账户信息，请重新确认账户，或注册...");
        }
        return $this->fetch('personal/invitation', ['invitation' => $result]);
    }
}